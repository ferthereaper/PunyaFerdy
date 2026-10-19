<?php
require_once __DIR__ . '/../../includes/session.php';

require_once __DIR__ . '/../../includes/auth.php';
require __DIR__ . '/../../includes/koneksi.php';

$page_title = "Pesanan Aktif";
include __DIR__ . '/../../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$daftarAktif = $pdo->query(
    "SELECT p.id, p.jumlah, p.total_harga, p.created_at,
            pl.nama AS nama_pelanggan, pr.nama_produk
     FROM pesanan p
     LEFT JOIN pelanggan pl ON pl.id = p.id_pelanggan
     LEFT JOIN produk pr ON pr.id = p.id_produk
     WHERE p.status = 'diproses'
     ORDER BY p.id ASC"
)->fetchAll(PDO::FETCH_ASSOC);
?>
<section>
    <h2>Pesanan Aktif</h2>

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
                    <th>Dipesan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($daftarAktif)): ?>
                    <tr>
                        <td colspan="7">Tidak ada pesanan yang sedang diproses.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($daftarAktif as $p): ?>
                        <tr>
                            <td>#<?php echo e($p['id']); ?></td>
                            <td><?php echo e($p['nama_pelanggan'] ?? 'Pelanggan Terhapus'); ?></td>
                            <td><?php echo e($p['nama_produk'] ?? 'Produk Terhapus'); ?></td>
                            <td><?php echo e($p['jumlah']); ?></td>
                            <td>Rp <?php echo number_format($p['total_harga'], 0, ',', '.'); ?></td>
                            <td><?php echo $p['created_at'] ? e(date('d-m-Y H:i', strtotime($p['created_at']))) : '-'; ?></td>
                            <td>
                                <form class="form-aksi" method="post" action="<?php echo BASE_URL; ?>/api/pesanan/proses_status.php">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="id" value="<?php echo e($p['id']); ?>">
                                    <input type="hidden" name="aksi" value="selesai">
                                    <button type="submit" class="btn-selesai" onclick="return confirm('Tandai pesanan ini selesai?')">Selesaikan</button>
                                </form>
                                <form class="form-aksi" method="post" action="<?php echo BASE_URL; ?>/api/pesanan/proses_status.php">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="id" value="<?php echo e($p['id']); ?>">
                                    <input type="hidden" name="aksi" value="batal">
                                    <button type="submit" class="btn-batal" onclick="return confirm('Batalkan pesanan ini? Stok produk akan dikembalikan.')">Batalkan</button>
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
