# 5. Audit SQL Injection & Security Checklist

Bab ini membahas dua kerentanan yang **sudah aman** dan cara `docs/security-checklist.md` mendokumentasikan seluruh audit.

## 5.1 Apa itu SQL Injection?

Penyerang **mengubah makna** query SQL dengan menyisipkan potongan SQL lewat input. Contoh query yang **tidak aman**:

```php
// CONTOH TIDAK AMAN — bukan cara aplikasi ini menulis query
"SELECT * FROM users WHERE username = '" . $username . "'"
```

Username `' OR '1'='1` mengubah query itu menjadi "cocokkan baris mana pun", dan login bisa ditembus tanpa tahu password.

## 5.2 Kenapa Aplikasi Ini Sudah Aman?

Semua query yang memakai input pengguna ditulis dengan **prepared statement** (`prepare()` + placeholder + `execute([...])`):

```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
$stmt->execute(['username' => $username]);
```

Isi `$username`, termasuk `' OR '1'='1`, selalu diperlakukan sebagai **satu nilai teks**, bukan bagian dari perintah SQL.

Hasil audit semua file:

| Lokasi | Query | Status |
|---|---|---|
| `auth/proses_login.php`, `auth/proses_register.php` | `SELECT`, `INSERT` pada `users` | prepared statement |
| `api/produk/*`, `api/pelanggan/*`, `api/pesanan/*` (tambah, proses_tambah, list) | `SELECT`, `INSERT`, `UPDATE`, `DELETE` dengan `?` / `:nama` | prepared statement |
| `api/index.php` | `SELECT COUNT(*)` | SQL statis, tanpa input |
| `api/*/list.php` | `SELECT ... ORDER BY` untuk daftar | SQL statis, tanpa input |
| `api/reset_session.php` | `TRUNCATE ...` | SQL statis, hanya admin, kini juga dilindungi CSRF |
| `includes/session.php` | `SELECT/INSERT/DELETE` pada `php_sessions` | prepared statement |

**Uji sendiri:** login dengan username `' OR '1'='1` dan password apa saja. Hasilnya tetap "Username atau password salah".

## 5.3 Audit Validasi & Sanitasi Input

Validasi yang sudah ada tetap dipertahankan:

- Wajib isi: `nama_produk`, `nama` pelanggan (`=== ''`).
- Angka: `is_numeric()` untuk `harga` dan `stok`, `(int)` untuk `jumlah` pesanan.
- Total harga pesanan dihitung **di server** dari harga produk di database, bukan dari input pengguna. Ini keputusan yang benar dan dipertahankan.

Tambahan di jobsheet ini, **type casting `(int)`** untuk ID:

```php
// api/produk/proses_tambah.php
$id = !empty($_POST['id']) ? (int) $_POST['id'] : null;

// api/pesanan/proses_tambah.php
$idPelanggan = (int) ($_POST['id_pelanggan'] ?? 0);
$idProduk    = (int) ($_POST['id_produk'] ?? 0);
```

Ini lapisan pengaman tambahan sebelum nilai dipakai di query dan di redirect (`tambah.php?id=...`).

## 5.4 Membaca `docs/security-checklist.md`

Dokumen ini adalah **laporan audit terstruktur**:

| Kolom | Isinya |
|---|---|
| `#` | Nomor kerentanan |
| `Kerentanan` | Nama celah |
| `Ditemukan di` | File yang terdampak |
| `Sebelum` | Kondisi kode sebelum audit |
| `Sesudah (perbaikan)` | Apa yang dilakukan, disertai **bukti pengujian** |

Klaim "sudah aman" tanpa bukti pengujian tidak banyak nilainya, jadi setiap baris punya langkah uji yang bisa kamu ulang. Bagian "Temuan Tambahan" mencatat hal di luar 5 poin utama, misalnya password database yang tertulis langsung di `koneksi.php`.

Lanjut ke: [Rangkuman & Latihan Lanjutan](06-rangkuman-latihan.md)
