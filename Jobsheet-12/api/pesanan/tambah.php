<?php
require_once __DIR__ . '/../../includes/session.php';

require_once __DIR__ . '/../../includes/auth.php';
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
        // Hanya pesanan yang masih diproses yang boleh diubah (stok sudah dipotong).
        if ($data['status'] !== 'diproses') {
            $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Hanya pesanan berstatus diproses yang bisa diubah.'];
            header('Location: /PunyaFerdy/Jobsheet-12/api/pesanan/list.php');
            exit;
        }
        $is_edit = true;
        $pesanan = $data;
    }
}

// Option dropdown untuk relasi
$pelangganList = $pdo->query("SELECT id, nama FROM pelanggan ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
// Hanya produk yang masih tersedia (stok > 0); saat edit, produk pada pesanan ini tetap tampil.
$stProduk = $pdo->prepare("SELECT id, nama_produk, harga, stok FROM produk WHERE stok > 0 OR id = :cur ORDER BY nama_produk ASC");
$stProduk->execute(['cur' => (int) ($pesanan['id_produk'] ?: 0)]);
$produkList = $stProduk->fetchAll(PDO::FETCH_ASSOC);
$bisaPesan = !empty($pelangganList) && !empty($produkList);

$page_title = $is_edit ? "Edit Pesanan" : "Tambah Pesanan";
include __DIR__ . '/../../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<section>
    <h2><?php echo $is_edit ? "Edit Pesanan" : "Tambah Pesanan Baru"; ?></h2>

    <?php if ($flash): ?>
        <p class="flash flash-<?php echo e($flash['type']); ?>"><?php echo e($flash['pesan']); ?></p>
    <?php endif; ?>

    <?php if (!$bisaPesan): ?>
        <p>Pesanan belum bisa dibuat: pastikan ada data pelanggan dan minimal satu produk dengan stok lebih dari 0.</p>
    <?php else: ?>
    <form id="form-tambah" method="post" action="proses_tambah.php">
        <?php echo csrf_field(); ?>
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?php echo e($pesanan['id']); ?>">
        <?php endif; ?>

        <p>
            <label for="id_pelanggan">Pelanggan</label><br>
            <select id="id_pelanggan" name="id_pelanggan" required>
                <option value="">-- Pilih Pelanggan --</option>
                <?php foreach ($pelangganList as $p): ?>
                    <option value="<?php echo e($p['id']); ?>" <?php echo ($p['id'] == $pesanan['id_pelanggan']) ? 'selected' : ''; ?>>
                        <?php echo e($p['nama']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="id_produk">Produk</label><br>
            <select id="id_produk" name="id_produk" required>
                <option value="">-- Pilih Produk --</option>
                <?php foreach ($produkList as $pr): ?>
                    <option value="<?php echo e($pr['id']); ?>" <?php echo ($pr['id'] == $pesanan['id_produk']) ? 'selected' : ''; ?>>
                        <?php echo e($pr['nama_produk']); ?> (Rp <?php echo number_format($pr['harga'], 0, ',', '.'); ?>) &mdash; stok: <?php echo e($pr['stok']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="jumlah">Jumlah</label><br>
            <input type="number" id="jumlah" name="jumlah" min="1" value="<?php echo e($pesanan['jumlah']); ?>" required>
        </p>

        <p>
            <button type="submit"><?php echo $is_edit ? "Simpan Perubahan" : "Simpan"; ?></button>
        </p>
    </form>
    <?php endif; ?>
</section>
<?php include __DIR__ . '/../../includes/footer.php'; ?>