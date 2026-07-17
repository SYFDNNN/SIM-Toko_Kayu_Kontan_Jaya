-- =============================================================
-- Migration 001: Create users table
-- =============================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id`         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100)     NOT NULL,
  `email`      VARCHAR(150)     NOT NULL UNIQUE,
  `password`   VARCHAR(255)     NOT NULL,  -- bcrypt hash
  `role`       ENUM('owner','admin','cashier') NOT NULL DEFAULT 'cashier',
  `is_active`  TINYINT(1)       NOT NULL DEFAULT 1,
  `created_at` DATETIME         NULL,
  `updated_at` DATETIME         NULL,
  `deleted_at` DATETIME         NULL,      -- soft delete
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- Migration 002: Create categories table
-- =============================================================
CREATE TABLE IF NOT EXISTS `categories` (
  `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100)  NOT NULL UNIQUE,
  `slug`       VARCHAR(120)  NOT NULL UNIQUE,
  `description` TEXT         NULL,
  `created_at` DATETIME      NULL,
  `updated_at` DATETIME      NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- Migration 003: Create products table
-- =============================================================
CREATE TABLE IF NOT EXISTS `products` (
  `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `category_id`   INT UNSIGNED  NOT NULL,
  `sku`           VARCHAR(50)   NOT NULL UNIQUE,
  `name`          VARCHAR(200)  NOT NULL,
  `description`   TEXT          NULL,
  `cost_price`    DECIMAL(15,2) NOT NULL DEFAULT 0.00,   -- Harga beli
  `selling_price` DECIMAL(15,2) NOT NULL DEFAULT 0.00,   -- Harga jual
  `stock`         INT           NOT NULL DEFAULT 0,
  `stock_minimum` INT           NOT NULL DEFAULT 5,       -- Untuk notifikasi low stock
  `lead_time_days` INT          NOT NULL DEFAULT 3,       -- Untuk kalkulasi ROP
  `unit`          VARCHAR(20)   NOT NULL DEFAULT 'pcs',
  `image`         VARCHAR(255)  NULL,                     -- Path gambar utama
  `is_active`     TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`    DATETIME      NULL,
  `updated_at`    DATETIME      NULL,
  `deleted_at`    DATETIME      NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_products_category` (`category_id`),
  INDEX `idx_products_sku` (`sku`),
  CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`)
    REFERENCES `categories`(`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- Migration 004: Create suppliers table (opsional)
-- =============================================================
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(150) NOT NULL,
  `phone`       VARCHAR(20)  NULL,
  `email`       VARCHAR(150) NULL,
  `address`     TEXT         NULL,
  `created_at`  DATETIME     NULL,
  `updated_at`  DATETIME     NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- Migration 005: Create transactions table (header)
-- =============================================================
CREATE TABLE IF NOT EXISTS `transactions` (
  `id`              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `transaction_no`  VARCHAR(30)     NOT NULL UNIQUE,   -- e.g. TRX-20240115-0001
  `user_id`         INT UNSIGNED    NOT NULL,           -- kasir yang memproses
  `customer_name`   VARCHAR(100)    NULL,               -- nama pelanggan (opsional)
  `subtotal`        DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
  `discount`        DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
  `total`           DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
  `payment_method`  ENUM('cash','transfer','other') NOT NULL DEFAULT 'cash',
  `payment_amount`  DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
  `change_amount`   DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
  `notes`           TEXT            NULL,
  `status`          ENUM('pending','completed','cancelled') NOT NULL DEFAULT 'completed',
  `transaction_date` DATETIME       NOT NULL,
  `created_at`      DATETIME        NULL,
  `updated_at`      DATETIME        NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_transactions_user` (`user_id`),
  INDEX `idx_transactions_date` (`transaction_date`),
  CONSTRAINT `fk_transactions_user` FOREIGN KEY (`user_id`)
    REFERENCES `users`(`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- Migration 006: Create transaction_details table
-- =============================================================
CREATE TABLE IF NOT EXISTS `transaction_details` (
  `id`             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `transaction_id` INT UNSIGNED  NOT NULL,
  `product_id`     INT UNSIGNED  NOT NULL,
  `quantity`       INT           NOT NULL,
  `unit_price`     DECIMAL(15,2) NOT NULL,   -- harga saat transaksi (snapshot)
  `subtotal`       DECIMAL(15,2) NOT NULL,   -- qty × unit_price
  PRIMARY KEY (`id`),
  INDEX `idx_td_transaction` (`transaction_id`),
  INDEX `idx_td_product` (`product_id`),
  CONSTRAINT `fk_td_transaction` FOREIGN KEY (`transaction_id`)
    REFERENCES `transactions`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_td_product` FOREIGN KEY (`product_id`)
    REFERENCES `products`(`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- Migration 007: Create stock_movements table (audit trail)
-- =============================================================
CREATE TABLE IF NOT EXISTS `stock_movements` (
  `id`              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `product_id`      INT UNSIGNED  NOT NULL,
  `user_id`         INT UNSIGNED  NOT NULL,
  `type`            ENUM('IN','OUT','ADJUSTMENT') NOT NULL,
  `quantity`        INT           NOT NULL,              -- positif untuk IN, negatif untuk OUT
  `stock_before`    INT           NOT NULL,
  `stock_after`     INT           NOT NULL,
  `reference_type`  VARCHAR(50)   NULL,                 -- 'transaction', 'purchase_order', 'manual'
  `reference_id`    INT UNSIGNED  NULL,                 -- ID transaksi atau PO
  `reference_no`    VARCHAR(50)   NULL,                 -- Nomor referensi (TRX-xxx / PO-xxx)
  `notes`           TEXT          NULL,
  `created_at`      DATETIME      NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_sm_product` (`product_id`),
  INDEX `idx_sm_type` (`type`),
  CONSTRAINT `fk_sm_product` FOREIGN KEY (`product_id`)
    REFERENCES `products`(`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_sm_user` FOREIGN KEY (`user_id`)
    REFERENCES `users`(`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- Migration 008: Create product_images table
-- =============================================================
CREATE TABLE IF NOT EXISTS `product_images` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `is_primary` TINYINT(1)   NOT NULL DEFAULT 0,
  `sort_order` INT          NOT NULL DEFAULT 0,
  `created_at` DATETIME     NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_pi_product` FOREIGN KEY (`product_id`)
    REFERENCES `products`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- Migration 009: Create settings table
-- =============================================================
CREATE TABLE IF NOT EXISTS `settings` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `key`        VARCHAR(100) NOT NULL UNIQUE,
  `value`      TEXT         NULL,
  `created_at` DATETIME     NULL,
  `updated_at` DATETIME     NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default settings
INSERT INTO `settings` (`key`, `value`, `created_at`, `updated_at`) VALUES
('store_name',    'Toko Kayu Kontan Jaya', NOW(), NOW()),
('store_address', 'Jl. Raya Mebel No. 1, Kota', NOW(), NOW()),
('store_phone',   '08123456789', NOW(), NOW()),
('store_email',   'info@tokokayukontan.com', NOW(), NOW()),
('currency',      'IDR', NOW(), NOW()),
('lead_time_days','3', NOW(), NOW()),
('low_stock_alert','1', NOW(), NOW());

-- =============================================================
-- Migration 010: Create audit_logs table
-- =============================================================
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     INT UNSIGNED NULL,
  `action`      VARCHAR(100) NOT NULL,   -- e.g. 'product.create', 'transaction.checkout'
  `table_name`  VARCHAR(50)  NULL,
  `record_id`   INT UNSIGNED NULL,
  `old_values`  JSON         NULL,
  `new_values`  JSON         NULL,
  `ip_address`  VARCHAR(45)  NULL,
  `user_agent`  TEXT         NULL,
  `created_at`  DATETIME     NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_al_user` (`user_id`),
  INDEX `idx_al_action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
