<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Cek apakah parameter 'slug' ada di URL
if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    // Jika tidak ada, redirect ke halaman utama
    header('Location: index.php');
    exit;
}

 $category_slug = $_GET['slug'];

// Ambil data kategori berdasarkan slug
 $stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ?");
 $stmt->execute([$category_slug]);
 $category = $stmt->fetch(PDO::FETCH_ASSOC);

// Jika kategori tidak ditemukan
if (!$category) {
    echo "<h1 style='text-align:center; margin-top: 50px;'>Kategori tidak ditemukan.</h1>";
    echo "<p style='text-align:center;'><a href='index.php'>Kembali ke Beranda</a></p>";
    exit;
}

// Ambil semua produk yang termasuk dalam kategori ini
 $stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND status IN ('active', 'out_of_stock') ORDER BY created_at DESC");
 $stmt->execute([$category['id']]);
 $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php include 'includes/header.php'; ?>

<main class="section-padding">
    <div class="container">
        <a href="javascript:history.back()" class="btn-secondary" style="margin-bottom: 20px;"><i class="fas fa-arrow-left"></i> Kembali</a>
        
        <div class="section-header">
            <h1>Koleksi: <?php echo escape($category['name']); ?></h1>
            <p><?php echo escape($category['description']); ?></p>
        </div>

        <div class="product-grid">
            <?php if (empty($products)): ?>
                <p style="width: 100%; text-align: center;">Belum ada produk di kategori ini.</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="assets/images/uploads/<?php echo escape($product['image_url']); ?>" alt="<?php echo escape($product['name']); ?>">
                            <?php if ($product['is_featured']): ?>
                                <span class="product-badge">Unggulan</span>
                            <?php endif; ?>
                            <?php if ($product['stock'] == 0 || $product['status'] === 'out_of_stock' || $product['has_tiered_pricing']): ?>
                                <span class="product-badge out-of-stock"><?php echo $product['has_tiered_pricing'] ? 'Pesan Detail' : 'Habis'; ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <h4><?php echo escape($product['name']); ?></h4>
                            <p><?php echo escape($category['name']); ?></p>
                            <div class="product-price">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></div>
                            <a href="detail.php?id=<?php echo $product['id']; ?>" class="btn-primary">Lihat Detail</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>