<?php
require_once 'includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Ambil data dari POST
 $name = trim($_POST['name'] ?? '');
 $phone = trim($_POST['phone'] ?? '');
 $address = trim($_POST['address'] ?? '');
 $notes = trim($_POST['notes'] ?? '');
 $cart = json_decode($_POST['cart'] ?? '[]', true);

if (empty($name) || empty($phone) || empty($address) || empty($cart)) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

// --- PERBAIKAN UTAMA: CROSS-CHECK DATA PRODUK DENGAN DATABASE ---
 $secureItems = [];
 $grandTotal = 0;

foreach ($cart as $item) {
    $productId = (int)($item['id'] ?? 0);
    $requestedQty = (int)($item['quantity'] ?? 1);

    if ($productId === 0) {
        echo json_encode(['success' => false, 'message' => 'ID produk tidak valid di keranjang.']);
        exit;
    }

    // Ambil DATA ASLI dari database berdasarkan ID
    $stmt = $pdo->prepare("SELECT name, price, image_url, stock, status, has_tiered_pricing FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Validasi produk ada dan aktif
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Produk dengan ID ' . $productId . ' tidak ditemukan.']);
        exit;
    }

    if ($product['stock'] == 0 || $product['status'] === 'out_of_stock') {
        echo json_encode(['success' => false, 'message' => 'Produk ' . $product['name'] . ' sudah habis stok.']);
        exit;
    }

    // Validasi stok tidak melebihi
    if ($requestedQty > $product['stock']) {
        echo json_encode(['success' => false, 'message' => 'Stok ' . $product['name'] . ' hanya tersedia ' . $product['stock'] . ' unit.']);
        exit;
    }

    // Untuk tiered pricing: gunakan harga dari cart (user sudah memilih tier)
    // Untuk normal: gunakan harga dari database (untuk keamanan)
    $itemPrice = $product['has_tiered_pricing'] ? (float)($item['price'] ?? 0) : (float)$product['price'];
    
    if ($itemPrice <= 0) {
        echo json_encode(['success' => false, 'message' => 'Harga produk tidak valid.']);
        exit;
    }

    // Gunakan DATA ASLI dari database (nama & image dari DB, harga sesuai kondisi)
    $secureItems[] = [
        'id'    => $productId,
        'name'  => $product['name'],    // NAMA PASTI BENAR DARI DB
        'price' => $itemPrice,          // HARGA: dari tier jika ada, dari DB jika normal
        'image' => $product['image_url'],
        'quantity' => $requestedQty
    ];

    $grandTotal += $itemPrice * $requestedQty;
}

// Simpan order menggunakan data yang sudah divalidasi dari database
 $itemsJson = json_encode($secureItems);
 $stmt = $pdo->prepare("
    INSERT INTO orders (customer_name, customer_phone, customer_address, notes, items, total_amount, status)
    VALUES (?, ?, ?, ?, ?, ?, 'pending')
");
 $result = $stmt->execute([$name, $phone, $address, $notes, $itemsJson, $grandTotal]);

if ($result) {
    $orderId = $pdo->lastInsertId();
    echo json_encode(['success' => true, 'order_id' => $orderId]);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal menyimpan pesanan']);
}
?>