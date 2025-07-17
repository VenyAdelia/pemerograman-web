<?php
$koneksi = new mysqli("localhost", "root", "", "penjualan");
$id = $_GET['id'];

$hapus = $koneksi->query("DELETE FROM transaksi WHERE id=$id");

if ($hapus) {
  header("Location: lihat.php");
} else {
  echo "Gagal menghapus data.";
}
?>
