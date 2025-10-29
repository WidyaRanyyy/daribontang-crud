<?php
require '../config/database.php';

$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$search = trim($_GET['search'] ?? '');
$where = '';
$params = [];

// Hanya tambahkan WHERE jika ada pencarian
if ($search !== '') {
    $where = "WHERE name LIKE ? OR location LIKE ?";
    $searchParam = '%' . $search . '%';
}

// 1. HITUNG TOTAL DATA
$countSql = "SELECT COUNT(*) FROM destinations $where";
$countStmt = $pdo->prepare($countSql);

// Bind parameter hanya jika ada
if ($search !== '') {
    $countStmt->execute([$searchParam, $searchParam]);
} else {
    $countStmt->execute();
}
$total = $countStmt->fetchColumn();
$pages = ceil($total / $limit);

// 2. AMBIL DATA
$dataSql = "SELECT * FROM destinations $where ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $pdo->prepare($dataSql);

// Bind parameters berdasarkan kondisi
if ($search !== '') {
    $stmt->execute([$searchParam, $searchParam, $limit, $offset]);
} else {
    $stmt->execute([$limit, $offset]);
}

$destinations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destinasi Wisata</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-container">
            <h2 class="logo">Destinasi Wisata</h2>
            <ul class="nav-links">
                <li><a href="index.php" class="active">Beranda</a></li>
                <li><a href="create.php">Tambah Data</a></li>
                <li><a href="about.php">Tentang</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="header">
            <h1>Destinasi Wisata</h1>
            <p>Kelola destinasi favorit Anda dengan mudah</p>
        </div>

        <a href="create.php" class="btn btn-primary">+ Tambah Destinasi</a>

        <!-- Search Form -->
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Cari nama atau lokasi..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Cari</button>
        </form>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-<?= $_GET['status'] ?? 'success' ?>">
                <?= htmlspecialchars($_GET['msg']) ?>
            </div>
        <?php endif; ?>

        <?php if ($search !== ''): ?>
            <p class="search-result">
                Menampilkan hasil untuk: <strong>"<?= htmlspecialchars($search) ?>"</strong> 
                (<?= $total ?> ditemukan)
            </p>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Lokasi</th>
                        <th>Harga Tiket</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($destinations) > 0): ?>
                        <?php foreach ($destinations as $i => $d): ?>
                            <tr>
                                <td><?= $offset + $i + 1 ?></td>
                                <td><?= htmlspecialchars($d['name']) ?></td>
                                <td><?= htmlspecialchars($d['location']) ?></td>
                                <td>Rp <?= number_format($d['ticket_price'], 0, ',', '.') ?></td>
                                <td>
                                    <a href="detail.php?id=<?= $d['id'] ?>" class="btn btn-detail">Detail</a>
                                    <a href="edit.php?id=<?= $d['id'] ?>" class="btn btn-edit">Edit</a>
                                    <a href="delete.php?id=<?= $d['id'] ?>" class="btn btn-delete" onclick="return confirm('Yakin hapus <?= addslashes(htmlspecialchars($d['name'])) ?>?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="no-data">
                            <?= $search !== '' ? 'Tidak ada hasil pencarian.' : 'Tidak ada data.' ?>
                        </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>">&laquo; Sebelumnya</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" <?= $i == $page ? 'class="active"' : '' ?>><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($page < $pages): ?>
                <a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>">Berikutnya &raquo;</a>
            <?php endif; ?>
        </div>
    </div>

    <footer class="footer">
        <p>2409106011 Widya Ayu Anggraini</p>
    </footer>
</body>
</html>
