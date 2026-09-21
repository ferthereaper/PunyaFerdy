<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIMPUS-Mini<?php echo isset($page_title) ? ' | ' . $page_title : ''; ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header>
        <h1>SIMPUS-Mini</h1>
        <button type="button" id="nav-toggle-btn" class="nav-toggle-label" aria-label="Menu">&#9776;</button>
        <nav>
            <ul>
                <li><a href="/">Beranda</a></li>
                <li><a href="/buku/list.php">Daftar Buku</a></li>
                <li><a href="/buku/tambah.php">Tambah Buku</a></li>
                <li><a href="/anggota/list.php">Daftar Anggota</a></li>
                <li><a href="/anggota/tambah.php">Tambah Anggota</a></li><li>
                    <form action="/reset_session.php" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin mereset seluruh data sementara?');">
                        <button type="submit" style="background: none; border: none; color: #999999; font: inherit; cursor: pointer; padding: 0;">Reset Data</button>
                    </form>
                </li>
            </ul>
        </nav>
    </header>

    <main>
