<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/../includes/koneksi.php';

$namaProduk = trim($_POST['nama_produk'] ?? '');
$kategori   = trim($_POST['kategori'] ?? '');
$harga      = trim($_POST['harga'] ?? '0');
$stok       = trim($_POST['stok'] ?? '0');
$deskripsi  = trim($_POST['deskripsi'] ?? '');

$errors = [];

if ($namaProduk === '') {
    $errors[] = "Nama produk wajib diisi.";
}
if (!is_numeric($harga) || $harga < 0) {
    $errors[] = "Harga harus berupa angka bernilai positif.";
}
if (!is_numeric($stok) || $stok < 0) {
    $errors[] = "Stok harus berupa angka bernilai positif.";
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: tambah.php');
    exit;
}

$stmt = $pdo->prepare(
    "INSERT INTO produk (nama_produk, kategori, harga, stok, deskripsi)
     VALUES (:nama_produk, :kategori, :harga, :stok, :deskripsi)
     RETURNING id"
);
$stmt->execute([
    'nama_produk' => $namaProduk,
    'kategori'    => $kategori,
    'harga'       => $harga,
    'stok'        => $stok,
    'deskripsi'   => $deskripsi,
]);

$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Produk berhasil ditambahkan.'];
header('Location: list.php');
exit;