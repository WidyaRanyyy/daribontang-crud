<?php
session_start();
require_once '../../config/database.php'; // Path ke config

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$product = null;

if ($id) {
    try {
        // Prepared Statements untuk Read Detail (Wajib)
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Product Detail Error: " . $e->getMessage());
    }
}

if (!$product) {
    $_SESSION['error_message'] = "Produk detail tidak ditemukan.";
    header("Location: ../dashboard.php?page=produk");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk - Dari Bontang</title>
    <!-- CSS Eksternal (untuk button style) -->
    <link rel="stylesheet" href="../../style.css">
    <style>
        /* CSS Konsisten dari add_product.php */
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

        /* Detail Card Styling */
        .profile-card {
            max-width: 100%;
            margin: 0;
        }
        
        .profile-info {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px dashed #ddd;
        }

        .info-item strong {
            color: #841751;
            font-weight: 700;
        }

        .info-item span, .profile-info p {
            color: #555;
            text-align: right;
            max-width: 70%;
        }

        .info-item:last-child {
            border-bottom: none;
        }
        
        /* Button Style Consistency */
        .btn-primary, .btn-secondary {
             width: 100%;
             padding: 16px;
             border-radius: 10px;
             font-size: 1.1rem;
             font-weight: 600;
             transition: all 0.3s ease;
             text-align: center;
             display: block;
             text-decoration: none;
        }
        .btn-primary { 
            background: linear-gradient(135deg, #841751, #db2796);
            color: white;
            margin-top: 20px;
            box-shadow: 0 4px 15px rgba(132,23,81,0.3);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #6b1242, #b92080);
            transform: translateY(-2px);
        }
        .btn-secondary {
            background: #6c757d; 
            color: white;
            margin-top: 10px;
            box-shadow: 0 4px 15px rgba(108,117,125,0.3);
        }
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        .btn-secondary::before {
             content: '← '; 
        }

        @media (max-width: 600px) {
            .info-item {
                flex-direction: column;
                align-items: flex-start;
                text-align: left;
            }
            .info-item span, .profile-info p {
                text-align: left;
                width: 100%;
                margin-top: 5px;
            }
            .content-area {
                padding: 20px;
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
            <span class="icon">🔍</span>
            <h2>Detail Produk: <?php echo htmlspecialchars($product['name']); ?></h2>
        </div>
        
        <div class="content-area">
            <div class="profile-card">
                <div class="profile-info">
                    <div class="info-item"><strong>ID Produk (Kunci):</strong><span><?php echo htmlspecialchars($product['id']); ?></span></div>
                    <div class="info-item"><strong>Nama:</strong><span><?php echo htmlspecialchars($product['name']); ?></span></div>
                    <div class="info-item"><strong>Kategori:</strong><span><?php echo htmlspecialchars($product['category']); ?></span></div>
                    <div class="info-item"><strong>Stok:</strong><span><?php echo htmlspecialchars($product['stock']); ?></span></div>
                    <div class="info-item"><strong>Harga:</strong><span>Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></span></div>
                    <div class="info-item"><strong>Dibuat Pada:</strong><span><?php echo date('d-m-Y H:i:s', strtotime($product['created_at'])); ?></span></div>
                    <div class="info-item"><strong>Diperbarui:</strong><span><?php echo date('d-m-Y H:i:s', strtotime($product['updated_at'])); ?></span></div>
                    <div class="info-item" style="flex-direction: column; align-items: flex-start;">
                        <strong>Deskripsi:</strong>
                        <p style="margin-top: 10px; text-align: left; max-width: 100%;"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    </div>
                </div>
                <div style="margin-top: 20px;">
                    <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn-primary">Edit Produk</a>
                    <a href="../dashboard.php?page=produk" class="btn-secondary">Kembali ke Daftar</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
