-- ================================================
--  Umutako Art Store — Live Server Export
--  Database: if0_42203229_umutako_art_store
-- ================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------
-- DROP in child-first order
-- ------------------------------------------------
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `customers`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;

SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------
-- categories
-- ------------------------------------------------
CREATE TABLE `categories` (
  `id`          int(11)      NOT NULL AUTO_INCREMENT,
  `name`        varchar(100) NOT NULL,
  `description` text         DEFAULT NULL,
  `icon`        varchar(20)  DEFAULT '🎁',
  `created_at`  timestamp    NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`id`,`name`,`description`,`icon`) VALUES
(1,'Baskets & Weaving',    'Traditional Rwandan woven baskets and mats',        '🧺'),
(2,'Pottery & Ceramics',   'Hand-crafted clay pots and ceramic items',          '🏺'),
(3,'Wood Carvings',        'Artisan wood-carved sculptures and trays',          '🪵'),
(4,'Jewelry & Accessories','Handmade beaded jewelry and fashion accessories',   '📿'),
(5,'Textiles & Fabrics',   'Imigongo art, kitenge fabrics and painted scarves', '🎨');

-- ------------------------------------------------
-- users
-- ------------------------------------------------
CREATE TABLE `users` (
  `id`         int(11)                  NOT NULL AUTO_INCREMENT,
  `name`       varchar(150)             NOT NULL,
  `email`      varchar(150)             NOT NULL,
  `password`   varchar(255)             NOT NULL,
  `role`       enum('customer','admin') DEFAULT 'customer',
  `phone`      varchar(20)              DEFAULT NULL,
  `address`    text                     DEFAULT NULL,
  `created_at` timestamp                NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin@1234 | Customer@1234  (SHA-256)
INSERT INTO `users` (`id`,`name`,`email`,`password`,`role`) VALUES
(1,'Admin User',    'admin@umutako.rw',    'bc78e58d55cde1346e68f8e5fe588dedf62fa457aa646a500a53347faff6ee24','admin'),
(2,'Test Customer', 'customer@umutako.rw', '7a080944e5cd93ec68d24fc71fa993f73eb724ef8bf39ed7e74609b506e89d93','customer');

-- ------------------------------------------------
-- products
-- ------------------------------------------------
CREATE TABLE `products` (
  `id`          int(11)       NOT NULL AUTO_INCREMENT,
  `name`        varchar(200)  NOT NULL,
  `description` text          DEFAULT NULL,
  `price`       decimal(10,2) NOT NULL,
  `stock`       int(11)       DEFAULT 0,
  `category_id` int(11)       DEFAULT NULL,
  `image`       varchar(500)  DEFAULT NULL,
  `created_at`  timestamp     NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`id`,`name`,`description`,`price`,`stock`,`category_id`,`image`) VALUES
(1,  'Agaseke Gift Basket',    'Beautiful traditional Rwandan agaseke basket, handwoven with fine sisal and raffia. Perfect as a gift or decoration for any home.',  15000.00, 20, 1, 'images/products/p1.jpg'),
(2,  'Large Market Basket',    'Spacious handwoven basket ideal for carrying goods at the market. Durable with vibrant geometric patterns.',                          22000.00, 15, 1, 'images/products/p2.jpg'),
(3,  'Woven Place Mat Set',    'Set of 4 handwoven place mats featuring traditional Rwandan patterns. Adds warmth to any dining table.',                             12000.00, 30, 1, 'images/products/p3.jpg'),
(4,  'Clay Water Pot (Inkono)','Traditional Rwandan clay pot for water storage. Keeps water naturally cool without electricity.',                                    18000.00, 10, 2, 'images/products/p4.jpg'),
(5,  'Decorative Ceramic Bowl','Beautiful painted ceramic bowl, perfect for fruit display or as a centerpiece decoration.',                                           9500.00, 25, 2, 'images/products/p5.jpg'),
(6,  'Clay Candle Holder',     'Handmade clay candle holder with traditional engravings. Creates a warm ambiance in any room.',                                       5500.00, 40, 2, 'images/products/p6.jpg'),
(7,  'Carved Wooden Giraffe',  'Hand-carved wooden giraffe figurine made from local hardwood. A stunning African art piece.',                                        25000.00,  8, 3, 'images/products/p7.jpg'),
(8,  'Wooden Serving Tray',    'Handcrafted wooden tray with beautifully carved border patterns. Functional and decorative.',                                        16000.00, 12, 3, 'images/products/p8.jpg'),
(9,  'Carved Wall Mask',       'Traditional African wall mask, hand-carved from aged hardwood. Unique statement piece for walls.',                                   35000.00,  5, 3, 'images/products/p9.jpg'),
(10, 'Beaded Necklace',        'Colorful handmade beaded necklace with traditional Rwandan patterns. Lightweight and elegant.',                                       8000.00, 50, 4, 'images/products/p10.jpg'),
(11, 'Beaded Bracelet Set',    'Set of 3 matching handmade beaded bracelets in complementary colors. Great gift idea.',                                               5000.00, 60, 4, 'images/products/p11.jpg'),
(12, 'Beaded Earrings',        'Beautiful handmade beaded earrings, lightweight and perfect for everyday wear.',                                                      4000.00, 45, 4, 'images/products/p12.jpg'),
(13, 'Imigongo Wall Art',      'Traditional Rwandan imigongo geometric art on canvas. Striking black and white cow-dung patterns.',                                  45000.00,  7, 5, 'images/products/p13.jpg'),
(14, 'Kitenge Fabric (2m)',    '2 meters of colorful African kitenge fabric. Great for clothing, bags, or home decoration.',                                         12000.00, 35, 5, 'images/products/p14.jpg'),
(15, 'Hand-Painted Scarf',     'Silk scarf hand-painted with Rwandan landscape and bird motifs. A wearable piece of art.',                                          20000.00, 18, 5, 'images/products/p15.jpg');

-- ------------------------------------------------
-- customers
-- ------------------------------------------------
CREATE TABLE `customers` (
  `id`         int(11)      NOT NULL AUTO_INCREMENT,
  `name`       varchar(150) NOT NULL,
  `email`      varchar(150) NOT NULL,
  `phone`      varchar(20)  DEFAULT NULL,
  `address`    text         DEFAULT NULL,
  `created_at` timestamp    NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------
-- orders
-- ------------------------------------------------
CREATE TABLE `orders` (
  `id`               int(11)       NOT NULL AUTO_INCREMENT,
  `user_id`          int(11)       DEFAULT NULL,
  `customer_name`    varchar(150)  NOT NULL,
  `customer_email`   varchar(150)  NOT NULL,
  `customer_phone`   varchar(20)   DEFAULT NULL,
  `customer_address` text          NOT NULL,
  `total_amount`     decimal(10,2) NOT NULL,
  `status`           enum('pending','confirmed','shipped','delivered','cancelled') DEFAULT 'pending',
  `notes`            text          DEFAULT NULL,
  `created_at`       timestamp     NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------
-- order_items
-- ------------------------------------------------
CREATE TABLE `order_items` (
  `id`           int(11)       NOT NULL AUTO_INCREMENT,
  `order_id`     int(11)       NOT NULL,
  `product_id`   int(11)       NOT NULL,
  `product_name` varchar(200)  NOT NULL,
  `quantity`     int(11)       NOT NULL,
  `unit_price`   decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id`   (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`)   REFERENCES `orders`   (`id`),
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
