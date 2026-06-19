-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 20, 2026 at 01:14 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `clothingstore`
--
CREATE DATABASE IF NOT EXISTS `clothingstore` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `clothingstore`;

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--
-- Creation: Jun 19, 2026 at 09:27 PM
--

DROP TABLE IF EXISTS `cart_items`;
CREATE TABLE IF NOT EXISTS `cart_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1 CHECK (`quantity` > 0),
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cart` (`user_id`,`product_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--
-- Creation: Jun 19, 2026 at 09:27 PM
-- Last update: Jun 19, 2026 at 09:27 PM
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(6, 'Accessories'),
(1, 'Men\'s Clothing'),
(4, 'Outerwear'),
(5, 'Shoes & Sneakers'),
(8, 'Sportswear'),
(3, 'Streetwear'),
(7, 'Vintage'),
(2, 'Women\'s Clothing');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--
-- Creation: Jun 19, 2026 at 09:27 PM
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL CHECK (`quantity` > 0),
  `price_at_purchase` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `idx_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblmessages`
--
-- Creation: Jun 19, 2026 at 09:27 PM
--

DROP TABLE IF EXISTS `tblmessages`;
CREATE TABLE IF NOT EXISTS `tblmessages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `message` varchar(1000) NOT NULL,
  `sent_at` datetime DEFAULT current_timestamp(),
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `sender_id` (`sender_id`),
  KEY `product_id` (`product_id`),
  KEY `idx_receiver` (`receiver_id`,`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblorders`
--
-- Creation: Jun 19, 2026 at 09:27 PM
--

DROP TABLE IF EXISTS `tblorders`;
CREATE TABLE IF NOT EXISTS `tblorders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `buyer_id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `delivery_address` text NOT NULL,
  `status` enum('Pending','Packed','In Transit','Delivered') NOT NULL DEFAULT 'Pending',
  `tracking_number` varchar(100) DEFAULT NULL,
  `payment_method` enum('Credit Card','Debit Card') DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_buyer` (`buyer_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblproducts`
--
-- Creation: Jun 19, 2026 at 09:27 PM
-- Last update: Jun 19, 2026 at 09:27 PM
--

DROP TABLE IF EXISTS `tblproducts`;
CREATE TABLE IF NOT EXISTS `tblproducts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seller_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL CHECK (`price` > 0),
  `condition` enum('New','Like New','Good','Fair','Poor') NOT NULL DEFAULT 'Good',
  `image` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `status` enum('active','sold') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `seller_id` (`seller_id`),
  KEY `category_id` (`category_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblproducts`
--

INSERT INTO `tblproducts` (`id`, `seller_id`, `category_id`, `title`, `description`, `price`, `condition`, `image`, `quantity`, `status`, `created_at`, `updated_at`) VALUES
(1, 3, 7, 'Vintage Levi\'s 501 Jeans', 'Classic straight-cut 501s from the 90s. Size 32x32. Faded wash.', 350.00, 'Good', 'vintage-clothing/denim-jacket-1.jpg', 5, 'active', '2026-06-19 23:27:22', '2026-06-19 23:27:22'),
(2, 3, 4, 'Guess Sherpa Denim Jacket', 'Lined denim jacket with sherpa collar. Size M. Barely worn.', 680.00, 'Like New', 'vintage-clothing/leather-jacket-1.jpg', 3, 'active', '2026-06-19 23:27:22', '2026-06-19 23:27:22'),
(3, 4, 3, 'Supreme Box Logo Hoodie', 'Black hoodie, size L. Some pilling but rare find.', 950.00, 'Good', 'streetwear/hoodie-black-1.jpg', 2, 'active', '2026-06-19 23:27:22', '2026-06-19 23:27:22'),
(4, 4, 5, 'Nike Air Force 1 Low White', 'Classic white AF1s, size 10. Worn 3 times, box included.', 1100.00, 'Like New', 'streetwear/sneakers-hightop-1.jpg', 1, 'active', '2026-06-19 23:27:22', '2026-06-19 23:27:22'),
(5, 3, 2, 'Zara Floral Midi Dress', 'Beautiful floral midi dress, size S. Perfect condition.', 420.00, 'Like New', 'vintage-clothing/denim-jacket-2.jpg', 2, 'active', '2026-06-19 23:27:22', '2026-06-19 23:27:22'),
(6, 4, 6, 'Leather Belt', 'Full-grain leather belt, size 34. Barely used.', 180.00, 'Good', 'accessories/leather-belt-1.jpg', 4, 'active', '2026-06-19 23:27:22', '2026-06-19 23:27:22'),
(7, 3, 1, 'Ralph Lauren Polo Shirt', 'Navy polo, size L. Minor wear on collar.', 220.00, 'Good', 'vintage-clothing/denim-jacket-1.jpg', 3, 'active', '2026-06-19 23:27:22', '2026-06-19 23:27:22'),
(8, 4, 8, 'Adidas Tiro Track Pants', '3-stripe track pants in black/white, size M.', 190.00, 'Good', 'sports-gear/running-shorts-1.jpg', 5, 'active', '2026-06-19 23:27:22', '2026-06-19 23:27:22'),
(9, 3, 3, 'Champion Reverse Weave Sweatshirt', 'Ash grey crewneck, size XL. Some fading.', 310.00, 'Fair', 'streetwear/hoodie-black-1.jpg', 2, 'active', '2026-06-19 23:27:22', '2026-06-19 23:27:22'),
(10, 4, 5, 'New Balance 574 Grey', 'Grey/white NB574, size 9. Well-loved.', 480.00, 'Fair', 'sports-gear/gym-tanktop-1.jpg', 1, 'active', '2026-06-19 23:27:22', '2026-06-19 23:27:22'),
(11, 3, 4, 'North Face Puffer Jacket', 'Black 700-fill down puffer, size L.', 890.00, 'Good', 'vintage-clothing/leather-jacket-1.jpg', 2, 'active', '2026-06-19 23:27:22', '2026-06-19 23:27:22'),
(12, 4, 2, 'H&M Linen Blazer', 'Sand-coloured linen blazer, size 40.', 340.00, 'Like New', 'vintage-clothing/denim-jacket-2.jpg', 1, 'active', '2026-06-19 23:27:22', '2026-06-19 23:27:22'),
(13, 5, 1, 'Tommy Hilfiger Chino Pants', 'Khaki chino, size 32. Classic straight cut.', 260.00, 'Good', 'streetwear/hoodie-black-1.jpg', 3, 'active', '2026-06-19 23:27:22', '2026-06-19 23:27:22'),
(14, 5, 7, 'Vintage Lee Riders Jacket', 'Stonewash denim, size L. 1980s authentic.', 520.00, 'Fair', 'vintage-clothing/denim-jacket-1.jpg', 1, 'active', '2026-06-19 23:27:22', '2026-06-19 23:27:22'),
(15, 5, 5, 'Converse Chuck Taylor High', 'Red, size 9. Slightly worn.', 380.00, 'Fair', 'streetwear/sneakers-hightop-1.jpg', 2, 'active', '2026-06-19 23:27:22', '2026-06-19 23:27:22'),
(16, 5, 2, 'Woolworths Linen Trousers', 'White wide-leg trousers, size 12.', 150.00, 'Good', 'vintage-clothing/denim-jacket-2.jpg', 2, 'active', '2026-06-19 23:27:22', '2026-06-19 23:27:22'),
(17, 5, 8, 'Asics Gel-Nimbus 24', 'Size 10. Used for 3 months of running.', 720.00, 'Fair', 'sports-gear/gym-tanktop-1.jpg', 1, 'active', '2026-06-19 23:27:22', '2026-06-19 23:27:22'),
(18, 3, 1, 'Polo Ralph Lauren Oxford Shirt', 'Blue stripe, size M. Classic.', 280.00, 'Good', 'vintage-clothing/leather-jacket-1.jpg', 2, 'active', '2026-06-19 23:27:22', '2026-06-19 23:27:22'),
(19, 4, 4, 'Stone Island Nylon Jacket', 'Dark navy, size L. Authentic.', 1800.00, 'Good', 'outerwear/puffer-jacket-1.jpg', 1, 'active', '2026-06-19 23:27:22', '2026-06-19 23:27:22'),
(20, 3, 7, 'Diesel Regular Denim Jacket', 'Distressed wash, size M.', 430.00, 'Fair', 'vintage-clothing/denim-jacket-2.jpg', 1, 'active', '2026-06-19 23:27:22', '2026-06-19 23:27:22'),
(21, 4, 2, 'H&M Knit Cardigan', 'Cream open-front cardigan, size S.', 90.00, 'Like New', 'vintage-clothing/leather-jacket-1.jpg', 3, 'active', '2026-06-19 23:27:22', '2026-06-19 23:27:22'),
(22, 3, 3, 'Puma RS-X Sneakers', 'White/blue chunky runner, size 9.', 510.00, 'Good', 'streetwear/sneakers-hightop-1.jpg', 2, 'active', '2026-06-19 23:27:22', '2026-06-19 23:27:22'),
(23, 5, 6, 'Michael Kors Tote Bag', 'Black leather tote. Minor pen mark.', 950.00, 'Fair', 'accessories/leather-belt-1.jpg', 1, 'active', '2026-06-19 23:27:22', '2026-06-19 23:27:22'),
(24, 4, 1, 'Ben Sherman Mod Shirt', 'Paisley print, size L.', 140.00, 'Good', 'vintage-clothing/denim-jacket-1.jpg', 2, 'active', '2026-06-19 23:27:22', '2026-06-19 23:27:22'),
(25, 5, 5, 'Reebok Classic Leather', 'White, size 8.5. Cleaned thoroughly.', 340.00, 'Good', 'streetwear/sneakers-hightop-1.jpg', 2, 'active', '2026-06-19 23:27:22', '2026-06-19 23:27:22'),
(26, 3, 5, 'Sperry Topsider Boat Shoes', 'Tan leather, size 10. Worn seaside only.', 420.00, 'Good', 'sports-gear/running-shorts-1.jpg', 1, 'active', '2026-06-19 23:27:22', '2026-06-19 23:27:22'),
(27, 4, 4, 'Columbia Fleece Jacket', 'Blue zip-up fleece, size XL.', 290.00, 'Good', 'outerwear/puffer-jacket-1.jpg', 2, 'active', '2026-06-19 23:27:22', '2026-06-19 23:27:22'),
(28, 5, 2, 'Topshop Denim Cut-offs', 'Frayed hem, size 10. Festival-ready.', 110.00, 'Good', 'vintage-clothing/denim-jacket-2.jpg', 3, 'active', '2026-06-19 23:27:22', '2026-06-19 23:27:22'),
(29, 3, 3, 'Dickies 874 Work Pants', 'Khaki, size 34x32. Classic workwear.', 220.00, 'Like New', 'streetwear/hoodie-black-1.jpg', 2, 'active', '2026-06-19 23:27:22', '2026-06-19 23:27:22'),
(30, 4, 8, 'Nike Flex Shorts', 'Black athletic shorts, size M.', 85.00, 'Good', 'sports-gear/running-shorts-1.jpg', 4, 'active', '2026-06-19 23:27:22', '2026-06-19 23:27:22');

-- --------------------------------------------------------

--
-- Table structure for table `tblreviews`
--
-- Creation: Jun 19, 2026 at 09:27 PM
--

DROP TABLE IF EXISTS `tblreviews`;
CREATE TABLE IF NOT EXISTS `tblreviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reviewer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `reviewer_id` (`reviewer_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblsellerrequests`
--
-- Creation: Jun 19, 2026 at 09:27 PM
--

DROP TABLE IF EXISTS `tblsellerrequests`;
CREATE TABLE IF NOT EXISTS `tblsellerrequests` (
  `user_id` int(11) NOT NULL,
  `motivation` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `requested_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  UNIQUE KEY `uq_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbluser`
--
-- Creation: Jun 19, 2026 at 09:27 PM
-- Last update: Jun 19, 2026 at 10:00 PM
--

DROP TABLE IF EXISTS `tbluser`;
CREATE TABLE IF NOT EXISTS `tbluser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('buyer','seller','admin') NOT NULL DEFAULT 'buyer',
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `seller_request` enum('none','pending','approved','rejected') NOT NULL DEFAULT 'none',
  `seller_request_note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_role` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbluser`
--

INSERT INTO `tbluser` (`id`, `name`, `email`, `password_hash`, `role`, `is_verified`, `seller_request`, `seller_request_note`, `created_at`, `last_login`) VALUES
(1, 'Admin User', 'admin@pastimes.co.za', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, 'none', NULL, '2026-06-19 23:27:22', NULL),
(2, 'John Buyer', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'buyer', 1, 'none', NULL, '2026-06-19 23:27:22', NULL),
(3, 'Sarah Seller', 'sarah@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'seller', 1, 'approved', NULL, '2026-06-19 23:27:22', NULL),
(4, 'Mike Seller', 'mike@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'seller', 1, 'approved', NULL, '2026-06-19 23:27:22', NULL),
(5, 'Demo Seller', 'demo@pastimes.co.za', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'seller', 1, 'approved', NULL, '2026-06-19 23:27:22', NULL),
(6, 'Demo Buyer', 'buyer@pastimes.co.za', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'buyer', 1, 'none', NULL, '2026-06-19 23:27:22', NULL),
(7, 'Koo Koo', 'hello@hello.com', '$2y$10$UHi6XBy.ESTMfMiNcvIl/OsZSE47qcL2oyTiI.l/zbTtERIlBSwYi', 'seller', 0, 'pending', NULL, '2026-06-20 00:00:40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblwishlist`
--
-- Creation: Jun 19, 2026 at 09:27 PM
--

DROP TABLE IF EXISTS `tblwishlist`;
CREATE TABLE IF NOT EXISTS `tblwishlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `added_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_wishlist` (`user_id`,`product_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblproducts`
--
ALTER TABLE `tblproducts` ADD FULLTEXT KEY `idx_search` (`title`,`description`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbluser` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `tblproducts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `tblorders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `tblproducts` (`id`);

--
-- Constraints for table `tblmessages`
--
ALTER TABLE `tblmessages`
  ADD CONSTRAINT `tblmessages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `tbluser` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tblmessages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `tbluser` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tblmessages_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `tblproducts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tblorders`
--
ALTER TABLE `tblorders`
  ADD CONSTRAINT `tblorders_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `tbluser` (`id`);

--
-- Constraints for table `tblproducts`
--
ALTER TABLE `tblproducts`
  ADD CONSTRAINT `tblproducts_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `tbluser` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tblproducts_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `tblreviews`
--
ALTER TABLE `tblreviews`
  ADD CONSTRAINT `tblreviews_ibfk_1` FOREIGN KEY (`reviewer_id`) REFERENCES `tbluser` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tblreviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `tblproducts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tblsellerrequests`
--
ALTER TABLE `tblsellerrequests`
  ADD CONSTRAINT `tblsellerrequests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbluser` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tblwishlist`
--
ALTER TABLE `tblwishlist`
  ADD CONSTRAINT `tblwishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbluser` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tblwishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `tblproducts` (`id`) ON DELETE CASCADE;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
