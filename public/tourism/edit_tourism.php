<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$tourism = null;
$errors = [];

try {
    if (!$id) throw new Exception("ID tidak valid.");

    $stmt = $pdo->prepare("SELECT * FROM tourism WHERE id = ?");
    $stmt->execute([$id]);
    $tourism = $stmt->fetch();

    if (!$tourism) throw new Exception("Wisata tidak ditemukan.");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = htmlspecialchars(trim($_POST['name']));
        $location = htmlspecialchars(trim($_POST['location']));
        $description = htmlspecialchars(trim($_POST['description']));
        $open_hours = htmlspecialchars(trim($_POST['open_hours']));
        $ticket_price = filter_input(INPUT_POST, 'ticket_price', FILTER_VALIDATE_FLOAT);
        $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_FLOAT);

        if (empty($name)) $errors[] = "Nama wisata wajib diisi.";
        if (empty($location)) $errors[] = "Lokasi wajib diisi.";
        if ($ticket_price === false || $ticket_price < 0) $errors[] = "Harga tiket harus valid.";
        if ($rating === false || $rating < 0 || $rating > 5) $errors[] = "Rating harus 0-5.";

        if (empty($errors)) {
            $sql = "UPDATE tourism SET name=:name, location=:location, description=:description, 
                    open_hours=:open_hours, ticket_price=:ticket_price, rating=:rating WHERE id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'name' => $name, 'location' => $location, 'description' => $description,
                'open_hours' => $open_hours, 'ticket_price' => $ticket_price, 'rating' => $rating, 'id' => $id
            ]);

            $_SESSION['success_message'] = "Wisata **$name** berhasil diperbarui!";
            header("Location: ../dashboard.php?page=wisata");
            exit();
        }

        $tourism = array_merge($tourism, [
            'name' => $name, 'location' => $location, 'description' => $description,
            'open_hours' => $open_hours, 'ticket_price' => $ticket_price, 'rating' => $rating
        ]);
    }

} catch (Exception $e) {
    $_SESSION['error_message'] = $e->getMessage();
    header("Location: ../dashboard.php?page=wisata");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Wisata - Dari Bontang</title>
    <link rel="stylesheet" href="../../style.css">
    <style>
        /* Sama seperti add_tourism.php */
        body { background: none; min-height: 100vh; padding: 20px; display: flex; justify-content: center; align-items: center; position: relative; }
        #myVideo { position: fixed; right: 0; bottom: 0; min-width: 100%; min-height: 100%; z-index: -1; object-fit: cover; filter: brightness(40%); }
        .page-container { max-width: 800px; margin: auto; z-index: 10; position: relative; animation: slideInUp 0.6s ease; width: 90%; }
        @keyframes slideInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        .page-header { background: linear-gradient(135deg, #841751, #db2796); color: white; padding: 30px; border-radius: 15px 15px 0 0; box-shadow: 0 4px 12px rgba(0,0,0,0.2); text-align: center; }
        .page-header h2 { margin: 0; font-size: 2rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
        .page-header .icon { font-size: 3rem; margin-bottom: 10px; display: block; }
        .content-area { background: rgba(255,255,255,0.95); backdrop-filter: blur(5px); padding: 40px; border-radius: 0 0 15px 15px; box-shadow: 0 8px 32px rgba(0,0,0,0.2); }
        .form-group label { display: block; margin-bottom: 8px; color: #333; font-weight: 600; }
        .content-area input, .content-area textarea { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 1rem; }
        .content-area input:focus, .content-area textarea:focus { outline: none; border-color: #841751; }
        .btn-primary, .btn-secondary { width: 100%; padding: 16px; border-radius: 10px; font-size: 1.1rem; font-weight: 600; text-align: center; text-decoration: none; transition: all 0.3s ease; margin-top: 10px; }
        .btn-primary { background: linear-gradient(135deg, #841751, #db2796); color: white; box-shadow: 0 4px 15px rgba(132,23,81,0.3); }
        .btn-primary:hover { background: linear-gradient(135deg, #6b1242, #b92080); transform: translateY(-2px); }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #5a6268; transform: translateY(-2px); }
        .btn-secondary::before { content: '← '; }
    </style>
</head>
<body>
    <video autoplay muted loop id="myVideo">
        <source src="../../foto/back.mp4" type="video/mp4">
    </video>

    <div class="page-container">
        <div class="page-header">
            <span class="icon">Peta</span>
            <h2>Edit: <?= htmlspecialchars($tourism['name']) ?></h2>
        </div>
        
        <div class="content-area">
            <?php if (!empty($errors)): ?>
                <div style="background:#f8d7da;color:#721c24;padding:15px;border-radius:5px;margin-bottom:20px;">
                    <strong>Kesalahan:</strong>
                    <ul><?php foreach ($errors as $e) echo "<li>$e</li>"; ?></ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="edit_tourism.php?id=<?= $id ?>">
                <div class="form-group"><label>Nama Wisata</label><input type="text" name="name" required value="<?= htmlspecialchars($tourism['name']) ?>"></div>
                <div class="form-group"><label>Lokasi</label><input type="text" name="location" required value="<?= htmlspecialchars($tourism['location']) ?>"></div>
                <div class="form-group"><label>Deskripsi</label><textarea name="description" rows="4"><?= htmlspecialchars($tourism['description']) ?></textarea></div>
                <div class="form-group"><label>Jam Buka</label><input type="text" name="open_hours" value="<?= htmlspecialchars($tourism['open_hours']) ?>"></div>
                <div class="form-group"><label>Harga Tiket (Rp)</label><input type="number" name="ticket_price" min="0" step="100" value="<?= $tourism['ticket_price'] ?>"></div>
                <div class="form-group"><label>Rating (0-5)</label><input type="number" name="rating" min="0" max="5" step="0.1" value="<?= $tourism['rating'] ?>"></div>
                
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
                <a href="../dashboard.php?page=wisata" class="btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</body>
</html>