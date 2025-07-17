<?php
$koneksi = new mysqli("localhost", "root", "", "penjualan");
$no = 1;
$data = $koneksi->query("SELECT * FROM transaksi");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Transaksi</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <h2>Data Transaksi Penjualan</h2>

    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>No</th>
            <th>Kode</th>
            <th>Nama Barang</th>
            <th>Jumlah</th>
            <th>Harga</th>
            <th>Total</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $data->fetch_assoc()) { ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= $row['kode_transaksi'] ?></td>
            <td><?= $row['nama_barang'] ?></td>
            <td><?= $row['jumlah_beli'] ?></td>
            <td><?= number_format($row['harga_satuan'], 0, ',', '.') ?></td>
            <td><?= number_format($row['total'], 0, ',', '.') ?></td>
            <td>
              <a href="edit.php?id=<?= $row['id'] ?>" class="aksi-link">Edit</a>
              <a href="hapus.php?id=<?= $row['id'] ?>" class="aksi-link" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>


            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
       <a href="index.html" class="btn-kembali">← Kembali ke Form</a>
    </div>
  </div>
</body>
</html>
