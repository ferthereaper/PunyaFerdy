# 2. XSS & Fungsi `e()`

## 2.1 Apa itu XSS?

**XSS (Cross-Site Scripting)** memungkinkan penyerang menyisipkan HTML/JavaScript miliknya ke halaman yang dilihat **pengguna lain**, lewat data yang tampak biasa.

## 2.2 Contoh Serangan pada Aplikasi UMKM

Seseorang menambah produk dengan nama:

```
<script>alert('halaman ini sudah diretas!')</script>
```

Kode lama di `api/produk/list.php` mencetak nama produk seperti ini:

```php
<td><?php echo $produk['nama_produk']; ?></td>
```

`echo` mencetaknya apa adanya, dan browser **menjalankan** tag `<script>` itu. Setiap kasir yang membuka Daftar Produk ikut menjalankannya. Skrip sungguhan bisa mencuri cookie session, mengubah tampilan untuk menipu, atau melakukan aksi atas nama kasir yang sedang login.

Hal yang sama berlaku untuk **nama pelanggan, alamat, email, no. HP**, dan **nama kasir** di navbar (nama itu berasal dari form Registrasi).

## 2.3 Solusi: Fungsi `e()`

```php
// includes/helpers.php
function e($value)
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}
```

- **`htmlspecialchars(...)`** mengubah `<`, `>`, `&`, `"`, `'` menjadi entity (`&lt;`, `&gt;`, ...). Teks `<script>` jadi `&lt;script&gt;` dan tampil sebagai teks biasa.
- **`(string) ($value ?? '')`** memaksa nilai apa pun (termasuk `null`) menjadi string, jadi `e(null)` aman.
- **`ENT_QUOTES`** ikut meng-escape kutip tunggal dan ganda. Ini penting untuk atribut `value="..."`.
- **`'UTF-8'`** memastikan karakter non-ASCII ditangani benar.

## 2.4 Di Mana `e()` Dipakai?

Sebelum (Jobsheet 10) dan sesudah (Jobsheet 11) di `api/pelanggan/list.php`:

```php
<td><?php echo htmlspecialchars($pelanggan['nama']); ?></td>   // sebelum
<td><?php echo e($pelanggan['nama']); ?></td>                   // sesudah
```

Selain ganti nama fungsi, ada tempat yang sebelumnya **belum di-escape sama sekali**:

- Pesan flash di `auth/login.php` dan `auth/register.php` (`$flash['pesan']`).
- `BASE_URL` di link/atribut `action` (dihitung dari `REQUEST_URI`, jadi tetap diperlakukan sebagai input).
- Kelas CSS `flash-<?php echo e($flash['type']); ?>`.

## 2.5 Kenapa Atribut `value` Juga Butuh `e()`?

```php
<input type="text" name="nama_produk" value="<?php echo e($produk['nama_produk']); ?>">
```

Tanda kutip yang tidak di-escape bisa **menutup atribut lebih awal**, lalu menyisipkan atribut berbahaya, misalnya `" onmouseover="kodeJahat()`. `ENT_QUOTES` mencegahnya. Ini berlaku di semua form Tambah/Edit (produk, pelanggan, textarea deskripsi/alamat).

## 2.6 Angka dan ID: Pakai `(int)`

Kolom `harga`, `stok`, `jumlah`, dan `id` bertipe angka di database, jadi tidak mungkin berisi tag HTML. Untuk ID yang dicetak ke atribut atau URL, kita pakai type casting:

```php
<input type="hidden" name="id" value="<?php echo (int) $produk['id']; ?>">
<a href="tambah.php?id=<?php echo (int) $produk['id']; ?>">Edit</a>
```

`harga` dicetak lewat `number_format(...)` yang juga selalu menghasilkan angka.

## 2.7 Membuktikannya

Tambah produk dengan nama `<script>alert(1)</script>`, lalu buka **Daftar Produk**. Teks itu tampil apa adanya di kolom Nama Produk, **tanpa** pop-up. Coba juga di Tambah Pelanggan (kolom Alamat) dan buka Edit-nya untuk memastikan atribut/isi textarea aman.

Lanjut ke: [CSRF & Token Verifikasi](03-csrf-dan-token.md)
