<?php
require_once 'includes/db.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'ID produk diperlukan']);
    exit;
}

$product_id = $_GET['id'];

$stmt = $pdo->prepare("SELECT status, stock, has_tiered_pricing FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo json_encode(['error' => 'Produk tidak ditemukan']);
    exit;
}

$is_out_of_stock = ($product['stock'] == 0 || $product['status'] === 'out_of_stock');

echo json_encode([
    'status' => $product['status'],
    'stock' => $product['stock'],
    'is_out_of_stock' => $is_out_of_stock,
    'has_tiered_pricing' => (bool)$product['has_tiered_pricing']
]);
?>