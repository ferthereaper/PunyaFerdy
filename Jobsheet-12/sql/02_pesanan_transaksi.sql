-- Jobsheet 12 (versi WebUMKM): integrasi modul pesanan <-> stok produk.
-- Aman dijalankan berulang kali (idempotent) dan menyesuaikan tabel yang sudah ada.
-- Jalankan SETELAH pemesanan_umkm.sql.

-- Tabel session (dipakai includes/session.php).
CREATE TABLE IF NOT EXISTS php_sessions (
    id VARCHAR(128) PRIMARY KEY,
    data TEXT NOT NULL DEFAULT '',
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Tabel pesanan versi lengkap (dibuat hanya jika belum ada).
CREATE TABLE IF NOT EXISTS pesanan (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    id_pelanggan BIGINT REFERENCES pelanggan(id) ON DELETE SET NULL,
    id_produk BIGINT REFERENCES produk(id) ON DELETE SET NULL,
    jumlah INT NOT NULL DEFAULT 1,
    total_harga NUMERIC(12, 2) NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'diproses',
    tanggal_selesai TIMESTAMPTZ,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Untuk tabel pesanan lama: tambahkan kolom yang dibutuhkan kode.
ALTER TABLE pesanan ADD COLUMN IF NOT EXISTS id_pelanggan BIGINT REFERENCES pelanggan(id) ON DELETE SET NULL;
ALTER TABLE pesanan ADD COLUMN IF NOT EXISTS id_produk BIGINT REFERENCES produk(id) ON DELETE SET NULL;
ALTER TABLE pesanan ADD COLUMN IF NOT EXISTS jumlah INT NOT NULL DEFAULT 1;
ALTER TABLE pesanan ADD COLUMN IF NOT EXISTS status VARCHAR(20) NOT NULL DEFAULT 'diproses';
ALTER TABLE pesanan ADD COLUMN IF NOT EXISTS created_at TIMESTAMPTZ DEFAULT NOW();

-- Hanya pada eksekusi pertama: pesanan lama dibuat sebelum stok otomatis
-- dikurangi, jadi dianggap 'selesai' agar pembatalan/hapus tidak menambah
-- stok yang tidak pernah dikurangi.
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_name = 'pesanan' AND column_name = 'tanggal_selesai'
    ) THEN
        UPDATE pesanan SET status = 'selesai';
        ALTER TABLE pesanan ADD COLUMN tanggal_selesai TIMESTAMPTZ;
    END IF;
END $$;

ALTER TABLE pesanan ALTER COLUMN status SET DEFAULT 'diproses';
ALTER TABLE pesanan DROP CONSTRAINT IF EXISTS pesanan_status_check;
ALTER TABLE pesanan ADD CONSTRAINT pesanan_status_check
    CHECK (status IN ('diproses', 'selesai', 'dibatalkan'));

CREATE INDEX IF NOT EXISTS idx_pesanan_status ON pesanan(status);
CREATE INDEX IF NOT EXISTS idx_pesanan_pelanggan ON pesanan(id_pelanggan);
