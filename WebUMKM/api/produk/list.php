<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/../../includes/koneksi.php';

// Logika Hapus Produk
if (isset($_GET['action']) && $_GET['action'] === 'hapus' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM produk WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['flash'] = [
        'type' => 'success',
        'pesan' => 'Data produk berhasil dihapus!'
    ];

    // Ubah ke path lengkap
    header("Location: /WebUMKM/api/produk/list.php");
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
        <label for="search-input">Cari Produk</label>
        <input type="text" id="search-input" placeholder="Ketik nama produk...">
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($daftarProduk)): ?>
                    <tr>
                        <td colspan="4">Belum ada data produk.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($daftarProduk as $produk): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($produk['nama'] ?? $produk['nama_produk']); ?></td>
                            <td>Rp <?php echo number_format($produk['harga'] ?? 0, 0, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($produk['stok'] ?? 0); ?></td>
                            <td>
                                <!-- Ubah link Edit & Hapus menggunakan path lengkap /WebUMKM/api/ -->
                                <a href="/WebUMKM/api/produk/tambah.php?id=<?php echo $produk['id']; ?>" class="btn">Edit</a>
                                <a href="/WebUMKM/api/produk/list.php?action=hapus&id=<?php echo $produk['id']; ?>" 
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