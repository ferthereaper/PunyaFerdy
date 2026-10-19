<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/csrf.php';
$sudahLogin = !empty($_SESSION['logged_in']);
$isAdmin    = ($_SESSION['role'] ?? '') === 'admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pemesanan UMKM<?php echo isset($page_title) ? ' | ' . e($page_title) : ''; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
    <header>
        <h1>Pemesanan UMKM</h1>

        <?php if ($sudahLogin): ?>
        <button type="button" id="nav-toggle-btn" class="nav-toggle-label" aria-label="Menu">&#9776;</button>
        <nav>
            <ul>
                <li><a href="<?php echo BASE_URL; ?>/api/index.php">Beranda</a></li>
                <li><a href="<?php echo BASE_URL; ?>/api/produk/list.php">Daftar Produk</a></li>
                <li><a href="<?php echo BASE_URL; ?>/api/produk/tambah.php">Tambah Produk</a></li>
                <li><a href="<?php echo BASE_URL; ?>/api/pelanggan/list.php">Daftar Pelanggan</a></li>
                <li><a href="<?php echo BASE_URL; ?>/api/pelanggan/tambah.php">Tambah Pelanggan</a></li>
                <li><a href="<?php echo BASE_URL; ?>/api/pesanan/list.php">Daftar Pesanan</a></li>
                <li><a href="<?php echo BASE_URL; ?>/api/pesanan/tambah.php">Tambah Pesanan</a></li>
                <li><a href="<?php echo BASE_URL; ?>/api/pesanan/aktif.php">Pesanan Aktif</a></li>
                <li><a href="<?php echo BASE_URL; ?>/api/pesanan/riwayat.php">Riwayat Pelanggan</a></li>
                <?php if ($isAdmin): ?>
                <li>
                    <form action="<?php echo BASE_URL; ?>/api/reset_session.php" method="POST" class="form-reset" onsubmit="return confirm('Apakah Anda yakin ingin mereset seluruh data?');">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn-reset">Reset Data</button>
                    </form>
                </li>
                <?php endif; ?>
            </ul>
        </nav>

        <!-- Elemen Navigasi User / Logout -->
        <nav class="navbar">
            <span>Halo, <?php echo e($_SESSION['nama'] ?? 'Pengguna'); ?></span>
            <a href="<?php echo BASE_URL; ?>/auth/logout.php" class="btn-logout">Logout</a>
        </nav>
        <?php endif; ?>
    </header>

    <main>