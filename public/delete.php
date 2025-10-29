<?php
require '../config/database.php';

if (!isset($_GET['id'])) {
    header("Location: index.php?msg=ID tidak valid&status=error");
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM destinations WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php?msg=Destinasi berhasil dihapus&status=success");
exit;