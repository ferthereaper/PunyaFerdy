<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/../../includes/koneksi.php';

$is_edit = false;
$pesanan = [
    'id' => '',
    'id_pelanggan' => '',
    'id_produk' => '',
    'jumlah' => '1',
    'total_harga' => '0'
];

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM pesanan WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        $is_edit = true;
        $pesanan = $data;
    }
}

// Option dropdown untuk relasi
$pelangganList = $pdo->query("SELECT id, nama FROM pelanggan ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
$produkList    = $pdo->query("SELECT id, nama_produk, harga FROM produk ORDER BY nama_produk ASC")->fetchAll(PDO::FETCH_ASSOC);

$page_title = $is_edit ? "Edit Pesanan" : "Tambah Pesanan";
include __DIR__ . '/../../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<section>
    <h2><?php echo $is_edit ? "Edit Pesanan" : "Tambah Pesanan Baru"; ?></h2>

    <?php if ($flash): ?>
        <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['pesan']); ?></p>
    <?php endif; ?>

    <form id="form-tambah" method="post" action="proses_tambah.php">
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?php echo $pesanan['id']; ?>">
        <?php endif; ?>

        <p>
            <label for="id_pelanggan">Pelanggan</label><br>
            <select id="id_pelanggan" name="id_pelanggan" required>
                <option value="">-- Pilih Pelanggan --</option>
                <?php foreach ($pelangganList as $p): ?>
                    <option value="<?php echo $p['id']; ?>" <?php echo ($p['id'] == $pesanan['id_pelanggan']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($p['nama']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="id_produk">Produk</label><br>
            <select id="id_produk" name="id_produk" required>
                <option value="">-- Pilih Produk --</option>
                <?php foreach ($produkList as $pr): ?>
                    <option value="<?php echo $pr['id']; ?>" <?php echo ($pr['id'] == $pesanan['id_produk']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($pr['nama_produk']); ?> (Rp <?php echo number_format($pr['harga'], 0, ',', '.'); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="jumlah">Jumlah</label><br>
            <input type="number" id="jumlah" name="jumlah" min="1" value="<?php echo htmlspecialchars($pesanan['jumlah']); ?>" required>
        </p>

        <p>
            <button type="submit"><?php echo $is_edit ? "Simpan Perubahan" : "Simpan"; ?></button>
        </p>
    </form>
</section>
<?php include __DIR__ . '/../../includes/footer.php'; ?>