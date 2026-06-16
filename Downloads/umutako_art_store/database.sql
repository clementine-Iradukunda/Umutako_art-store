-- ================================================
--  Umutako Art Store — Database Script
--  Run this file in MySQL Workbench
-- ================================================

CREATE DATABASE IF NOT EXISTS umutako_art_store;
USE umutako_art_store;

-- ------------------------------------------------
-- Table 1: categories
-- ------------------------------------------------
CREATE TABLE IF NOT EXISTS categories (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    description TEXT,
    icon        VARCHAR(10)  DEFAULT '🎁',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------
-- Table 2: products
-- ------------------------------------------------
CREATE TABLE IF NOT EXISTS products (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(200) NOT NULL,
    description TEXT,
    price       DECIMAL(10,2) NOT NULL,
    stock       INT           DEFAULT 0,
    category_id INT,
    image       VARCHAR(255),
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- ------------------------------------------------
-- Table 3: customers
-- ------------------------------------------------
CREATE TABLE IF NOT EXISTS customers (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(150) NOT NULL,
    email      VARCHAR(150) UNIQUE NOT NULL,
    phone      VARCHAR(20),
    address    TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------
-- Table 4: orders
-- ------------------------------------------------
CREATE TABLE IF NOT EXISTS orders (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    customer_name    VARCHAR(150) NOT NULL,
    customer_email   VARCHAR(150) NOT NULL,
    customer_phone   VARCHAR(20),
    customer_address TEXT         NOT NULL,
    total_amount     DECIMAL(10,2) NOT NULL,
    status           ENUM('pending','confirmed','shipped','delivered','cancelled') DEFAULT 'pending',
    notes            TEXT,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------
-- Table 5: order_items
-- ------------------------------------------------
CREATE TABLE IF NOT EXISTS order_items (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    order_id     INT NOT NULL,
    product_id   INT NOT NULL,
    product_name VARCHAR(200) NOT NULL,
    quantity     INT NOT NULL,
    unit_price   DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id)   REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- ================================================
-- SAMPLE DATA
-- ================================================

-- Categories
INSERT INTO categories (name, description, icon) VALUES
('Baskets & Weaving',    'Traditional Rwandan woven baskets and mats',              '🧺'),
('Pottery & Ceramics',   'Hand-crafted clay pots and ceramic items',                '🏺'),
('Wood Carvings',        'Artisan wood-carved sculptures and trays',                '🪵'),
('Jewelry & Accessories','Handmade beaded jewelry and fashion accessories',         '📿'),
('Textiles & Fabrics',   'Imigongo art, kitenge fabrics and painted scarves',       '🎨');

-- Products
INSERT INTO products (name, description, price, stock, category_id) VALUES
-- Baskets
('Agaseke Gift Basket',    'Beautiful traditional Rwandan agaseke basket, handwoven with fine sisal and raffia. Perfect as a gift or decoration for any home.', 15000, 20, 1),
('Large Market Basket',    'Spacious handwoven basket ideal for carrying goods at the market. Durable with vibrant geometric patterns.', 22000, 15, 1),
('Woven Place Mat Set',    'Set of 4 handwoven place mats featuring traditional Rwandan patterns. Adds warmth to any dining table.', 12000, 30, 1),
-- Pottery
('Clay Water Pot (Inkono)','Traditional Rwandan clay pot for water storage. Keeps water naturally cool without electricity.', 18000, 10, 2),
('Decorative Ceramic Bowl','Beautiful painted ceramic bowl, perfect for fruit display or as a centerpiece decoration.', 9500, 25, 2),
('Clay Candle Holder',     'Handmade clay candle holder with traditional engravings. Creates a warm ambiance in any room.', 5500, 40, 2),
-- Wood Carvings
('Carved Wooden Giraffe',  'Hand-carved wooden giraffe figurine made from local hardwood. A stunning African art piece.', 25000, 8, 3),
('Wooden Serving Tray',    'Handcrafted wooden tray with beautifully carved border patterns. Functional and decorative.', 16000, 12, 3),
('Carved Wall Mask',       'Traditional African wall mask, hand-carved from aged hardwood. Unique statement piece for walls.', 35000, 5, 3),
-- Jewelry
('Beaded Necklace',        'Colorful handmade beaded necklace with traditional Rwandan patterns. Lightweight and elegant.', 8000, 50, 4),
('Beaded Bracelet Set',    'Set of 3 matching handmade beaded bracelets in complementary colors. Great gift idea.', 5000, 60, 4),
('Beaded Earrings',        'Beautiful handmade beaded earrings, lightweight and perfect for everyday wear.', 4000, 45, 4),
-- Textiles
('Imigongo Wall Art',      'Traditional Rwandan imigongo geometric art on canvas. Striking black and white cow-dung patterns.', 45000, 7, 5),
('Kitenge Fabric (2m)',    '2 meters of colorful African kitenge fabric. Great for clothing, bags, or home decoration.', 12000, 35, 5),
('Hand-Painted Scarf',     'Silk scarf hand-painted with Rwandan landscape and bird motifs. A wearable piece of art.', 20000, 18, 5);

-- ================================================
-- Verify data was inserted correctly
-- ================================================
SELECT 'Categories inserted:' AS check_info;
SELECT id, name, icon FROM categories;

SELECT 'Products inserted:' AS check_info;
SELECT p.id, p.name, p.price, p.stock, c.name AS category
FROM products p JOIN categories c ON p.category_id = c.id
ORDER BY c.id, p.id;
