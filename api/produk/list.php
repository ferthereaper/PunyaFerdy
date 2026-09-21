<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = "Daftar Produk";
require __DIR__ . '/../includes/koneksi.php';
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$daftarProduk = $pdo->query("SELECT * FROM produk ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
        <section>
            <h2>Daftar Produk</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
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
                                <button type="button">Edit</button>
                                <button type="button" class="btn-hapus">Hapus</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>