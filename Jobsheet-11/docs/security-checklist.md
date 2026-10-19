# Security Checklist — Jobsheet 11 (Sistem Pemesanan UMKM)

Audit keamanan terhadap kode Jobsheet 7–10. Ada 5 kerentanan yang diperiksa.

| # | Kerentanan | Ditemukan di | Sebelum | Sesudah (perbaikan + bukti) |
|---|---|---|---|---|
| 1 | SQL Injection | `auth/`, `api/produk`, `api/pelanggan`, `api/pesanan` | Semua query berisi input pengguna sudah memakai prepared statement (`prepare` + `execute`). `query()`/`exec()` hanya dipakai untuk SQL statis (`COUNT(*)`, `SELECT` daftar, `TRUNCATE`). | **Tidak ada perubahan.** Diaudit ulang, aman. Uji: login dengan username `' OR '1'='1` → tetap "Username atau password salah". |
| 2 | XSS | `includes/header.php`, semua `list.php`, `tambah.php`, `login.php`, `register.php` | Output memakai `htmlspecialchars()` sebagian-sebagian; pesan flash di `login.php`/`register.php` dicetak mentah; beberapa ID/angka dicetak tanpa casting. | Fungsi `e()` baru di `includes/helpers.php` dipakai di **semua** output data (nama produk, nama/alamat pelanggan, nama kasir di navbar, flash, `BASE_URL`). Angka & ID dicetak dengan `(int)`. Uji: tambah produk bernama `<script>alert(1)</script>` → tampil sebagai teks di Daftar Produk, tidak ada pop-up. |
| 3 | CSRF | semua form `POST` + tombol Hapus + Reset Data | Tidak ada token. **Tombol Hapus berupa link GET** (`list.php?action=hapus&id=...`), sehingga bisa dipicu dari situs lain hanya dengan `<img src="...">`. | `includes/csrf.php` (`csrf_token`, `csrf_field`, `csrf_verify`). `csrf_field()` dipasang di semua form (Login, Register, Tambah/Edit Produk, Pelanggan, Pesanan, Hapus, Reset Data). `csrf_verify()` dipanggil di semua file proses. Hapus diubah dari link GET menjadi form POST. Uji: `curl -X POST .../api/produk/proses_tambah.php -d "nama_produk=x"` tanpa token → HTTP 403. |
| 4 | Validasi & Sanitasi Input | `api/*/proses_tambah.php` | Validasi wajib-isi dan `is_numeric()` sudah ada. ID dari `$_POST` dipakai apa adanya. | Tambahan type casting: `id`, `id_pelanggan`, `id_produk` memakai `(int)`. Validasi lama tetap dipertahankan. |
| 5 | Session Fixation | `auth/proses_login.php` | ID session sama sebelum dan sesudah login. | `session_regenerate_id(true)` dipanggil tepat setelah `password_verify()` berhasil, sebelum `$_SESSION['user_id']` diisi. Karena session disimpan di database (`DbSessionHandler`), `destroy()` menghapus baris session lama. |

## Catatan Implementasi

- Di file proses, **`auth.php` dijalankan sebelum `csrf.php`**: pengguna yang belum login langsung diarahkan ke Login dan tidak pernah sampai ke pengecekan CSRF.
- Di `proses_login.php` dan `proses_register.php` (halaman publik, tanpa `auth.php`), `csrf_verify()` dipanggil setelah session dimulai.
- Di `api/reset_session.php`, cek metode `POST` dan role admin dilakukan lebih dulu, baru `csrf_verify()`.
- Form pencarian/filter tabel berjalan di sisi browser (JavaScript) dan tidak mengirim data ke server, jadi tidak butuh token.

## Temuan Tambahan (di luar 5 poin utama)

| Temuan | Risiko | Saran |
|---|---|---|
| `includes/koneksi.php` menyimpan host, user, dan **password database** langsung di kode. | Tinggi bila repo publik. | Ganti password, lalu baca dari environment variable (`getenv('DB_PASS')`) dan set di Vercel. |
| `auth/logout.php` diakses lewat link GET. | Rendah (hanya memaksa logout). | Ubah menjadi form POST + `csrf_verify()`. |
| `sql/pemesanan_umkm.sql` tidak sinkron dengan kode (tabel `users` bersintaks MySQL, kolom `pesanan` berbeda, tabel `php_sessions` belum ada). | Menyulitkan setup ulang. | Samakan skema dengan database Supabase yang sebenarnya. |
| `api/reset_session.php` hanya mengizinkan role `admin`, sedangkan registrasi membuat role `Kasir`. | Fitur Reset tidak bisa dipakai akun baru. | Pastikan ada satu akun `admin` di tabel `users`. |
