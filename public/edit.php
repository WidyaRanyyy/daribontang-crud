<?php
require '../config/database.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM destinations WHERE id = ?");
$stmt->execute([$id]);
$dest = $stmt->fetch();

if (!$dest) die("Data tidak ditemukan.");

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $ticket_price = $_POST['ticket_price'] ?? '';

    if (empty($name)) $errors[] = "Nama wajib diisi.";
    if (empty($location)) $errors[] = "Lokasi wajib diisi.";
    if (empty($description)) $errors[] = "Deskripsi wajib diisi.";
    if (!is_numeric($ticket_price) || $ticket_price < 0) $errors[] = "Harga harus valid.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE destinations SET name = ?, location = ?, description = ?, ticket_price = ? WHERE id = ?");
        $stmt->execute([$name, $location, $description, $ticket_price, $id]);
        header("Location: index.php?msg=Destinasi berhasil diperbarui&status=success");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Destinasi</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Edit Destinasi</h1>
        </div>
        <a href="detail.php?id=<?= $id ?>" class="back-link">← Kembali ke Detail</a>

        <?php if ($errors): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" class="form">
            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="name" value="<?= htmlspecialchars($dest['name']) ?>" required>
            </div>

            <div class="form-group">
                <label>Lokasi</label>
                <input type="text" name="location" value="<?= htmlspecialchars($dest['location']) ?>" required>
            </div>

            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="description" required><?= htmlspecialchars($dest['description']) ?></textarea>
            </div>

            <div class="form-group">
                <label>Harga Tiket</label>
                <input type="number" name="ticket_price" value="<?= $dest['ticket_price'] ?>" min="0" required>
            </div>

            <button type="submit" class="btn btn-submit">Update</button>
        </form>
    </div>
</body>
</html>