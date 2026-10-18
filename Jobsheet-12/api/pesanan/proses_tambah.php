<?php
require_once __DIR__ . '/../../includes/auth.php';   // sudah memulai session & mengecek login
require_once __DIR__ . '/../../includes/csrf.php';
require __DIR__ . '/../../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

csrf_verify();

$idRaw       = $_POST['id'] ?? '';
$id          = $idRaw === '' ? null : filter_var($idRaw, FILTER_VALIDATE_INT);
$idPelanggan = filter_var($_POST['id_pelanggan'] ?? '', FILTER_VALIDATE_INT);
$idProduk    = filter_var($_POST['id_produk'] ?? '', FILTER_VALIDATE_INT);
$jumlah      = filter_var($_POST['jumlah'] ?? '', FILTER_VALIDATE_INT);

$errors = [];
if ($id === false) {
    $errors[] = "ID pesanan tidak valid.";
}
if ($idPelanggan === false || $idPelanggan < 1) {
    $errors[] = "Pelanggan wajib dipilih.";
}
if ($idProduk === false || $idProduk < 1) {
    $errors[] = "Produk wajib dipilih.";
}
if ($jumlah === false || $jumlah < 1) {
    $errors[] = "Jumlah minimal 1.";
}

$kembali = $id ? "tambah.php?id=" . urlencode((string) $id) : "tambah.php";

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header("Location: " . $kembali);
    exit;
}

$sukses = false;

try {
    $pdo->beginTransaction();

    $cek = $pdo->prepare("SELECT id FROM pelanggan WHERE id = :id");
    $cek->execute(['id' => $idPelanggan]);
    if (!$cek->fetch()) {
        throw new RuntimeException('Pelanggan tidak ditemukan.');
    }

    // Mode edit: kunci baris pesanan, hanya pesanan "diproses" yang boleh diubah.
    $lama = null;
    if ($id) {
        $st = $pdo->prepare("SELECT id_produk, jumlah, status FROM pesanan WHERE id = :id FOR UPDATE");
        $st->execute(['id' => $id]);
        $lama = $st->fetch(PDO::FETCH_ASSOC);
        if (!$lama) {
            throw new RuntimeException('Pesanan tidak ditemukan.');
        }
        if ($lama['status'] !== 'diproses') {
            throw new RuntimeException('Hanya pesanan berstatus diproses yang bisa diubah.');
        }
    }

    // Kunci baris produk (urut berdasarkan id agar dua transaksi tidak saling
    // menunggu / deadlock). FOR UPDATE mencegah dua pesanan bersamaan
    // sama-sama membaca stok lama (race condition: stok negatif).
    $idKunci = [$idProduk];
    if ($lama && $lama['id_produk'] !== null) {
        $idKunci[] = (int) $lama['id_produk'];
    }
    $idKunci = array_values(array_unique($idKunci));
    sort($idKunci);

    $produk = [];
    $kunci = $pdo->prepare("SELECT id, nama_produk, harga, stok FROM produk WHERE id = :id FOR UPDATE");
    foreach ($idKunci as $pid) {
        $kunci->execute(['id' => $pid]);
        $row = $kunci->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $produk[(int) $row['id']] = $row;
        }
    }

    if (!isset($produk[$idProduk])) {
        throw new RuntimeException('Produk tidak ditemukan.');
    }

    // Edit: kembalikan dulu stok dari pesanan lama, lalu hitung ulang.
    if ($lama && $lama['id_produk'] !== null && isset($produk[(int) $lama['id_produk']])) {
        $lamaId = (int) $lama['id_produk'];
        $pdo->prepare("UPDATE produk SET stok = stok + :j WHERE id = :id")
            ->execute(['j' => (int) $lama['jumlah'], 'id' => $lamaId]);
        $produk[$lamaId]['stok'] = (int) $produk[$lamaId]['stok'] + (int) $lama['jumlah'];
    }

    $target = $produk[$idProduk];
    if ((int) $target['stok'] < $jumlah) {
        throw new RuntimeException(
            'Stok "' . $target['nama_produk'] . '" tidak cukup (tersisa ' . (int) $target['stok'] . ').'
        );
    }

    $pdo->prepare("UPDATE produk SET stok = stok - :j WHERE id = :id")
        ->execute(['j' => $jumlah, 'id' => $idProduk]);

    // Total harga dihitung di server dari harga produk yang sedang dikunci.
    $totalHarga = round((float) $target['harga'] * $jumlah, 2);

    if ($id) {
        $pdo->prepare(
            "UPDATE pesanan
             SET id_pelanggan = :id_pelanggan, id_produk = :id_produk, jumlah = :jumlah, total_harga = :total_harga
             WHERE id = :id"
        )->execute([
            'id_pelanggan' => $idPelanggan,
            'id_produk'    => $idProduk,
            'jumlah'       => $jumlah,
            'total_harga'  => $totalHarga,
            'id'           => $id,
        ]);
        $pesan = 'Data pesanan berhasil diperbarui, stok produk disesuaikan.';
    } else {
        $pdo->prepare(
            "INSERT INTO pesanan (id_pelanggan, id_produk, jumlah, total_harga, status)
             VALUES (:id_pelanggan, :id_produk, :jumlah, :total_harga, 'diproses')"
        )->execute([
            'id_pelanggan' => $idPelanggan,
            'id_produk'    => $idProduk,
            'jumlah'       => $jumlah,
            'total_harga'  => $totalHarga,
        ]);
        $pesan = 'Pesanan berhasil dibuat, stok produk dikurangi.';
    }

    $pdo->commit();
    $sukses = true;
    $_SESSION['flash'] = ['type' => 'success', 'pesan' => $pesan];
} catch (RuntimeException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => $e->getMessage()];
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Simpan pesanan gagal: ' . $e->getMessage());
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Terjadi kesalahan sistem, pesanan dibatalkan.'];
}

header('Location: ' . ($sukses ? 'list.php' : $kembali));
exit;
