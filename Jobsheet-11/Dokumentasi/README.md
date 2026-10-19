# Dokumentasi Jobsheet 11 — Keamanan Web Dasar

Dokumentasi ini melanjutkan Jobsheet 10 (Autentikasi & Manajemen Sesi) pada aplikasi **Sistem Pemesanan UMKM** (produk, pelanggan, pesanan). Jobsheet ini mengaudit kode sebelumnya terhadap 5 kerentanan; detail lengkapnya ada di [`../docs/security-checklist.md`](../docs/security-checklist.md).

## Apa yang baru?

1. **SQL Injection** — diaudit ulang, sudah aman (prepared statement). Tidak ada perubahan kode.
2. **XSS** — semua output data dibungkus fungsi `e()` yang baru.
3. **CSRF** — token tersembunyi ditambahkan ke semua form `POST` dan diverifikasi sebelum menyentuh database. Tombol Hapus ikut diubah dari link GET menjadi form POST.
4. **Validasi & sanitasi input** — diaudit ulang, ditambah type casting `(int)` untuk ID.
5. **Session Fixation** — `session_regenerate_id(true)` dipanggil setelah login berhasil.

Dua file baru jadi pusat perubahan: **`includes/helpers.php`** (`e()`) dan **`includes/csrf.php`** (`csrf_token()`, `csrf_field()`, `csrf_verify()`).

## Daftar Isi

1. [Konsep Dasar Keamanan Web](01-konsep-dasar-keamanan-web.md)
2. [XSS & Fungsi `e()`](02-xss-dan-fungsi-e.md)
3. [CSRF & Token Verifikasi](03-csrf-dan-token.md)
4. [Session Fixation](04-session-fixation.md)
5. [Audit SQL Injection & Security Checklist](05-audit-sql-injection-dan-checklist.md)
6. [Rangkuman & Latihan Lanjutan](06-rangkuman-latihan.md)

## Struktur Folder

```
Jobsheet-11/
├── includes/
│   ├── helpers.php        # BARU — e()
│   ├── csrf.php           # BARU — token CSRF
│   ├── auth.php           # tidak berubah
│   ├── session.php        # tidak berubah (session disimpan di database)
│   └── header.php         # memuat helpers & csrf, e() di semua output
├── auth/
│   ├── proses_login.php   # + csrf_verify(), session_regenerate_id(true)
│   └── login.php, register.php, proses_register.php   # + csrf_field() / csrf_verify()
├── api/
│   ├── produk/, pelanggan/, pesanan/
│   │   ├── list.php           # output pakai e(), Hapus jadi form POST + token
│   │   ├── tambah.php         # + csrf_field(), e() di atribut value
│   │   └── proses_tambah.php  # + csrf_verify(), casting (int)
│   └── reset_session.php      # + csrf_verify()
├── docs/security-checklist.md # BARU
└── Dokumentasi/               # folder ini
```
