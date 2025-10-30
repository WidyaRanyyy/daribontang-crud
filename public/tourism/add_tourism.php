<?php
session_start();
require_once '../../config/database.php'; 

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php"); 
    exit();
}

$errors = [];
$name = $location = $description = $open_hours = '';
$ticket_price = $rating = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $location = htmlspecialchars(trim($_POST['location']));
    $description = htmlspecialchars(trim($_POST['description']));
    $open_hours = htmlspecialchars(trim($_POST['open_hours']));
    
    $ticket_price = filter_input(INPUT_POST, 'ticket_price', FILTER_VALIDATE_FLOAT);
    $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_FLOAT);

    if (empty($name)) $errors[] = "Nama wisata wajib diisi.";
    if (empty($location)) $errors[] = "Lokasi wajib diisi.";
    if ($ticket_price === false || $ticket_price < 0) $errors[] = "Harga tiket harus angka positif.";
    if ($rating === false || $rating < 0 || $rating > 5) $errors[] = "Rating harus antara 0 - 5.";

    if (empty($errors)) {
        try {
            $sql = "INSERT INTO tourism (name, location, description, ticket_price, open_hours, rating) 
                    VALUES (:name, :location, :description, :ticket_price, :open_hours, :rating)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'name' => $name,
                'location' => $location,
                'description' => $description,
                'ticket_price' => $ticket_price,
                'open_hours' => $open_hours,
                'rating' => $rating
            ]);

            $_SESSION['success_message'] = "Wisata **" . $name . "** berhasil ditambahkan!";
            header("Location: ../dashboard.php?page=wisata");
            exit();

        } catch (PDOException $e) {
            error_log("Tourism Create Error: " . $e->getMessage());
            $errors[] = "Gagal menyimpan data. Coba lagi.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Wisata - Dari Bontang</title>
    <link rel="stylesheet" href="../../style.css">
    <style>
        body { background: none; min-height: 100vh; padding: 20px; display: flex; justify-content: center; align-items: center; position: relative; }
        #myVideo { position: fixed; right: 0; bottom: 0; min-width: 100%; min-height: 100%; z-index: -1; object-fit: cover; filter: brightness(40%); }
        .page-container { max-width: 800px; margin: auto; z-index: 10; position: relative; animation: slideInUp 0.6s ease; width: 90%; }
        @keyframes slideInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        .page-header { background: linear-gradient(135deg, #841751, #db2796); color: white; padding: 30px; border-radius: 15px 15px 0 0; box-shadow: 0 4px 12px rgba(0,0,0,0.2); text-align: center; }
        .page-header h2 { margin: 0; font-size: 2rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
        .page-header .icon { font-size: 3rem; margin-bottom: 10px; display: block; }
        .content-area { background: rgba(255,255,255,0.95); backdrop-filter: blur(5px); padding: 40px; border-radius: 0 0 15px 15px; box-shadow: 0 8px 32px rgba(0,0,0,0.2); }
        .btn-primary, .btn-secondary { width: 100%; padding: 16px; border-radius: 10px; font-size: 1.1rem; font-weight: 600; text-align: center; text-decoration: none; transition: all 0.3s ease; margin-top: 10px; }
        .btn-primary { background: linear-gradient(135deg, #841751, #db2796); color: white; box-shadow: 0 4px 15px rgba(132,23,81,0.3); }
        .btn-primary:hover { background: linear-gradient(135deg, #6b1242, #b92080); transform: translateY(-2px); }
        .btn-secondary { background: #6c757d; color: white; box-shadow: 0 4px 15px rgba(108,117,125,0.3); }
        .btn-secondary:hover { background: #5a6268; transform: translateY(-2px); }
        .btn-secondary::before { content: '← '; }
        @media (max-width: 600px) { .content-area { padding: 20px; } }
    </style>
</head>
<body>
    <video autoplay muted loop id="myVideo">
        <source src="../../foto/back.mp4" type="video/mp4">
    </video>

    <div class="page-container">
        <div class="page-header">
            <span class="icon">Tiket</span>
            <h2>Tambah Wisata Baru</h2>
        </div>
        
        <div class="content-area">
            <?php if (!empty($errors)): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                    <strong>Kesalahan:</strong>
                    <ul><?php foreach ($errors as $e) echo "<li>$e</li>"; ?></ul>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Nama Wisata</label>
                    <input type="text" name="name" required value="<?= htmlspecialchars($name) ?>" style="width:100%;padding:10px;">
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Lokasi</label>
                    <input type="text" name="location" required value="<?= htmlspecialchars($location) ?>" style="width:100%;padding:10px;">
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Deskripsi</label>
                    <textarea name="description" rows="4" style="width:100%;padding:10px;"><?= htmlspecialchars($description) ?></textarea>
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Jam Buka</label>
                    <input type="text" name="open_hours" placeholder="Contoh: 08:00 - 17:00" value="<?= htmlspecialchars($open_hours) ?>" style="width:100%;padding:10px;">
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Harga Tiket (Rp)</label>
                    <input type="number" name="ticket_price" min="0" step="100" value="<?= $ticket_price ?>" style="width:100%;padding:10px;">
                </div>
                <div class="form-group" style="margin-bottom: 25px;">
                    <label>Rating (0-5)</label>
                    <input type="number" name="rating" min="0" max="5" step="0.1" value="<?= $rating ?>" style="width:100%;padding:10px;">
                </div>
                
                <button type="submit" class="btn-primary">Simpan Wisata</button>
                <a href="../dashboard.php?page=wisata" class="btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</body>
</html>