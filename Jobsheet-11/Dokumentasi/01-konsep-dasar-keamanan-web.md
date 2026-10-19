# 1. Konsep Dasar Keamanan Web

## 1.1 Kenapa Keamanan Dibahas Setelah Fitur Selesai?

Untuk memahami **cara memperbaiki** sebuah celah, kamu harus lebih dulu paham **cara kerja** fiturnya: form tambah produk/pelanggan/pesanan, proses server-side, database PostgreSQL (Supabase), dan session login. Karena semua fondasi itu sudah ada, sekarang kita bisa melihat **mengapa** tiap celah berbahaya dalam kode yang kamu tulis sendiri.

## 1.2 Lima Kerentanan yang Diaudit

| # | Kerentanan | Pertanyaan Intinya |
|---|---|---|
| 1 | SQL Injection | Bisakah pengguna menyisipkan **perintah SQL** lewat input? |
| 2 | XSS | Bisakah pengguna menyisipkan **HTML/JavaScript** yang dijalankan di browser pengguna lain? |
| 3 | CSRF | Bisakah **situs lain** memicu aksi (hapus/ubah data) atas nama kasir yang sedang login? |
| 4 | Validasi & Sanitasi Input | Apakah data yang masuk benar-benar diperiksa bentuk dan jenisnya? |
| 5 | Session Fixation | Bisakah penyerang memaksa korban memakai ID session yang sudah ia ketahui? |

Kerentanan #1 dan #4 **sudah aman** sejak jobsheet sebelumnya, jadi audit hanya mengonfirmasinya. Yang butuh kode baru adalah #2, #3, dan #5. Audit yang hasilnya "sudah aman" tetap berharga, sama pentingnya dengan menulis kode baru.

## 1.3 Prinsip Besar: Jangan Percaya Input dari Luar

Kelima celah berakar dari satu prinsip: **jangan mempercayai data yang datang dari luar aplikasi** (`$_POST`, `$_GET`, atau permintaan yang tampak berasal dari pengguna). Prinsip ini sudah muncul di jobsheet sebelumnya:

- Validasi server-side: tidak percaya validasi JavaScript saja.
- Prepared statement: tidak percaya `$_POST` boleh digabung ke query SQL.
- Guard `auth.php`: tidak percaya pengunjung hanya akan membuka halaman yang "seharusnya".

Jobsheet ini menambah dua aturan: teks yang dicetak kembali ke HTML **tidak otomatis aman** (→ XSS), dan permintaan `POST` yang diterima server **belum tentu berasal dari niat kasir sendiri** (→ CSRF).
