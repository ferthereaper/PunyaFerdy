<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah session user/login sudah ada
if (!isset($_SESSION['user_id']) && !isset($_SESSION['logged_in'])) {
    header("Location: ../auth/login.php");
    exit();
}
?>