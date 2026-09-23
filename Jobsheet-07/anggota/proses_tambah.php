<?php
session_start();

$nama = trim($_POST['nama'] ?? '');
$noAnggota = trim($_POST['no_anggota'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$noHp = trim($_POST['no_hp'] ?? '');

$errors = [];
if ($nama === '') {
    $errors[] = "Nama wajib diisi.";
// Jobsheet 7 Latihan 2
} elseif (strlen($nama) < 3) {
    $errors[] = "Nama minimal terdiri dari 3 karakter.";
}
if ($noAnggota === '') {
    $errors[] = "No. Anggota wajib diisi.";
// Jobsheet 7 Latihan 2
} elseif (!preg_match('/^[a-zA-Z0-9-]+$/', $noAnggota)) {
    $errors[] = "No. Anggota hanya boleh berisi huruf, angka, dan tanda hubung.";
}

// Jobsheet 7 Latihan 2
if ($noHp !== '') {
    if (!preg_match('/^[0-9]+$/', $noHp)) {
        $errors[] = "No. HP hanya boleh berisi angka.";
    } elseif (strlen($noHp) < 10 || strlen($noHp) > 13) {
        $errors[] = "No. HP harus berjumlah antara 10 sampai 13 digit.";
    }
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: tambah.php');
    exit;
}

if (!isset($_SESSION['anggota'])) {
    $_SESSION['anggota'] = [];
}

$_SESSION['anggota'][] = [
    'nama' => $nama,
    'no_anggota' => $noAnggota,
    'alamat' => $alamat,
    'no_hp' => $noHp,
];

$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Anggota berhasil ditambahkan.'];
header('Location: list.php');
exit;