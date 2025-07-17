<?php
include '../config/koneksi.php';

// Validasi dan sanitasi input ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = (int)$_GET['id'];

// Gunakan prepared statement untuk keamanan
$stmt = mysqli_prepare($koneksi, "SELECT * FROM kategori WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

// Cek apakah data ditemukan
if (!$row) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['update'])) {
    $nama = trim($_POST['nama']);
    
    // Validasi input
    if (!empty($nama)) {
        // Gunakan prepared statement untuk update
        $stmt = mysqli_prepare($koneksi, "UPDATE kategori SET nama = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $nama, $id);
        
        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php");
            exit();
        } else {
            $error = "Gagal mengupdate data. Silakan coba lagi.";
        }
    } else {
        $error = "Nama kategori tidak boleh kosong.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kategori</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.05)"/><circle cx="20" cy="60" r="0.5" fill="rgba(255,255,255,0.05)"/><circle cx="80" cy="40" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            z-index: -1;
        }

        .container {
            max-width: 450px;
            margin: 0 auto;
            position: relative;
        }

        h2 {
            text-align: center;
            color: #ffffff;
            margin-bottom: 40px;
            font-size: 2.5em;
            font-weight: 300;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
            letter-spacing: 1px;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px 35px;
            box-shadow: 
                0 20px 40px rgba(0,0,0,0.1),
                0 0 0 1px rgba(255,255,255,0.2),
                inset 0 1px 0 rgba(255,255,255,0.3);
            position: relative;
            overflow: hidden;
        }

        .form-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2, #667eea);
            background-size: 200% 100%;
            animation: shimmer 3s linear infinite;
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        input[type="text"] {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e1e8ed;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #fff;
            position: relative;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper::after {
            content: '✏️';
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 16px;
            opacity: 0.6;
        }

        button[type="submit"] {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
        }

        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        button[type="submit"]:active {
            transform: translateY(0);
        }

        button[type="submit"]::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        button[type="submit"]:hover::before {
            left: 100%;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: rgba(255, 255, 255, 0.9);
            color: #2c3e50;
            text-decoration: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            margin: 30px auto 0;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
            border: 2px solid rgba(255,255,255,0.3);
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 1);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .back-wrapper {
            text-align: center;
        }

        .error {
            color: #e74c3c;
            background: linear-gradient(135deg, #ffeaea 0%, #ffdbdb 100%);
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 25px;
            text-align: center;
            border: 2px solid #f5b7b1;
            font-weight: 500;
            box-shadow: 0 4px 10px rgba(231, 76, 60, 0.1);
        }

        .success {
            color: #27ae60;
            background: linear-gradient(135deg, #eafaf1 0%, #d5f4e6 100%);
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 25px;
            text-align: center;
            border: 2px solid #a9dfbf;
            font-weight: 500;
            box-shadow: 0 4px 10px rgba(39, 174, 96, 0.1);
        }

        @media (max-width: 480px) {
            .container {
                max-width: 100%;
            }
            
            h2 {
                font-size: 2em;
            }
            
            .form-container {
                padding: 30px 25px;
                margin: 0 10px;
            }
            
            body {
                padding: 20px 10px;
            }
        }

        /* Floating animation */
        .form-container {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Kategori</h2>
        <div class="form-container">
            <form method="POST">
                <?php if (isset($error)): ?>
                    <div class="error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="nama">Nama Kategori</label>
                    <div class="input-wrapper">
                        <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($row['nama']) ?>" required maxlength="100" placeholder="Masukkan nama kategori">
                    </div>
                </div>
                
                <button type="submit" name="update">Update Kategori</button>
            </form>
        </div>

        <div class="back-wrapper">
            <a href="index.php" class="btn-back">
                <span>←</span>
                Kembali ke Daftar Kategori
            </a>
        </div>
    </div>
</body>
</html>