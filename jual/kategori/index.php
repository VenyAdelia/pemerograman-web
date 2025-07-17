<?php
include '../config/koneksi.php';

$result = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama ASC");
$total_kategori = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kategori</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 30px 40px;
            text-align: center;
            position: relative;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .header .subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .stats {
            background: #f8f9fa;
            padding: 20px 40px;
            border-bottom: 1px solid #e9ecef;
        }

        .stats-item {
            display: inline-flex;
            align-items: center;
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-right: 15px;
        }

        .stats-icon {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 1.2rem;
        }

        .stats-info h3 {
            font-size: 1.8rem;
            color: #2c3e50;
            margin-bottom: 2px;
        }

        .stats-info p {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .content {
            padding: 30px 40px;
        }

        .top-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .search-box {
            position: relative;
            flex: 1;
            max-width: 300px;
        }

        .search-box input {
            width: 100%;
            padding: 12px 45px 12px 20px;
            border: 2px solid #e9ecef;
            border-radius: 25px;
            font-size: 1rem;
            transition: all 0.3s ease;
            outline: none;
        }

        .search-box input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .search-box i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #7f8c8d;
        }

        .btn-add {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 12px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        }

        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            color: #495057;
            font-weight: 600;
            padding: 18px 20px;
            text-align: left;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        table th:first-child {
            text-align: center;
            width: 80px;
        }

        table th:last-child {
            text-align: center;
            width: 200px;
        }

        table td {
            padding: 18px 20px;
            border-bottom: 1px solid #f1f3f4;
            font-size: 1rem;
            color: #495057;
        }

        table tr:hover {
            background: linear-gradient(135deg, #f8f9ff, #fff5f5);
            transform: scale(1.01);
            transition: all 0.2s ease;
        }

        .row-number {
            text-align: center;
            font-weight: 600;
            color: #667eea;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }

        .category-name {
            font-weight: 500;
            color: #2c3e50;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .btn-action {
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            min-width: 80px;
            justify-content: center;
        }

        .btn-edit {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            box-shadow: 0 2px 10px rgba(0, 123, 255, 0.3);
        }

        .btn-edit:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4);
        }

        .btn-delete {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            box-shadow: 0 2px 10px rgba(220, 53, 69, 0.3);
        }

        .btn-delete:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4);
        }

        .footer {
            background: #f8f9fa;
            padding: 25px 40px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }

        .btn-home {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
            padding: 12px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
        }

        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #bdc3c7;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #34495e;
        }

        .empty-state p {
            font-size: 1rem;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .container {
                margin: 10px;
                border-radius: 15px;
            }
            
            .header, .content, .footer {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .top-actions {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-box {
                max-width: none;
            }
            
            .table-container {
                overflow-x: auto;
            }
            
            table {
                min-width: 600px;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 4px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-tags"></i> Manajemen Kategori</h1>
            <p class="subtitle">Kelola kategori produk dengan mudah dan efisien</p>
        </div>

        <div class="stats">
            <div class="stats-item">
                <div class="stats-icon">
                    <i class="fas fa-list"></i>
                </div>
                <div class="stats-info">
                    <h3><?php echo $total_kategori; ?></h3>
                    <p>Total Kategori</p>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="top-actions">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Cari kategori..." onkeyup="searchTable()">
                    <i class="fas fa-search"></i>
                </div>
                <a href="tambah.php" class="btn-add">
                    <i class="fas fa-plus"></i> Tambah Kategori
                </a>
            </div>

            <div class="table-container">
                <?php if ($total_kategori > 0): ?>
                <table id="kategoriTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kategori</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>
                                    <td><span class='row-number'>$no</span></td>
                                    <td><span class='category-name'>{$row['nama']}</span></td>
                                    <td>
                                        <div class='action-buttons'>
                                            <a href='edit.php?id={$row['id']}' class='btn-action btn-edit'>
                                                <i class='fas fa-edit'></i> Edit
                                            </a>
                                            <a href='hapus.php?id={$row['id']}' class='btn-action btn-delete' 
                                               onclick='return confirmDelete(\"{$row['nama']}\")'>
                                                <i class='fas fa-trash'></i> Hapus
                                            </a>
                                        </div>
                                    </td>
                                  </tr>";
                            $no++;
                        }
                        ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>Belum Ada Kategori</h3>
                    <p>Mulai dengan menambahkan kategori pertama Anda</p>
                    <a href="tambah.php" class="btn-add">
                        <i class="fas fa-plus"></i> Tambah Kategori
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="footer">
            <a href="../index.php" class="btn-home">
                <i class="fas fa-home"></i> Kembali ke Halaman Utama
            </a>
        </div>
    </div>

    <script>
        function searchTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('kategoriTable');
            
            if (!table) return;
            
            const tr = table.getElementsByTagName('tr');
            
            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName('td')[1];
                if (td) {
                    const txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = '';
                    } else {
                        tr[i].style.display = 'none';
                    }
                }
            }
        }

        function confirmDelete(namaKategori) {
            return confirm('Apakah Anda yakin ingin menghapus kategori "' + namaKategori + '"?\n\nData yang sudah dihapus tidak dapat dikembalikan.');
        }

        // Add smooth scroll behavior
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Add loading animation for buttons
        document.querySelectorAll('.btn-action').forEach(button => {
            button.addEventListener('click', function() {
                if (!this.classList.contains('btn-delete')) {
                    this.style.opacity = '0.7';
                    this.style.pointerEvents = 'none';
                }
            });
        });
    </script>
</body>
</html>