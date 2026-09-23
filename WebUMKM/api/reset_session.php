<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}
// 1. Sertakan koneksi database
require_once __DIR__ . '/../includes/koneksi.php';

// 2. Hapus seluruh data tabel UMKM dari database menggunakan PDO
try {
    // TRUNCATE CASCADE digunakan agar tabel yang saling berhubungan (pesanan ke pelanggan) bisa direset bersamaan
    $query = "TRUNCATE TABLE pesanan, produk, pelanggan RESTART IDENTITY CASCADE";
    $pdo->exec($query);
} catch (PDOException $e) {
    die("Gagal menghapus data: " . $e->getMessage());
}

// 3. Bersihkan data session
$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// 4. Buat session baru untuk pesan sukses
session_start();
$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Seluruh data session, produk, pelanggan, dan pesanan berhasil direset.'];

header('Location: index.php');
exit;