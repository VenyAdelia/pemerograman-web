<?php
$koneksi = new mysqli("localhost", "root", "", "penjualan");

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Ambil data dari POST
$kode   = $_POST['kode_transaksi'];
$nama   = $_POST['nama_barang'];
$jumlah = (int) $_POST['jumlah_beli'];
$harga  = (int) $_POST['harga_satuan'];
$total  = (int) $_POST['total'];

// Gunakan prepared statement untuk keamanan
$stmt = $koneksi->prepare("INSERT INTO transaksi (kode_transaksi, nama_barang, jumlah_beli, harga_satuan, total) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssiii", $kode, $nama, $jumlah, $harga, $total);

if ($stmt->execute()) {
    echo "Data berhasil disimpan!";
} else {
    echo "Gagal menyimpan data: " . $stmt->error;
}

$stmt->close();
$koneksi->close();
?>
