<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: ../dashboard.php?page=makanan");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM foods WHERE id = ?");
$stmt->execute([$id]);
$food = $stmt->fetch();

if (!$food) {
    $_SESSION['error_message'] = "Makanan tidak ditemukan.";
    header("Location: ../dashboard.php?page=makanan");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail: <?= htmlspecialchars($food['name']) ?></title>
    <link rel="stylesheet" href="../../style.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f4f9; padding: 40px; }
        .container { max-width: 700px; margin: auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        h2 { color: #841751; text-align: center; margin-bottom: 20px; }
        .detail-item { margin-bottom: 15px; }
        .detail-item strong { color: #555; display: inline-block; width: 150px; }
        .rating { font-size: 1.5rem; color: #ffc107; }
        .btn { padding: 10px 15px; border-radius: 6px; text-decoration: none; margin-right: 10px; font-weight: 600; }
        .btn-edit { background: #ffc107; color: #333; }
        .btn-delete { background: #dc3545; color: white; }
        .btn-back { background: #6c757d; color: white; }
        .btn:hover { opacity: 0.9; }
        .back-link { display: block; margin-top: 20px; text-align: center; color: #841751; }
    </style>
</head>
<body>
    <div class="container">
        <h2><?= htmlspecialchars($food['name']) ?></h2>

        <div class="detail-item"><strong>Asal:</strong> <?= htmlspecialchars($food['origin'] ?: '-') ?></div>
        <div class="detail-item"><strong>Deskripsi:</strong><br> <?= nl2br(htmlspecialchars($food['description'] ?: '-')) ?></div>
        <div class="detail-item"><strong>Harga:</strong> <?= htmlspecialchars($food['price_range'] ?: '-') ?></div>
        <div class="detail-item"><strong>Tempat Terbaik:</strong> <?= htmlspecialchars($food['best_place'] ?: '-') ?></div>
        <div class="detail-item"><strong>Rating:</strong> <span class="rating">★★★★★</span> <?= $food['rating'] ?>/5</div>
        <div class="detail-item"><strong>Dibuat:</strong> <?= date('d-m-Y H:i', strtotime($food['created_at'])) ?></div>

        <hr style="margin: 25px 0; border: 1px solid #eee;">

        <a href="edit_food.php?id=<?= $food['id'] ?>" class="btn btn-edit">Edit</a>
        <a href="delete_food.php?id=<?= $food['id'] ?>" class="btn btn-delete" 
           onclick="return confirm('Yakin hapus <?= htmlspecialchars($food['name']) ?>?')">Hapus</a>
        <a href="../dashboard.php?page=makanan" class="btn btn-back">Kembali</a>
    </div>

    <a href="../dashboard.php?page=makanan" class="back-link">Kembali ke Daftar Makanan</a>
</body>
</html>