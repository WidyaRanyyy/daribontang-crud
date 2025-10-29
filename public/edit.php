<?php
require '../config/database.php';

// INISIALISASI
$errors = [];
$id = $_GET['id'] ?? 0;

// AMBIL DATA
$stmt = $pdo->prepare("SELECT * FROM destinations WHERE id = ?");
$stmt->execute([$id]);
$dest = $stmt->fetch();

// CEK DATA ADA?
if (!$dest) {
    die("<div style='padding:20px; text-align:center; color:#dc2626; font-weight:600;'>Destinasi tidak ditemukan atau ID tidak valid.</div>");
}

// PROSES UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $ticket_price = $_POST['ticket_price'] ?? '';

    // Validasi
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
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-container">
            <h2 class="logo">Destinasi Wisata</h2>
            <ul class="nav-links">
                <li><a href="index.php">Beranda</a></li>
                <li><a href="create.php">Tambah Data</a></li>
                <li><a href="about.php">Tentang</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="form-container">
            <h1 class="form-title">Edit Destinasi</h1>
            <a href="detail.php?id=<?= $id ?>" class="back-link">← Kembali ke Detail</a>

            <!-- ERROR -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- FORM -->
            <form method="POST" class="form-grid">
                <div class="form-group">
                    <label>Nama Destinasi</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($dest['name'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label>Lokasi</label>
                    <input type="text" name="location" value="<?= htmlspecialchars($dest['location'] ?? '') ?>" required>
                </div>

                <div class="form-group full-width">
                    <label>Deskripsi</label>
                    <textarea name="description" required><?= htmlspecialchars($dest['description'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label>Harga Tiket (Rp)</label>
                    <input type="number" name="ticket_price" value="<?= $dest['ticket_price'] ?? 0 ?>" min="0" required>
                </div>

                <button type="submit" class="btn-submit">Update Destinasi</button>
            </form>
        </div>
    </div>

    <footer class="footer">
        <p>2409106011 Widya Ayu Anggraini</p>
    </footer>
</body>
</html>