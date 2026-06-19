-- ============================================================
-- Pastimes — ClothingStore Schema (Complete)
-- WEDE6021 POE | Mzamo Ndlovu | June 2026
-- Password for all accounts: Kookemooi10!
-- ============================================================

CREATE DATABASE IF NOT EXISTS ClothingStore
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE ClothingStore;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS tblWishlist, tblReviews, tblMessages, order_items,
                     tblOrders, tblProducts, cart_items, categories,
                     tblSellerRequests, tblUser;
SET FOREIGN_KEY_CHECKS = 1;

-- ── tblUser ──────────────────────────────────────────────────
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

-- ── tblSellerRequests ──────────────────────────────────────
CREATE TABLE tblSellerRequests (
    user_id      INT  NOT NULL,
    motivation   TEXT NULL,
    status       ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    requested_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user (user_id),
    FOREIGN KEY (user_id) REFERENCES tblUser(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── categories ──────────────────────────────────────────────
CREATE TABLE categories (
    id   INT         AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(60) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── tblProducts ─────────────────────────────────────────────
CREATE TABLE tblProducts (
    id          INT           AUTO_INCREMENT PRIMARY KEY,
    seller_id   INT           NOT NULL,
    category_id INT           NOT NULL,
    title       VARCHAR(150)  NOT NULL,
    brand       VARCHAR(100)  NULL,
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

-- ── cart_items ──────────────────────────────────────────────
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

-- ── tblOrders ───────────────────────────────────────────────
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

-- ── order_items ─────────────────────────────────────────────
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

-- ── tblMessages ─────────────────────────────────────────────
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

-- ── tblReviews ──────────────────────────────────────────────
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

-- ── tblWishlist ─────────────────────────────────────────────
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
-- SAMPLE DATA (all passwords = Kookemooi10!)
-- Replace {{BCRYPT_HASH}} with your generated hash
-- ============================================================

INSERT INTO tblUser (name, email, password_hash, role, is_verified, seller_request, seller_request_note) VALUES
('Admin User',   'admin@pastimes.co.za',  '{{BCRYPT_HASH}}', 'admin',  1, 'none',     NULL),
('Koos Kookemooi', 'koos@gmail.com',       '{{BCRYPT_HASH}}', 'buyer',  1, 'none',     NULL),
('Sarah Seller', 'sarah@example.com',     '{{BCRYPT_HASH}}', 'seller', 1, 'approved', 'Vintage clothing and outerwear'),
('Mike Seller',  'mike@example.com',      '{{BCRYPT_HASH}}', 'seller', 1, 'approved', 'Streetwear and branded sneakers'),
('Demo Seller',  'demo@pastimes.co.za',   '{{BCRYPT_HASH}}', 'seller', 1, 'approved', 'Demo seller account'),
('Demo Buyer',   'buyer@pastimes.co.za',  '{{BCRYPT_HASH}}', 'buyer',  1, 'none',     NULL);

INSERT INTO categories (name) VALUES
("Men's Clothing"),
("Women's Clothing"),
('Streetwear'),
('Outerwear'),
("Shoes & Sneakers"),
('Accessories'),
('Vintage'),
('Sportswear');

INSERT INTO tblProducts (seller_id, category_id, title, brand, description, price, `condition`, image, quantity, status) VALUES
(3, 7, 'Vintage Levi\'s 501 Jeans',          'Levi\'s',      'Classic straight-cut 501s from the 90s. Size 32x32. Faded wash.', 350.00, 'Good',     'vintage-clothing/denim-jacket-1.jpg',   1, 'active'),
(3, 4, 'Guess Sherpa Denim Jacket',          'Guess',        'Lined denim jacket with sherpa collar. Size M. Barely worn.',          680.00, 'Like New', 'vintage-clothing/leather-jacket-1.jpg', 1, 'active'),
(4, 3, 'Supreme Box Logo Hoodie',            'Supreme',      'Black hoodie, size L. Some pilling but rare.',                         950.00, 'Good',     'streetwear/hoodie-black-1.jpg',         1, 'active'),
(4, 5, 'Nike Air Force 1 Low White',         'Nike',         'Classic white AF1s, size 10. Worn 3 times.',                          1100.00, 'Like New', 'streetwear/sneakers-hightop-1.jpg',     1, 'active'),
(3, 2, 'Zara Floral Midi Dress',             'Zara',         'Beautiful floral midi dress, size S. Perfect condition.',               420.00, 'Like New', 'vintage-clothing/denim-jacket-2.jpg',   1, 'active'),
(4, 6, 'Woolworths Genuine Leather Belt',    'Woolworths',   'Full-grain leather belt, size 34. Brown with brass buckle.',            180.00, 'Good',     'accessories/leather-belt-1.jpg',        2, 'active'),
(3, 1, 'Ralph Lauren Polo Shirt',            'Ralph Lauren', 'Navy polo, size L. Minor wear on collar.',                              220.00, 'Good',     'vintage-clothing/denim-jacket-1.jpg',   1, 'active'),
(4, 8, 'Adidas Tiro Track Pants',            'Adidas',       '3-stripe track pants in black/white, size M.',                          190.00, 'Good',     'sports-gear/running-shorts-1.jpg',      2, 'active'),
(3, 3, 'Champion Reverse Weave Sweatshirt',  'Champion',     'Ash grey crewneck, size XL. Some fading.',                              310.00, 'Fair',     'streetwear/hoodie-black-1.jpg',         1, 'active'),
(4, 5, 'New Balance 574 Grey',               'New Balance',  'Grey/white NB574, size 9. Well-loved.',                                 480.00, 'Fair',     'sports-gear/gym-tanktop-1.jpg',         1, 'active'),
(3, 4, 'North Face Puffer Jacket',           'North Face',   'Black 700-fill down puffer, size L.',                                   890.00, 'Good',     'vintage-clothing/leather-jacket-1.jpg', 1, 'active'),
(4, 2, 'H&M Linen Blazer',                   'H&M',          'Sand-coloured linen blazer, size 40.',                                  340.00, 'Like New', 'vintage-clothing/denim-jacket-2.jpg',   1, 'active'),
(5, 1, 'Tommy Hilfiger Chino Pants',         'Tommy Hilfiger','Khaki chino pants, size 32x30.',                                      260.00, 'Good',     'streetwear/hoodie-black-1.jpg',         2, 'active'),
(5, 7, 'Vintage Lee Riders Jacket',          'Lee',          'Stonewash denim jacket, size L. 1980s.',                                520.00, 'Fair',     'vintage-clothing/denim-jacket-1.jpg',   1, 'active'),
(5, 5, 'Converse Chuck Taylor High',         'Converse',     'Red high-top sneakers, size 9.',                                        380.00, 'Fair',     'streetwear/sneakers-hightop-1.jpg',     2, 'active'),
(5, 2, 'Woolworths Linen Trousers',          'Woolworths',   'White wide-leg linen trousers, size 12.',                                150.00, 'Good',     'vintage-clothing/denim-jacket-2.jpg',   2, 'active'),
(5, 8, 'Asics Gel-Nimbus 24',                'Asics',        'Running shoes, size 10. Used for 3 months.',                            720.00, 'Fair',     'sports-gear/gym-tanktop-1.jpg',         1, 'active'),
(3, 1, 'Polo Ralph Lauren Oxford Shirt',     'Ralph Lauren', 'Blue stripe Oxford shirt, size M.',                                     280.00, 'Good',     'vintage-clothing/leather-jacket-1.jpg', 2, 'active'),
(4, 4, 'Stone Island Nylon Jacket',          'Stone Island', 'Dark navy nylon jacket, size L. Authentic.',                           1800.00, 'Good',     'outerwear/puffer-jacket-1.jpg',         1, 'active'),
(3, 7, 'Diesel Regular Denim Jacket',        'Diesel',       'Distressed wash denim jacket, size M.',                                 430.00, 'Fair',     'vintage-clothing/denim-jacket-2.jpg',   1, 'active'),
(4, 2, 'H&M Knit Cardigan',                  'H&M',          'Cream open-front cardigan, size S.',                                    90.00, 'Like New', 'vintage-clothing/leather-jacket-1.jpg', 3, 'active'),
(3, 3, 'Puma RS-X Sneakers',                 'Puma',         'White/blue chunky runner, size 9.',                                     510.00, 'Good',     'streetwear/sneakers-hightop-1.jpg',     2, 'active'),
(5, 6, 'Michael Kors Tote Bag',              'Michael Kors', 'Black leather tote bag. Minor pen mark.',                              950.00, 'Fair',     'accessories/leather-belt-1.jpg',        1, 'active'),
(4, 1, 'Ben Sherman Mod Shirt',              'Ben Sherman',  'Paisley print shirt, size L.',                                         140.00, 'Good',     'vintage-clothing/denim-jacket-1.jpg',   2, 'active'),
(5, 5, 'Reebok Classic Leather',             'Reebok',       'White classic leather trainers, size 8.5.',                            340.00, 'Good',     'streetwear/sneakers-hightop-1.jpg',     2, 'active'),
(3, 5, 'Sperry Topsider Boat Shoes',         'Sperry',       'Tan leather boat shoes, size 10.',                                     420.00, 'Good',     'sports-gear/running-shorts-1.jpg',      1, 'active'),
(4, 4, 'Columbia Fleece Jacket',             'Columbia',     'Blue zip-up fleece jacket, size XL.',                                  290.00, 'Good',     'outerwear/puffer-jacket-1.jpg',         2, 'active'),
(5, 2, 'Topshop Denim Cut-offs',             'Topshop',      'Frayed hem denim cut-offs, size 10.',                                  110.00, 'Good',     'vintage-clothing/denim-jacket-2.jpg',   3, 'active'),
(3, 3, 'Dickies 874 Work Pants',             'Dickies',      'Khaki work pants, size 34x32.',                                        220.00, 'Like New', 'streetwear/hoodie-black-1.jpg',         2, 'active'),
(4, 8, 'Nike Flex Shorts',                   'Nike',         'Black athletic shorts, size M.',                                        85.00, 'Good',     'sports-gear/running-shorts-1.jpg',      4, 'active');

INSERT INTO tblOrders (buyer_id, total, delivery_address, status, tracking_number, payment_method) VALUES
(2, 450.00,  '123 Main Street, Johannesburg, 2000', 'Delivered',  'TRK000001', 'Credit Card'),
(2, 1150.00, '45 Park Avenue, Cape Town, 8001',     'In Transit', 'TRK000002', 'Debit Card');

INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES
(1, 1, 1, 350.00),
(1, 8, 1, 190.00),
(2, 4, 1, 1100.00);

INSERT INTO tblMessages (sender_id, receiver_id, product_id, message, is_read) VALUES
(2, 3, 1, 'Hi Sarah, are the Levi\'s still available?',   1),
(3, 2, 1, 'Yes! Still available. Would you like photos?',  1),
(2, 3, 1, 'That would be great, thank you!',               0);

INSERT INTO tblReviews (reviewer_id, product_id, rating, comment) VALUES
(2, 1, 5, 'Exactly as described! Arrived quickly and packaged well. Very happy.'),
(2, 8, 4, 'Good condition, fast delivery. Slight pilling but expected for the price.');

INSERT INTO tblSellerRequests (user_id, motivation, status) VALUES
(3, 'Vintage clothing and outerwear', 'approved'),
(4, 'Streetwear and branded sneakers', 'approved');

INSERT INTO tblWishlist (user_id, product_id) VALUES (2, 3), (2, 11);