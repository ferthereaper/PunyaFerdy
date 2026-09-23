<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pemesanan UMKM<?php echo isset($page_title) ? ' | ' . $page_title : ''; ?></title>
    <link rel="stylesheet" href="/WebUMKM/assets/css/style.css">
</head>
<body>
    <header>
        <h1>Pemesanan UMKM</h1>
        <button type="button" id="nav-toggle-btn" class="nav-toggle-label" aria-label="Menu">&#9776;</button>
        <nav>
            <ul>
                <li><a href="/">Beranda</a></li>
                <li><a href="/produk/list.php">Daftar Produk</a></li>
                <li><a href="/produk/tambah.php">Tambah Produk</a></li>
                <li><a href="/pelanggan/list.php">Daftar Pelanggan</a></li>
                <li><a href="/pelanggan/tambah.php">Tambah Pelanggan</a></li>
                <li><a href="/pesanan/list.php">Daftar Pesanan</a></li>
                <li><a href="/pesanan/tambah.php">Tambah Pesanan</a></li>
                <li>
                    <form action="/reset_session.php" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin mereset seluruh data sementara?');">
                        <button type="submit" style="background: none; border: none; color: #999999; font: inherit; cursor: pointer; padding: 0;">Reset Data</button>
                    </form>
                </li>
            </ul>
        </nav>
    </header>

    <main>