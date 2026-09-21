<?php
session_start();

// 1. Sertakan koneksi database
require_once '/../koneksi.php'; // Sesuaikan dengan nama file koneksi kamu

// 2. Hapus seluruh data buku dari tabel database
$query = "TRUNCATE TABLE buku"; // Atau "DELETE FROM buku" jika ada relasi Foreign Key
mysqli_query($koneksi, $query);

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
$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Seluruh data session dan data buku berhasil direset.'];

header('Location: index.php');
exit;
