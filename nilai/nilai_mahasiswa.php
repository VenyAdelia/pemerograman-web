<?php
//error_reporting("E_ALL & ~E_NOTICE");
?>
<!DOCTYPE HTML>
<html>
<head>
    <title> menghitung nilai matakuliah</title>
</head>
<body>
<?php
/* fungsi konversi nilai ke huruf */

function grade($nilai)
{
    if ($nilai >= 80){
        return "A";
   } elseif ($nilai >= 70){
    return "B";
   } elseif ($nilai >= 60) {
    return "C";
   } elseif ($nilai >= 50) {
    return "D";
   } else {
    return "E";
   }
   
} 
?>
<form action="" method="post">
<div>NIM Mahasiswa</div>
<input type="text" name="nim" placeholder="NIM">
<div>Nama Mahsiswa</div>
<input type="text" name="nama" placeholder="Nama Lengkap">
<div>Jumlah Kehadiran</div>
<input type="text" name="jk" size="10">
<div>Jumlah Pertemuan</div>
<input type="text" name="jp" size="10">
<div>Nilai Ts</div>
<input type="text" name="ts" size="10" placeholder="0-100">
<div>Nilai Tm</div>
<input type="text" name="tm" size="10" placeholder="0-100">
<div>Nilai Uts</div>
<input type="text" name="nilaiuts" size="10" placeholder="0-100">
<div>Nilai Uas</div>
<input type="text" name="nilaiuas" size="10" placeholder="0-100">
<br><br>
<input type="submit" value="hitung nilai">
</form>

<hr>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST"){
$nim = trim($_POST["nim"]);
$nama = trim($_POST["nama"]);
$jk = (float)trim($_POST["jk"]);
$jp = (float)trim($_POST["jp"]);
$ts = (float)trim($_POST["ts"]);
$tm = (float)trim($_POST["tm"]);
$uts =(float) trim($_POST["nilaiuts"]);
$uas = (float)trim($_POST["nilaiuas"]);

if ($jp > 0) {
    $absensi = ($jk / $jp) * 100;
} else {
    $absensi = 0;
}

$nilai = ($absensi * 0.1)+ ($ts * 0.15)+ ($tm * 0.15)+ ($uts * 0.3)+ ($uas * 0.3);
$grade = grade($nilai);

?>
<h2 align = "center"> data nilai mahasiswa</h2>

<table width="100%" bgcolor="#5F9EA0" cellspacing="1">
    <tr bgcolor="#6495ED">
    <th width="100">NIM</th>
    <th>Nama Mahasiswa</th>
    <th width="100">Nilai basensi</th>
    <th width="100">Nilai ts</th>
    <th width="100">Nilai tm</th>
    <th width="100">Nilai uts</th>
    <th width="100">Nilai uas</th>
    <th width="100">Nilai akhir</th>
    <th width="100">grade</th>
    </tr>
    <tr bgcolor="white">
    <td align="center"><?php echo htmlspecialchars($nim); ?></td>
    <td><?php echo htmlspecialchars($nama); ?></td>
    <td align="center"><?php echo round ($absensi, 2); ?></td>
    <td align="center"><?php echo $ts;?></td>
    <td align="center"><?php echo $tm;?></td>
    <td align="center"><?php echo $uts;?></td>
    <td align="center"><?php echo $uas;?></td>
    <td align="center"><?php echo $nilai;?></td>
    <td align="center"><?php echo $grade;?></td>
    </tr>
</table>
<?php } ?>
</body>
</html>