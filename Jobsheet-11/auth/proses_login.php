<?php
require_once __DIR__ . '/../includes/session.php';
require __DIR__ . '/../includes/koneksi.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password,$user['password'])) {
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id']   =$user['id'];
    $_SESSION['nama']      =$user['nama'];
    $_SESSION['username']  =$user['username'];
    $_SESSION['role']      =$user['role'];
    
    header("Location: ../api/index.php");
    exit();
} else {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Username atau password salah.'];
    header("Location: login.php?error=invalid");
    exit();
}