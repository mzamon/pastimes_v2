-- ============================================================
-- Pastimes — ClothingStore Schema
-- WEDE6021 POE | Complete database with sample data
-- ============================================================

DROP TABLE IF EXISTS tblWishlist, tblReviews, tblMessages, order_items,
                     tblOrders, tblProducts, cart_items, categories,
                     tblSellerRequests, tblUser;

-- ── 1. Users ────────────────────────────────────────────────
CREATE TABLE tblUser (
    id                  INT          AUTO_INCREMENT PRIMARY KEY,
    name                VARCHAR(100) NOT NULL,
    email               VARCHAR(100) NOT NULL UNIQUE,
    password_hash       VARCHAR(255) NOT NULL,
    role                ENUM('buyer','seller','admin') NOT NULL DEFAULT 'buyer',
    is_verified         TINYINT(1)   NOT NULL DEFAULT 0,
    seller_request      ENUM('none','pending','approved','rejected') NOT NULL DEFAULT 'none',
    seller_request_note TEXT         NULL,
    created_at          DATETIME     DEFAULT CURRENT_TIMESTAMP,
    last_login          DATETIME     NULL,
    INDEX idx_email (email),
    INDEX idx_role  (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── 1b. Seller Requests ──────────────────────────────────────
CREATE TABLE tblSellerRequests (
    user_id      INT  NOT NULL,
    motivation   TEXT NULL,
    status       ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    requested_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user (user_id),
    FOREIGN KEY (user_id) REFERENCES tblUser(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── 2. Categories ───────────────────────────────────────────
CREATE TABLE categories (
    id   INT         AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(60) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── 3. Products ─────────────────────────────────────────────
CREATE TABLE tblProducts (
    id          INT           AUTO_INCREMENT PRIMARY KEY,
    seller_id   INT           NOT NULL,
    category_id INT           NOT NULL,
    title       VARCHAR(150)  NOT NULL,
    description TEXT          NOT NULL,
    price       DECIMAL(10,2) NOT NULL CHECK (price > 0),
    `condition` ENUM('New','Like New','Good','Fair','Poor') NOT NULL DEFAULT 'Good',
    image       VARCHAR(255)  NULL,
    quantity    INT           NOT NULL DEFAULT 1,
    status      ENUM('active','sold') NOT NULL DEFAULT 'active',
    created_at  DATETIME      DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id)   REFERENCES tblUser(id)    ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    INDEX idx_status (status),
    FULLTEXT INDEX idx_search (title, description)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── 4. Cart Items ───────────────────────────────────────────
CREATE TABLE cart_items (
    id         INT      AUTO_INCREMENT PRIMARY KEY,
    user_id    INT      NOT NULL,
    product_id INT      NOT NULL,
    quantity   INT      NOT NULL DEFAULT 1 CHECK (quantity > 0),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_cart (user_id, product_id),
    FOREIGN KEY (user_id)    REFERENCES tblUser(id)     ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES tblProducts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── 5. Orders ───────────────────────────────────────────────
CREATE TABLE tblOrders (
    id               INT           AUTO_INCREMENT PRIMARY KEY,
    buyer_id         INT           NOT NULL,
    total            DECIMAL(10,2) NOT NULL,
    delivery_address TEXT          NOT NULL,
    status           ENUM('Pending','Packed','In Transit','Delivered') NOT NULL DEFAULT 'Pending',
    tracking_number  VARCHAR(100)  NULL,
    payment_method   ENUM('Credit Card','Debit Card') NULL,
    created_at       DATETIME      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (buyer_id) REFERENCES tblUser(id) ON DELETE RESTRICT,
    INDEX idx_buyer  (buyer_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── 6. Order Items ──────────────────────────────────────────
CREATE TABLE order_items (
    id                INT           AUTO_INCREMENT PRIMARY KEY,
    order_id          INT           NOT NULL,
    product_id        INT           NOT NULL,
    quantity          INT           NOT NULL CHECK (quantity > 0),
    price_at_purchase DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id)   REFERENCES tblOrders(id)   ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES tblProducts(id) ON DELETE RESTRICT,
    INDEX idx_order (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── 7. Messages ─────────────────────────────────────────────
CREATE TABLE tblMessages (
    id          INT          AUTO_INCREMENT PRIMARY KEY,
    sender_id   INT          NOT NULL,
    receiver_id INT          NOT NULL,
    product_id  INT          NULL,
    message     VARCHAR(1000) NOT NULL,
    sent_at     DATETIME     DEFAULT CURRENT_TIMESTAMP,
    is_read     TINYINT(1)   NOT NULL DEFAULT 0,
    FOREIGN KEY (sender_id)   REFERENCES tblUser(id)     ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES tblUser(id)     ON DELETE CASCADE,
    FOREIGN KEY (product_id)  REFERENCES tblProducts(id) ON DELETE SET NULL,
    INDEX idx_receiver (receiver_id, is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── 8. Reviews ──────────────────────────────────────────────
CREATE TABLE tblReviews (
    id          INT      AUTO_INCREMENT PRIMARY KEY,
    reviewer_id INT      NOT NULL,
    product_id  INT      NOT NULL,
    rating      TINYINT  NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment     TEXT     NOT NULL,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reviewer_id) REFERENCES tblUser(id)     ON DELETE CASCADE,
    FOREIGN KEY (product_id)  REFERENCES tblProducts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── 9. Wishlist ─────────────────────────────────────────────
CREATE TABLE tblWishlist (
    id         INT      AUTO_INCREMENT PRIMARY KEY,
    user_id    INT      NOT NULL,
    product_id INT      NOT NULL,
    added_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_wishlist (user_id, product_id),
    FOREIGN KEY (user_id)    REFERENCES tblUser(id)     ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES tblProducts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SAMPLE DATA
-- Password: password
-- Hash (bcrypt): $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
-- ============================================================

INSERT INTO tblUser (name, email, password_hash, role, is_verified, seller_request) VALUES
('Admin User',   'admin@pastimes.co.za',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin',  1, 'none'),
('John Buyer',   'john@example.com',      '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'buyer',  1, 'none'),
('Sarah Seller', 'sarah@example.com',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'seller', 1, 'approved'),
('Mike Seller',  'mike@example.com',      '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'seller', 1, 'approved'),
('Demo Seller',  'demo@pastimes.co.za',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'seller', 1, 'approved'),
('Demo Buyer',   'buyer@pastimes.co.za',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'buyer',  1, 'none');

INSERT INTO categories (name) VALUES
("Men's Clothing"),
("Women's Clothing"),
('Streetwear'),
('Outerwear'),
("Shoes & Sneakers"),
('Accessories'),
('Vintage'),
('Sportswear');

INSERT INTO tblProducts (seller_id, category_id, title, description, price, `condition`, image, quantity, status) VALUES
(3, 7, 'Vintage Levi''s 501 Jeans',          'Classic straight-cut 501s from the 90s. Size 32x32. Faded wash.', 350.00, 'Good', 'vintage-clothing/denim-jacket-1.jpg', 5, 'active'),
(3, 4, 'Guess Sherpa Denim Jacket',         'Lined denim jacket with sherpa collar. Size M. Barely worn.', 680.00, 'Like New', 'vintage-clothing/leather-jacket-1.jpg', 3, 'active'),
(4, 3, 'Supreme Box Logo Hoodie',            'Black hoodie, size L. Some pilling but rare find.', 950.00, 'Good', 'streetwear/hoodie-black-1.jpg', 2, 'active'),
(4, 5, 'Nike Air Force 1 Low White',         'Classic white AF1s, size 10. Worn 3 times, box included.', 1100.00, 'Like New', 'streetwear/sneakers-hightop-1.jpg', 1, 'active'),
(3, 2, 'Zara Floral Midi Dress',             'Beautiful floral midi dress, size S. Perfect condition.', 420.00, 'Like New', 'vintage-clothing/denim-jacket-2.jpg', 2, 'active'),
(4, 6, 'Leather Belt',                       'Full-grain leather belt, size 34. Barely used.', 180.00, 'Good', 'accessories/leather-belt-1.jpg', 4, 'active'),
(3, 1, 'Ralph Lauren Polo Shirt',            'Navy polo, size L. Minor wear on collar.', 220.00, 'Good', 'vintage-clothing/denim-jacket-1.jpg', 3, 'active'),
(4, 8, 'Adidas Tiro Track Pants',            '3-stripe track pants in black/white, size M.', 190.00, 'Good', 'sports-gear/running-shorts-1.jpg', 5, 'active'),
(3, 3, 'Champion Reverse Weave Sweatshirt',  'Ash grey crewneck, size XL. Some fading.', 310.00, 'Fair', 'streetwear/hoodie-black-1.jpg', 2, 'active'),
(4, 5, 'New Balance 574 Grey',               'Grey/white NB574, size 9. Well-loved.', 480.00, 'Fair', 'sports-gear/gym-tanktop-1.jpg', 1, 'active'),
(3, 4, 'North Face Puffer Jacket',           'Black 700-fill down puffer, size L.', 890.00, 'Good', 'vintage-clothing/leather-jacket-1.jpg', 2, 'active'),
(4, 2, 'H&M Linen Blazer',                  'Sand-coloured linen blazer, size 40.', 340.00, 'Like New', 'vintage-clothing/denim-jacket-2.jpg', 1, 'active'),
(5, 1, 'Tommy Hilfiger Chino Pants',        'Khaki chino, size 32. Classic straight cut.', 260.00, 'Good', 'streetwear/hoodie-black-1.jpg', 3, 'active'),
(5, 7, 'Vintage Lee Riders Jacket',         'Stonewash denim, size L. 1980s authentic.', 520.00, 'Fair', 'vintage-clothing/denim-jacket-1.jpg', 1, 'active'),
(5, 5, 'Converse Chuck Taylor High',        'Red, size 9. Slightly worn.', 380.00, 'Fair', 'streetwear/sneakers-hightop-1.jpg', 2, 'active'),
(5, 2, 'Woolworths Linen Trousers',         'White wide-leg trousers, size 12.', 150.00, 'Good', 'vintage-clothing/denim-jacket-2.jpg', 2, 'active'),
(5, 8, 'Asics Gel-Nimbus 24',               'Size 10. Used for 3 months of running.', 720.00, 'Fair', 'sports-gear/gym-tanktop-1.jpg', 1, 'active'),
(3, 1, 'Polo Ralph Lauren Oxford Shirt',    'Blue stripe, size M. Classic.', 280.00, 'Good', 'vintage-clothing/leather-jacket-1.jpg', 2, 'active'),
(4, 4, 'Stone Island Nylon Jacket',         'Dark navy, size L. Authentic.', 1800.00, 'Good', 'outerwear/puffer-jacket-1.jpg', 1, 'active'),
(3, 7, 'Diesel Regular Denim Jacket',       'Distressed wash, size M.', 430.00, 'Fair', 'vintage-clothing/denim-jacket-2.jpg', 1, 'active'),
(4, 2, 'H&M Knit Cardigan',                'Cream open-front cardigan, size S.', 90.00, 'Like New', 'vintage-clothing/leather-jacket-1.jpg', 3, 'active'),
(3, 3, 'Puma RS-X Sneakers',                'White/blue chunky runner, size 9.', 510.00, 'Good', 'streetwear/sneakers-hightop-1.jpg', 2, 'active'),
(5, 6, 'Michael Kors Tote Bag',             'Black leather tote. Minor pen mark.', 950.00, 'Fair', 'accessories/leather-belt-1.jpg', 1, 'active'),
(4, 1, 'Ben Sherman Mod Shirt',             'Paisley print, size L.', 140.00, 'Good', 'vintage-clothing/denim-jacket-1.jpg', 2, 'active'),
(5, 5, 'Reebok Classic Leather',            'White, size 8.5. Cleaned thoroughly.', 340.00, 'Good', 'streetwear/sneakers-hightop-1.jpg', 2, 'active'),
(3, 5, 'Sperry Topsider Boat Shoes',        'Tan leather, size 10. Worn seaside only.', 420.00, 'Good', 'sports-gear/running-shorts-1.jpg', 1, 'active'),
(4, 4, 'Columbia Fleece Jacket',            'Blue zip-up fleece, size XL.', 290.00, 'Good', 'outerwear/puffer-jacket-1.jpg', 2, 'active'),
(5, 2, 'Topshop Denim Cut-offs',            'Frayed hem, size 10. Festival-ready.', 110.00, 'Good', 'vintage-clothing/denim-jacket-2.jpg', 3, 'active'),
(3, 3, 'Dickies 874 Work Pants',            'Khaki, size 34x32. Classic workwear.', 220.00, 'Like New', 'streetwear/hoodie-black-1.jpg', 2, 'active'),
(4, 8, 'Nike Flex Shorts',                  'Black athletic shorts, size M.', 85.00, 'Good', 'sports-gear/running-shorts-1.jpg', 4, 'active');
