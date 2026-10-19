# 4. Session Fixation

Perbaikan paling singkat di jobsheet ini: **satu baris kode**, tapi butuh pemahaman session untuk tahu kenapa penting.

## 4.1 Apa itu Session Fixation?

Penyerang **memaksa** korban memakai **ID session yang sudah ia ketahui**, lalu menunggu korban login. Begitu korban login, ID session yang sama kini terhubung ke akun korban, dan penyerang bisa ikut memakainya.

## 4.2 Kenapa Bisa Terjadi?

Secara default, **satu ID session yang sama** dipakai sebelum dan sesudah login. Jika ID itu sudah diketahui pihak lain sebelum login, ID tersebut tetap valid setelah korban masuk.

## 4.3 Solusi: `session_regenerate_id(true)`

```php
// auth/proses_login.php
if ($user && password_verify($password, $user['password'])) {
    // Ganti ID session setelah login berhasil untuk mencegah session fixation.
    session_regenerate_id(true);

    $_SESSION['logged_in'] = true;
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['nama']      = $user['nama'];
    $_SESSION['username']  = $user['username'];
    $_SESSION['role']      = $user['role'];

    header("Location: ../api/index.php");
    exit();
}
```

- **`session_regenerate_id(true)`** mengganti ID session dengan ID baru yang acak, **data `$_SESSION` tetap ada**.
- Argumen **`true`** menghapus session lama. Pada project ini, session disimpan lewat `DbSessionHandler` (`includes/session.php`), sehingga pemanggilan ini memicu `destroy()` yang menghapus baris lama di tabel `php_sessions`. ID lama pun tidak bisa dipakai lagi.
- Dipanggil **setelah** `password_verify()` berhasil dan **sebelum** `$_SESSION['user_id']` diisi: ID lama dibuang tepat pada transisi "belum login → sudah login".

## 4.4 Kenapa Tidak di Registrasi dan Logout?

- **Registrasi**: tidak langsung membuat kasir login, melainkan mengarahkan ke Login. Tidak ada transisi login yang perlu diamankan.
- **Logout**: `session_destroy()` sudah menghapus seluruh data session.

(`api/reset_session.php` juga sudah membuat ID session baru lewat `session_create_id()` setelah mereset data.)

Lanjut ke: [Audit SQL Injection & Security Checklist](05-audit-sql-injection-dan-checklist.md)
