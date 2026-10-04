<?php
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Akses ditolak.');
}
require_once __DIR__ . '/../includes/koneksi.php';

// 1. Hapus seluruh data tabel UMKM.
// CASCADE agar tabel yang saling berhubungan (pesanan -> pelanggan/produk) ikut direset.
try {
    $pdo->exec("TRUNCATE TABLE pesanan, produk, pelanggan RESTART IDENTITY CASCADE");
} catch (PDOException $e) {
    error_log('Reset data gagal: ' . $e->getMessage());
    http_response_code(500);
    exit('Gagal mereset data. Silakan coba lagi.');
}

// 2. Akhiri sesi lama, lalu buat sesi baru (ID baru) hanya untuk membawa pesan sukses
$_SESSION = [];
session_destroy();
session_id(session_create_id());
session_start();
$_SESSION['flash'] = [
    'type'  => 'success',
    'pesan' => 'Seluruh data produk, pelanggan, dan pesanan berhasil direset. Silakan login kembali.'
];

header('Location: /WebUMKM/auth/login.php');
exit;