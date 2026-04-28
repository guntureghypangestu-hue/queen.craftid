<?php
// --- LANGKAH 1: HANYA INCLUDE FILE LOGIKA (TANPA OUTPUT) ---
require_once '../includes/auth.php';
require_once '../../includes/db.php';

// Inisialisasi variabel
 $error_message = '';
 $category = null; // Inisialisasi $category untuk menghindari error jika ID tidak valid

// --- LANGKAH 2: AMBIL DATA KATEGORI BERDASARKAN ID ---
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}
 $category_id = $_GET['id'];

 $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
 $stmt->execute([$category_id]);
 $category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    header('Location: index.php');
    exit;
}

// --- LANGKAH 3: PROSES FORM JIKA DI-POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    
    // --- PERIKSA DUPLIKAT SLUG (KECUALI KATEGORI INI SENDIRI) ---
    // Ini adalah kunci perbaikannya: kita mengecek slug yang sama, tapi ID-nya harus berbeda
    $check_stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ? AND id != ?");
    $check_stmt->execute([$slug, $category_id]);
    
    if ($check_stmt->fetch()) {
        // Jika slug sudah digunakan kategori lain, set pesan error
        $error_message = "Nama kategori '" . htmlspecialchars($name) . "' sudah ada atau mirip dengan kategori lain. Silakan gunakan nama lain.";
        // Update $category dengan data yang baru di-submit untuk ditampilkan kembali di form
        $category['name'] = $name;
    } else {
        // Jika slug aman, lanjutkan proses
        // Handle Image Update
        $image_url = $category['image_url']; // Default: tetap pakai gambar lama
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = '../../assets/images/categories/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $file_name = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                // Hapus gambar lama jika bukan gambar default
                if ($category['image_url'] !== 'category-default.jpg' && file_exists($upload_dir . $category['image_url'])) {
                    unlink($upload_dir . $category['image_url']);
                }
                $image_url = $file_name;
            }
        }

        // Simpan perubahan ke database
        $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ?, image_url = ? WHERE id = ?");
        if ($stmt->execute([$name, $slug, $image_url, $category_id])) {
            // Jika berhasil, redirect
            header('Location: index.php?status=updated');
            exit;
        } else {
            // Jika terjadi error lain saat update
            $error_message = "Terjadi kesalahan tak terduga. Silakan coba lagi.";
        }
    }
}

// --- LANGKAH 4: JIKA BUKAN POST (MENAMPILKAN HALAMAN FORM) ---
// Sekarang baru include file header (mulai output HTML)
require_once '../includes/admin-header.php';
?>

<!-- ============================================================= -->
<!--                BAGIAN HTML (TAMPILAN)                       -->
<!-- ============================================================= -->
<div class="bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Kategori: <?php echo htmlspecialchars($category['name']); ?></h1>
    
    <!-- TAMPILKAN PESAN ERROR JIKA ADA -->
    <?php if (!empty($error_message)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline"><?php echo $error_message; ?></span>
        </div>
    <?php endif; ?>
    
    <form action="edit.php?id=<?php echo $category_id; ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nama Kategori</label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($category['name']); ?>" required
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
        </div>
        
        <div>
            <label for="image" class="block text-sm font-medium text-gray-700">Gambar Kategori</label>
            <p class="text-xs text-gray-500 mb-2">Kosongkan jika tidak ingin mengubah gambar.</p>
            <input type="file" name="image" id="image" accept="image/*"
                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-pink-700">
            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. Maksimal 2MB.</p>
            <!-- PERBAIKAN: Path gambar disesuaikan -->
            <img src="../../assets/images/categories/<?php echo htmlspecialchars($category['image_url']); ?>" alt="Current Image" class="mt-4 h-24 w-24 object-cover rounded-md">
        </div>
        
        <div class="flex items-center justify-end">
            <a href="index.php" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg mr-2 transition-colors">Batal</a>
            <button type="submit" class="bg-primary hover:bg-pink-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">Perbarui Kategori</button>
        </div>
    </form>
</div>

<?php require_once '../includes/admin-footer.php'; ?>