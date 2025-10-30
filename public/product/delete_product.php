<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    $_SESSION['error_message'] = "ID produk tidak valid untuk dihapus.";
    header("Location: dashboard.php?page=produk");
    exit();
}

try {
    // Opsional: Ambil nama produk untuk pesan sukses
    $stmtName = $pdo->prepare("SELECT name FROM products WHERE id = ?");
    $stmtName->execute([$id]);
    $productName = $stmtName->fetchColumn() ?: "ID: " . $id;

    // Prepared Statements untuk DELETE (Wajib)
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);

    // Cek apakah ada baris yang terpengaruh
    if ($stmt->rowCount() > 0) {
        $_SESSION['success_message'] = "🗑️ Produk **" . htmlspecialchars($productName) . "** berhasil dihapus.";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus produk. Mungkin produk sudah terhapus.";
    }
    
} catch (PDOException $e) {
    error_log("Product Delete Error: " . $e->getMessage());
    $_SESSION['error_message'] = "Gagal menghapus data: Kesalahan sistem.";
}

header("Location: ../dashboard.php?page=produk");
exit();
?>