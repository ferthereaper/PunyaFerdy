# WebUMKM — Sistem Pemesanan UMKM

## Integrasi Modul Pesanan (padanan Jobsheet 12)

Pola Jobsheet 12 (peminjaman ⇄ stok buku) diterapkan ke domain UMKM: **pesanan ⇄ stok produk**.

| Jobsheet 12 (perpustakaan) | WebUMKM |
|---|---|
| `peminjaman` (anggota + buku) | `pesanan` (pelanggan + produk) |
| status `dipinjam` / `dikembalikan` | status `diproses` / `selesai` / `dibatalkan` |
| Pinjam → stok buku − 1 | Buat pesanan → stok produk − jumlah |
| Kembalikan → stok + 1 | Batalkan → stok + jumlah (Selesaikan: stok tetap) |
| Riwayat per anggota | Riwayat per pelanggan |
| Kartu "Sedang Dipinjam" | Kartu "Sedang Diproses" |

### Perubahan
- `sql/02_pesanan_transaksi.sql` — migrasi idempotent: tabel `php_sessions`, kolom `id_pelanggan`, `id_produk`, `jumlah`, `status`, `tanggal_selesai` pada `pesanan`, CHECK status, index. Pesanan lama otomatis ditandai `selesai` (dibuat sebelum stok dipotong otomatis).
- `api/pesanan/proses_tambah.php` — buat/edit pesanan dalam satu **transaction** (`beginTransaction` / `commit` / `rollBack`) dengan `SELECT ... FOR UPDATE` pada baris produk (mencegah race condition stok). Saat edit, stok pesanan lama dikembalikan lalu dihitung ulang. Total harga dihitung di server.
- `api/pesanan/tambah.php` — dropdown hanya produk `stok > 0`; hanya pesanan `diproses` yang bisa diedit.
- `api/pesanan/aktif.php` + `proses_status.php` — daftar pesanan `diproses`, tombol **Selesaikan** dan **Batalkan** (batal mengembalikan stok), dikunci `FOR UPDATE` agar tidak bisa diproses dua kali.
- `api/pesanan/riwayat.php` — histori pesanan per pelanggan (JOIN `pesanan` + `produk`).
- `api/pesanan/list.php` — kolom Status; hapus pesanan `diproses` mengembalikan stok (dalam transaction).
- `includes/header.php` — menu Pesanan Aktif dan Riwayat Pelanggan.
- `api/index.php` — kartu "Sedang Diproses" kini `COUNT(*) FROM pesanan WHERE status = 'diproses'` (sebelumnya statis `0`).

Seluruh halaman baru mengikuti `docs/security-checklist.md`: `e()`, `csrf_field()`, `csrf_verify()`, dan wajib login.

### Cara menjalankan
1. Jalankan `sql/pemesanan_umkm.sql`, lalu `sql/02_pesanan_transaksi.sql`.
2. Buat tabel `users` versi PostgreSQL (file SQL lama memakai sintaks MySQL).
