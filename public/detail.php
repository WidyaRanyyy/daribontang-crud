<?php
require '../config/database.php';

// AMBIL ID
$id = $_GET['id'] ?? 0;

// VALIDASI ID
if (!is_numeric($id) || $id <= 0) {
    die("<div style='padding:30px; text-align:center; color:#dc2626; font-weight:600; font-size:1.1rem;'>
          ID tidak valid. <a href='index.php' style='color:#3b82f6; text-decoration:underline;'>Kembali ke Beranda</a>
         </div>");
}

// AMBIL DATA
$stmt = $pdo->prepare("SELECT * FROM destinations WHERE id = ?");
$stmt->execute([$id]);
$dest = $stmt->fetch();

// CEK DATA ADA?
if (!$dest) {
    die("<div style='padding:30px; text-align:center; color:#dc2626; font-weight:600; font-size:1.1rem;'>
          Destinasi tidak ditemukan. <a href='index.php' style='color:#3b82f6; text-decoration:underline;'>Kembali ke Beranda</a>
         </div>");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($dest['name'] ?? 'Detail Destinasi') ?></title>
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
        <a href="index.php" class="back-link">← Kembali ke Daftar</a>

        <div class="detail-card">
            <h3><?= htmlspecialchars($dest['name']) ?></h3>
            <p><strong>Lokasi:</strong> <?= htmlspecialchars($dest['location']) ?></p>
            <p><strong>Harga Tiket:</strong> Rp <?= number_format($dest['ticket_price'], 0, ',', '.') ?></p>
            <p><strong>Deskripsi:</strong><br><?= nl2br(htmlspecialchars($dest['description'])) ?></p>
            <p class="created-at">Dibuat pada: <?= $dest['created_at'] ?></p>
            <a href="edit.php?id=<?= $dest['id'] ?>" class="btn-edit-card">Edit Destinasi</a>
        </div>
    </div>

    <footer class="footer">
        <p>2409106011 Widya Ayu Anggraini</p>
    </footer>
</body>
</html>