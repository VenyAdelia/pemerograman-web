<?php
include '../config/koneksi.php';

$id = $_GET['id'];
$data = mysqli_query($koneksi, "SELECT * FROM produk WHERE id = $id");
$row = mysqli_fetch_assoc($data);
$kategori = mysqli_query($koneksi, "SELECT * FROM kategori");

if (isset($_POST['update'])) {
  $nama = $_POST['nama'];
  $harga = $_POST['harga'];
  $kategori_id = $_POST['kategori_id'];

  mysqli_query($koneksi, "UPDATE produk SET nama='$nama', harga='$harga', kategori_id='$kategori_id' WHERE id=$id");
  header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            position: relative;
            overflow: hidden;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
        }

        .header {
            text-align: center;
            margin-bottom: 35px;
        }

        .header h2 {
            color: #2d3748;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .header p {
            color: #718096;
            font-size: 16px;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2d3748;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            z-index: 1;
        }

        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 16px;
            background: #ffffff;
            transition: all 0.3s ease;
            outline: none;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 12px center;
            background-repeat: no-repeat;
            background-size: 16px;
            padding-right: 45px;
        }

        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 35px;
        }

        .btn {
            flex: 1;
            padding: 15px 25px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #ffffff;
            color: #4a5568;
            border: 2px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background: #f7fafc;
            border-color: #cbd5e0;
            transform: translateY(-1px);
        }

        .form-floating {
            position: relative;
        }

        .form-floating input,
        .form-floating select {
            padding-top: 25px;
            padding-bottom: 10px;
        }

        .form-floating label {
            position: absolute;
            top: 50%;
            left: 45px;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 16px;
            font-weight: 400;
            text-transform: none;
            letter-spacing: normal;
            transition: all 0.3s ease;
            pointer-events: none;
            margin-bottom: 0;
        }

        .form-floating input:focus + label,
        .form-floating input:not(:placeholder-shown) + label,
        .form-floating select:focus + label,
        .form-floating select:not([value=""]) + label {
            top: 20px;
            font-size: 12px;
            color: #667eea;
            font-weight: 600;
        }

        @media (max-width: 480px) {
            .container {
                padding: 25px;
                margin: 10px;
            }
            
            .btn-group {
                flex-direction: column;
            }
            
            .header h2 {
                font-size: 24px;
            }
        }

        .success-animation {
            animation: slideInFromTop 0.6s ease-out;
        }

        @keyframes slideInFromTop {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container success-animation">
        <div class="header">
            <h2><i class="fas fa-edit" style="color: #667eea; margin-right: 10px;"></i>Edit Produk</h2>
            <p>Perbarui informasi produk Anda</p>
        </div>

        <form method="POST">
            <div class="form-group">
                <div class="form-floating input-wrapper">
                    <i class="fas fa-box"></i>
                    <input type="text" name="nama" value="<?= $row['nama'] ?>" placeholder=" " required>
                    <label>Nama Produk</label>
                </div>
            </div>

            <div class="form-group">
                <div class="form-floating input-wrapper">
                    <i class="fas fa-money-bill-wave"></i>
                    <input type="number" name="harga" value="<?= $row['harga'] ?>" placeholder=" " required>
                    <label>Harga (Rp)</label>
                </div>
            </div>

            <div class="form-group">
                <div class="form-floating input-wrapper">
                    <i class="fas fa-tags"></i>
                    <select name="kategori_id" required>
                        <option value="">Pilih Kategori</option>
                        <?php while($k = mysqli_fetch_assoc($kategori)) {
                            $selected = ($k['id'] == $row['kategori_id']) ? "selected" : "";
                            echo "<option value='{$k['id']}' $selected>{$k['nama']}</option>";
                        } ?>
                    </select>
                    <label>Kategori</label>
                </div>
            </div>

            <div class="btn-group">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>
                <button type="submit" name="update" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Update Produk
                </button>
            </div>
        </form>
    </div>
</body>
</html>