<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/../../includes/koneksi.php';

$nama   = trim($_POST['nama'] ?? '');
$email  = trim($_POST['email'] ?? '');
$noHp   = trim($_POST['no_hp'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');

$errors = [];

// Validasi Nama
if ($nama === '') {
    $errors[] = "Nama pelanggan wajib diisi.";
} elseif (strlen($nama) < 3) {
    $errors[] = "Nama minimal terdiri dari 3 karakter.";
}

// Validasi Email (Opsional, tapi jika diisi harus format email valid)
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Format email tidak valid.";
}

// Validasi No. HP
if ($noHp !== '') {
    if (!preg_match('/^[0-9]+$/', $noHp)) {
        $errors[] = "No. HP hanya boleh berisi angka.";
    } elseif (strlen($noHp) < 10 || strlen($noHp) > 13) {
        $errors[] = "No. HP harus berjumlah antara 10 sampai 13 digit.";
    }
}

// Jika ada error, kembalikan ke form tambah
if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: tambah.php');
    exit;
}

// Simpan ke tabel pelanggan Supabase (PostgreSQL)
$stmt = $pdo->prepare(
    "INSERT INTO pelanggan (nama, email, no_hp, alamat)
     VALUES (:nama, :email, :no_hp, :alamat)
     RETURNING id"
);
$stmt->execute([
    'nama'   => $nama,
    'email'  => $email,
    'no_hp'  => $noHp,
    'alamat' => $alamat,
]);

$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Pelanggan berhasil ditambahkan.'];
header('Location: list.php');
exit;