<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = "Beranda";
require __DIR__ . '/../includes/koneksi.php';
include __DIR__ . '/../includes/header.php';

$totalProduk = $pdo->query("SELECT COUNT(*) FROM produk")->fetchColumn();
$totalPelanggan = $pdo->query("SELECT COUNT(*) FROM pelanggan")->fetchColumn();
?>

<section>
    <h2>Selamat Datang di Sistem Pemesanan UMKM</h2>
    <p>Aplikasi sederhana untuk mengelola data produk dan pelanggan UMKM.</p>
</section>

<section>
    <h2>Ringkasan</h2>
    <article>
        <h3>Total Produk</h3>
        <p><?php echo $totalProduk; ?></p>
    </article>
    <article>
        <h3>Total Pelanggan</h3>
        <p><?php echo $totalPelanggan; ?></p>
    </article>
    <article>
        <h3>Sedang Diproses</h3>
        <p>0</p>
    </article>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>