-- =============================================================
-- SEED DATA — Toko Kayu Kontan Jaya
-- 3 users, 5 categories, 10 products, 15 transactions
-- Jalankan SETELAH all_migrations.sql
-- =============================================================

-- ---------------------------------------------------------------
-- USERS (password: "password123" di-hash dengan bcrypt)
-- ---------------------------------------------------------------
INSERT INTO `users` (`id`,`name`,`email`,`password`,`role`,`is_active`,`created_at`,`updated_at`) VALUES
(1,'Budi Santoso','owner@tokokayukontan.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','owner',1,NOW(),NOW()),
(2,'Siti Rahayu','kasir1@tokokayukontan.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','cashier',1,NOW(),NOW()),
(3,'Ahmad Fauzi','kasir2@tokokayukontan.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','cashier',1,NOW(),NOW());

-- ---------------------------------------------------------------
-- CATEGORIES
-- ---------------------------------------------------------------
INSERT INTO `categories` (`id`,`name`,`slug`,`description`,`created_at`,`updated_at`) VALUES
(1,'Meja','meja','Berbagai jenis meja kayu',NOW(),NOW()),
(2,'Kursi','kursi','Kursi kayu jati dan mahoni',NOW(),NOW()),
(3,'Lemari','lemari','Lemari pakaian dan rak buku',NOW(),NOW()),
(4,'Tempat Tidur','tempat-tidur','Dipan dan set kamar tidur',NOW(),NOW()),
(5,'Aksesori','aksesori','Aksesori dan furnitur kecil',NOW(),NOW());

-- ---------------------------------------------------------------
-- PRODUCTS (10 produk)
-- ---------------------------------------------------------------
INSERT INTO `products`
  (`id`,`category_id`,`sku`,`name`,`description`,`cost_price`,`selling_price`,`stock`,`stock_minimum`,`lead_time_days`,`unit`,`is_active`,`created_at`,`updated_at`) VALUES
(1, 1,'MJK-001','Meja Kerja Jati Minimalis','Meja kerja kayu jati solid, ukuran 120x60x75cm',850000,1350000,12,3,5,'pcs',1,NOW(),NOW()),
(2, 1,'MJM-002','Meja Makan 6 Kursi Mahoni','Set meja makan kayu mahoni, finishing natural',3200000,4800000,4,2,7,'set',1,NOW(),NOW()),
(3, 2,'KRS-001','Kursi Santai Rotan Kombinasi','Kursi santai rangka kayu + anyaman rotan',450000,750000,20,5,3,'pcs',1,NOW(),NOW()),
(4, 2,'KJT-002','Kursi Jati Antik Ukir','Kursi kayu jati ukiran tangan, motif bunga',980000,1650000,6,3,5,'pcs',1,NOW(),NOW()),
(5, 3,'LMR-001','Lemari Pakaian 3 Pintu Pinus','Lemari 3 pintu kayu pinus, warna natural',1750000,2600000,3,2,7,'pcs',1,NOW(),NOW()),
(6, 3,'RBK-002','Rak Buku 5 Susun Jati','Rak buku kayu jati, 5 tingkat adjustable',620000,980000,8,3,5,'pcs',1,NOW(),NOW()),
(7, 4,'TPT-001','Tempat Tidur Kayu Jati Single','Dipan single 90x200cm, kayu jati solid',1200000,1900000,5,2,7,'pcs',1,NOW(),NOW()),
(8, 4,'TPT-002','Set Kamar Tidur Mahoni Queen','Dipan queen + 2 nakas + lemari, mahoni',5800000,8500000,2,1,14,'set',1,NOW(),NOW()),
(9, 5,'AKS-001','Nakas Kayu Mahoni','Nakas/meja samping tempat tidur, 1 laci',280000,450000,15,5,3,'pcs',1,NOW(),NOW()),
(10,5,'AKS-002','Cermin Kayu Bingkai Jati','Cermin dinding frame kayu jati ukiran',380000,620000,2,3,5,'pcs',1,NOW(),NOW());

-- ---------------------------------------------------------------
-- TRANSACTIONS (15 transaksi, 30 hari terakhir)
-- ---------------------------------------------------------------
INSERT INTO `transactions`
  (`id`,`transaction_no`,`user_id`,`customer_name`,`subtotal`,`discount`,`total`,`payment_method`,`payment_amount`,`change_amount`,`status`,`transaction_date`,`created_at`,`updated_at`) VALUES
(1, 'TRX-20240101-0001',2,'Pak Hendra',1350000,0,1350000,'cash',1500000,150000,'completed',DATE_SUB(NOW(),INTERVAL 28 DAY),DATE_SUB(NOW(),INTERVAL 28 DAY),DATE_SUB(NOW(),INTERVAL 28 DAY)),
(2, 'TRX-20240103-0002',2,'Bu Wati',750000,0,750000,'transfer',750000,0,'completed',DATE_SUB(NOW(),INTERVAL 26 DAY),DATE_SUB(NOW(),INTERVAL 26 DAY),DATE_SUB(NOW(),INTERVAL 26 DAY)),
(3, 'TRX-20240105-0003',3,'Pak Doni',4800000,200000,4600000,'cash',5000000,400000,'completed',DATE_SUB(NOW(),INTERVAL 24 DAY),DATE_SUB(NOW(),INTERVAL 24 DAY),DATE_SUB(NOW(),INTERVAL 24 DAY)),
(4, 'TRX-20240107-0004',2,NULL,1650000,0,1650000,'cash',2000000,350000,'completed',DATE_SUB(NOW(),INTERVAL 22 DAY),DATE_SUB(NOW(),INTERVAL 22 DAY),DATE_SUB(NOW(),INTERVAL 22 DAY)),
(5, 'TRX-20240109-0005',2,'Ibu Sari',2600000,0,2600000,'transfer',2600000,0,'completed',DATE_SUB(NOW(),INTERVAL 20 DAY),DATE_SUB(NOW(),INTERVAL 20 DAY),DATE_SUB(NOW(),INTERVAL 20 DAY)),
(6, 'TRX-20240111-0006',3,'Pak Bimo',980000,0,980000,'cash',1000000,20000,'completed',DATE_SUB(NOW(),INTERVAL 18 DAY),DATE_SUB(NOW(),INTERVAL 18 DAY),DATE_SUB(NOW(),INTERVAL 18 DAY)),
(7, 'TRX-20240113-0007',2,'Toko Jaya',8500000,500000,8000000,'transfer',8000000,0,'completed',DATE_SUB(NOW(),INTERVAL 16 DAY),DATE_SUB(NOW(),INTERVAL 16 DAY),DATE_SUB(NOW(),INTERVAL 16 DAY)),
(8, 'TRX-20240115-0008',2,NULL,450000,0,450000,'cash',500000,50000,'completed',DATE_SUB(NOW(),INTERVAL 14 DAY),DATE_SUB(NOW(),INTERVAL 14 DAY),DATE_SUB(NOW(),INTERVAL 14 DAY)),
(9, 'TRX-20240117-0009',3,'Bu Lina',1900000,0,1900000,'cash',2000000,100000,'completed',DATE_SUB(NOW(),INTERVAL 12 DAY),DATE_SUB(NOW(),INTERVAL 12 DAY),DATE_SUB(NOW(),INTERVAL 12 DAY)),
(10,'TRX-20240119-0010',2,'Pak Yusuf',750000,0,750000,'transfer',750000,0,'completed',DATE_SUB(NOW(),INTERVAL 10 DAY),DATE_SUB(NOW(),INTERVAL 10 DAY),DATE_SUB(NOW(),INTERVAL 10 DAY)),
(11,'TRX-20240121-0011',2,NULL,620000,0,620000,'cash',700000,80000,'completed',DATE_SUB(NOW(),INTERVAL 8 DAY),DATE_SUB(NOW(),INTERVAL 8 DAY),DATE_SUB(NOW(),INTERVAL 8 DAY)),
(12,'TRX-20240123-0012',3,'Bu Rina',1350000,0,1350000,'cash',1350000,0,'completed',DATE_SUB(NOW(),INTERVAL 6 DAY),DATE_SUB(NOW(),INTERVAL 6 DAY),DATE_SUB(NOW(),INTERVAL 6 DAY)),
(13,'TRX-20240125-0013',2,'Pak Fajar',2250000,0,2250000,'transfer',2250000,0,'completed',DATE_SUB(NOW(),INTERVAL 4 DAY),DATE_SUB(NOW(),INTERVAL 4 DAY),DATE_SUB(NOW(),INTERVAL 4 DAY)),
(14,'TRX-20240127-0014',2,NULL,450000,0,450000,'cash',500000,50000,'completed',DATE_SUB(NOW(),INTERVAL 2 DAY),DATE_SUB(NOW(),INTERVAL 2 DAY),DATE_SUB(NOW(),INTERVAL 2 DAY)),
(15,'TRX-20240129-0015',3,'Bu Dewi',1650000,0,1650000,'cash',2000000,350000,'completed',DATE_SUB(NOW(),INTERVAL 1 DAY),DATE_SUB(NOW(),INTERVAL 1 DAY),DATE_SUB(NOW(),INTERVAL 1 DAY));

-- ---------------------------------------------------------------
-- TRANSACTION DETAILS
-- ---------------------------------------------------------------
INSERT INTO `transaction_details` (`transaction_id`,`product_id`,`quantity`,`unit_price`,`subtotal`) VALUES
(1, 1,1,1350000,1350000),
(2, 3,1,750000,750000),
(3, 2,1,4800000,4800000),
(4, 4,1,1650000,1650000),
(5, 5,1,2600000,2600000),
(6, 6,1,980000,980000),
(7, 8,1,8500000,8500000),
(8, 3,1,450000,450000),
(9, 7,1,1900000,1900000),
(10,3,1,750000,750000),
(11,6,1,620000,620000),
(12,1,1,1350000,1350000),
(13,1,1,1350000,1350000),(13,9,2,450000,900000),
(14,3,1,450000,450000),
(15,4,1,1650000,1650000);

-- ---------------------------------------------------------------
-- STOCK MOVEMENTS (corresponding to transactions above)
-- ---------------------------------------------------------------
INSERT INTO `stock_movements`
  (`product_id`,`user_id`,`type`,`quantity`,`stock_before`,`stock_after`,`reference_type`,`reference_id`,`reference_no`,`notes`,`created_at`) VALUES
-- Stok awal (IN)
(1,1,'IN',15,0,15,'manual',NULL,NULL,'Stok awal',DATE_SUB(NOW(),INTERVAL 60 DAY)),
(2,1,'IN',6,0,6,'manual',NULL,NULL,'Stok awal',DATE_SUB(NOW(),INTERVAL 60 DAY)),
(3,1,'IN',25,0,25,'manual',NULL,NULL,'Stok awal',DATE_SUB(NOW(),INTERVAL 60 DAY)),
(4,1,'IN',10,0,10,'manual',NULL,NULL,'Stok awal',DATE_SUB(NOW(),INTERVAL 60 DAY)),
(5,1,'IN',5,0,5,'manual',NULL,NULL,'Stok awal',DATE_SUB(NOW(),INTERVAL 60 DAY)),
(6,1,'IN',12,0,12,'manual',NULL,NULL,'Stok awal',DATE_SUB(NOW(),INTERVAL 60 DAY)),
(7,1,'IN',7,0,7,'manual',NULL,NULL,'Stok awal',DATE_SUB(NOW(),INTERVAL 60 DAY)),
(8,1,'IN',4,0,4,'manual',NULL,NULL,'Stok awal',DATE_SUB(NOW(),INTERVAL 60 DAY)),
(9,1,'IN',20,0,20,'manual',NULL,NULL,'Stok awal',DATE_SUB(NOW(),INTERVAL 60 DAY)),
(10,1,'IN',5,0,5,'manual',NULL,NULL,'Stok awal',DATE_SUB(NOW(),INTERVAL 60 DAY)),
-- OUT dari transaksi
(1,2,'OUT',-1,14,13,'transaction',1,'TRX-20240101-0001','POS Checkout',DATE_SUB(NOW(),INTERVAL 28 DAY)),
(3,2,'OUT',-1,24,23,'transaction',2,'TRX-20240103-0002','POS Checkout',DATE_SUB(NOW(),INTERVAL 26 DAY)),
(2,3,'OUT',-1,5,4,'transaction',3,'TRX-20240105-0003','POS Checkout',DATE_SUB(NOW(),INTERVAL 24 DAY)),
(4,2,'OUT',-1,9,8,'transaction',4,'TRX-20240107-0004','POS Checkout',DATE_SUB(NOW(),INTERVAL 22 DAY)),
(5,2,'OUT',-1,4,3,'transaction',5,'TRX-20240109-0005','POS Checkout',DATE_SUB(NOW(),INTERVAL 20 DAY)),
(6,3,'OUT',-1,11,10,'transaction',6,'TRX-20240111-0006','POS Checkout',DATE_SUB(NOW(),INTERVAL 18 DAY)),
(8,2,'OUT',-1,3,2,'transaction',7,'TRX-20240113-0007','POS Checkout',DATE_SUB(NOW(),INTERVAL 16 DAY)),
(3,2,'OUT',-1,22,21,'transaction',8,'TRX-20240115-0008','POS Checkout',DATE_SUB(NOW(),INTERVAL 14 DAY)),
(7,3,'OUT',-1,6,5,'transaction',9,'TRX-20240117-0009','POS Checkout',DATE_SUB(NOW(),INTERVAL 12 DAY)),
(3,2,'OUT',-1,21,20,'transaction',10,'TRX-20240119-0010','POS Checkout',DATE_SUB(NOW(),INTERVAL 10 DAY));
