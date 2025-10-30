<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$tourism = null;

if ($id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM tourism WHERE id = ?");
        $stmt->execute([$id]);
        $tourism = $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Tourism Detail Error: " . $e->getMessage());
    }
}

if (!$tourism) {
    $_SESSION['error_message'] = "Wisata tidak ditemukan.";
    header("Location: ../dashboard.php?page=wisata");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Wisata - Dari Bontang</title>
    <link rel="stylesheet" href="../../style.css">
    <style>
        body { background: none; min-height: 100vh; padding: 20px; display: flex; justify-content: center; align-items: center; position: relative; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        #myVideo { position: fixed; right: 0; bottom: 0; min-width: 100%; min-height: 100%; z-index: -1; object-fit: cover; filter: brightness(40%); }
        .page-container { max-width: 800px; margin: auto; z-index: 10; position: relative; animation: slideInUp 0.6s ease; width: 90%; }
        @keyframes slideInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        .page-header { background: linear-gradient(135deg, #841751, #db2796); color: white; padding: 30px; border-radius: 15px 15px 0 0; box-shadow: 0 4px 12px rgba(0,0,0,0.2); text-align: center; }
        .page-header h2 { margin: 0; font-size: 2rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
        .page-header .icon { font-size: 3rem; margin-bottom: 10px; display: block; }
        .content-area { background: rgba(255,255,255,0.95); backdrop-filter: blur(5px); padding: 40px; border-radius: 0 0 15px 15px; box-shadow: 0 8px 32px rgba(0,0,0,0.2); color: #333; }
        .info-item { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px dashed #ddd; }
        .info-item strong { color: #841751; font-weight: 700; }
        .info-item span, p { color: #555; text-align: right; max-width: 70%; }
        .info-item:last-child { border-bottom: none; }
        .btn-primary, .btn-secondary { width: 100%; padding: 16px; border-radius: 10px; font-size: 1.1rem; font-weight: 600; text-align: center; text-decoration: none; transition: all 0.3s ease; margin-top: 10px; }
        .btn-primary { background: linear-gradient(135deg, #841751, #db2796); color: white; box-shadow: 0 4px 15px rgba(132,23,81,0.3); }
        .btn-primary:hover { background: linear-gradient(135deg, #6b1242, #b92080); transform: translateY(-2px); }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #5a6268; transform: translateY(-2px); }
        .btn-secondary::before { content: '← '; }
        @media (max-width: 600px) { .info-item { flex-direction: column; align-items: flex-start; } .info-item span, p { text-align: left; width: 100%; margin-top: 5px; } }
    </style>
</head>
<body>
    <video autoplay muted loop id="myVideo">
        <source src="../../foto/back.mp4" type="video/mp4">
    </video>

    <div class="page-container">
        <div class="page-header">
            <span class="icon">Pemandangan</span>
            <h2>Detail: <?= htmlspecialchars($tourism['name']) ?></h2>
        </div>
        
        <div class="content-area">
            <div class="profile-info">
                <div class="info-item"><strong>ID:</strong><span><?= $tourism['id'] ?></span></div>
                <div class="info-item"><strong>Nama Wisata:</strong><span><?= htmlspecialchars($tourism['name']) ?></span></div>
                <div class="info-item"><strong>Lokasi:</strong><span><?= htmlspecialchars($tourism['location']) ?></span></div>
                <div class="info-item"><strong>Jam Buka:</strong><span><?= htmlspecialchars($tourism['open_hours'] ?: 'Tidak tersedia') ?></span></div>
                <div class="info-item"><strong>Harga Tiket:</strong><span>Rp <?= number_format($tourism['ticket_price'], 0, ',', '.') ?></span></div>
                <div class="info-item"><strong>Rating:</strong><span><?= number_format($tourism['rating'], 1) ?> / 5.0</span></div>
                <div class="info-item"><strong>Dibuat:</strong><span><?= date('d-m-Y H:i', strtotime($tourism['created_at'])) ?></span></div>
                <div class="info-item"><strong>Diperbarui:</strong><span><?= date('d-m-Y H:i', strtotime($tourism['updated_at'])) ?></span></div>
                <div class="info-item" style="flex-direction: column; align-items: flex-start;">
                    <strong>Deskripsi:</strong>
                    <p style="margin-top:10px; text-align:left; max-width:100%;"><?= nl2br(htmlspecialchars($tourism['description'])) ?></p>
                </div>
            </div>
            <div style="margin-top:20px;">
                <a href="edit_tourism.php?id=<?= $tourism['id'] ?>" class="btn-primary">Edit Wisata</a>
                <a href="../dashboard.php?page=wisata" class="btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
</body>
</html>