<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

require __DIR__ . '/../includes/koneksi.php';

$nama = trim($_POST['nama'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$errors = [];
if ($nama === '') {
    $errors[] = "Nama wajib diisi.";
}
if ($username === '') {
    $errors[] = "Username wajib diisi.";
}
if (strlen($password) < 8) {
    $errors[] = "Password minimal 8 karakter.";
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: register.php');
    exit;
}

try {
    // Cek ketersediaan username
    $cek = $pdo->prepare("SELECT id FROM users WHERE username = :username");
    $cek->execute(['username' => $username]);
    if ($cek->fetch()) {
        $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Username sudah digunakan.'];
        header('Location: register.php');
        exit;
    }

    // Simpan data user baru
    $stmt = $pdo->prepare(
        "INSERT INTO users (nama, username, password, role) VALUES (:nama, :username, :password, 'Kasir')"
    );
    $stmt->execute([
        'nama' => $nama,
        'username' => $username,
        'password' => password_hash($password, PASSWORD_DEFAULT),
    ]);

    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Registrasi berhasil, silakan login.'];
    header('Location: login.php');
    exit;

} catch (PDOException $e) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Terjadi kesalahan sistem, silakan coba lagi.'];
    header('Location: register.php');
    exit;
}