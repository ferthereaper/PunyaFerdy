<?php
require_once __DIR__ . '/session.php';
$sudahLogin = !empty($_SESSION['logged_in']);
$isAdmin    = ($_SESSION['role'] ?? '') === 'admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pemesanan UMKM<?php echo isset($page_title) ? ' | ' . htmlspecialchars($page_title) : ''; ?></title>
    <link rel="stylesheet" href="/WebUMKM/assets/css/style.css">
</head>
<body>
    <header>
        <h1>Pemesanan UMKM</h1>

        <?php if ($sudahLogin): ?>
        <button type="button" id="nav-toggle-btn" class="nav-toggle-label" aria-label="Menu">&#9776;</button>
        <nav>
            <ul>
                <li><a href="/WebUMKM/api/index.php">Beranda</a></li>
                <li><a href="/WebUMKM/api/produk/list.php">Daftar Produk</a></li>
                <li><a href="/WebUMKM/api/produk/tambah.php">Tambah Produk</a></li>
                <li><a href="/WebUMKM/api/pelanggan/list.php">Daftar Pelanggan</a></li>
                <li><a href="/WebUMKM/api/pelanggan/tambah.php">Tambah Pelanggan</a></li>
                <li><a href="/WebUMKM/api/pesanan/list.php">Daftar Pesanan</a></li>
                <li><a href="/WebUMKM/api/pesanan/tambah.php">Tambah Pesanan</a></li>
                <?php if ($isAdmin): ?>
                <li>
                    <form action="/WebUMKM/api/reset_session.php" method="POST" class="form-reset" onsubmit="return confirm('Apakah Anda yakin ingin mereset seluruh data?');">
                        <button type="submit" class="btn-reset">Reset Data</button>
                    </form>
                </li>
                <?php endif; ?>
            </ul>
        </nav>

        <!-- Elemen Navigasi User / Logout -->
        <nav class="navbar">
            <span>Halo, <?php echo htmlspecialchars($_SESSION['nama'] ?? 'Pengguna'); ?></span>
            <a href="/WebUMKM/auth/logout.php" class="btn-logout">Logout</a>
        </nav>
        <?php endif; ?>
    </header>

    <main>