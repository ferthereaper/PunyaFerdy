<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = "Daftar Pesanan";
require __DIR__ . '/../../includes/koneksi.php';
include __DIR__ . '/../../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Query Join untuk mengambil nama pelanggan dari tabel pelanggan
$sql = "SELECT pesanan.id, pelanggan.nama AS nama_pelanggan, pesanan.status, pesanan.total_harga, pesanan.created_at 
        FROM pesanan 
        LEFT JOIN pelanggan ON pesanan.pelanggan_id = pelanggan.id 
        ORDER BY pesanan.id DESC";
$daftarPesanan = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
        <section>
            <h2>Daftar Pesanan</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
            <?php endif; ?>

            <div class="search-box">
                <label for="search-input">Cari Pesanan</label>
                <input type="text" id="search-input" placeholder="Ketik nama pelanggan...">
            </div>

            <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($daftarPesanan)): ?>
                    <tr>
                        <td colspan="5">Belum ada data pesanan. Silakan tambah lewat menu "Tambah Pesanan".</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($daftarPesanan as $pesanan): ?>
                        <tr>
                            <td>#<?php echo $pesanan['id']; ?></td>
                            <td><?php echo htmlspecialchars($pesanan['nama_pelanggan'] ?? 'Umum'); ?></td>
                            <td>Rp <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?></td>
                            <td><strong><?php echo htmlspecialchars($pesanan['status']); ?></strong></td>
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
<?php include __DIR__ . '/../../includes/footer.php'; ?>