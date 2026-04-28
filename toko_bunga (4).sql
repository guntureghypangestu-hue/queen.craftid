-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 28, 2026 at 01:51 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `toko_bunga`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) NOT NULL DEFAULT 'category-default.jpg',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `image_url`, `created_at`) VALUES
(1, 'Buket Bunga', 'buket-bunga', 'Buket segar dengan berbagai pilihan bunga mawar, lili, dan lainnya.', '1770464750_WhatsApp Image 2026-02-06 at 18.29.39.jpeg', '2026-01-24 07:16:34'),
(3, 'Buket Uang', 'buket-uang', 'Cara kreatif memberikan hadiah uang dengan susunan yang menarik.', '1770807968_WhatsApp Image 2026-02-11 at 17.52.30.jpeg', '2026-01-24 07:16:34'),
(4, 'Buket Balon', 'buket-balon', 'Buket balon berbagai bentuk dan warna untuk acara pesta.', '1770807440_WhatsApp Image 2026-02-11 at 17.42.36.jpeg', '2026-01-24 07:16:34'),
(5, 'Buket Coklat', 'buket-coklat', 'Buket berisi aneka snack favorit yang manis dan gurih.', '1770465423_WhatsApp Image 2026-02-06 at 18.33.15.jpeg', '2026-01-24 07:16:34'),
(6, 'Buket Wisuda', 'buket-wisuda', 'Buket wisuda elegan untuk merayakan momen kelulusan', '1770465150_WhatsApp Image 2026-02-06 at 18.31.36.jpeg', '2026-01-24 08:36:38'),
(17, 'Buket Custom', 'buket-custom', 'Buket berisi berbagai snack favorit untuk hadiah unik.', '1777101151_WhatsApp Image 2026-04-25 at 1.55.08 PM.jpeg', '2026-04-25 07:08:09');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `customer_address` text NOT NULL,
  `notes` text DEFAULT NULL,
  `items` longtext NOT NULL COMMENT 'JSON array of items: [{id, name, price, quantity}, ...]',
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_name`, `customer_phone`, `customer_address`, `notes`, `items`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
(4, 'Andi Saputra', '081234567890', 'Ponorogo', 'Tolong kirim cepat', '[{\"id\":10,\"name\":\"Buket Balon\",\"price\":50000,\"quantity\":1}]', 50000.00, 'pending', '2026-04-25 06:21:15', '2026-04-25 06:21:15'),
(5, 'Siti Nurhaliza', '082345678901', 'Madiun', '', '[{\"id\":15,\"name\":\"Buket Bunga\",\"price\":75000,\"quantity\":2}]', 150000.00, 'confirmed', '2026-04-25 06:21:15', '2026-04-25 06:21:15'),
(6, 'Budi Santoso', '083456789012', 'Ngawi', 'Untuk ulang tahun', '[{\"id\":26,\"name\":\"Buket uang\",\"price\":100000,\"quantity\":1}]', 100000.00, 'pending', '2026-04-25 06:21:15', '2026-04-25 06:21:15'),
(7, 'Dewi Lestari', '084567890123', 'Magetan', '', '[{\"id\":31,\"name\":\"Buket Coklat 3\",\"price\":209999.99,\"quantity\":1}]', 209999.99, 'confirmed', '2026-04-25 06:21:15', '2026-04-25 06:21:15'),
(8, 'Rudi Hartono', '085678901234', 'Solo', 'Jangan sampai rusak', '[{\"id\":32,\"name\":\"Buket wisuda\",\"price\":200000,\"quantity\":1}]', 200000.00, 'pending', '2026-04-25 06:21:15', '2026-04-25 06:21:15'),
(9, 'Maya Putri', '086789012345', 'Surabaya', '', '[{\"id\":33,\"name\":\"Buket Wisuda 2\",\"price\":199999.99,\"quantity\":2}]', 399999.98, 'confirmed', '2026-04-25 06:21:15', '2026-04-25 06:21:15'),
(10, 'Agus Salim', '087890123456', 'Yogyakarta', '', '[{\"id\":11,\"name\":\"Buket Banga 2\",\"price\":23332,\"quantity\":3}]', 69996.00, 'cancelled', '2026-04-25 06:21:15', '2026-04-25 06:21:15'),
(11, 'Nina Amelia', '088901234567', 'Semarang', 'Packing rapi ya', '[{\"id\":27,\"name\":\"Buket uang 2\",\"price\":150000,\"quantity\":1}]', 150000.00, 'pending', '2026-04-25 06:21:15', '2026-04-25 06:21:15'),
(12, 'Fajar Nugroho', '089012345678', 'Malang', '', '[{\"id\":34,\"name\":\"Buket Wisuda 3\",\"price\":230000,\"quantity\":1}]', 230000.00, 'confirmed', '2026-04-25 06:21:15', '2026-04-25 06:21:15'),
(13, 'Lina Marlina', '081122334455', 'Kediri', '', '[{\"id\":10,\"name\":\"Buket Balon\",\"price\":50000,\"quantity\":2}]', 100000.00, 'pending', '2026-04-25 06:21:15', '2026-04-25 06:21:15'),
(14, 'Hendra Wijaya', '082233445566', 'Blitar', 'Hadiah anniversary', '[{\"id\":15,\"name\":\"Buket Bunga\",\"price\":75000,\"quantity\":3}]', 225000.00, 'confirmed', '2026-04-25 06:21:15', '2026-04-25 06:21:15'),
(15, 'Putra Mahesa', '083344556677', 'Pacitan', '', '[{\"id\":31,\"name\":\"Buket Coklat 3\",\"price\":209999.99,\"quantity\":2}]', 419999.98, 'pending', '2026-04-25 06:21:15', '2026-04-25 06:21:15'),
(16, 'Rahmat Hidayat', '085112223333', 'Ponorogo', 'Kado untuk teman', '[{\"id\":26,\"name\":\"Buket uang\",\"price\":100000,\"quantity\":2}]', 200000.00, 'confirmed', '2026-04-25 06:27:33', '2026-04-25 06:27:33'),
(17, 'Intan Permata', '086223334444', 'Madiun', '', '[{\"id\":32,\"name\":\"Buket wisuda\",\"price\":200000,\"quantity\":1}]', 200000.00, 'cancelled', '2026-04-25 06:27:33', '2026-04-25 06:45:17'),
(18, 'Yoga Pratama', '087334445555', 'Ngawi', 'Mohon kirim sore hari', '[{\"id\":10,\"name\":\"Buket Balon\",\"price\":50000,\"quantity\":3}]', 150000.00, 'cancelled', '2026-04-25 06:27:33', '2026-04-25 06:27:33'),
(19, 'Salsa Aulia', '088445556666', 'Magetan', '', '[{\"id\":33,\"name\":\"Buket Wisuda 2\",\"price\":199999.99,\"quantity\":1}]', 199999.99, 'confirmed', '2026-04-25 06:27:33', '2026-04-25 06:27:33'),
(20, 'Dimas Saputra', '089556667777', 'Surabaya', 'Untuk acara wisuda', '[{\"id\":34,\"name\":\"Buket Wisuda 3\",\"price\":230000,\"quantity\":2}]', 460000.00, 'pending', '2026-04-25 06:27:33', '2026-04-25 06:27:33'),
(21, 'j', '086786', 'j', 'n', '[{\"id\":35,\"name\":\"Midnight Ever Bloom \",\"price\":200000,\"image\":\"1777101315_WhatsApp Image 2026-04-25 at 1.57.48 PM.jpeg\",\"quantity\":1}]', 200000.00, 'pending', '2026-04-25 07:16:55', '2026-04-25 07:16:55'),
(22, 'test', '0827272772', 'jl.tungtung', 'segera', '[{\"id\":35,\"name\":\"Midnight Ever Bloom \",\"price\":200000,\"image\":\"1777101315_WhatsApp Image 2026-04-25 at 1.57.48 PM.jpeg\",\"quantity\":1}]', 200000.00, 'pending', '2026-04-25 07:19:11', '2026-04-25 07:19:11'),
(23, 'dewg', '424243', 'dfdf', 'dndnnd', '[{\"id\":34,\"name\":\"Buket Wisuda 3\",\"price\":230000,\"image\":\"1776958173_WhatsApp Image 2026-04-23 at 10.10.09 PM.jpeg\",\"quantity\":1},{\"id\":27,\"name\":\"Buket uang 2\",\"price\":150000,\"image\":\"1776955847_WhatsApp Image 2026-04-23 at 9.31.17 PM.jpeg\",\"quantity\":1}]', 380000.00, 'pending', '2026-04-26 08:07:06', '2026-04-26 08:07:06'),
(24, 'nnz', 's bnsb', 'ejnbwbf', 'ndbnwdb', '[{\"id\":34,\"name\":\"Buket Wisuda 3\",\"price\":230000,\"image\":\"1776958173_WhatsApp Image 2026-04-23 at 10.10.09 PM.jpeg\",\"quantity\":1}]', 230000.00, 'confirmed', '2026-04-28 07:22:40', '2026-04-28 07:23:20'),
(25, 'ab ba ba', '34325435', 'j;.tungtung', 'babbba', '[{\"id\":27,\"name\":\"Buket uang 2\",\"price\":340000,\"image\":\"1776955847_WhatsApp Image 2026-04-23 at 9.31.17 PM.jpeg\",\"quantity\":1}]', 340000.00, 'cancelled', '2026-04-28 07:26:52', '2026-04-28 07:27:10'),
(26, 'andre ilmi pangestu', '087755682566', 'jl,freefiregep', 'cepat', '[{\"id\":32,\"name\":\"Buket wisuda\",\"price\":200000,\"image\":\"1776958101_WhatsApp Image 2026-04-23 at 10.10.08 PM.jpeg\",\"quantity\":1}]', 200000.00, 'pending', '2026-04-28 07:28:35', '2026-04-28 07:28:35'),
(28, 'gep', '08225252525', 'wjbssbhws', 'hi', '[{\"id\":27,\"name\":\"Buket uang 2\",\"price\":225000,\"image\":\"1776955847_WhatsApp Image 2026-04-23 at 9.31.17 PM.jpeg\",\"quantity\":1}]', 225000.00, 'pending', '2026-04-28 07:48:42', '2026-04-28 07:48:42');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('active','inactive','out_of_stock') NOT NULL DEFAULT 'active',
  `has_tiered_pricing` tinyint(1) NOT NULL DEFAULT 0,
  `sheet_1_min` int(11) DEFAULT NULL,
  `sheet_1_max` int(11) DEFAULT NULL,
  `price_1` decimal(10,2) DEFAULT NULL,
  `sheet_2_min` int(11) DEFAULT NULL,
  `sheet_2_max` int(11) DEFAULT NULL,
  `price_2` decimal(10,2) DEFAULT NULL,
  `sheet_3_min` int(11) DEFAULT NULL,
  `sheet_3_max` int(11) DEFAULT NULL,
  `price_3` decimal(10,2) DEFAULT NULL,
  `sheet_4_min` int(11) DEFAULT NULL,
  `sheet_4_max` int(11) DEFAULT NULL,
  `price_4` decimal(10,2) DEFAULT NULL,
  `sheet_5_min` int(11) DEFAULT NULL,
  `sheet_5_max` int(11) DEFAULT NULL,
  `price_5` decimal(10,2) DEFAULT NULL,
  `sheet_6_min` int(11) DEFAULT NULL,
  `sheet_6_max` int(11) DEFAULT NULL,
  `price_6` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `price`, `image_url`, `stock`, `is_featured`, `status`, `has_tiered_pricing`, `sheet_1_min`, `sheet_1_max`, `price_1`, `sheet_2_min`, `sheet_2_max`, `price_2`, `sheet_3_min`, `sheet_3_max`, `price_3`, `sheet_4_min`, `sheet_4_max`, `price_4`, `sheet_5_min`, `sheet_5_max`, `price_5`, `sheet_6_min`, `sheet_6_max`, `price_6`, `created_at`) VALUES
(10, 4, 'Buket Balon', 'buket-balon', 'anam', 50000.00, '1773214896_WhatsApp Image 2026-02-06 at 18.32.12.jpeg', 2, 0, 'active', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-28 17:52:25'),
(11, 1, 'Buket Banga 2', 'buket-banga-2', 's', 23332.00, '1776957186_WhatsApp Image 2026-04-23 at 9.53.58 PM (1).jpeg', 2, 0, 'active', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-28 17:53:14'),
(15, 1, 'Buket Bunga', 'buket-bunga', 'Bunga berkualitas tinggi', 750000.00, '1776957167_WhatsApp Image 2026-04-23 at 9.53.58 PM.jpeg', 1, 0, 'active', 0, 1, 10, 75000.00, 11, 20, 125000.00, 21, 30, 185000.00, 31, 40, 245000.00, 41, 50, 325000.00, 76, 100, 585000.00, '2026-02-04 13:15:03'),
(26, 3, 'Buket uang', 'buket-uang', 'uang asli 100%', 100000.00, '1776955505_WhatsApp Image 2026-04-23 at 9.24.26 PM.jpeg', 0, 0, 'out_of_stock', 1, 1, 10, 75000.00, 11, 20, 125000.00, 21, 30, 185000.00, 31, 39, 245000.00, 41, 50, 325000.00, 76, 100, 584999.99, '2026-04-23 14:45:05'),
(27, 3, 'Buket uang 2', 'buket-uang-2', 'uang  bagus', 150000.00, '1776955847_WhatsApp Image 2026-04-23 at 9.31.17 PM.jpeg', 1, 0, 'active', 1, 20, 30, 150000.00, 40, 50, 225000.00, 51, 75, 295000.00, 80, 100000, 340000.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-23 14:50:47'),
(28, 4, 'Buket balon 2', 'buket-balon-2', 'Buket balon', 60000.00, '1776956217_WhatsApp Image 2026-04-23 at 9.38.35 PM.jpeg', 0, 0, 'out_of_stock', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-23 14:56:57'),
(29, 5, 'Buket Coklat 2', 'buket-coklat-2', '.', 199999.99, '1776957379_WhatsApp Image 2026-04-23 at 9.57.30 PM (1).jpeg', 1, 0, 'active', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-23 15:16:19'),
(30, 5, 'Buket Coklat', 'buket-coklat', '.', 229999.99, '1776957693_WhatsApp Image 2026-04-23 at 9.57.30 PM (2).jpeg', 0, 0, 'out_of_stock', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-23 15:21:33'),
(31, 5, 'Buket Coklat 3', 'buket-coklat-3', '.', 209999.99, '1776957886_WhatsApp Image 2026-04-23 at 9.57.30 PM.jpeg', 1, 0, 'active', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-23 15:24:46'),
(32, 6, 'Buket wisuda', 'buket-wisuda', '.', 200000.00, '1776958101_WhatsApp Image 2026-04-23 at 10.10.08 PM.jpeg', 1, 0, 'active', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-23 15:28:21'),
(33, 6, 'Buket Wisuda 2', 'buket-wisuda-2', '.', 199999.99, '1776958135_WhatsApp Image 2026-04-23 at 10.10.09 PM (1).jpeg', 1, 0, 'active', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-23 15:28:55'),
(34, 6, 'Buket Wisuda 3', 'buket-wisuda-3', '.', 230000.00, '1776958173_WhatsApp Image 2026-04-23 at 10.10.09 PM.jpeg', 0, 0, 'out_of_stock', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-23 15:29:33'),
(35, 17, 'Midnight Ever Bloom ', 'midnight-ever-bloom-', 'Midnight Ever Bloom ????', 200000.00, '1777101315_WhatsApp Image 2026-04-25 at 1.57.48 PM.jpeg', 1, 0, 'active', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-25 07:15:15');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `created_at`) VALUES
(1, 'admin', '$2y$10$7hIKI5I.bJUfKP5mV7t9vefk9YDwQhL4wBGW03Wx3Z.UFnyrf5Vne', '2026-02-01 03:28:54'),
(2, 'gep', '$2y$10$Wh.b0uq.43RTBA3U4jtVJeFCyFWaGXlKHCriyCqw1OMTOR0b2bqjC', '2026-04-23 13:49:18'),
(3, 'royff', '$2y$10$pOqXhErZFW7kyKsXU7QKeub8EOFalBM5FyCDHIMhgnhXh7wr032/K', '2026-04-23 14:18:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
