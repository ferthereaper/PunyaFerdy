<?php
require_once __DIR__ . '/../../includes/session.php';

require_once __DIR__ . '/../../includes/auth.php';
require __DIR__ . '/../../includes/koneksi.php';

// Cek apakah ada parameter ID di URL (Mode Edit)
$is_edit = false;
$produk = [
    'id' => '',
    'nama_produk' => '',
    'kategori' => '',
    'harga' => '',
    'stok' => '0',
    'deskripsi' => ''
];

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM produk WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        $is_edit = true;
        $produk = $data;
    }
}

$page_title = $is_edit ? "Edit Produk" : "Tambah Produk";
include __DIR__ . '/../../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
        <section>
            <h2><?php echo $is_edit ? "Edit Produk" : "Tambah Produk Baru"; ?></h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo e($flash['type']); ?>"><?php echo e($flash['pesan']); ?></p>
            <?php endif; ?>

            <form id="form-tambah" method="post" action="proses_tambah.php">
                <?php echo csrf_field(); ?>
                <!-- Input hidden ID untuk nandain kalau ini proses update -->
                <?php if ($is_edit): ?>
                    <input type="hidden" name="id" value="<?php echo e($produk['id']); ?>">
                <?php endif; ?>

                <p>
                    <label for="nama_produk">Nama Produk</label><br>
                    <input type="text" id="nama_produk" name="nama_produk" value="<?php echo e($produk['nama_produk']); ?>" required>
                </p>
                <p>
                    <label for="kategori">Kategori</label><br>
                    <input type="text" id="kategori" name="kategori" value="<?php echo e($produk['kategori'] ?? ''); ?>">
                </p>
                <p>
                    <label for="harga">Harga (Rp)</label><br>
                    <input type="number" id="harga" name="harga" min="0" value="<?php echo e($produk['harga']); ?>" required>
                </p>
                <p>
                    <label for="stok">Stok</label><br>
                    <input type="number" id="stok" name="stok" min="0" value="<?php echo e($produk['stok']); ?>" required>
                </p>
                <p>
                    <label for="deskripsi">Deskripsi</label><br>
                    <textarea id="deskripsi" name="deskripsi" rows="3"><?php echo e($produk['deskripsi'] ?? ''); ?></textarea>
                </p>
                <p>
                    <button type="submit"><?php echo $is_edit ? "Simpan Perubahan" : "Simpan"; ?></button>
                </p>
            </form>
        </section>
<?php include __DIR__ . '/../../includes/footer.php'; ?>