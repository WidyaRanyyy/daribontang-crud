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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $origin = trim($_POST['origin']);
    $description = trim($_POST['description']);
    $price_range = trim($_POST['price_range']);
    $best_place = trim($_POST['best_place']);
    $rating = (float)$_POST['rating'];

    if ($name && $rating >= 0 && $rating <= 5) {
        try {
            $stmt = $pdo->prepare("UPDATE foods SET name=?, origin=?, description=?, price_range=?, best_place=?, rating=? WHERE id=?");
            $stmt->execute([$name, $origin, $description, $price_range, $best_place, $rating, $id]);
            $_SESSION['success_message'] = "Makanan berhasil diperbarui.";
            header("Location: ../dashboard.php?page=makanan");
            exit();
        } catch (PDOException $e) {
            $error = "Gagal memperbarui makanan.";
        }
    } else {
        $error = "Nama dan rating wajib diisi. Rating 0–5.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Makanan</title>
    <link rel="stylesheet" href="../../style.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f4f9; padding: 40px; }
        .container { max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        h2 { color: #841751; text-align: center; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; }
        input, textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; }
        textarea { height: 100px; resize: vertical; }
        .btn { padding: 12px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; margin-top: 10px; display: inline-block; text-decoration: none; }
        .btn-primary { background: linear-gradient(135deg, #841751, #db2796); color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn:hover { opacity: 0.9; }
        .back-link { display: block; margin-top: 20px; color: #841751; text-align: center; }
        .error { color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 6px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Makanan: <?= htmlspecialchars($food['name']) ?></h2>

        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nama Makanan</label>
                <input type="text" name="name" value="<?= htmlspecialchars($food['name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Asal Daerah</label>
                <input type="text" name="origin" value="<?= htmlspecialchars($food['origin']) ?>">
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="description"><?= htmlspecialchars($food['description']) ?></textarea>
            </div>
            <div class="form-group">
                <label>Harga (Rentang)</label>
                <input type="text" name="price_range" value="<?= htmlspecialchars($food['price_range']) ?>">
            </div>
            <div class="form-group">
                <label>Tempat Terbaik</label>
                <input type="text" name="best_place" value="<?= htmlspecialchars($food['best_place']) ?>">
            </div>
            <div class="form-group">
                <label>Rating (0–5)</label>
                <input type="number" name="rating" step="0.1" min="0" max="5" value="<?= $food['rating'] ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Makanan</button>
            <a href="../dashboard.php?page=makanan" class="btn btn-secondary">Batal</a>
        </form>

        <a href="../dashboard.php?page=makanan" class="back-link">Kembali</a>
    </div>
</body>
</html>