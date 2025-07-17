<?php
include '../config/koneksi.php';

// Inisialisasi variabel
$error = '';
$success = '';
$nama = '';
$harga = '';
$kategori_id = '';

// Ambil data kategori untuk dropdown
$kategori_result = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama ASC");

if (isset($_POST['simpan'])) {
    $nama = trim($_POST['nama']);
    $harga = trim($_POST['harga']);
    $kategori_id = $_POST['kategori_id'];
    
    // Validasi input
    if (empty($nama)) {
        $error = 'Nama produk tidak boleh kosong!';
    } elseif (strlen($nama) < 3) {
        $error = 'Nama produk minimal 3 karakter!';
    } elseif (strlen($nama) > 100) {
        $error = 'Nama produk maksimal 100 karakter!';
    } elseif (empty($harga) || !is_numeric($harga)) {
        $error = 'Harga harus berupa angka!';
    } elseif ($harga < 0) {
        $error = 'Harga tidak boleh negatif!';
    } elseif ($harga > 999999999) {
        $error = 'Harga maksimal 999,999,999!';
    } elseif (empty($kategori_id)) {
        $error = 'Kategori harus dipilih!';
    } else {
        // Cek apakah kategori masih ada
        $check_kategori = "SELECT id FROM kategori WHERE id = ?";
        $stmt_check = mysqli_prepare($koneksi, $check_kategori);
        mysqli_stmt_bind_param($stmt_check, "i", $kategori_id);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);
        
        if (mysqli_num_rows($result_check) == 0) {
            $error = 'Kategori yang dipilih tidak valid!';
        } else {
            // Cek apakah produk sudah ada
            $check_product = "SELECT id FROM produk WHERE nama = ?";
            $stmt_product = mysqli_prepare($koneksi, $check_product);
            mysqli_stmt_bind_param($stmt_product, "s", $nama);
            mysqli_stmt_execute($stmt_product);
            $result_product = mysqli_stmt_get_result($stmt_product);
            
            if (mysqli_num_rows($result_product) > 0) {
                $error = 'Produk dengan nama tersebut sudah ada!';
            } else {
                // Insert dengan prepared statement
                $insert_query = "INSERT INTO produk (nama, harga, kategori_id, created_at) VALUES (?, ?, ?, NOW())";
                $stmt = mysqli_prepare($koneksi, $insert_query);
                
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "sdi", $nama, $harga, $kategori_id);
                    
                    if (mysqli_stmt_execute($stmt)) {
                        $success = 'Produk berhasil ditambahkan!';
                        $nama = '';
                        $harga = '';
                        $kategori_id = '';
                        
                        // Redirect setelah 2 detik
                        echo "<script>
                            setTimeout(function() {
                                window.location.href = 'index.php';
                            }, 2000);
                        </script>";
                    } else {
                        $error = 'Gagal menambahkan produk: ' . mysqli_error($koneksi);
                    }
                    
                    mysqli_stmt_close($stmt);
                } else {
                    $error = 'Gagal menyiapkan query: ' . mysqli_error($koneksi);
                }
            }
            
            mysqli_stmt_close($stmt_product);
        }
        
        mysqli_stmt_close($stmt_check);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
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
    margin: 0;
  }

  .container {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 500px;
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
    width: 60px;
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

  input[type="text"],
  input[type="number"],
  select {
    width: 100%;
    padding: 15px 20px;
    border: 2px solid #e1e8ed;
    border-radius: 12px;
    font-size: 16px;
    transition: all 0.3s ease;
    background: #f8f9fa;
    margin-bottom: 20px;
  }

  input[type="text"]:focus,
  input[type="number"]:focus,
  select:focus {
    outline: none;
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  }

  select {
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 12px center;
    background-repeat: no-repeat;
    background-size: 16px;
    padding-right: 40px;
  }

  .price-display {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: #667eea;
    font-weight: 600;
    font-size: 14px;
    pointer-events: none;
    background: white;
    padding: 0 8px;
    border-radius: 4px;
    margin-top: -10px;
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

  .input-group {
    position: relative;
  }

  .input-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    margin-top: -10px;
  }

  .input-with-icon {
    padding-left: 45px;
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

<div class="container">
  <h2><i class="fas fa-plus-circle"></i> Tambah Produk</h2>
  
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
  
  <form method="POST" id="productForm">
    <div class="form-group">
      <div class="input-group">
        <i class="fas fa-box input-icon"></i>
        <input 
          type="text" 
          name="nama" 
          placeholder="Nama produk" 
          value="<?php echo htmlspecialchars($nama); ?>"
          class="input-with-icon"
          maxlength="100"
          required>
        <div class="char-counter">
          <span id="charCount">0</span>/100 karakter
        </div>
      </div>
    </div>
    
    <div class="form-group">
      <div class="input-group">
        <i class="fas fa-rupiah-sign input-icon"></i>
        <input 
          type="number" 
          name="harga" 
          placeholder="Harga (dalam rupiah)" 
          value="<?php echo htmlspecialchars($harga); ?>"
          class="input-with-icon"
          min="0"
          max="999999999"
          id="hargaInput"
          required>
        <div class="price-display" id="priceDisplay">Rp 0</div>
      </div>
    </div>
    
    <div class="form-group">
      <div class="input-group">
        <i class="fas fa-tags input-icon"></i>
        <select name="kategori_id" class="input-with-icon" required>
          <option value="">-- Pilih Kategori --</option>
          <?php 
          // Reset pointer result untuk loop ulang
          mysqli_data_seek($kategori_result, 0);
          while ($row = mysqli_fetch_assoc($kategori_result)): ?>
            <option value="<?= $row['id'] ?>" <?= ($kategori_id == $row['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($row['nama']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
    </div>

    <button type="submit" name="simpan" id="submitBtn">
      <i class="fas fa-save"></i> Simpan Produk
    </button>
    
    <div class="loading" id="loading">
      <div class="spinner"></div>
      <p>Menyimpan produk...</p>
    </div>
  </form>

  <div class="back-wrapper">
    <a href="index.php" class="btn-back">
      <i class="fas fa-arrow-left"></i> Kembali ke Daftar Produk
    </a>
  </div>
</div>

<script>
  // Format currency
  function formatRupiah(angka) {
    const number = parseInt(angka) || 0;
    return 'Rp ' + number.toLocaleString('id-ID');
  }

  // Character counter
  const namaInput = document.querySelector('input[name="nama"]');
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

  // Price formatter
  const hargaInput = document.getElementById('hargaInput');
  const priceDisplay = document.getElementById('priceDisplay');
  
  hargaInput.addEventListener('input', function() {
    const value = this.value;
    priceDisplay.textContent = formatRupiah(value);
    
    if (value && parseInt(value) > 0) {
      priceDisplay.style.display = 'block';
    } else {
      priceDisplay.style.display = 'none';
    }
  });

  // Form submission
  const form = document.getElementById('productForm');
  const submitBtn = document.getElementById('submitBtn');
  const loading = document.getElementById('loading');
  
  form.addEventListener('submit', function(e) {
    const nama = namaInput.value.trim();
    const harga = hargaInput.value;
    const kategori = document.querySelector('select[name="kategori_id"]').value;
    
    if (nama.length < 3) {
      e.preventDefault();
      alert('Nama produk minimal 3 karakter!');
      return;
    }
    
    if (!harga || parseInt(harga) <= 0) {
      e.preventDefault();
      alert('Harga harus diisi dan lebih dari 0!');
      return;
    }
    
    if (!kategori) {
      e.preventDefault();
      alert('Kategori harus dipilih!');
      return;
    }
    
    submitBtn.disabled = true;
    loading.classList.add('show');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
  });

  // Auto-hide alerts
  const alerts = document.querySelectorAll('.alert');
  alerts.forEach(alert => {
    setTimeout(() => {
      alert.style.opacity = '0';
      setTimeout(() => {
        alert.style.display = 'none';
      }, 300);
    }, 5000);
  });

  // Initialize counters
  charCount.textContent = namaInput.value.length;
  if (hargaInput.value) {
    priceDisplay.textContent = formatRupiah(hargaInput.value);
    priceDisplay.style.display = 'block';
  }

  // Input validation
  hargaInput.addEventListener('keypress', function(e) {
    if (e.key === '-' || e.key === '+') {
      e.preventDefault();
    }
  });
</script>

</body>
</html>