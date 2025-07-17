<?php
$koneksi = new mysqli("localhost", "root", "", "penjualan");

$id     = $_POST['id'];
$kode   = $_POST['kode_transaksi'];
$nama   = $_POST['nama_barang'];
$jumlah = $_POST['jumlah_beli'];
$harga  = $_POST['harga_satuan'];
$total  = $jumlah * $harga;

$update = $koneksi->query("UPDATE transaksi SET 
  kode_transaksi='$kode', 
  nama_barang='$nama', 
  jumlah_beli=$jumlah, 
  harga_satuan=$harga, 
  total=$total 
  WHERE id=$id");

if ($update) {
  header("Location: lihat.php");
} else {
  echo "Gagal update data.";
}
?>
