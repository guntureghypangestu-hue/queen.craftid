<?php
require_once 'db.php';
require_once 'functions.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>queencraft.id - Toko Buket Online Terpercaya</title>
    <link rel="stylesheet" href="/buketqueen/assets/css/style.css">
    <link rel="stylesheet" href="/buketqueen/assets/css/pages.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header & Navigasi -->
    <header class="sticky-header">
        <div class="container">
            <a href="/buketqueen/" class="logo">
                <img src="assets/images/uploads/logo.png" alt="Queencraft.id - Toko Buket Online">
            </a>
            <nav class="main-nav">

                <ul>
                    <li><a href="/buketqueen/index.php">Beranda</a></li>
                    <li><a href="/buketqueen/index.php#kategori">Kategori</a></li>
                    <li><a href="/buketqueen/index.php#produk-unggulan">Produk</a></li>
                    <li><a href="/buketqueen/kontak.php">Kontak</a></li>
                    <li><a href="/buketqueen/tentang-kami.php">Tentang kami</a></li>
                </ul>
            </nav>
                <div class="nav-icons">
                <a href="wishlist.php" class="wishlist-icon" title="Wishlist">
                    <i class="fas fa-heart"></i>
                    <span class="wishlist-count">0</span>
                </a>
                <a href="cart.php" class="cart-icon" title="Keranjang">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count">0</span>
                </a>
            
                <div class="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </header>
