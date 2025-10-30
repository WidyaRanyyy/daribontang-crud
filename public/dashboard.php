<?php
// TAMPILKAN SEMUA ERROR UNTUK DEBUGGING (HAPUS KETIKA PRODUKSI)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// 1. Sertakan file koneksi database
// Karena ini file public/, path ke config adalah ../config/database.php
require_once '../config/database.php'; 

// Jika belum login, redirect ke login.php
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// 2. DEKLARASI VARIABEL UTAMA (PENTING AGAR TIDAK UNDEFINED)
$page = $_GET['page'] ?? 'home';

// --- LOGIKA PAGINATION & PENCARIAN (untuk page=produk) ---
$products = [];
$total_products = 0;
$total_pages = 1;
$limit = 5; // Wajib: Minimal 5 data per halaman

// Data statistik sementara. Dalam praktik, ini dihitung dari DB.
$stats = [
    'total_products' => 0, 
    'total_wisata' => 2,
    'total_makanan' => 1,
    'total_views' => 1234
];

if ($page === 'produk') {
    $page_num = isset($_GET['p']) ? (int)$_GET['p'] : 1;
    $offset = ($page_num - 1) * $limit;
    $searchTerm = isset($_GET['search']) ? htmlspecialchars(trim($_GET['search'])) : '';

    try {
        // Query COUNT (untuk Pagination) dan Pencarian
        $countQuery = "SELECT COUNT(id) FROM products";
        $whereClause = "";
        
        if (!empty($searchTerm)) {
            $whereClause = " WHERE name LIKE :search OR category LIKE :search";
            $countQuery .= $whereClause;
        }

        $stmtCount = $pdo->prepare($countQuery);
        if (!empty($searchTerm)) {
            $stmtCount->bindValue(':search', '%' . $searchTerm . '%');
        }
        $stmtCount->execute();
        $total_products = $stmtCount->fetchColumn();
        $stats['total_products'] = $total_products;
        $total_pages = ceil($total_products / $limit);

        // Query Data dengan Limit, Offset, dan Order (Wajib: created_at desc)
        $dataQuery = "SELECT * FROM products" . $whereClause . " ORDER BY created_at DESC LIMIT :limit OFFSET :offset"; 

        $stmt = $pdo->prepare($dataQuery);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        if (!empty($searchTerm)) {
            $stmt->bindValue(':search', '%' . $searchTerm . '%');
        }
        $stmt->execute();
        $products = $stmt->fetchAll();

    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Gagal memuat data produk. Pastikan tabel 'products' dan 'users' sudah ada."; 
        error_log("Dashboard Product Load Error: " . $e->getMessage());
    }
}
// --------------------------------------------------------

// Menu items
$menu_items = [
    'home' => ['icon' => '🏠', 'title' => 'Beranda'],
    'profil' => ['icon' => '👤', 'title' => 'Profil'],
    'produk' => ['icon' => '🛍️', 'title' => 'Produk'],
    'wisata' => ['icon' => '🗺️', 'title' => 'Wisata'],
    'makanan' => ['icon' => '🍜', 'title' => 'Makanan'],
    'setting' => ['icon' => '⚙️', 'title' => 'Pengaturan']
];

if (!array_key_exists($page, $menu_items)) {
    $page = 'home';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | <?php echo $menu_items[$page]['title']; ?></title>
    <link rel="stylesheet" href="../style.css"> <!-- Menggunakan CSS utama dari root -->
    <style>
        /* CSS Tambahan Khusus Dashboard */
        /* Catatan: CSS di bawah ditujukan untuk layout sidebar dan tabel. */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .sidebar {
            width: 250px;
            /* Mengambil gradien warna dari tema utama (style.css) */
            background: linear-gradient(180deg, #841751, #6b1242);
            color: white;
            padding: 20px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            flex-shrink: 0;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 1.5rem;
            text-transform: uppercase;
        }
        .sidebar a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 15px;
            margin-bottom: 10px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .sidebar a:hover, .sidebar a.active {
            background: rgba(255, 255, 255, 0.15);
            color: white;
        }
        .main-content {
            flex-grow: 1;
            padding: 40px;
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
            padding-bottom: 15px;
        }
        .section-header h2 {
            font-size: 2rem;
            color: #841751; /* Warna tema utama */
        }
        
        /* Menggunakan style.css untuk Button */
        .btn-primary { background: linear-gradient(135deg, #841751, #db2796); color: white; border: none; padding: 10px 15px; border-radius: 8px; text-decoration: none; font-weight: 600; }
        .btn-primary:hover { background: linear-gradient(135deg, #6b1242, #b92080); }
        .btn-secondary { background: #6c757d; color: white; border: none; padding: 10px 15px; border-radius: 8px; text-decoration: none; font-weight: 600; }
        .btn-secondary:hover { background: #5a6268; }

        /* Style untuk Tabel Data */
        .table-container {
            overflow-x: auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-top: 20px;
        }
        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        .data-table th, .data-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #f1f1f1;
        }
        .data-table th {
            background-color: #f1f1f1;
            color: #555;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
        }
        .data-table tr:hover {
            background-color: #fafafa;
        }
        .btn-small {
            padding: 6px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.85rem;
            display: inline-block;
            margin-right: 5px;
        }
        .btn-edit {
            background: #ffc107;
            color: #333;
        }
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .logout-btn {
            background: #dc3545;
            color: white;
            padding: 12px 15px;
            border-radius: 8px;
            text-align: center;
            margin-top: 30px;
            display: block;
            text-decoration: none;
        }
        .logout-btn:hover {
            background: #c82333;
        }
        /* Card Stats */
        .card-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border-left: 5px solid #841751;
        }
        .stat-card h4 {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 5px;
        }
        .stat-card p {
            font-size: 2.2rem;
            font-weight: 700;
            color: #841751;
        }
        /* Responsiveness */
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                height: auto;
                padding-bottom: 10px;
            }
            .sidebar-nav {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
            }
            .sidebar a {
                padding: 8px 10px;
                flex-grow: 1;
                margin-right: 5px;
                text-align: center;
                gap: 5px;
            }
            .logout-btn {
                margin-top: 10px;
            }
            .main-content {
                padding: 20px;
            }
            .section-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .section-header a {
                margin-top: 15px;
            }
            .card-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>DASHBOARD ADMIN</h2>
        <p style="color: #ccc; margin-bottom: 20px; font-size: 0.9rem;">Halo, <?php echo htmlspecialchars($_SESSION['username']); ?></p>
        
        <nav class="sidebar-nav">
            <?php foreach ($menu_items as $key => $item): ?>
                <a href="dashboard.php?page=<?php echo $key; ?>" class="<?php echo $page === $key ? 'active' : ''; ?>">
                    <?php echo $item['icon']; ?> <span><?php echo $item['title']; ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
        
        <a href="logout.php" class="logout-btn">
            🚪 Logout
        </a>
    </div>

    <div class="main-content">
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error_message'])): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>
        
        
        <?php if ($page === 'home'): ?>
            <div class="section-header">
                <h2><?php echo $menu_items[$page]['title']; ?></h2>
            </div>
            
            <div class="card-stats">
                <div class="stat-card">
                    <h4>Total Produk</h4>
                    <p><?php echo $stats['total_products']; ?></p>
                </div>
                <div class="stat-card">
                    <h4>Total Wisata</h4>
                    <p><?php echo $stats['total_wisata']; ?></p>
                </div>
                <div class="stat-card">
                    <h4>Total Makanan Khas</h4>
                    <p><?php echo $stats['total_makanan']; ?></p>
                </div>
                <div class="stat-card">
                    <h4>Total Kunjungan</h4>
                    <p><?php echo number_format($stats['total_views']); ?></p>
                </div>
            </div>
            
            <p style="text-align: center; padding: 50px; background: #eee; border-radius: 10px;">Selamat datang di Dashboard Admin Dari Bontang. Silakan kelola data melalui menu di samping.</p>

        <?php elseif ($page === 'produk'): ?>
            <div class="section-header">
                <h2>Kelola Produk</h2>
                <a href="product/add_product.php" class="btn-primary"><span>+ Tambah Produk</span></a> 
            </div>

            <form method="GET" action="dashboard.php" style="margin-bottom: 20px; display: flex; gap: 10px;">
                <input type="hidden" name="page" value="produk">
                <input type="text" name="search" placeholder="Cari berdasarkan nama/kategori..." 
                       value="<?php echo htmlspecialchars($searchTerm); ?>" 
                       style="padding: 10px; border: 1px solid #ccc; border-radius: 8px; flex-grow: 1; max-width: 300px;">
                <button type="submit" class="btn-secondary" style="padding: 10px 15px;">Cari</button>
                <a href="dashboard.php?page=produk" class="btn-secondary" style="padding: 10px 15px;">Reset</a>
            </form>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID (Kunci)</th> <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Stok</th>
                            <th>Dibuat Pada</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = $offset + 1;
                        foreach ($products as $product): 
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($product['id']); ?></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['category']); ?></td>
                            <td><span class="badge badge-success"><?php echo htmlspecialchars($product['stock']); ?></span></td>
                            <td><?php echo date('d-m-Y H:i', strtotime($product['created_at'])); ?></td>
                            <td>
                                <a href="product/detail_product.php?id=<?php echo $product['id']; ?>" class="btn-small btn-secondary">Detail</a>
                                <a href="product/edit_product.php?id=<?php echo $product['id']; ?>" class="btn-small btn-edit">Edit</a>
                                <a href="product/delete_product.php?id=<?php echo $product['id']; ?>" 
                                   class="btn-small btn-delete" 
                                   onclick="return confirm('❗ Yakin ingin menghapus produk <?php echo htmlspecialchars($product['name']); ?>? Tindakan ini tidak dapat dibatalkan.');">
                                   Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($products)): ?>
                            <tr><td colspan="7" class="text-center" style="text-align: center;">Tidak ditemukan data.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="pagination" style="margin-top: 20px; text-align: center;">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <?php 
                        $activeClass = ($i === $page_num) ? 'background: #841751; color: white;' : 'background: #eee;';
                        $searchParam = !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : '';
                    ?>
                    <a href="dashboard.php?page=produk&p=<?php echo $i . $searchParam; ?>" 
                       style="padding: 8px 12px; margin: 0 5px; border-radius: 5px; text-decoration: none; <?php echo $activeClass; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        
        <?php elseif ($page === 'wisata'): ?>
    <?php
    // --- LOGIKA PAGINATION & PENCARIAN WISATA ---
    $page_num = max(1, (int)($_GET['p'] ?? 1));
    $offset = ($page_num - 1) * $limit;
    $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

    $tourisms = [];
    $total_tourisms = 0;
    $total_pages_tourisms = 1;

    try {
        $whereClause = '';
        $params = [];

        if (!empty($searchTerm)) {
            $whereClause = " WHERE name LIKE ? OR location LIKE ?";
            $params = ["%$searchTerm%", "%$searchTerm%"];
        }

        // Hitung total
        $countSql = "SELECT COUNT(*) FROM tourism" . $whereClause;
        $stmt = $pdo->prepare($countSql);
        $stmt->execute($params);
        $total_tourisms = $stmt->fetchColumn();
        $stats['total_wisata'] = $total_tourisms;
        $total_pages_tourisms = ceil($total_tourisms / $limit);

        // Ambil data
        $dataSql = "SELECT * FROM tourism" . $whereClause . " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $pdo->prepare($dataSql);
        $stmt->execute(array_merge($params, [$limit, $offset]));
        $tourisms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Gagal memuat data wisata. Pastikan tabel 'tourism' ada.";
        error_log("Tourism Load Error: " . $e->getMessage());
    }
    ?>

    <div class="section-header">
        <h2>Kelola Wisata</h2>
        <a href="tourism/add_tourism.php" class="btn-primary">+ Tambah Wisata</a>
    </div>

    <form method="GET" style="margin-bottom:20px; display:flex; gap:10px;">
        <input type="hidden" name="page" value="wisata">
        <input type="text" name="search" placeholder="Cari nama/lokasi..." 
               value="<?= htmlspecialchars($searchTerm) ?>" 
               style="padding:10px; border:1px solid #ccc; border-radius:8px; flex-grow:1; max-width:300px;">
        <button type="submit" class="btn-secondary">Cari</button>
        <a href="dashboard.php?page=wisata" class="btn-secondary">Reset</a>
    </form>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID</th>
                    <th>Nama Wisata</th>
                    <th>Lokasi</th>
                    <th>Rating</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = $offset + 1;
                foreach ($tourisms as $t): 
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($t['id']) ?></td>
                    <td><?= htmlspecialchars($t['name']) ?></td>
                    <td><?= htmlspecialchars($t['location']) ?></td>
                    <td><span class="badge badge-success"><?= number_format($t['rating'], 1) ?>/5</span></td>
                    <td><?= date('d-m-Y H:i', strtotime($t['created_at'])) ?></td>
                    <td>
                        <a href="tourism/detail_tourism.php?id=<?= $t['id'] ?>" class="btn-small btn-secondary">Detail</a>
                        <a href="tourism/edit_tourism.php?id=<?= $t['id'] ?>" class="btn-small btn-edit">Edit</a>
                        <a href="tourism/delete_tourism.php?id=<?= $t['id'] ?>" 
                           class="btn-small btn-delete"
                           onclick="return confirm('Yakin hapus wisata <?= htmlspecialchars($t['name']) ?>?')">
                           Hapus
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($tourisms)): ?>
                    <tr><td colspan="7" style="text-align:center;">Tidak ada data wisata.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="pagination" style="margin-top:20px; text-align:center;">
        <?php for ($i = 1; $i <= $total_pages_tourisms; $i++): ?>
            <?php 
            $activeClass = ($i === $page_num) ? 'background:#841751; color:white;' : 'background:#eee;';
            $url = "dashboard.php?page=wisata&p=$i";
            if (!empty($searchTerm)) $url .= "&search=" . urlencode($searchTerm);
            ?>
            <a href="<?= $url ?>" style="padding:8px 12px; margin:0 5px; border-radius:5px; text-decoration:none; <?= $activeClass ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>

    <?php elseif ($page === 'makanan'): ?>
        <?php
        // --- LOGIKA PAGINATION & PENCARIAN MAKANAN ---
        $page_num = max(1, (int)($_GET['p'] ?? 1));
        $offset = ($page_num - 1) * $limit;
        $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

        $foods = [];
        $total_foods = 0;
        $total_pages_foods = 1;

        try {
            $whereClause = '';
            $params = [];

            if (!empty($searchTerm)) {
                $whereClause = " WHERE name LIKE ? OR best_place LIKE ?";
                $params = ["%$searchTerm%", "%$searchTerm%"];
            }

            // Hitung total
            $countSql = "SELECT COUNT(*) FROM foods" . $whereClause;
            $stmt = $pdo->prepare($countSql);
            $stmt->execute($params);
            $total_foods = $stmt->fetchColumn();
            $stats['total_makanan'] = $total_foods;
            $total_pages_foods = ceil($total_foods / $limit);

            // Ambil data
            $dataSql = "SELECT * FROM foods" . $whereClause . " ORDER BY rating DESC, created_at DESC LIMIT ? OFFSET ?";
            $stmt = $pdo->prepare($dataSql);
            $stmt->execute(array_merge($params, [$limit, $offset]));
            $foods = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Gagal memuat data makanan. Pastikan tabel 'foods' ada.";
            error_log("Food Load Error: " . $e->getMessage());
        }
        ?>

        <div class="section-header">
            <h2>Kelola Makanan Khas</h2>
            <a href="food/add_food.php" class="btn-primary">+ Tambah Makanan</a>
        </div>

        <form method="GET" style="margin-bottom:20px; display:flex; gap:10px;">
            <input type="hidden" name="page" value="makanan">
            <input type="text" name="search" placeholder="Cari nama/tempat..." 
                value="<?= htmlspecialchars($searchTerm) ?>" 
                style="padding:10px; border:1px solid #ccc; border-radius:8px; flex-grow:1; max-width:300px;">
            <button type="submit" class="btn-secondary">Cari</button>
            <a href="dashboard.php?page=makanan" class="btn-secondary">Reset</a>
        </form>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID</th>
                        <th>Nama Makanan</th>
                        <th>Asal</th>
                        <th>Rating</th>
                        <th>Tempat Terbaik</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = $offset + 1;
                    foreach ($foods as $f): 
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($f['id']) ?></td>
                        <td><?= htmlspecialchars($f['name']) ?></td>
                        <td><?= htmlspecialchars($f['origin']) ?></td>
                        <td><span class="badge badge-success"><?= number_format($f['rating'], 1) ?>/5</span></td>
                        <td><?= htmlspecialchars($f['best_place']) ?></td>
                        <td>
                            <a href="food/detail_food.php?id=<?= $f['id'] ?>" class="btn-small btn-secondary">Detail</a>
                            <a href="food/edit_food.php?id=<?= $f['id'] ?>" class="btn-small btn-edit">Edit</a>
                            <a href="food/delete_food.php?id=<?= $f['id'] ?>" 
                            class="btn-small btn-delete"
                            onclick="return confirm('Yakin hapus makanan <?= htmlspecialchars($f['name']) ?>?')">
                            Hapus
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($foods)): ?>
                        <tr><td colspan="7" style="text-align:center;">Tidak ada data makanan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="pagination" style="margin-top:20px; text-align:center;">
            <?php for ($i = 1; $i <= $total_pages_foods; $i++): ?>
                <?php 
                $activeClass = ($i === $page_num) ? 'background:#841751; color:white;' : 'background:#eee;';
                $url = "dashboard.php?page=makanan&p=$i";
                if (!empty($searchTerm)) $url .= "&search=" . urlencode($searchTerm);
                ?>
                <a href="<?= $url ?>" style="padding:8px 12px; margin:0 5px; border-radius:5px; text-decoration:none; <?= $activeClass ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>

    <?php elseif ($page === 'profil' || $page === 'setting'): ?>
        <div class="section-header">
            <h2><?= $menu_items[$page]['title'] ?></h2>
        </div>
        <p style="padding:20px; background:#fff; border:1px solid #ddd; border-radius:8px;">
            Halaman <strong><?= $menu_items[$page]['title'] ?></strong> sedang dalam pengembangan.
        </p>
    <?php endif; ?>
    </div>
</body>
</html>
