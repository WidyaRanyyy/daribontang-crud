<?php
require '../config/database.php';

$limit = 5;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Hitung total
$countStmt = $pdo->query("SELECT COUNT(*) FROM destinations");
$total = $countStmt->fetchColumn();
$pages = max(1, ceil($total / $limit));

// Ambil data
$stmt = $pdo->prepare("SELECT * FROM destinations ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
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
    <div class="container">
        <div class="header">
            <h1>Destinasi Wisata</h1>
            <p>Kelola destinasi favorit Anda dengan mudah</p>
        </div>

        <a href="create.php" class="btn btn-primary">+ Tambah Destinasi</a>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-<?= $_GET['status'] ?? 'success' ?>">
                <?= htmlspecialchars($_GET['msg']) ?>
            </div>
        <?php endif; ?>

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
                    <tr><td colspan="5" class="no-data">Tidak ada data destinasi.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>">&laquo; Sebelumnya</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <a href="?page=<?= $i ?>" <?= $i === $page ? 'class="active"' : '' ?>><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($page < $pages): ?>
                <a href="?page=<?= $page + 1 ?>">Berikutnya &raquo;</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>