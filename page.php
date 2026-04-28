<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Cek apakah ada slug di URL
if (!isset($_GET['slug'])) {
    header('Location: index.php');
    exit;
}

 $slug = $_GET['slug'];

// Ambil data halaman dari database berdasarkan slug
 $stmt = $pdo->prepare("SELECT title, content FROM pages WHERE slug = ?");
 $stmt->execute([$slug]);
 $page = $stmt->fetch(PDO::FETCH_ASSOC);

// Jika halaman tidak ditemukan, tampilkan error 404
if (!$page) {
    http_response_code(404);
    echo '<div class="container mx-auto p-8 text-center"><h1 class="text-4xl font-bold">Halaman Tidak Ditemukan</h1><p class="mt-4">Maaf, halaman yang Anda cari tidak ada.</p></div>';
    require_once 'includes/footer.php';
    exit;
}
?>

<div class="container mx-auto p-4 my-8 bg-white rounded-lg shadow-md">
    <h1 class="text-3xl font-bold mb-6 text-gray-800"><?php echo htmlspecialchars($page['title']); ?></h1>
    <div class="prose max-w-none text-gray-600">
        <?php echo $page['content']; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>