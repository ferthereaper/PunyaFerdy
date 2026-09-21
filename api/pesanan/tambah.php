<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = "Tambah Pesanan";
require __DIR__ . '/../includes/koneksi.php';
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Mengambil daftar pelanggan untuk opsi pilihan
$daftarPelanggan = $pdo->query("SELECT id, nama FROM pelanggan ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
        <section>
            <h2>Tambah Pesanan Baru</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
            <?php endif; ?>

            <form id="form-tambah" method="post" action="proses_tambah.php">
                <p>
                    <label for="pelanggan_id">Pelanggan</label><br>
                    <select id="pelanggan_id" name="pelanggan_id" required>
                        <option value="">-- Pilih Pelanggan --</option>
                        <?php foreach ($daftarPelanggan as $pelanggan): ?>
                            <option value="<?php echo $pelanggan['id']; ?>"><?php echo htmlspecialchars($pelanggan['nama']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </p>
                <p>
                    <label for="total_harga">Total Harga (Rp)</label><br>
                    <input type="number" id="total_harga" name="total_harga" min="0" required>
                </p>
                <p>
                    <label for="status">Status</label><br>
                    <select id="status" name="status">
                        <option value="Diproses">Diproses</option>
                        <option value="Selesai">Selesai</option>
                        <option value="Dibatalkan">Dibatalkan</option>
                    </select>
                </p>
                <p>
                    <button type="submit">Simpan</button>
                </p>
            </form>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>