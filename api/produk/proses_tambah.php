<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/../../includes/koneksi.php';

// Tangkap id (jika ada, artinya mode EDIT)
$id         = $_POST['id'] ?? null;

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

// Jika ada error validasi, kembalikan ke form yang sesuai
if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    
    // Redirect kembali membawa parameter id jika dalam mode edit
    $redirectUrl = $id ? "tambah.php?id=" . urlencode($id) : "tambah.php";
    header("Location: " . $redirectUrl);
    exit;
}

if ($id) {
    // === LOGIKA UPDATE (JIKA ID TERSEDIA) ===
    $stmt = $pdo->prepare(
        "UPDATE produk 
         SET nama_produk = :nama_produk, 
             kategori    = :kategori, 
             harga       = :harga, 
             stok        = :stok, 
             deskripsi   = :deskripsi 
         WHERE id = :id"
    );
    $stmt->execute([
        'nama_produk' => $namaProduk,
        'kategori'    => $kategori,
        'harga'       => $harga,
        'stok'        => $stok,
        'deskripsi'   => $deskripsi,
        'id'          => $id,
    ]);

    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Produk berhasil diperbarui.'];
} else {
    // === LOGIKA INSERT (JIKA ID TIDAK ADA) ===
    $stmt = $pdo->prepare(
        "INSERT INTO produk (nama_produk, kategori, harga, stok, deskripsi)
         VALUES (:nama_produk, :kategori, :harga, :stok, :deskripsi)"
    );
    $stmt->execute([
        'nama_produk' => $namaProduk,
        'kategori'    => $kategori,
        'harga'       => $harga,
        'stok'        => $stok,
        'deskripsi'   => $deskripsi,
    ]);

    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Produk berhasil ditambahkan.'];
}

// Redirect ke daftar produk
header('Location: list.php');
exit;