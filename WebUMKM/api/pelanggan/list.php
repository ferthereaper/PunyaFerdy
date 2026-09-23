<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/../../includes/koneksi.php';

// Logika Hapus Data
if (isset($_GET['action']) && $_GET['action'] === 'hapus' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM pelanggan WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['flash'] = [
        'type' => 'success',
        'pesan' => 'Data pelanggan berhasil dihapus!'
    ];

    // FIX 1: Ubah redirect agar kembali ke path yang benar di Vercel
    header("Location: /WebUMKM/api/pelanggan/list.php");
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
        <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['pesan']); ?></p>
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
                            <td><?php echo htmlspecialchars($pelanggan['nama']); ?></td>
                            <td><?php echo htmlspecialchars($pelanggan['email'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($pelanggan['no_hp'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($pelanggan['alamat'] ?? '-'); ?></td>
                            <td>
                                <!-- FIX 2: Sesuaikan path tombol Edit & Hapus -->
                                <a href="/WebUMKM/api/pelanggan/tambah.php?id=<?php echo $pelanggan['id']; ?>" class="btn">Edit</a>
                                <a href="/WebUMKM/api/pelanggan/list.php?action=hapus&id=<?php echo $pelanggan['id']; ?>" 
                                   class="btn-hapus" 
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus pelanggan ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>