<?php
require_once __DIR__ . '/../../includes/session.php';

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/csrf.php';
require __DIR__ . '/../../includes/koneksi.php';

// Logika Hapus Data Pesanan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'hapus') {
    csrf_verify();
    $id = (int)($_POST['id'] ?? 0);
    // Pesanan "diproses" sudah memotong stok, jadi stok dikembalikan saat dihapus.
    try {
        $pdo->beginTransaction();

        $st = $pdo->prepare("SELECT id_produk, jumlah, status FROM pesanan WHERE id = ? FOR UPDATE");
        $st->execute([$id]);
        $lama = $st->fetch(PDO::FETCH_ASSOC);

        if ($lama) {
            if ($lama['status'] === 'diproses' && $lama['id_produk'] !== null) {
                $pdo->prepare("UPDATE produk SET stok = stok + ? WHERE id = ?")
                    ->execute([(int) $lama['jumlah'], $lama['id_produk']]);
            }
            $pdo->prepare("DELETE FROM pesanan WHERE id = ?")->execute([$id]);
        }

        $pdo->commit();
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }

    $_SESSION['flash'] = [
        'type' => 'success',
        'pesan' => 'Data pesanan berhasil dihapus!'
    ];

    header('Location: ' . BASE_URL . '/api/pesanan/list.php');
    exit;
}

$page_title = "Daftar Pesanan";
include __DIR__ . '/../../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Query Join untuk mengambil Nama Pelanggan & Nama Produk
$query = "SELECT pesanan.*, pelanggan.nama AS nama_pelanggan, produk.nama_produk 
          FROM pesanan 
          LEFT JOIN pelanggan ON pesanan.id_pelanggan = pelanggan.id 
          LEFT JOIN produk ON pesanan.id_produk = produk.id 
          ORDER BY pesanan.id DESC";
$daftarPesanan = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<section>
    <h2>Daftar Pesanan</h2>

    <?php if ($flash): ?>
        <p class="flash flash-<?php echo e($flash['type']); ?>"><?php echo e($flash['pesan']); ?></p>
    <?php endif; ?>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Pelanggan</th>
                    <th>Produk</th>
                    <th>Jumlah</th>
                    <th>Total Harga</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($daftarPesanan)): ?>
                    <tr>
                        <td colspan="7">Belum ada data pesanan. Silakan tambah lewat menu "Tambah Pesanan".</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($daftarPesanan as $pesanan): ?>
                        <tr>
                            <td>#<?php echo e($pesanan['id']); ?></td>
                            <td><?php echo e($pesanan['nama_pelanggan'] ?? 'Pelanggan Terhapus'); ?></td>
                            <td><?php echo e($pesanan['nama_produk'] ?? 'Produk Terhapus'); ?></td>
                            <td><?php echo e($pesanan['jumlah']); ?></td>
                            <td>Rp <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?></td>
                            <td><?php echo e($pesanan['status']); ?></td>
                            <td>
                                <?php if ($pesanan['status'] === 'diproses'): ?>
                                    <a href="<?php echo BASE_URL; ?>/api/pesanan/tambah.php?id=<?php echo e($pesanan['id']); ?>" class="btn">Edit</a>
                                <?php endif; ?>
                                <form class="form-hapus" method="post" action="<?php echo BASE_URL; ?>/api/pesanan/list.php">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="action" value="hapus">
                                    <input type="hidden" name="id" value="<?php echo e($pesanan['id']); ?>">
                                    <button type="submit" class="btn-hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus pesanan ini?')">Hapus</button>
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