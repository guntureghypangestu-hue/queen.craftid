<?php
require_once '../includes/auth.php';
require_once '../../includes/db.php';

// Cek apakah ada parameter id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

 $id = (int) $_GET['id'];

// Cek apakah pesanan ada
 $stmt = $pdo->prepare("SELECT id FROM orders WHERE id = ?");
 $stmt->execute([$id]);
 $order = $stmt->fetch();

if (!$order) {
    $_SESSION['error'] = "Pesanan tidak ditemukan.";
    header("Location: index.php");
    exit;
}

// Hapus pesanan
 $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
 $deleted = $stmt->execute([$id]);

if ($deleted) {
    $_SESSION['success'] = "Pesanan berhasil dihapus.";
} else {
    $_SESSION['error'] = "Gagal menghapus pesanan.";
}

header("Location: index.php");
exit;