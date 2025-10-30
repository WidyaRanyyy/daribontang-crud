<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['username'])) {
    exit();
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM tourism WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success_message'] = "Wisata berhasil dihapus.";
    } catch (PDOException $e) {
        error_log("Delete Tourism Error: " . $e->getMessage());
        $_SESSION['error_message'] = "Gagal menghapus wisata.";
    }
}

header("Location: ../dashboard.php?page=wisata");
exit();