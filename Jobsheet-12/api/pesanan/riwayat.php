<?php
require_once __DIR__ . '/../../includes/session.php';

require_once __DIR__ . '/../../includes/auth.php';
require __DIR__ . '/../../includes/koneksi.php';

$page_title = "Riwayat Pesanan Pelanggan";
include __DIR__ . '/../../includes/header.php';

$daftarPelanggan = $pdo->query("SELECT id, nama FROM pelanggan ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);

$pelangganId = filter_var($_GET['pelanggan_id'] ?? '', FILTER_VALIDATE_INT);
$pelanggan = null;
$riwayat = [];

if ($pelangganId !== false && $pelangganId > 0) {
    $st = $pdo->prepare("SELECT id, nama FROM pelanggan WHERE id = :id");
    $st->execute(['id' => $pelangganId]);
    $pelanggan = $st->fetch(PDO::FETCH_ASSOC);

    if ($pelanggan) {
        $st = $pdo->prepare(
            "SELECT p.id, p.jumlah, p.total_harga, p.status, p.created_at, p.tanggal_selesai, pr.nama_produk
             FROM pesanan p
             LEFT JOIN produk pr ON pr.id = p.id_produk
             WHERE p.id_pelanggan = :pelanggan_id
             ORDER BY p.id DESC"
        );
        $st->execute(['pelanggan_id' => $pelangganId]);
        $riwayat = $st->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
<section>
    <h2>Riwayat Pesanan Pelanggan</h2>

    <div class="search-box">
        <form method="get" action="<?php echo BASE_URL; ?>/api/pesanan/riwayat.php">
            <label for="pelanggan_id">Pilih Pelanggan</label>
            <select id="pelanggan_id" name="pelanggan_id" required>
                <option value="">-- Pilih Pelanggan --</option>
                <?php foreach ($daftarPelanggan as $pl): ?>
                    <option value="<?php echo e($pl['id']); ?>" <?php echo ($pelanggan && (int) $pl['id'] === (int) $pelanggan['id']) ? 'selected' : ''; ?>><?php echo e($pl['nama']); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Tampilkan</button>
        </form>
    </div>

    <?php if ($pelanggan): ?>
        <h3>Riwayat: <?php echo e($pelanggan['nama']); ?></h3>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Produk</th>
                        <th>Jumlah</th>
                        <th>Total Harga</th>
                        <th>Dipesan</th>
                        <th>Selesai/Batal</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($riwayat)): ?>
                        <tr>
                            <td colspan="7">Pelanggan ini belum pernah memesan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($riwayat as $r): ?>
                            <tr>
                                <td>#<?php echo e($r['id']); ?></td>
                                <td><?php echo e($r['nama_produk'] ?? 'Produk Terhapus'); ?></td>
                                <td><?php echo e($r['jumlah']); ?></td>
                                <td>Rp <?php echo number_format($r['total_harga'], 0, ',', '.'); ?></td>
                                <td><?php echo $r['created_at'] ? e(date('d-m-Y H:i', strtotime($r['created_at']))) : '-'; ?></td>
                                <td><?php echo $r['tanggal_selesai'] ? e(date('d-m-Y H:i', strtotime($r['tanggal_selesai']))) : '-'; ?></td>
                                <td><?php echo e($r['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php elseif ($pelangganId !== false && $pelangganId > 0): ?>
        <p>Pelanggan tidak ditemukan.</p>
    <?php else: ?>
        <p>Pilih pelanggan untuk melihat riwayat pesanannya.</p>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
