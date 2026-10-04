<?php
require_once __DIR__ . '/session.php';

// Cek apakah session user/login sudah ada
if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: /WebUMKM/auth/login.php");
    exit();
}
?>