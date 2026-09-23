<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/../../includes/koneksi.php';

$is_edit = false;
$pelanggan = [
    'id' => '',
    'nama' => '',
    'email' => '',
    'no_hp' => '',
    'alamat' => ''
];

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM pelanggan WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        $is_edit = true;
        $pelanggan = $data;
    }
}

$page_title = $is_edit ? "Edit Pelanggan" : "Tambah Pelanggan";
include __DIR__ . '/../../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<section>
    <h2><?php echo $is_edit ? "Edit Pelanggan" : "Tambah Pelanggan Baru"; ?></h2>

    <?php if ($flash): ?>
        <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['pesan']); ?></p>
    <?php endif; ?>

    <form id="form-tambah" method="post" action="proses_tambah.php">
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?php echo $pelanggan['id']; ?>">
        <?php endif; ?>

        <p>
            <label for="nama">Nama Pelanggan</label><br>
            <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($pelanggan['nama']); ?>" required>
        </p>
        <p>
            <label for="email">Email</label><br>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($pelanggan['email'] ?? ''); ?>">
        </p>
        <p>
            <label for="no_hp">No. HP</label><br>
            <input type="text" id="no_hp" name="no_hp" value="<?php echo htmlspecialchars($pelanggan['no_hp'] ?? ''); ?>">
        </p>
        <p>
            <label for="alamat">Alamat</label><br>
            <textarea id="alamat" name="alamat" rows="3"><?php echo htmlspecialchars($pelanggan['alamat'] ?? ''); ?></textarea>
        </p>
        <p>
            <button type="submit"><?php echo $is_edit ? "Simpan Perubahan" : "Simpan"; ?></button>
        </p>
    </form>
</section>
<?php include __DIR__ . '/../../includes/footer.php'; ?>