<?php
include '../config/koneksi.php';

// Inisialisasi variabel
$error = '';
$success = '';
$nama = '';

if (isset($_POST['simpan'])) {
    $nama = trim($_POST['nama']);
    
    // Validasi input
    if (empty($nama)) {
        $error = 'Nama kategori tidak boleh kosong!';
    } elseif (strlen($nama) < 3) {
        $error = 'Nama kategori minimal 3 karakter!';
    } elseif (strlen($nama) > 100) {
        $error = 'Nama kategori maksimal 100 karakter!';
    } else {
        // Cek apakah kategori sudah ada
        $nama_escaped = mysqli_real_escape_string($koneksi, $nama);
        $check_query = "SELECT id FROM kategori WHERE nama = '$nama_escaped'";
        $result = mysqli_query($koneksi, $check_query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $error = 'Kategori dengan nama tersebut sudah ada!';
        } else {
            // Insert data
            $insert_query = "INSERT INTO kategori (nama, created_at) VALUES ('$nama_escaped', NOW())";
            
            if (mysqli_query($koneksi, $insert_query)) {
                $success = 'Kategori berhasil ditambahkan!';
                $nama = ''; // Reset form
                
                // Redirect setelah 2 detik
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'index.php';
                    }, 2000);
                </script>";
            } else {
                $error = 'Gagal menambahkan kategori: ' . mysqli_error($koneksi);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kategori</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
            font-size: 28px;
            font-weight: 700;
            position: relative;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 2px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #34495e;
            font-weight: 600;
            font-size: 14px;
        }

        input[type="text"] {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e1e8ed;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .char-counter {
            position: absolute;
            right: 10px;
            bottom: -20px;
            font-size: 12px;
            color: #6c757d;
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
            position: relative;
            overflow: hidden;
            margin-bottom: 10px;
        }

        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        button[type="submit"]:active {
            transform: translateY(0);
        }

        button[type="submit"]:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }



        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            margin-top: 20px;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: #5a6268;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
        }

        .back-wrapper {
            text-align: center;
        }

        .alert {
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }



        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .loading {
            display: none;
            text-align: center;
            margin-top: 10px;
        }

        .loading.show {
            display: block;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 480px) {
            .container {
                padding: 30px 25px;
                margin: 10px;
            }
            
            h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-plus-circle"></i> Tambah Kategori</h2>
        

        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" id="categoryForm">
            <div class="form-group">
                <label for="nama">
                    <i class="fas fa-tag"></i> Nama Kategori
                </label>
                <input 
                    type="text" 
                    id="nama"
                    name="nama" 
                    placeholder="Masukkan nama kategori..." 
                    value="<?php echo htmlspecialchars($nama); ?>"
                    maxlength="100"
                    required
                >
                <div class="char-counter">
                    <span id="charCount">0</span>/100 karakter
                </div>
            </div>
            
            <button type="submit" name="simpan" id="submitBtn">
                <i class="fas fa-save"></i> Simpan Kategori
            </button>
            
            <div class="loading" id="loading">
                <div class="spinner"></div>
                <p>Menyimpan kategori...</p>
            </div>
        </form>
        
        <div class="back-wrapper">
            <a href="index.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <script>
        // Character counter
        const namaInput = document.getElementById('nama');
        const charCount = document.getElementById('charCount');
        
        namaInput.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = length;
            
            if (length > 90) {
                charCount.style.color = '#dc3545';
            } else if (length > 70) {
                charCount.style.color = '#fd7e14';
            } else {
                charCount.style.color = '#6c757d';
            }
        });
        
        // Form submission with loading
        const form = document.getElementById('categoryForm');
        const submitBtn = document.getElementById('submitBtn');
        const loading = document.getElementById('loading');
        
        form.addEventListener('submit', function(e) {
            if (namaInput.value.trim().length < 3) {
                e.preventDefault();
                alert('Nama kategori minimal 3 karakter!');
                return;
            }
            
            submitBtn.disabled = true;
            loading.classList.add('show');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
        });
        
        // Auto-hide alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 300);
            }, 5000);
        });
        
        // Initialize character counter
        charCount.textContent = namaInput.value.length;
    </script>
</body>
</html>