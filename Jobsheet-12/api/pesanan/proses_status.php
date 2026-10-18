<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/csrf.php';
require __DIR__ . '/../../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: aktif.php');
    exit;
}

csrf_verify();

$id   = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
$aksi = $_POST['aksi'] ?? '';

if ($id === false || $id < 1 || !in_array($aksi, ['selesai', 'batal'], true)) {
    header('Location: aktif.php');
    exit;
}

try {
    $pdo->beginTransaction();

    // Kunci baris pesanan agar klik ganda / dua kasir tidak bisa memproses
    // pesanan yang sama dua kali (stok bertambah dua kali saat dibatalkan).
    $st = $pdo->prepare("SELECT id_produk, jumlah, status FROM pesanan WHERE id = :id FOR UPDATE");
    $st->execute(['id' => $id]);
    $pesanan = $st->fetch(PDO::FETCH_ASSOC);

    if (!$pesanan) {
        throw new RuntimeException('Pesanan tidak ditemukan.');
    }
    if ($pesanan['status'] !== 'diproses') {
        throw new RuntimeException('Pesanan ini sudah ' . $pesanan['status'] . ' sebelumnya.');
    }

    if ($aksi === 'selesai') {
        $pdo->prepare("UPDATE pesanan SET status = 'selesai', tanggal_selesai = NOW() WHERE id = :id")
            ->execute(['id' => $id]);
        $pesan = 'Pesanan ditandai selesai.';
    } else {
        // Batal: stok produk dikembalikan (jika produknya masih ada).
        if ($pesanan['id_produk'] !== null) {
            $pdo->prepare("UPDATE produk SET stok = stok + :j WHERE id = :id")
                ->execute(['j' => (int) $pesanan['jumlah'], 'id' => $pesanan['id_produk']]);
        }
        $pdo->prepare("UPDATE pesanan SET status = 'dibatalkan', tanggal_selesai = NOW() WHERE id = :id")
            ->execute(['id' => $id]);
        $pesan = 'Pesanan dibatalkan, stok produk dikembalikan.';
    }

    $pdo->commit();
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
    error_log('Ubah status pesanan gagal: ' . $e->getMessage());
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Terjadi kesalahan sistem, perubahan dibatalkan.'];
}

header('Location: aktif.php');
exit;
