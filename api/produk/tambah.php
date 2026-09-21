<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$page_title = "Tambah Produk";
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
        <section>
            <h2>Tambah Produk Baru</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
            <?php endif; ?>

            <form id="form-tambah" method="post" action="proses_tambah.php">
                <p>
                    <label for="nama_produk">Nama Produk</label><br>
                    <input type="text" id="nama_produk" name="nama_produk" required>
                </p>
                <p>
                    <label for="kategori">Kategori</label><br>
                    <input type="text" id="kategori" name="kategori">
                </p>
                <p>
                    <label for="harga">Harga (Rp)</label><br>
                    <input type="number" id="harga" name="harga" min="0" required>
                </p>
                <p>
                    <label for="stok">Stok</label><br>
                    <input type="number" id="stok" name="stok" min="0" value="0" required>
                </p>
                <p>
                    <label for="deskripsi">Deskripsi</label><br>
                    <textarea id="deskripsi" name="deskripsi" rows="3"></textarea>
                </p>
                <p>
                    <button type="submit">Simpan</button>
                </p>
            </form>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>