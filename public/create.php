<?php
require '../config/database.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $ticket_price = $_POST['ticket_price'] ?? '';

    if (empty($name)) $errors[] = "Nama destinasi wajib diisi.";
    if (empty($location)) $errors[] = "Lokasi wajib diisi.";
    if (empty($description)) $errors[] = "Deskripsi wajib diisi.";
    if (!is_numeric($ticket_price) || $ticket_price < 0) $errors[] = "Harga tiket harus angka positif.";

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO destinations (name, location, description, ticket_price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $location, $description, $ticket_price]);
            $success = true;
        } catch (Exception $e) {
            $errors[] = "Gagal menyimpan: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Destinasi</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Tambah Destinasi Wisata</h1>
        </div>
        <a href="index.php" class="back-link">← Kembali ke Daftar</a>

        <?php if ($success): ?>
            <div class="alert alert-success">Destinasi berhasil ditambahkan!</div>
            <script>setTimeout(() => location.href='index.php', 1500);</script>
        <?php endif; ?>

        <?php if ($errors): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?= $e ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" class="form">
            <div class="form-group">
                <label>Nama Destinasi</label>
                <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label>Lokasi</label>
                <input type="text" name="location" value="<?= htmlspecialchars($_POST['location'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="description" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>Harga Tiket (Rp)</label>
                <input type="number" name="ticket_price" min="0" step="500" value="<?= htmlspecialchars($_POST['ticket_price'] ?? '0') ?>" required>
            </div>

            <button type="submit" class="btn btn-submit">Simpan</button>
        </form>
    </div>
</body>
</html>