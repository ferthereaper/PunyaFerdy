<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/../../includes/koneksi.php';

// Logika Hapus Data Pesanan
if (isset($_GET['action']) && $_GET['action'] === 'hapus' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM pesanan WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['flash'] = [
        'type' => 'success',
        'pesan' => 'Data pesanan berhasil dihapus!'
    ];

    header("Location: /WebUMKM/api/pesanan/list.php");
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
        <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['pesan']); ?></p>
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
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($daftarPesanan)): ?>
                    <tr>
                        <td colspan="6">Belum ada data pesanan. Silakan tambah lewat menu "Tambah Pesanan".</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($daftarPesanan as $pesanan): ?>
                        <tr>
                            <td>#<?php echo $pesanan['id']; ?></td>
                            <td><?php echo htmlspecialchars($pesanan['nama_pelanggan'] ?? 'Pelanggan Terhapus'); ?></td>
                            <td><?php echo htmlspecialchars($pesanan['nama_produk'] ?? 'Produk Terhapus'); ?></td>
                            <td><?php echo $pesanan['jumlah']; ?></td>
                            <td>Rp <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?></td>
                            <td>
                                <a href="/pesanan/tambah.php?id=<?php echo $pesanan['id']; ?>" class="btn">Edit</a>
                                <a href="/pesanan/list.php?action=hapus&id=<?php echo $pesanan['id']; ?>" 
                                   class="btn-hapus" 
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus pesanan ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>