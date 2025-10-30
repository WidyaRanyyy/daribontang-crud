<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM foods WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success_message'] = "Makanan berhasil dihapus.";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Gagal menghapus makanan.";
        error_log("Delete Food Error: " . $e->getMessage());
    }
}

header("Location: ../dashboard.php?page=makanan");
exit();