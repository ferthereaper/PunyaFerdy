# Security Checklist — WebUMKM

Audit keamanan dasar Sistem Pemesanan UMKM. Bagian **Bukti** diisi screenshot hasil uji (simpan di `docs/images/`).

| # | Kerentanan | Status | Perbaikan |
|---|------------|--------|-----------|
| 1 | XSS | Diperbaiki | helper `e()` di semua output (menggantikan `htmlspecialchars` yang tidak konsisten; sebelumnya `flash` di login/register, `id`, `jumlah`, `type` masih dicetak mentah) |
| 2 | CSRF | Diperbaiki | token per-session, `csrf_field()` di semua form POST, `csrf_verify()` di semua handler POST |
| 3 | CSRF pada aksi hapus | Diperbaiki | hapus sebelumnya `GET ...list.php?action=hapus&id=N`; sekarang POST + token |
| 4 | CSRF pada Reset Data (admin) | Diperbaiki | `reset_session.php` memanggil `csrf_verify()` sebelum `TRUNCATE` |
| 5 | Session fixation | Diperbaiki | `session_regenerate_id(true)` di `auth/proses_login.php` |
| 6 | SQL Injection | Aman | semua query memakai prepared statement |

Sudah baik sebelumnya: cookie session `HttpOnly` + `SameSite=Lax`, password di-hash (`password_hash`), cek login di semua halaman (`includes/auth.php`).

## 1. XSS
Uji: tambah produk dengan nama `<script>alert('XSS')</script>`, lalu buka Daftar Produk.
Sebelum: ![before](images/xss-before.png)  Sesudah: ![after](images/xss-after.png)

## 2–3. CSRF (termasuk hapus via GET)
Sebelum, dalam keadaan login, membuka link ini langsung menghapus data (cukup lewat `<img src>` di situs lain):
`/Jobsheet-12/api/produk/list.php?action=hapus&id=1`
Sesudah: request yang sama tidak lagi menghapus, dan POST tanpa token mendapat 403.
Sebelum: ![before](images/csrf-before.png)  Sesudah: ![after](images/csrf-after.png)

## 4. Reset Data
Uji: kirim POST ke `/Jobsheet-12/api/reset_session.php` tanpa token sebagai admin → 403 dan data tetap ada.
![reset](images/reset-after.png)

## 5. Session fixation
DevTools → Application → Cookies: bandingkan `PHPSESSID` sebelum dan sesudah login (harus berbeda).
Sebelum: ![before](images/session-before.png)  Sesudah: ![after](images/session-after.png)

## 6. SQL Injection
Login dengan username `' OR '1'='1` → gagal. ![sqli](images/sqli.png)

## Catatan / risiko tersisa
- `includes/koneksi.php` menyimpan kredensial database langsung di kode; pindahkan ke environment variable dan ganti passwordnya.
- `auth/logout.php` masih lewat GET (dampak kecil: memaksa logout).
