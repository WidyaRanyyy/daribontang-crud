<?php
session_start();
require_once '../../config/database.php'; 

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php"); 
    exit();
}

$errors = [];
$name = $category = $description = $stock = $price = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Sanitasi Input (Wajib: Hindari XSS)
    $name = htmlspecialchars(trim($_POST['name']));
    $category = htmlspecialchars(trim($_POST['category']));
    $description = htmlspecialchars(trim($_POST['description']));
    
    // Gunakan filter_input untuk validasi numerik
    $stock = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT); 
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT); 

    // 2. Validasi Sisi Server (Wajib)
    if (empty($name)) {
        $errors[] = "Nama produk wajib diisi.";
    }
    if (empty($category)) {
        $errors[] = "Kategori wajib diisi.";
    }
    if ($stock === false || $stock < 0) {
        $errors[] = "Stok harus berupa angka positif.";
    }
    if ($price === false || $price < 0) {
        $errors[] = "Harga harus berupa angka positif.";
    }

    if (empty($errors)) {
        try {
            // 3. Prepared Statements (Wajib: Hindari SQL Injection)
            $sql = "INSERT INTO products (name, category, description, stock, price) VALUES (:name, :category, :description, :stock, :price)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'name' => $name, 
                'category' => $category, 
                'description' => $description,
                'stock' => $stock,
                'price' => $price
            ]);

            $_SESSION['success_message'] = "✅ Produk **" . $name . "** berhasil ditambahkan!";
            header("Location: ../dashboard.php?page=produk"); 
            exit();

        } catch (PDOException $e) {
            error_log("Product Create Error: " . $e->getMessage());
            $errors[] = "Gagal menyimpan data ke database. Silakan coba lagi.";
        }
    }
    
    // Pre-fill form jika ada error
    $name = $_POST['name'] ?? '';
    $category = $_POST['category'] ?? '';
    $description = $_POST['description'] ?? '';
    $stock = $_POST['stock'] ?? '';
    $price = $_POST['price'] ?? '';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - Dari Bontang</title>
    <!-- CSS Eksternal (untuk button style) -->
    <link rel="stylesheet" href="../../style.css">
    <style>
        /* CSS Khusus untuk Background Video dan Layout Form */
        
        body {
            /* Menghilangkan background gradient login */
            background: none; 
            min-height: 100vh;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        /* Styling Video Background (Diambil dari index.php) */
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
            /* Pastikan form di atas video */
            z-index: 10; 
            position: relative;
            animation: slideInUp 0.6s ease;
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Menggunakan style dari login.php untuk page-header/card */
        .page-header {
            background: linear-gradient(135deg, #841751, #db2796);
            color: white;
            padding: 30px;
            border-radius: 15px 15px 0 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .page-header h2 {
            margin: 0;
            font-size: 2rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            position: relative;
            z-index: 1;
        }
        
        .page-header .icon {
            font-size: 3rem;
            margin-bottom: 10px;
            display: block;
        }
        
        .content-area {
            background: rgba(255, 255, 255, 0.95); /* Transparan sedikit agar video terlihat */
            backdrop-filter: blur(5px); /* Efek blur ringan */
            padding: 40px;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
        }
        
        /* Tambahan styling untuk form */
        .form-group label {
            color: #333;
        }
        
        /* PERBAIKAN STYLING BTN-SECONDARY */
        .btn-secondary {
            display: block; /* Agar lebarnya penuh */
            width: 100%; /* Lebar penuh */
            text-align: center;
            margin-top: 15px;
            padding: 16px; /* Sama dengan btn-primary */
            border-radius: 10px;
            text-decoration: none;
            background: #6c757d; /* Warna abu-abu */
            color: white;
            font-size: 1.1rem; /* Sama dengan btn-primary */
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(108,117,125,0.3); /* Shadow konsisten */
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(108,117,125,0.4);
        }
        
        .btn-secondary::before {
             content: '← '; /* Menggunakan tanda panah */
        }
        
        /* Gaya btn-primary (didefinisikan di style.css, tetapi di override di bawah untuk konsistensi) */
        .btn-primary {
             width: 100%;
             padding: 16px;
             /* Pastikan gradien dari style.css dimuat */
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
         }
        
        @media (max-width: 768px) {
            .page-container {
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <!-- ELEMEN VIDEO BACKGROUND (Diambil dari index.php) -->
    <video autoplay muted loop id="myVideo">
        <source src="../../foto/back.mp4" type="video/mp4"> 
        <!-- Path ke video di folder foto/ sudah disesuaikan -->
    </video>

    <div class="page-container">
        <div class="page-header">
            <span class="icon">📦</span>
            <h2>Tambah Produk Baru</h2>
        </div>
        
        <div class="content-area">
            <?php if (!empty($errors)): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                    <strong>⚠️ Terjadi Kesalahan:</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="add_product.php">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="name">Nama Produk</label>
                    <input type="text" name="name" id="name" required value="<?php echo htmlspecialchars($name); ?>" style="width: 100%; padding: 10px;">
                </div>
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="category">Kategori</label>
                    <input type="text" name="category" id="category" required value="<?php echo htmlspecialchars($category); ?>" style="width: 100%; padding: 10px;">
                </div>
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="description">Deskripsi</label>
                    <textarea name="description" id="description" rows="4" style="width: 100%; padding: 10px;"><?php echo htmlspecialchars($description); ?></textarea>
                </div>
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="stock">Stok</label>
                    <input type="number" name="stock" id="stock" required min="0" value="<?php echo htmlspecialchars($stock); ?>" style="width: 100%; padding: 10px;">
                </div>
                
                <div class="form-group" style="margin-bottom: 25px;">
                    <label for="price">Harga (Rp)</label>
                    <input type="number" name="price" id="price" required min="0" step="1" value="<?php echo htmlspecialchars($price); ?>" style="width: 100%; padding: 10px;">
                </div>
                
                <button type="submit" class="btn-primary" style="width: 100%;">Simpan Produk</button>
                <!-- Tombol Batal yang mengarah ke dashboard -->
                <a href="../dashboard.php?page=produk" class="btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</body>
</html>
