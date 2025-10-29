<?php
require '../config/database.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM destinations WHERE id = ?");
$stmt->execute([$id]);
$dest = $stmt->fetch();

if (!$dest) {
    die("Destinasi tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($dest['name']) ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?= htmlspecialchars($dest['name']) ?></h1>
        </div>
        <a href="index.php" class="back-link">← Kembali ke Daftar</a>

        <div class="detail-card">
            <p><strong>Lokasi:</strong> <?= htmlspecialchars($dest['location']) ?></p>
            <p><strong>Harga Tiket:</strong> Rp <?= number_format($dest['ticket_price'], 0, ',', '.') ?></p>
            <p><strong>Deskripsi:</strong><br><?= nl2br(htmlspecialchars($dest['description'])) ?></p>
            <p class="created-at"><small>Dibuat pada: <?= $dest['created_at'] ?></small></p>
        </div>

        <a href="edit.php?id=<?= $dest['id'] ?>" class="btn btn-edit">Edit Destinasi</a>
    </div>
</body>
</html>