<?php
require_once __DIR__ . '/../../includes/session.php';

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/csrf.php';
require __DIR__ . '/../../includes/koneksi.php';

// Logika Hapus Data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'hapus') {
    csrf_verify();
    $id = (int)($_POST['id'] ?? 0);
    $stmt = $pdo->prepare("DELETE FROM pelanggan WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['flash'] = [
        'type' => 'success',
        'pesan' => 'Data pelanggan berhasil dihapus!'
    ];

    // FIX 1: Ubah redirect agar kembali ke path yang benar di Vercel
    header("Location: /PunyaFerdy/Jobsheet-12/api/pelanggan/list.php");
    exit;
}

$page_title = "Daftar Pelanggan";
include __DIR__ . '/../../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$daftarPelanggan = $pdo->query("SELECT * FROM pelanggan ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<section>
    <h2>Daftar Pelanggan</h2>

    <?php if ($flash): ?>
        <p class="flash flash-<?php echo e($flash['type']); ?>"><?php echo e($flash['pesan']); ?></p>
    <?php endif; ?>

    <div class="search-box">
        <label for="search-input">Cari Nama Pelanggan</label>
        <input type="text" id="search-input" placeholder="Ketik nama pelanggan...">
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>No. HP</th>
                    <th>Alamat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($daftarPelanggan)): ?>
                    <tr>
                        <td colspan="5">Belum ada data pelanggan. Silakan tambah lewat menu "Tambah Pelanggan".</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($daftarPelanggan as $pelanggan): ?>
                        <tr>
                            <td><?php echo e($pelanggan['nama']); ?></td>
                            <td><?php echo e($pelanggan['email'] ?? '-'); ?></td>
                            <td><?php echo e($pelanggan['no_hp'] ?? '-'); ?></td>
                            <td><?php echo e($pelanggan['alamat'] ?? '-'); ?></td>
                            <td>
                                <!-- FIX 2: Sesuaikan path tombol Edit & Hapus -->
                                <a href="/PunyaFerdy/Jobsheet-12/api/pelanggan/tambah.php?id=<?php echo e($pelanggan['id']); ?>" class="btn">Edit</a>
                                <form class="form-hapus" method="post" action="/PunyaFerdy/Jobsheet-12/api/pelanggan/list.php">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="action" value="hapus">
                                    <input type="hidden" name="id" value="<?php echo e($pelanggan['id']); ?>">
                                    <button type="submit" class="btn-hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus pelanggan ini?')">Hapus</button>
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