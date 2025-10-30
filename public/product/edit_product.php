<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$product = null;
$errors = [];

try {
    if (!$id) {
        throw new Exception("ID produk tidak valid.");
    }
    
    // 1. Ambil data lama (prefill data - Wajib)
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if (!$product) {
        throw new Exception("Produk tidak ditemukan.");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 2. Proses Update dengan Sanitasi dan Validasi (Sama seperti CREATE)
        $name = htmlspecialchars(trim($_POST['name']));
        $category = htmlspecialchars(trim($_POST['category']));
        $description = htmlspecialchars(trim($_POST['description']));
        $stock = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT); 
        $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT); 

        // Validasi Sisi Server
        if (empty($name)) { $errors[] = "Nama produk wajib diisi."; }
        if (empty($category)) { $errors[] = "Kategori wajib diisi."; }
        if ($stock === false || $stock < 0) { $errors[] = "Stok harus berupa angka positif."; }
        if ($price === false || $price < 0) { $errors[] = "Harga harus berupa angka positif."; }

        if (empty($errors)) {
            // 3. Prepared Statements untuk UPDATE (Wajib)
            $sql = "UPDATE products SET name = :name, category = :category, description = :description, stock = :stock, price = :price WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'name' => $name, 
                'category' => $category, 
                'description' => $description,
                'stock' => $stock, 
                'price' => $price,
                'id' => $id
            ]);

            $_SESSION['success_message'] = "✅ Produk **" . $name . "** berhasil diperbarui!";
            header("Location: ../dashboard.php?page=produk");
            exit();
        }
        
        // Jika ada error, update variabel $product untuk prefill form
        // Catatan: Logic ini penting agar input yang sudah diisi tidak hilang saat error validasi.
        $product = [
            'id' => $id,
            'name' => $name, 
            'category' => $category, 
            'description' => $description,
            'stock' => $_POST['stock'] ?? $product['stock'],
            'price' => $_POST['price'] ?? $product['price'],
        ];

    }

} catch (Exception $e) {
    error_log("Product Edit Error: " . $e->getMessage());
    $_SESSION['error_message'] = $e->getMessage() === 'Produk tidak ditemukan.' ? 'Produk tidak ditemukan.' : "Gagal memproses data.";
    header("Location: ../dashboard.php?page=produk");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - Dari Bontang</title>
    <!-- CSS Eksternal (untuk button style) -->
    <link rel="stylesheet" href="../../style.css">
    <style>
        /* CSS Konsisten dari add_product.php dan detail_product.php */
        body {
            background: none; 
            min-height: 100vh;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        #myVideo {
            position: fixed;
            right: 0;
            bottom: 0;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            z-index: -1;
            object-fit: cover;
            filter: brightness(40%);
        }

        .page-container {
            max-width: 800px;
            margin: auto;
            z-index: 10; 
            position: relative;
            animation: slideInUp 0.6s ease;
            width: 90%;
        }
        
        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .page-header {
            background: linear-gradient(135deg, #841751, #db2796);
            color: white;
            padding: 30px;
            border-radius: 15px 15px 0 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            text-align: center;
        }
        
        .page-header h2 {
            margin: 0;
            font-size: 2rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .page-header .icon {
            font-size: 3rem;
            margin-bottom: 10px;
            display: block;
        }
        
        .content-area {
            background: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(5px); 
            padding: 40px;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
            color: #333;
        }
        
        /* Form Styling */
        .form-group {
             margin-bottom: 15px; /* Menghapus style inline di PHP */
        }
        .form-group label {
            display: block; /* Memastikan label di baris baru */
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
        }
        
        .content-area input[type="text"],
        .content-area input[type="number"],
        .content-area textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .content-area input:focus,
        .content-area textarea:focus {
            outline: none;
            border-color: #841751; 
            box-shadow: 0 0 0 3px rgba(132, 23, 81, 0.1);
        }

        /* Button Styling */
        .btn-primary {
             width: 100%;
             padding: 16px;
             background: linear-gradient(135deg, #841751, #db2796);
             color: white;
             border: none;
             border-radius: 10px;
             font-size: 1.1rem;
             font-weight: 600;
             cursor: pointer;
             transition: all 0.3s ease;
             box-shadow: 0 4px 15px rgba(132,23,81,0.3);
             display: flex;
             align-items: center;
             justify-content: center;
             gap: 10px;
             margin-top: 10px; /* Tambahkan sedikit margin dari input terakhir */
         }
        .btn-primary:hover {
            background: linear-gradient(135deg, #6b1242, #b92080);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(132, 23, 81, 0.4);
        }
        .btn-secondary {
            display: block; 
            width: 100%; 
            text-align: center;
            margin-top: 15px;
            padding: 16px; 
            border-radius: 10px;
            text-decoration: none;
            background: #6c757d; 
            color: white;
            font-size: 1.1rem; 
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(108,117,125,0.3); 
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(108,117,125,0.4);
        }
        
        .btn-secondary::before {
             content: '← '; 
        }
        
        /* Responsiveness */
        @media (max-width: 768px) {
            .page-container {
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <!-- ELEMEN VIDEO BACKGROUND -->
    <video autoplay muted loop id="myVideo">
        <source src="../../foto/back.mp4" type="video/mp4"> 
    </video>

    <div class="page-container">
        <div class="page-header">
            <span class="icon">✏️</span>
            <h2>Edit Produk: <?php echo htmlspecialchars($product['name']); ?></h2>
        </div>
        
        <div class="content-area">
            <div class="profile-card">
                <?php if (!empty($errors)): ?>
                    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #f44336;">
                        <strong>⚠️ Terjadi Kesalahan:</strong>
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="edit_product.php?id=<?php echo $id; ?>">
                    <div class="form-group">
                        <label for="name">Nama Produk</label>
                        <input type="text" name="name" id="name" required value="<?php echo htmlspecialchars($product['name']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Kategori</label>
                        <input type="text" name="category" id="category" required value="<?php echo htmlspecialchars($product['category']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea name="description" id="description" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="stock">Stok</label>
                        <input type="number" name="stock" id="stock" required min="0" value="<?php echo htmlspecialchars($product['stock']); ?>">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label for="price">Harga (Rp)</label>
                        <input type="number" name="price" id="price" required min="0" step="100" value="<?php echo htmlspecialchars($product['price']); ?>">
                    </div>
                    
                    <button type="submit" class="btn-primary">Simpan Perubahan</button>
                    <a href="../dashboard.php?page=produk" class="btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
