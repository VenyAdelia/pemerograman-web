<?php
$koneksi = new mysqli("localhost", "root", "", "penjualan");
$id = $_GET['id'];
$data = $koneksi->query("SELECT * FROM transaksi WHERE id=$id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Transaksi</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <h2>Edit Transaksi</h2>
    <form action="update.php" method="POST">
      <input type="hidden" name="id" value="<?= $data['id'] ?>">

      <label>Kode Transaksi</label>
      <input type="text" name="kode_transaksi" value="<?= $data['kode_transaksi'] ?>" required>

      <label>Nama Barang</label>
      <input type="text" name="nama_barang" value="<?= $data['nama_barang'] ?>" required>

      <label>Jumlah Beli</label>
      <input type="number" name="jumlah_beli" value="<?= $data['jumlah_beli'] ?>" required>

      <label>Harga Satuan</label>
      <input type="number" name="harga_satuan" value="<?= $data['harga_satuan'] ?>" required>

      <label>Total</label>
      <input type="number" name="total" value="<?= $data['total'] ?>" readonly>

      <button type="submit">UPDATE</button>
    </form>
  </div>
</body>
</html>
