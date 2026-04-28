<?php
require_once 'includes/db.php';

// Test 1: Get all products with stock
$stmt = $pdo->query("SELECT id, name, stock FROM products LIMIT 10");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "===== PRODUCTS IN DATABASE =====\n";
foreach ($products as $p) {
    echo "ID: {$p['id']}, Name: {$p['name']}, Stock: {$p['stock']}\n";
}

// Test 2: Get specific product
echo "\n===== API TEST: Get Stock for ID 1 =====\n";
$stmt = $pdo->prepare("SELECT id, stock FROM products WHERE id = ?");
$stmt->execute([1]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) {
    echo json_encode(["1" => (int)$row['stock']]);
} else {
    echo "Product ID 1 not found";
}

// Test 3: Get stock for multiple IDs
echo "\n\n===== API TEST: Get Stocks for IDs 1,2,3 =====\n";
$ids = [1, 2, 3];
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $pdo->prepare("SELECT id, stock FROM products WHERE id IN ($placeholders)");
$stmt->execute($ids);
$stocks = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $stocks[$row['id']] = (int)$row['stock'];
}
echo json_encode($stocks);
?>
