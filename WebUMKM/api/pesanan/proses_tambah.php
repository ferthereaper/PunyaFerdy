<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/../../includes/koneksi.php';

$id          = $_POST['id'] ?? null;
$idPelanggan = $_POST['id_pelanggan'] ?? '';
$idProduk    = $_POST['id_produk'] ?? '';
$jumlah      = (int)($_POST['jumlah'] ?? 1);

$errors = [];

if (empty($idPelanggan)) {
    $errors[] = "Pelanggan wajib dipilih.";
}
if (empty($idProduk)) {
    $errors[] = "Produk wajib dipilih.";
}
if ($jumlah < 1) {
    $errors[] = "Jumlah minimal 1.";
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    $redirectUrl = $id ? "tambah.php?id=" . urlencode($id) : "tambah.php";
    header("Location: " . $redirectUrl);
    exit;
}

// Hitung total harga otomatis berdasarkan harga produk di DB
$stmtHarga = $pdo->prepare("SELECT harga FROM produk WHERE id = ?");
$stmtHarga->execute([$idProduk]);
$produkData = $stmtHarga->fetch(PDO::FETCH_ASSOC);

$hargaSatuan = $produkData['harga'] ?? 0;
$totalHarga  = $hargaSatuan * $jumlah;

if ($id) {
    $stmt = $pdo->prepare(
        "UPDATE pesanan 
         SET id_pelanggan = :id_pelanggan, id_produk = :id_produk, jumlah = :jumlah, total_harga = :total_harga 
         WHERE id = :id"
    );
    $stmt->execute([
        'id_pelanggan' => $idPelanggan,
        'id_produk'    => $idProduk,
        'jumlah'       => $jumlah,
        'total_harga'  => $totalHarga,
        'id'           => $id,
    ]);

    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Data pesanan berhasil diperbarui.'];
} else {
    $stmt = $pdo->prepare(
        "INSERT INTO pesanan (id_pelanggan, id_produk, jumlah, total_harga)
         VALUES (:id_pelanggan, :id_produk, :jumlah, :total_harga)"
    );
    $stmt->execute([
        'id_pelanggan' => $idPelanggan,
        'id_produk'    => $idProduk,
        'jumlah'       => $jumlah,
        'total_harga'  => $totalHarga,
    ]);

    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Pesanan berhasil dibuat.'];
}

header('Location: list.php');
exit;