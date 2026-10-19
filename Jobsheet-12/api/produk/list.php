<?php
require_once __DIR__ . '/../../includes/session.php';

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/csrf.php';
require __DIR__ . '/../../includes/koneksi.php';

// Logika Hapus Produk
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'hapus') {
    csrf_verify();
    $id = (int)($_POST['id'] ?? 0);
    $stmt = $pdo->prepare("DELETE FROM produk WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['flash'] = [
        'type' => 'success',
        'pesan' => 'Data produk berhasil dihapus!'
    ];

    // Ubah ke path lengkap
    header('Location: ' . BASE_URL . '/api/produk/list.php');
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
        <p class="flash flash-<?php echo e($flash['type']); ?>"><?php echo e($flash['pesan']); ?></p>
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
                            <td><?php echo e($produk['nama'] ?? $produk['nama_produk']); ?></td>
                            <td>Rp <?php echo number_format($produk['harga'] ?? 0, 0, ',', '.'); ?></td>
                            <td><?php echo e($produk['stok'] ?? 0); ?></td>
                            <td>
                                <!-- Link memakai BASE_URL, jadi tidak bergantung pada nama folder -->
                                <a href="<?php echo BASE_URL; ?>/api/produk/tambah.php?id=<?php echo e($produk['id']); ?>" class="btn">Edit</a>
                                <form class="form-hapus" method="post" action="<?php echo BASE_URL; ?>/api/produk/list.php">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="action" value="hapus">
                                    <input type="hidden" name="id" value="<?php echo e($produk['id']); ?>">
                                    <button type="submit" class="btn-hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>