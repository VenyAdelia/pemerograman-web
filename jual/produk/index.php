<?php
include '../config/koneksi.php';

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search functionality
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$kategori_filter = isset($_GET['kategori']) ? (int)$_GET['kategori'] : '';

// Build query with search and filter
$where_conditions = [];
if (!empty($search)) {
    $where_conditions[] = "produk.nama LIKE '%$search%'";
}
if (!empty($kategori_filter)) {
    $where_conditions[] = "produk.kategori_id = $kategori_filter";
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Get total records for pagination
$count_query = "SELECT COUNT(*) as total FROM produk LEFT JOIN kategori ON produk.kategori_id = kategori.id $where_clause";
$count_result = mysqli_query($koneksi, $count_query);
$total_records = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_records / $limit);

// Main query with pagination
$query = "SELECT produk.*, kategori.nama AS kategori_nama
          FROM produk
          LEFT JOIN kategori ON produk.kategori_id = kategori.id
          $where_clause
          ORDER BY produk.id DESC
          LIMIT $limit OFFSET $offset";
$result = mysqli_query($koneksi, $query);

// Get categories for filter dropdown
$kategori_query = "SELECT * FROM kategori ORDER BY nama ASC";
$kategori_result = mysqli_query($koneksi, $kategori_query);

// Get statistics
$stats_query = "SELECT 
                  COUNT(*) as total_produk,
                  COALESCE(SUM(harga), 0) as total_nilai,
                  COALESCE(AVG(harga), 0) as rata_harga
                FROM produk $where_clause";
$stats_result = mysqli_query($koneksi, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Produk</title>
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
    padding: 20px;
  }

  .container {
    max-width: 1200px;
    margin: 0 auto;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
  }

  h2 {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 30px;
    font-size: 32px;
    font-weight: 700;
    position: relative;
  }

  h2::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 3px;
    background: linear-gradient(90deg, #667eea, #764ba2);
    border-radius: 2px;
  }

  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
  }

  .stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);
    transition: transform 0.3s ease;
  }

  .stat-card:hover {
    transform: translateY(-5px);
  }

  .stat-card i {
    font-size: 32px;
    margin-bottom: 10px;
  }

  .stat-card h3 {
    font-size: 24px;
    margin-bottom: 5px;
  }

  .stat-card p {
    font-size: 14px;
    opacity: 0.9;
  }

  .controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    flex-wrap: wrap;
    gap: 15px;
  }

  .search-filter {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
  }

  .search-box {
    position: relative;
  }

  .search-box input {
    padding: 12px 45px 12px 20px;
    border: 2px solid #e1e8ed;
    border-radius: 10px;
    font-size: 16px;
    width: 300px;
    transition: all 0.3s ease;
  }

  .search-box input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  }

  .search-box i {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
  }

  .filter-select {
    padding: 12px 20px;
    border: 2px solid #e1e8ed;
    border-radius: 10px;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .filter-select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  }

  .btn-add {
    text-decoration: none;
    color: white;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    padding: 12px 20px;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
  }

  .btn-add:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
  }

  .table-wrapper {
    overflow-x: auto;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  }

  table {
    width: 100%;
    border-collapse: collapse;
    background-color: #fff;
    border-radius: 15px;
    overflow: hidden;
  }

  table th, table td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #e9ecef;
  }

  table th {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    color: #2c3e50;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 14px;
    letter-spacing: 0.5px;
  }

  tr:hover {
    background-color: #f8f9fa;
    transition: background-color 0.3s ease;
  }

  .product-name {
    font-weight: 600;
    color: #2c3e50;
  }

  .price {
    font-weight: 700;
    color: #28a745;
    font-size: 16px;
  }

  .category-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .btn-action {
    padding: 8px 12px;
    border-radius: 8px;
    color: white;
    text-decoration: none;
    font-size: 14px;
    margin: 0 3px;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-weight: 500;
  }

  .btn-edit {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
  }

  .btn-edit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 123, 255, 0.4);
  }

  .btn-delete {
    background: linear-gradient(135deg, #dc3545 0%, #a71d2a 100%);
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
  }

  .btn-delete:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
  }

  .pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 30px;
    gap: 10px;
  }

  .pagination a, .pagination span {
    padding: 10px 15px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  .pagination a {
    background: #f8f9fa;
    color: #2c3e50;
    border: 1px solid #e9ecef;
  }

  .pagination a:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    transform: translateY(-2px);
  }

  .pagination .current {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
  }

  .back-home {
    text-align: center;
    margin-top: 30px;
  }

  .btn-home {
    text-decoration: none;
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
  }

  .btn-home:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(108, 117, 125, 0.4);
  }

  .empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
  }

  .empty-state i {
    font-size: 64px;
    margin-bottom: 20px;
    color: #dee2e6;
  }

  .empty-state h3 {
    font-size: 24px;
    margin-bottom: 10px;
    color: #495057;
  }

  .clear-filter {
    background: #dc3545;
    color: white;
    padding: 8px 12px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 5px;
  }

  .clear-filter:hover {
    background: #c82333;
    transform: translateY(-1px);
  }

  .action-buttons {
    display: flex;
    gap: 5px;
    justify-content: center;
  }

  @media (max-width: 768px) {
    .controls {
      flex-direction: column;
      align-items: stretch;
    }
    
    .search-filter {
      flex-direction: column;
    }
    
    .search-box input {
      width: 100%;
    }
    
    .stats-grid {
      grid-template-columns: 1fr;
    }
    
    table th, table td {
      padding: 10px 8px;
      font-size: 14px;
    }
    
    .btn-action {
      padding: 6px 8px;
      font-size: 12px;
    }
  }
</style>

<div class="container">
  <h2><i class="fas fa-boxes"></i> Daftar Produk</h2>
  
  <!-- Statistics Cards -->
  <div class="stats-grid">
    <div class="stat-card">
      <i class="fas fa-box"></i>
      <h3><?php echo number_format($stats['total_produk']); ?></h3>
      <p>Total Produk</p>
    </div>
    <div class="stat-card">
      <i class="fas fa-coins"></i>
      <h3>Rp <?php echo number_format($stats['total_nilai'], 0, ',', '.'); ?></h3>
      <p>Total Nilai</p>
    </div>
    <div class="stat-card">
      <i class="fas fa-chart-line"></i>
      <h3>Rp <?php echo number_format($stats['rata_harga'], 0, ',', '.'); ?></h3>
      <p>Rata-rata Harga</p>
    </div>
  </div>

  <!-- Controls -->
  <div class="controls">
    <div class="search-filter">
      <form method="GET" style="display: flex; gap: 15px; flex-wrap: wrap;">
        <div class="search-box">
          <input type="text" name="search" placeholder="Cari produk..." value="<?php echo htmlspecialchars($search); ?>">
          <i class="fas fa-search"></i>
        </div>
        
        <select name="kategori" class="filter-select" onchange="this.form.submit()">
          <option value="">Semua Kategori</option>
          <?php while ($kategori = mysqli_fetch_assoc($kategori_result)): ?>
            <option value="<?php echo $kategori['id']; ?>" <?php echo ($kategori_filter == $kategori['id']) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($kategori['nama']); ?>
            </option>
          <?php endwhile; ?>
        </select>
        
        <button type="submit" style="display: none;"></button>
      </form>
      
      <?php if (!empty($search) || !empty($kategori_filter)): ?>
        <a href="index.php" class="clear-filter">
          <i class="fas fa-times"></i> Clear Filter
        </a>
      <?php endif; ?>
    </div>
    
    <a href="tambah.php" class="btn-add">
      <i class="fas fa-plus"></i> Tambah Produk
    </a>
  </div>

  <!-- Table -->
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th><i class="fas fa-hashtag"></i> No</th>
          <th><i class="fas fa-box"></i> Nama Produk</th>
          <th><i class="fas fa-tag"></i> Harga</th>
          <th><i class="fas fa-tags"></i> Kategori</th>
          <th><i class="fas fa-cog"></i> Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
          <?php
          $no = $offset + 1;
          while ($row = mysqli_fetch_assoc($result)):
          ?>
            <tr>
              <td><?php echo $no; ?></td>
              <td class="product-name"><?php echo htmlspecialchars($row['nama']); ?></td>
              <td class="price">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
              <td>
                <?php if ($row['kategori_nama']): ?>
                  <span class="category-badge"><?php echo htmlspecialchars($row['kategori_nama']); ?></span>
                <?php else: ?>
                  <span style="color: #6c757d; font-style: italic;">Tidak ada kategori</span>
                <?php endif; ?>
              </td>
              <td>
                <div class="action-buttons">
                  <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn-action btn-edit">
                    <i class="fas fa-edit"></i> Edit
                  </a>
                  <a href="hapus.php?id=<?php echo $row['id']; ?>" class="btn-action btn-delete" 
                     onclick="return confirm('Apakah Anda yakin ingin menghapus produk \'<?php echo htmlspecialchars($row['nama']); ?>\'?')">
                    <i class="fas fa-trash"></i> Hapus
                  </a>
                </div>
              </td>
            </tr>
          <?php
          $no++;
          endwhile;
          ?>
        <?php else: ?>
          <tr>
            <td colspan="5">
              <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>Tidak ada produk</h3>
                <p>
                  <?php if (!empty($search) || !empty($kategori_filter)): ?>
                    Tidak ada produk yang sesuai dengan pencarian atau filter Anda.
                  <?php else: ?>
                    Belum ada produk yang ditambahkan. Klik tombol "Tambah Produk" untuk menambahkan produk pertama.
                  <?php endif; ?>
                </p>
              </div>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <?php if ($total_pages > 1): ?>
    <div class="pagination">
      <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($kategori_filter) ? '&kategori=' . $kategori_filter : ''; ?>">
          <i class="fas fa-chevron-left"></i> Sebelumnya
        </a>
      <?php endif; ?>
      
      <?php
      $start_page = max(1, $page - 2);
      $end_page = min($total_pages, $page + 2);
      
      for ($i = $start_page; $i <= $end_page; $i++):
      ?>
        <?php if ($i == $page): ?>
          <span class="current"><?php echo $i; ?></span>
        <?php else: ?>
          <a href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($kategori_filter) ? '&kategori=' . $kategori_filter : ''; ?>">
            <?php echo $i; ?>
          </a>
        <?php endif; ?>
      <?php endfor; ?>
      
      <?php if ($page < $total_pages): ?>
        <a href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($kategori_filter) ? '&kategori=' . $kategori_filter : ''; ?>">
          Selanjutnya <i class="fas fa-chevron-right"></i>
        </a>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <div class="back-home">
    <a href="../index.php" class="btn-home">
      <i class="fas fa-home"></i> Kembali ke Halaman Utama
    </a>
  </div>
</div>

<script>
// Auto-submit search form on Enter
document.querySelector('input[name="search"]').addEventListener('keypress', function(e) {
  if (e.key === 'Enter') {
    this.form.submit();
  }
});

// Smooth scroll for pagination
document.querySelectorAll('.pagination a').forEach(link => {
  link.addEventListener('click', function(e) {
    e.preventDefault();
    const url = this.href;
    
    // Add loading state
    document.querySelector('.table-wrapper').style.opacity = '0.7';
    
    // Navigate to new page
    setTimeout(() => {
      window.location.href = url;
    }, 200);
  });
});

// Enhanced delete confirmation
document.querySelectorAll('.btn-delete').forEach(button => {
  button.addEventListener('click', function(e) {
    e.preventDefault();
    const productName = this.getAttribute('onclick').match(/'([^']+)'/)[1];
    
    if (confirm(`Apakah Anda yakin ingin menghapus produk "${productName}"?\n\nTindakan ini tidak dapat dibatalkan.`)) {
      // Add loading state
      this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghapus...';
      this.style.opacity = '0.7';
      
      // Navigate to delete URL
      setTimeout(() => {
        window.location.href = this.href;
      }, 500);
    }
  });
});
</script>

</body>
</html>