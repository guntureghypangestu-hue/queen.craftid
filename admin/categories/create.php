<?php
// --- LANGKAH 1: HANYA INCLUDE FILE LOGIKA (TANPA OUTPUT) ---
require_once '../includes/auth.php';
require_once '../../includes/db.php';

// Inisialisasi variabel untuk pesan error dan data form
 $error_message = '';
 $form_data = ['name' => ''];

// --- LANGKAH 2: PROSES FORM JIKA DI-POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $form_data['name'] = $name; // Simpan nama yang diisi untuk ditampilkan kembali jika ada error
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    
    // --- PERIKSA DUPLIKAT SLUG SEBELUM MENYIMPAN ---
    $check_stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
    $check_stmt->execute([$slug]);
    
    if ($check_stmt->fetch()) {
        // Jika slug sudah ada, set pesan error
        $error_message = "Nama kategori '" . htmlspecialchars($name) . "' sudah ada atau mirip. Silakan gunakan nama lain.";
    } else {
        // Jika slug belum ada, lanjutkan proses
        // Handle Image Upload
        $image_url = 'category-default.jpg'; // Gambar default
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            // Pastikan direktori upload ada
            $upload_dir = '../../assets/images/categories/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $file_name = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_url = $file_name;
            }
        }

        // Simpan ke database
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, image_url) VALUES (?, ?, ?)");
        if ($stmt->execute([$name, $slug, $image_url])) {
            // Jika berhasil, redirect
            header('Location: index.php?status=created');
            exit;
        } else {
            // Jika terjadi error lain saat insert
            $error_message = "Terjadi kesalahan tak terduga. Silakan coba lagi.";
        }
    }
}

// --- LANGKAH 3: JIKA BUKAN POST (MENAMPILKAN HALAMAN FORM) ---
// Sekarang baru include file header (mulai output HTML)
require_once '../includes/admin-header.php';
?>

<!-- ============================================================= -->
<!--                BAGIAN HTML (TAMPILAN)                       -->
<!-- ============================================================= -->
<div class="bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Tambah Kategori Baru</h1>
    
    <!-- TAMPILKAN PESAN ERROR JIKA ADA -->
    <?php if (!empty($error_message)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline"><?php echo $error_message; ?></span>
        </div>
    <?php endif; ?>

    <form action="create.php" method="POST" enctype="multipart/form-data" class="space-y-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nama Kategori</label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($form_data['name']); ?>" required
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
        </div>
        
        <div>
            <label for="image" class="block text-sm font-medium text-gray-700">Gambar Kategori</label>
            <input type="file" name="image" id="image" accept="image/*"
                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-pink-700">
            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. Maksimal 2MB. Kosongkan untuk menggunakan gambar default.</p>
            <img id="image-preview" class="mt-4 h-24 w-24 object-cover rounded-md hidden" alt="Preview Gambar">
        </div>
        
        <div class="flex items-center justify-end">
            <a href="index.php" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg mr-2 transition-colors">Batal</a>
            <button type="submit" class="bg-primary hover:bg-pink-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">Simpan Kategori</button>
        </div>
    </form>
</div>

<!-- Script untuk Preview Gambar -->
<script>
document.getElementById('image').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('image-preview');
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    } else {
        preview.classList.add('hidden');
    }
});
</script>

<?php
// --- LANGKAH 4: TERAKHIR, INCLUDE FOOTER UNTUK MENUTUP HTML ---
require_once '../includes/admin-footer.php';
?>