<?php
// --- LANGKAH 1: HANYA INCLUDE FILE LOGIKA (TANPA OUTPUT) ---
require_once '../includes/auth.php';
require_once '../../includes/db.php';

// --- LANGKAH 2: AMBIL DATA PRODUK UNTUK FORM ---
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}
 $product_id = $_GET['id'];

 $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
 $stmt->execute([$product_id]);
 $product = $stmt->fetch(PDO::FETCH_ASSOC);

 $categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: index.php');
    exit;
}


// --- LANGKAH 3: PROSES FORM JIKA DI-POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // --- Proses Upload Gambar ---
    $image_url = $product['image_url']; 
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_dir = '../../assets/images/uploads/';
        $file_name = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            if ($product['image_url'] !== 'default.jpg' && file_exists($upload_dir . $product['image_url'])) {
                unlink($upload_dir . $product['image_url']);
            }
            $image_url = $file_name;
        }
    }

    // --- Ambil Data Form Lainnya ---
    $name = $_POST['name'];
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $stock = (int)$_POST['stock'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $has_tiered_pricing = isset($_POST['has_tiered_pricing']) ? 1 : 0;

    // --- STATUS OTOMATIS BERDASARKAN STOK ---
    // Stok lebih dari 0 = aktif, Stok 0 = habis
    $status = ($stock > 0) ? 'active' : 'out_of_stock';

    // --- Siapkan Array untuk Query ---
    $toNull = function($value) {
        return ($value === '' || $value === null) ? null : $value;
    };

    $execute_values = [
        $category_id, $name, $slug, $description, $price, $image_url, $stock, $is_featured,
        $status,
        $has_tiered_pricing
    ];

    for ($i = 1; $i <= 6; $i++) {
        $execute_values[] = $toNull($_POST["sheet_{$i}_min"]);
        $execute_values[] = $toNull($_POST["sheet_{$i}_max"]);
        $execute_values[] = $toNull($_POST["price_{$i}"]);
    }

    $execute_values[] = $product_id;

    // --- Eksekusi Query UPDATE ---
    $stmt = $pdo->prepare("
        UPDATE products SET 
            category_id = ?, name = ?, slug = ?, description = ?, price = ?, image_url = ?, stock = ?, is_featured = ?, status = ?,
            has_tiered_pricing = ?, 
            sheet_1_min = ?, sheet_1_max = ?, price_1 = ?, 
            sheet_2_min = ?, sheet_2_max = ?, price_2 = ?, 
            sheet_3_min = ?, sheet_3_max = ?, price_3 = ?,
            sheet_4_min = ?, sheet_4_max = ?, price_4 = ?,
            sheet_5_min = ?, sheet_5_max = ?, price_5 = ?,
            sheet_6_min = ?, sheet_6_max = ?, price_6 = ?
        WHERE id = ?
    ");
    
    $stmt->execute($execute_values);
    
    header('Location: index.php?status=updated');
    exit;
}

// --- LANGKAH 5: JIKA BUKAN POST (MENAMPILKAN HALAMAN FORM) ---
require_once '../includes/admin-header.php';
?>

<!-- ============================================================= -->
<!--                BAGIAN HTML (TAMPILAN)                       -->
<!-- ============================================================= -->
<div class="bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Produk: <?php echo htmlspecialchars($product['name']); ?></h1>
    
    <form action="edit.php?id=<?php echo $product_id; ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nama Produk</label>
                <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($product['name']); ?>" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
            </div>
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori</label>
                <select name="category_id" id="category_id" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($product['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700">Harga Standar</label>
                <input type="number" name="price" id="price" value="<?php echo $product['price']; ?>" step="0.01" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                <p class="text-xs text-gray-500 mt-1">Harga ini akan digunakan jika harga bertingkat tidak aktif.</p>
            </div>
            <div>
                <label for="stock" class="block text-sm font-medium text-gray-700">Stok</label>
                <input type="number" name="stock" id="stock" value="<?php echo $product['stock']; ?>" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
            </div>
        </div>
        
        <!-- INFO STATUS OTOMATIS -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm text-blue-700">
                    <strong>Status Produk Otomatis:</strong> Stok lebih dari 0 = <span class="font-semibold text-green-600">Aktif</span>, Stok 0 = <span class="font-semibold text-red-600">Habis</span>
                </p>
            </div>
            <p class="text-xs text-blue-600 mt-2 ml-7">
                Status saat ini: 
                <?php if ($product['status'] === 'active'): ?>
                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">Aktif</span>
                <?php elseif ($product['status'] === 'out_of_stock'): ?>
                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">Habis</span>
                <?php else: ?>
                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800"><?php echo htmlspecialchars($product['status']); ?></span>
                <?php endif; ?>
                (akan diperbarui otomatis saat disimpan)
            </p>
        </div>
        
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
            <textarea name="description" id="description" rows="4" required
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm"><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>

        <div>
            <label for="image" class="block text-sm font-medium text-gray-700">Gambar Produk</label>
            <p class="text-xs text-gray-500 mb-2">Kosongkan jika tidak ingin mengubah gambar.</p>
            <input type="file" name="image" id="image" accept="image/*"
                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-pink-700">
            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. Maksimal 2MB.</p>
            <img src="../../assets/images/uploads/<?php echo htmlspecialchars($product['image_url']); ?>" alt="Current Image" class="mt-4 h-32 w-32 object-cover rounded-md">
        </div>

        <div class="flex items-center">
            <input type="checkbox" name="is_featured" id="is_featured" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded" <?php echo ($product['is_featured']) ? 'checked' : ''; ?>>
            <label for="is_featured" class="ml-2 block text-sm text-gray-900">Tandai sebagai Produk Unggulan</label>
        </div>

        <div class="border-t pt-6">
            <div class="flex items-center">
                <input type="checkbox" name="has_tiered_pricing" id="has_tiered_pricing" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded" <?php echo ($product['has_tiered_pricing']) ? 'checked' : ''; ?>>
                <label for="has_tiered_pricing" class="ml-2 block text-sm font-medium text-gray-900">Aktifkan Harga Bertingkat (Untuk Buket Uang)</label>
            </div>
            
            <div id="tiered-pricing-fields" class="mt-6 space-y-6" style="display: <?php echo ($product['has_tiered_pricing']) ? 'block' : 'none'; ?>;">
                <p class="text-sm text-gray-600">Isi detail harga untuk setiap tingkatan jumlah lembar. Kosongkan jika tidak digunakan.</p>
                
                <?php for ($i = 1; $i <= 6; $i++): ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 border rounded-lg">
                    <h4 class="md:col-span-3 font-semibold text-gray-800">Tingkat <?php echo $i; ?></h4>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Lembar (Min)</label>
                        <input type="number" name="sheet_<?php echo $i; ?>_min" 
                               value="<?php echo !is_null($product["sheet_{$i}_min"]) ? htmlspecialchars($product["sheet_{$i}_min"]) : ''; ?>" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Lembar (Max)</label>
                        <input type="number" name="sheet_<?php echo $i; ?>_max" 
                               value="<?php echo !is_null($product["sheet_{$i}_max"]) ? htmlspecialchars($product["sheet_{$i}_max"]) : ''; ?>" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Harga (Rp)</label>
                        <input type="number" name="price_<?php echo $i; ?>" step="0.01" 
                               value="<?php echo !is_null($product["price_{$i}"]) ? htmlspecialchars($product["price_{$i}"]) : ''; ?>" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>
        
        <div class="flex items-center justify-end">
            <a href="index.php" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg mr-2 transition-colors">Batal</a>
            <button type="submit" class="bg-primary hover:bg-pink-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">Perbarui Produk</button>
        </div>
    </form>
</div>

<!-- Script untuk Toggle Harga Bertingkat -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const hasTieredPricingCheckbox = document.getElementById('has_tiered_pricing');
    const tieredPricingFields = document.getElementById('tiered-pricing-fields');

    if (hasTieredPricingCheckbox && tieredPricingFields) {
        function toggleTieredFields() {
            tieredPricingFields.style.display = hasTieredPricingCheckbox.checked ? 'block' : 'none';
        }
        toggleTieredFields();
        hasTieredPricingCheckbox.addEventListener('change', toggleTieredFields);
    }
});
</script>

<?php
require_once '../includes/admin-footer.php';
?>