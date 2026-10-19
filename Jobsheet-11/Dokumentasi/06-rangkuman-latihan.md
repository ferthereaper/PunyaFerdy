# 6. Rangkuman & Latihan Lanjutan

## 6.1 Rangkuman

| Bagian | Konsep |
|---|---|
| [Konsep Dasar](01-konsep-dasar-keamanan-web.md) | 5 kerentanan, prinsip "jangan percaya input dari luar" |
| [XSS & `e()`](02-xss-dan-fungsi-e.md) | `htmlspecialchars()`, `ENT_QUOTES`, `(int)` untuk ID |
| [CSRF & Token](03-csrf-dan-token.md) | `random_bytes()`, `hash_equals()`, token per session, Hapus via POST |
| [Session Fixation](04-session-fixation.md) | `session_regenerate_id(true)` setelah login |
| [Audit SQLi & Checklist](05-audit-sql-injection-dan-checklist.md) | Prepared statement, format laporan audit |

## 6.2 Konsep Inti

1. **Audit bisa menghasilkan "sudah aman"**, tidak selalu perbaikan baru.
2. **Escape data sebelum dicetak ke HTML** dengan `e()`, terutama data yang pernah melewati input pengguna.
3. **Metode POST saja tidak cukup mencegah CSRF**, dan **aksi yang mengubah data tidak boleh lewat GET** (contoh nyata: tombol Hapus).
4. **Regenerasi ID session tepat setelah login** menutup session fixation.
5. **Laporan audit yang baik menyertakan bukti pengujian**.

## 6.3 Cara Mencoba Sendiri

1. **CSRF** — `curl -i -X POST .../api/produk/proses_tambah.php -b "PHPSESSID=<cookie login>" -d "nama_produk=x&harga=1000&stok=1"` → HTTP 403.
2. **XSS** — tambah produk bernama `<script>alert(1)</script>` → tampil sebagai teks, tanpa pop-up.
3. **Urutan guard** — `POST` ke `proses_tambah.php` tanpa cookie login → diarahkan ke Login.
4. **SQL Injection** — login dengan username `' OR '1'='1` → tetap gagal.
5. **Hapus via GET** — buka `.../api/produk/list.php?action=hapus&id=1` → tidak ada yang terhapus.
6. **Session fixation** — catat `PHPSESSID` di halaman Login, lalu login. Cookie `PHPSESSID` harus **berubah** setelah login berhasil.

## 6.4 Ide Latihan Tambahan

1. **Form pencarian tidak diberi token.** Pencarian di Daftar Produk/Pelanggan berjalan di JavaScript (tanpa kirim data ke server) dan sifatnya hanya membaca. Diskusikan: kenapa risiko CSRF pada aksi *baca* jauh lebih kecil daripada aksi *ubah data*?
2. **Amankan Logout.** `auth/logout.php` masih diakses lewat link GET. Ubah menjadi form POST dengan `csrf_field()` dan `csrf_verify()`.
3. **Pindahkan kredensial database** dari `includes/koneksi.php` ke environment variable (`getenv()`), lalu set di pengaturan Vercel. Tambahkan barisnya ke `security-checklist.md`.
4. **Telusuri `e()` yang terlewat.** Cari semua `echo` di project, apakah ada data dari database/`$_GET`/`$_SESSION` yang masih dicetak tanpa `e()` atau `(int)`?
5. **Pelajari `Content-Security-Policy` (CSP)** sebagai lapisan pertahanan tambahan terhadap XSS.
