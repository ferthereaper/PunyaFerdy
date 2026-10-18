<?php
// Proteksi CSRF (Cross-Site Request Forgery) dengan token per-session.
// session.php sudah memulai session (handler database), jadi cukup di-require.
require_once __DIR__ . '/session.php';

// Membuat token acak satu kali per session, lalu memakainya kembali.
function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Mencetak input hidden yang ditaruh di dalam setiap form POST.
function csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="'
        . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

// Dipanggil di awal setiap proses_*.php / aksi hapus, sebelum menyentuh database.
function csrf_verify()
{
    $kirim = $_POST['csrf_token'] ?? '';
    $asli = $_SESSION['csrf_token'] ?? '';

    if (!is_string($kirim) || $asli === '' || !hash_equals($asli, $kirim)) {
        http_response_code(403);
        die('Permintaan ditolak: token CSRF tidak valid.');
    }
}
