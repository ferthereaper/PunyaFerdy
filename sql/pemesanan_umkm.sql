CREATE TABLE IF NOT EXISTS buku (
    id SERIAL PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    pengarang VARCHAR(255) NOT NULL,
    tahun INTEGER NOT NULL,
    isbn VARCHAR(50),
    stok INTEGER NOT NULL DEFAULT 0,
    kategori VARCHAR(50)
);

CREATE TABLE IF NOT EXISTS anggota (
    id SERIAL PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    no_anggota VARCHAR(50) NOT NULL UNIQUE,
    alamat VARCHAR(255),
    no_hp VARCHAR(30)
);

-- 1. Buat Tabel Produk
CREATE TABLE produk (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    nama_produk VARCHAR(255) NOT NULL,
    harga NUMERIC(12, 2) NOT NULL DEFAULT 0,
    stok INT NOT NULL DEFAULT 0,
    kategori VARCHAR(100),
    deskripsi TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- 2. Buat Tabel Pelanggan
CREATE TABLE pelanggan (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    no_hp VARCHAR(20),
    alamat TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- 3. Buat Tabel Pesanan (Untuk statistik "Sedang Diproses")
CREATE TABLE pesanan (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    pelanggan_id BIGINT REFERENCES pelanggan(id) ON DELETE SET NULL,
    status VARCHAR(50) DEFAULT 'Diproses', -- Contoh status: 'Diproses', 'Selesai', 'Dibatalkan'
    total_harga NUMERIC(12, 2) DEFAULT 0,
    created_at TIMESTAMPTZ DEFAULT NOW()
);