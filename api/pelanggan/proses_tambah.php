<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/../../includes/koneksi.php';

$id     = $_POST['id'] ?? null;
$nama   = trim($_POST['nama'] ?? '');
$email  = trim($_POST['email'] ?? '');
$noHp   = trim($_POST['no_hp'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');

$errors = [];

if ($nama === '') {
    $errors[] = "Nama pelanggan wajib diisi.";
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    $redirectUrl = $id ? "tambah.php?id=" . urlencode($id) : "tambah.php";
    header("Location: " . $redirectUrl);
    exit;
}

if ($id) {
    $stmt = $pdo->prepare(
        "UPDATE pelanggan 
         SET nama = :nama, email = :email, no_hp = :no_hp, alamat = :alamat 
         WHERE id = :id"
    );
    $stmt->execute([
        'nama'   => $nama,
        'email'  => $email,
        'no_hp'  => $noHp,
        'alamat' => $alamat,
        'id'     => $id,
    ]);

    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Data pelanggan berhasil diperbarui.'];
} else {
    $stmt = $pdo->prepare(
        "INSERT INTO pelanggan (nama, email, no_hp, alamat)
         VALUES (:nama, :email, :no_hp, :alamat)"
    );
    $stmt->execute([
        'nama'   => $nama,
        'email'  => $email,
        'no_hp'  => $noHp,
        'alamat' => $alamat,
    ]);

    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Pelanggan berhasil ditambahkan.'];
}

header('Location: list.php');
exit;