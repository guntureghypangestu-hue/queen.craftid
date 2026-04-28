<?php
// --- LANGKAH 1: HANYA INCLUDE FILE LOGIKA (TANPA OUTPUT) ---
require_once '../includes/auth.php';
require_once '../../includes/db.php';

// --- LANGKAH 2: PROSES FORM JIKA DI-POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // --- Proses Upload Gambar ---
    $image_url = 'default.jpg';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_dir = '../../assets/images/uploads/';
        // Buat folder jika belum ada
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $file_name = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_url = $file_name;
        }
    }

    // --- Ambil Data Form ---
    $name = $_POST['name'];
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $stock = $_POST['stock'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $status = $_POST['status'];
    $has_tiered_pricing = isset($_POST['has_tiered_pricing']) ? 1 : 0;

    // Jika stok 0, otomatis set status ke out_of_stock
    if ($stock == 0) {
        $status = 'out_of_stock';
    }

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

    // --- Eksekusi Query ---
    $stmt = $pdo->prepare("
        INSERT INTO products (
            category_id, name, slug, description, price, image_url, stock, is_featured, status,
            has_tiered_pricing, 
            sheet_1_min, sheet_1_max, price_1, 
            sheet_2_min, sheet_2_max, price_2, 
            sheet_3_min, sheet_3_max, price_3,
            sheet_4_min, sheet_4_max, price_4,
            sheet_5_min, sheet_5_max, price_5,
            sheet_6_min, sheet_6_max, price_6
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute($execute_values);
    
    // --- LANGKAH 3: REDIRECT SEBELUM ADA OUTPUT APAPUN ---
    // Karena belum ada HTML yang dikirim, header() akan berhasil.
    header('Location: index.php?status=created');
    exit; // Hentikan eksekusi script di sini.
}

// --- LANGKAH 4: JIKA BUKAN POST (MENAMPILKAN HALAMAN FORM) ---
// Ambil data yang diperlukan untuk form
 $categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

// --- SEKARANG BARU INCLUDE FILE HEADER (MULAI OUTPUT HTML) ---
require_once '../includes/admin-header.php';
?>

<!-- ============================================================= -->
<!--                BAGIAN HTML (TAMPILAN)                       -->
<!-- ============================================================= -->
<div class="flex-1 flex flex-col overflow-hidden">
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Tambah Produk Baru</h1>
            
            <form action="create.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama Produk</label>
                        <input type="text" name="name" id="name" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                    </div>
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori</label>
                        <select name="category_id" id="category_id" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700">Harga Standar</label>
                        <input type="number" name="price" id="price" step="0.01" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                        <p class="text-xs text-gray-500 mt-1">Harga ini akan digunakan jika harga bertingkat tidak aktif.</p>
                    </div>
                    <div>
                        <label for="stock" class="block text-sm font-medium text-gray-700">Stok</label>
                        <input type="number" name="stock" id="stock" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                    </div>
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status Produk</label>
                    <select name="status" id="status" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                        <option value="active">Aktif</option>
                        <option value="inactive">Tidak Aktif (Disembunyikan)</option>
                        <option value="out_of_stock">Habis</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Produk dengan status 'Tidak Aktif' atau 'Habis' tidak akan muncul di toko.</p>
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea name="description" id="description" rows="4" required
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm"></textarea>
                </div>

                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700">Gambar Produk</label>
                    <input type="file" name="image" id="image" accept="image/*" required
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-pink-700">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. Maksimal 2MB.</p>
                    <img id="image-preview" class="mt-4 h-32 w-32 object-cover rounded-md hidden" alt="Preview Gambar">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_featured" id="is_featured" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                    <label for="is_featured" class="ml-2 block text-sm text-gray-900">Tandai sebagai Produk Unggulan</label>
                </div>

                <div class="border-t pt-6">
                    <div class="flex items-center">
                        <input type="checkbox" name="has_tiered_pricing" id="has_tiered_pricing" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                        <label for="has_tiered_pricing" class="ml-2 block text-sm font-medium text-gray-900">Aktifkan Harga Bertingkat (Untuk Buket Uang)</label>
                    </div>
                    
                    <div id="tiered-pricing-fields" class="mt-6 space-y-6" style="display: none;">
                        <p class="text-sm text-gray-600">Isi detail harga untuk setiap tingkatan jumlah lembar. Kosongkan jika tidak digunakan.</p>
                        
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 border rounded-lg">
                            <h4 class="md:col-span-3 font-semibold text-gray-800">Tingkat <?php echo $i; ?></h4>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Lembar (Min)</label>
                                <input type="number" name="sheet_<?php echo $i; ?>_min" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Lembar (Max)</label>
                                <input type="number" name="sheet_<?php echo $i; ?>_max" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Harga (Rp)</label>
                                <input type="number" name="price_<?php echo $i; ?>" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <div class="flex items-center justify-end">
                    <a href="index.php" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg mr-2 transition-colors">Batal</a>
                    <button type="submit" class="bg-primary hover:bg-pink-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">Tambah Produk</button>
                </div>
            </form>
        </div>
    </main>
</div>

<!-- Script untuk Preview Gambar dan Toggle Harga Bertingkat -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');
    if (imageInput) {
        imageInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            } else {
                imagePreview.classList.add('hidden');
            }
        });
    }

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
// --- LANGKAH 5: TERAKHIR, INCLUDE FOOTER UNTUK MENUTUP HTML ---
require_once '../includes/admin-footer.php';
?>