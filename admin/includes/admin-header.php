<?php
// --- TENTUKAN PATH DASAR PROYEK ANDA ---
// Jika website Anda berada di folder utama (misal: http://localhost/), biarkan seperti ini.
// Jika website Anda berada di subfolder (misal: http://localhost/buketqueen/), ubah menjadi: $base_path = '/buketqueen/';
 $base_path = '/buketqueen/'; 

// --- LOGIKA UNTUK MENDAPATKAN HALAMAN AKTIF & PATH NAVIGASI ---
// Ambil nama direktori dari URL saat ini (misal: 'admin', 'categories', 'products')
 $current_dir = basename(dirname($_SERVER['PHP_SELF']));

// Tentukan apakah kita perlu "naik satu level" (../) untuk mencapai folder admin
// Jika kita di 'categories' atau 'products', kita perlu naik satu level.
 $go_up = ($current_dir === 'admin') ? '' : '../';

// Tentukan halaman aktif untuk highlight
 $current_page = 'dashboard'; // Default
if ($current_dir === 'categories') {
    $current_page = 'categories';
} elseif ($current_dir === 'products') {
    $current_page = 'products';
} elseif ($current_dir === 'orders') {
    $current_page = 'orders';
}

// Buat link navigasi yang benar
 $dashboard_link = $go_up . 'index.php';
 $categories_link = $go_up . 'categories/index.php';
 $products_link = $go_up . 'products/index.php';
 $orders_link = $go_up . 'orders/index.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - BuketQueen</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome untuk Ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Konfigurasi Tailwind -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#E8B4B8',
                        secondary: '#4A6A52',
                        // --- WARNA BARU UNTUK SIDEBAR TEMBA HITAM-PUTIH ---
                        sidebar: '#1f2937',          // Latar belakang abu-abu gelap (gray-800)
                        'sidebar-hover': '#374151', // Warna saat hover (gray-700)
                        accent: '#ffffff',           // Aksen putih untuk menu aktif
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 font-sans antialiased">

<div class="flex h-screen overflow-hidden">
    <!-- Overlay untuk mobile (tersembunyi secara default) -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-sidebar text-white transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto flex flex-col shadow-xl">
        
        <!-- Branding Section -->
        <div class="p-6 border-b border-gray-700">
            <div class="flex items-center">
                <!-- PERUBAHAN DI SINI -->
                <!-- Kode yang BENAR dan HANDAL -->
                <img src="<?php echo $base_path; ?>assets/images/uploads/logo.png" alt="BuketQueen Logo" class="h-10 w-auto">
                <div class="ml-3">
                    <h2 class="text-xm font-bold text-white">QUEENCRAFT.ID</h2>
                    <p class="text-xs text-gray-400">Admin Panel</p>
                </div>
            </div>
        </div>

        <!-- Navigation Section -->
        <nav class="flex-1 mt-6 px-3 space-y-1">
            <a href="<?php echo $dashboard_link; ?>" class="nav-link flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 <?php echo ($current_page == 'dashboard') ? 'bg-sidebar-hover text-white border-l-4 border-accent' : 'text-gray-400 hover:bg-sidebar-hover hover:text-white'; ?>">
                <i class="fas fa-tachometer-alt mr-3 w-5 text-center"></i> Dashboard
            </a>
            <a href="<?php echo $categories_link; ?>" class="nav-link flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 <?php echo ($current_page == 'categories') ? 'bg-sidebar-hover text-white border-l-4 border-accent' : 'text-gray-400 hover:bg-sidebar-hover hover:text-white'; ?>">
                <i class="fas fa-tags mr-3 w-5 text-center"></i> Kategori
            </a>
            <a href="<?php echo $products_link; ?>" class="nav-link flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 <?php echo ($current_page == 'products') ? 'bg-sidebar-hover text-white border-l-4 border-accent' : 'text-gray-400 hover:bg-sidebar-hover hover:text-white'; ?>">
                <i class="fas fa-box mr-3 w-5 text-center"></i> Produk
            </a>
            <a href="<?php echo $orders_link; ?>" class="nav-link flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 <?php echo ($current_page == 'orders') ? 'bg-sidebar-hover text-white border-l-4 border-accent' : 'text-gray-400 hover:bg-sidebar-hover hover:text-white'; ?>">
                <i class="fas fa-shopping-cart mr-3 w-5 text-center"></i> Pesanan
            </a>
            
            <!-- Garis Pembatas -->
            <div class="my-4 border-t border-gray-700"></div>

            <!-- Link Lihat Website -->
            <a href="/buketqueen/" target="_blank" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg text-gray-400 hover:bg-sidebar-hover hover:text-white transition-all duration-200 group">
                <i class="fas fa-external-link-alt mr-3 w-5 text-center group-hover:rotate-12 transition-transform duration-200"></i> 
                Lihat Website
            </a>
        </nav>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-gray-700">
            <p class="text-xs text-gray-500 text-center">QUEENCRAFT.ID Admin</p>
        </div>
    </aside>

    <!-- Main Content -->
    <!-- ... sisanya dari kode Anda ... -->

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden lg:ml-0">
        <!-- Top Bar -->
        <header class="bg-white shadow-sm border-b border-gray-200 z-30">
            <div class="flex items-center justify-between px-6 py-4">
                <!-- Tombol Hamburger (hanya terlihat di mobile) -->
                <button id="sidebar-toggle" class="text-gray-500 hover:text-gray-700 focus:outline-none lg:hidden">
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <div class="flex items-center ml-auto lg:ml-0">
                    <span class="text-gray-700 mr-4 hidden sm:block">Halo, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                    <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded transition-colors text-sm">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main id="main-content" class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-4 lg:p-6">
            <!-- Konten halaman akan dimuat di sini -->