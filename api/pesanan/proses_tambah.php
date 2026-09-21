<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/../../includes/koneksi.php';

$pelangganId = trim($_POST['pelanggan_id'] ?? '');
$totalHarga  = trim($_POST['total_harga'] ?? '0');
$status      = trim($_POST['status'] ?? 'Diproses');

$errors = [];

if ($pelangganId === '') {
    $errors[] = "Pelanggan wajib dipilih.";
}
if (!is_numeric($totalHarga) || $totalHarga < 0) {
    $errors[] = "Total harga harus berupa angka bernilai positif.";
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: tambah.php');
    exit;
}

$stmt = $pdo->prepare(
    "INSERT INTO pesanan (pelanggan_id, total_harga, status)
     VALUES (:pelanggan_id, :total_harga, :status)
     RETURNING id"
);
$stmt->execute([
    'pelanggan_id' => $pelangganId,
    'total_harga'  => $totalHarga,
    'status'       => $status,
]);

$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Pesanan berhasil ditambahkan.'];
header('Location: list.php');
exit;