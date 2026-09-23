<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/../../includes/koneksi.php';

// === 1. TAMBAHAN LOGIKA HAPUS DATA DI SINI ===
if (isset($_GET['action']) && $_GET['action'] === 'hapus' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM produk WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['flash'] = [
        'type' => 'success',
        'pesan' => 'Data produk berhasil dihapus!'
    ];

    // Redirect kembali ke halaman list agar URL bersih
    header("Location: /produk/list.php");
    exit;
}

$page_title = "Daftar Produk";
include __DIR__ . '/../../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$daftarProduk = $pdo->query("SELECT * FROM produk ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<section>
    <h2>Daftar Produk</h2>

    <?php if ($flash): ?>
        <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['pesan']); ?></p>
    <?php endif; ?>

    <div class="search-box">
        <label for="search-input">Cari Nama Produk</label>
        <input type="text" id="search-input" placeholder="Ketik nama produk...">
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($daftarProduk)): ?>
                    <tr>
                        <td colspan="5">Belum ada data produk. Silakan tambah lewat menu "Tambah Produk".</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($daftarProduk as $produk): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($produk['nama_produk']); ?></td>
                            <td><?php echo htmlspecialchars($produk['kategori'] ?? '-'); ?></td>
                            <td>Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?></td>
                            <td><?php echo $produk['stok']; ?></td>
                            <td>
                                <!-- === 2. PERUBAHAN TOMBOL AKSI DI SINI === -->
                                <a href="/produk/tambah.php?id=<?php echo $produk['id']; ?>" class="btn">Edit</a>
                                <a href="/produk/list.php?action=hapus&id=<?php echo $produk['id']; ?>" 
                                   class="btn-hapus" 
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>