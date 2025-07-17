
-- Buat database
CREATE DATABASE IF NOT EXISTS produk_kategori_db;
USE produk_kategori_db;

-- Tabel kategori
CREATE TABLE IF NOT EXISTS kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL
);

-- Tabel produk
CREATE TABLE IF NOT EXISTS produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    harga INT NOT NULL,
    kategori_id INT,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE SET NULL
);

-- Data contoh kategori
INSERT INTO kategori (nama) VALUES
('Minuman'),
('Makanan'),
('Elektronik');

-- Data contoh produk
INSERT INTO produk (nama, harga, kategori_id) VALUES
('Teh Botol', 5000, 1),
('Nasi Goreng', 15000, 2),
('Kipas Angin', 200000, 3);
