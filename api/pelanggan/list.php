<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$page_title = "Daftar Pelanggan";
include __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$daftarPelanggan = $pdo->query("SELECT * FROM pelanggan ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
        <section>
            <h2>Daftar Pelanggan</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
            <?php endif; ?>

            <div class="search-box">
                <label for="search-input">Cari Nama Pelanggan</label>
                <input type="text" id="search-input" placeholder="Ketik nama pelanggan...">
            </div>

            <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID Pelanggan</th>
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
                        <td colspan="6">Belum ada data pelanggan. Silakan tambah lewat menu "Tambah Pelanggan".</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($daftarPelanggan as $pelanggan): ?>
                        <tr>
                            <td><?php echo $pelanggan['id']; ?></td>
                            <td><?php echo htmlspecialchars($pelanggan['nama']); ?></td>
                            <td><?php echo htmlspecialchars($pelanggan['email']); ?></td>
                            <td><?php echo htmlspecialchars($pelanggan['no_hp']); ?></td>
                            <td><?php echo htmlspecialchars($pelanggan['alamat']); ?></td>
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