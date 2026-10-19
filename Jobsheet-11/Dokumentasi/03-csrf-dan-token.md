# 3. CSRF & Token Verifikasi

Bab paling teknis di jobsheet ini: kita memakai **token acak per session**.

## 3.1 Apa itu CSRF?

**CSRF (Cross-Site Request Forgery)** memungkinkan **situs lain** memicu permintaan ke aplikasi ini **atas nama** kasir yang sedang login, tanpa kasir itu sadar.

## 3.2 Contoh Serangan pada Aplikasi UMKM

Kasir sedang login, lalu membuka situs jahat di tab lain. Situs itu berisi:

```html
<form action="https://aplikasi-umkm.example/api/produk/list.php" method="post" id="jahat">
    <input type="hidden" name="action" value="hapus">
    <input type="hidden" name="id" value="1">
</form>
<script>document.getElementById('jahat').submit();</script>
```

Browser tetap menyertakan **cookie session** kasir, sehingga server mengira permintaan itu sah. Memeriksa `REQUEST_METHOD === 'POST'` **tidak cukup**, karena form jahat juga memakai POST.

### Temuan khusus: Hapus lewat link GET

Di Jobsheet 10, tombol Hapus berupa link:

```php
<a href=".../api/produk/list.php?action=hapus&id=<?php echo $produk['id']; ?>">Hapus</a>
```

Ini lebih parah: penyerang cukup menaruh `<img src="https://.../list.php?action=hapus&id=1">` di halaman mana pun, dan produk terhapus saat kasir membukanya. Aksi yang **mengubah data** tidak boleh lewat GET. Di Jobsheet 11, Hapus menjadi form POST dengan token:

```php
<form method="post" action=".../api/produk/list.php" class="form-hapus">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="action" value="hapus">
    <input type="hidden" name="id" value="<?php echo (int) $produk['id']; ?>">
    <button type="submit" class="btn-hapus" onclick="return confirm('...')">Hapus</button>
</form>
```

Penangannya di bagian atas `list.php`:

```php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'hapus') {
    csrf_verify();
    $id = (int)($_POST['id'] ?? 0);
    // DELETE ... WHERE id = ?
}
```

## 3.3 Ide Dasar Token CSRF

Setiap form `POST` yang sah disisipi **token rahasia** yang hanya diketahui server dan halaman yang benar-benar dibuka dari aplikasi ini. Saat form dikirim, server membandingkan token itu dengan yang tersimpan di session. Situs jahat tidak bisa mengetahuinya, jadi permintaannya **ditolak** meski cookie session valid.

## 3.4 Membuat Token: `csrf_token()`

```php
function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
```

- **`random_bytes(32)`** menghasilkan 32 byte acak yang aman secara kriptografis (berbeda dari `rand()`).
- **`bin2hex()`** mengubahnya menjadi teks heksadesimal (64 karakter) yang aman ditaruh di HTML.
- Token **dibuat sekali per session**, lalu dipakai ulang selama session aktif.
- Token disimpan di `$_SESSION`. Pada project ini, session disimpan di tabel `php_sessions` (lihat `includes/session.php`), jadi token ikut tersimpan di database dan tetap konsisten di Vercel.

## 3.5 Menyisipkan Token: `csrf_field()`

```php
function csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}
```

Dipakai sebagai `<?php echo csrf_field(); ?>` **di dalam** setiap `<form method="post">`. Pada project ini dipasang di:

| Form | File |
|---|---|
| Login, Registrasi | `auth/login.php`, `auth/register.php` |
| Tambah/Edit Produk, Pelanggan, Pesanan | `api/*/tambah.php` |
| Hapus Produk, Pelanggan, Pesanan | `api/*/list.php` |
| Reset Data | `includes/header.php` |

## 3.6 Memverifikasi Token: `csrf_verify()`

```php
function csrf_verify()
{
    $token = $_POST['csrf_token'] ?? '';
    if ($token === '' || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('Permintaan ditolak: token CSRF tidak valid atau kedaluwarsa.');
    }
}
```

- **`hash_equals()`** membandingkan dua string dengan waktu eksekusi yang selalu sama, sehingga tidak membocorkan informasi lewat perbedaan waktu (praktik standar untuk token).
- **`http_response_code(403)`** menandakan "dipahami tapi ditolak".
- **`die()`** menghentikan script seketika, sehingga `INSERT`/`UPDATE`/`DELETE` tidak sempat berjalan.

## 3.7 Memanggil `csrf_verify()` di Awal Setiap Proses

```php
// api/produk/proses_tambah.php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/csrf.php';
require __DIR__ . '/../../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

csrf_verify();

$namaProduk = trim($_POST['nama_produk'] ?? '');
```

**Urutan penting:** `auth.php` dulu, baru `csrf.php`. Pengunjung yang belum login langsung diarahkan ke Login dan tidak perlu dicek token. Pola yang sama dipakai di `list.php` (untuk Hapus) dan `api/reset_session.php`.

Untuk halaman publik (`proses_login.php`, `proses_register.php`) tidak ada `auth.php`; `csrf_verify()` dipanggil setelah `session.php` memulai session.

## 3.8 Membuktikannya

1. **Tanpa token → 403.** Login lewat browser, salin cookie `PHPSESSID` dari DevTools, lalu:
   ```bash
   curl -i -X POST http://localhost/Jobsheet-11/api/produk/proses_tambah.php \
        -b "PHPSESSID=<isi cookie>" -d "nama_produk=x&harga=1000&stok=1"
   ```
   Respons: `403` dengan pesan "Permintaan ditolak...". Tanpa `-b` (belum login), kamu justru diarahkan ke Login. Itu membuktikan urutan guard di [§3.7](#37-memanggil-csrf_verify-di-awal-setiap-proses).
2. **Hapus lewat GET tidak lagi bekerja.** Buka `.../api/produk/list.php?action=hapus&id=1` di browser. Tidak ada data yang terhapus.

Lanjut ke: [Session Fixation](04-session-fixation.md)
