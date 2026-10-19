# Jobsheet 11 — Keamanan Web Dasar (Sistem Pemesanan UMKM)

Melanjutkan Jobsheet 10 (Autentikasi & Manajemen Sesi). Jobsheet ini mengaudit kode Jobsheet 7–10 terhadap 5 kerentanan, lalu memperbaikinya.
Detail audit: [`docs/security-checklist.md`](docs/security-checklist.md). Penjelasan konsep: folder [`Dokumentasi/`](Dokumentasi/README.md).

## Yang berubah

1. **SQL Injection** — diaudit ulang, sudah aman (prepared statement). Tidak ada perubahan kode.
2. **XSS** — fungsi `e()` baru (`includes/helpers.php`) membungkus semua output data.
3. **CSRF** — `includes/csrf.php` baru. Token disisipkan ke semua form `POST` dan diverifikasi di semua file proses. Tombol **Hapus** diubah dari link GET menjadi form POST.
4. **Validasi & sanitasi input** — diaudit ulang, ditambah type casting `(int)` untuk ID.
5. **Session Fixation** — `session_regenerate_id(true)` setelah login berhasil.

## Struktur

```
Jobsheet-11/
├── includes/
│   ├── helpers.php        # BARU — e()
│   ├── csrf.php           # BARU — csrf_token(), csrf_field(), csrf_verify()
│   ├── header.php         # memuat helpers & csrf, token di form Reset Data
│   └── auth.php, session.php, config.php, koneksi.php, footer.php
├── auth/                  # + csrf_field() di form, csrf_verify() + session_regenerate_id(true) di proses_login
├── api/
│   ├── produk/, pelanggan/, pesanan/   # list (Hapus via POST), tambah (+ token), proses_tambah (+ csrf_verify)
│   └── index.php, reset_session.php
├── docs/security-checklist.md          # BARU
└── Dokumentasi/                        # BARU
```

## Cara menguji

1. **CSRF** — login lewat browser, salin cookie `PHPSESSID` dari DevTools, lalu:
   ```bash
   curl -i -X POST http://localhost/Jobsheet-11/api/produk/proses_tambah.php \
        -b "PHPSESSID=<isi cookie>" -d "nama_produk=x&harga=1000&stok=1"
   ```
   Harus mendapat **HTTP 403** (tanpa `csrf_token`).
2. **XSS** — tambah produk dengan nama `<script>alert(1)</script>`. Di Daftar Produk harus tampil sebagai teks, tanpa pop-up.
3. **Urutan guard** — kirim `POST` ke `api/produk/proses_tambah.php` tanpa cookie login. Harus diarahkan ke halaman Login (bukan 403).
4. **SQL Injection** — login dengan username `' OR '1'='1`. Harus tetap gagal.
5. **Hapus** — buka `.../api/produk/list.php?action=hapus&id=1` langsung di browser. Data tidak boleh terhapus.
