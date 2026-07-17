# API Documentation — Toko Kayu Kontan Jaya

Base URL: `http://localhost/toko-kayu-kontan-jaya/public/api`

Semua endpoint API memerlukan autentikasi sesi (login via `/login` terlebih dahulu).
Response format: `application/json`

---

## Authentication

### POST /login
Login dan buat sesi.

**Request:**
```json
{ "email": "owner@tokokayukontan.com", "password": "password123" }
```

**Response 200:**
```json
{ "success": true, "user": { "id": 1, "name": "Budi Santoso", "role": "owner" } }
```

---

## Products

### GET /api/products
Daftar produk dengan paginasi.

**Query params:** `?page=1&limit=15&search=meja&category_id=1`

**Response 200:**
```json
{
  "data": [
    {
      "id": 1,
      "sku": "MJK-001",
      "name": "Meja Kerja Jati Minimalis",
      "category_name": "Meja",
      "cost_price": 850000,
      "selling_price": 1350000,
      "stock": 12,
      "stock_minimum": 3,
      "unit": "pcs",
      "is_active": 1
    }
  ],
  "meta": { "total": 10, "per_page": 15, "current_page": 1 }
}
```

### GET /api/products/:id
Detail satu produk.

**Response 200:**
```json
{
  "data": {
    "id": 1,
    "sku": "MJK-001",
    "name": "Meja Kerja Jati Minimalis",
    "category_id": 1,
    "category_name": "Meja",
    "cost_price": 850000,
    "selling_price": 1350000,
    "stock": 12,
    "stock_minimum": 3,
    "lead_time_days": 5,
    "unit": "pcs",
    "description": "Meja kerja kayu jati solid...",
    "image": "products/abc123.jpg",
    "is_active": 1
  }
}
```

### POST /api/products
Buat produk baru.

**Request:**
```json
{
  "sku": "MJK-003",
  "name": "Meja Komputer Jati",
  "category_id": 1,
  "cost_price": 650000,
  "selling_price": 980000,
  "stock": 5,
  "stock_minimum": 2,
  "unit": "pcs"
}
```

**Response 201:**
```json
{ "success": true, "id": 11, "message": "Produk berhasil dibuat." }
```

### PUT /api/products/:id
Update produk.

**Response 200:**
```json
{ "success": true, "message": "Produk berhasil diperbarui." }
```

### DELETE /api/products/:id
Hapus produk (soft delete).

**Response 200:**
```json
{ "success": true, "message": "Produk berhasil dihapus." }
```

---

## Transactions / POS

### GET /api/transactions
Daftar transaksi.

**Query params:** `?start_date=2024-01-01&end_date=2024-01-31&status=completed`

**Response 200:**
```json
{
  "data": [
    {
      "id": 1,
      "transaction_no": "TRX-20240115-0001",
      "cashier_name": "Siti Rahayu",
      "customer_name": "Pak Hendra",
      "total": 1350000,
      "payment_method": "cash",
      "status": "completed",
      "transaction_date": "2024-01-15 10:30:00"
    }
  ]
}
```

### POST /api/transactions/checkout
Proses checkout POS (ATOMIC).

**Request:**
```json
{
  "customer_name": "Pak Hendra",
  "payment_method": "cash",
  "payment_amount": 1500000,
  "notes": "",
  "cart": [
    { "product_id": 1, "quantity": 1, "unit_price": 1350000 }
  ]
}
```

**Response 200:**
```json
{
  "success": true,
  "message": "Transaksi berhasil!",
  "transaction_id": 16,
  "transaction_no": "TRX-20240130-0001",
  "invoice_url": "http://localhost/.../pos/invoice/16"
}
```

**Response 400 (stok tidak cukup):**
```json
{
  "success": false,
  "message": "Stok tidak cukup untuk produk 'Meja Kerja Jati Minimalis'. Tersedia: 2, diminta: 3."
}
```

### GET /api/transactions/:id
Detail transaksi + items.

---

## Inventory

### GET /api/inventory/movements
Riwayat pergerakan stok.

**Query params:** `?product_id=1&type=OUT&start_date=2024-01-01`

**Response 200:**
```json
{
  "data": [
    {
      "id": 11,
      "product_name": "Meja Kerja Jati",
      "type": "OUT",
      "quantity": -1,
      "stock_before": 13,
      "stock_after": 12,
      "reference_no": "TRX-20240115-0001",
      "created_at": "2024-01-15 10:30:05"
    }
  ]
}
```

### POST /api/inventory/stock-in
Tambah stok.

**Request:**
```json
{ "product_id": 1, "quantity": 10, "reference_no": "PO-2024-001", "notes": "Pembelian dari supplier" }
```

**Response 200:**
```json
{ "success": true, "stock_before": 12, "stock_after": 22 }
```

### POST /api/inventory/stock-out
Kurangi stok manual.

**Request:**
```json
{ "product_id": 1, "quantity": 2, "type": "ADJUSTMENT", "notes": "Barang rusak" }
```

---

## Dashboard

### GET /api/dashboard/stats
Metrik utama dashboard.

**Response 200:**
```json
{
  "today_sales": 1350000,
  "today_count": 2,
  "month_revenue": 32500000,
  "total_products": 10,
  "low_stock_count": 3,
  "recent_transactions": [...]
}
```

### GET /api/dashboard/sales-trend
Tren penjualan 30 hari.

**Response 200:**
```json
{
  "data": [
    { "date": "1 Jan", "total": 1350000 },
    { "date": "2 Jan", "total": 750000 }
  ]
}
```

### GET /api/dashboard/top-products
Top 5 produk terlaris bulan ini.

**Response 200:**
```json
{
  "data": [
    { "product_name": "Meja Kerja Jati", "total_qty": 8, "total_revenue": 10800000 }
  ]
}
```

---

## cURL Examples

```bash
# Login
curl -X POST http://localhost/toko-kayu-kontan-jaya/public/login \
  -H "Content-Type: application/json" \
  -d '{"email":"owner@tokokayukontan.com","password":"password123"}' \
  -c cookies.txt

# Daftar produk
curl http://localhost/toko-kayu-kontan-jaya/public/api/products \
  -b cookies.txt

# Checkout
curl -X POST http://localhost/toko-kayu-kontan-jaya/public/api/transactions/checkout \
  -H "Content-Type: application/json" \
  -b cookies.txt \
  -d '{
    "payment_method": "cash",
    "payment_amount": 1500000,
    "cart": [{"product_id": 1, "quantity": 1, "unit_price": 1350000}]
  }'

# Stock In
curl -X POST http://localhost/toko-kayu-kontan-jaya/public/api/inventory/stock-in \
  -H "Content-Type: application/json" \
  -b cookies.txt \
  -d '{"product_id": 1, "quantity": 10, "reference_no": "PO-001"}'
```
