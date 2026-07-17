<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * ProductModel — Model untuk tabel products
 * Fitur: soft delete, validasi, ROP helper
 */
class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'category_id', 'sku', 'name', 'description',
        'cost_price', 'selling_price', 'stock', 'stock_minimum',
        'lead_time_days', 'unit', 'image', 'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation rules
    protected $validationRules = [
        'sku'           => 'required|min_length[3]|max_length[50]',
        'name'          => 'required|min_length[3]|max_length[200]',
        'category_id'   => 'required|integer',
        'cost_price'    => 'required|decimal',
        'selling_price' => 'required|decimal',
        'stock'         => 'integer|greater_than_equal_to[0]',
        'stock_minimum' => 'integer|greater_than_equal_to[0]',
    ];

    // ----------------------------------------------------------------
    // Cari produk beserta nama kategorinya
    // ----------------------------------------------------------------
    public function getWithCategory(int $id): ?array
    {
        return $this->select('products.*, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id')
            ->find($id);
    }

    // ----------------------------------------------------------------
    // Produk dengan stok di bawah minimum (low stock)
    // ----------------------------------------------------------------
    public function getLowStock(): array
    {
        return $this->select('products.*, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id')
            ->where('products.stock <=', $this->db->protectIdentifiers('products.stock_minimum', false), false)
            ->where('products.is_active', 1)
            ->findAll();
    }

    // ----------------------------------------------------------------
    // Hitung ROP untuk satu produk
    // ROP = avg_daily_sales(30 hari) × lead_time_days
    // ----------------------------------------------------------------
    public function calculateRop(int $productId): float
    {
        $product = $this->find($productId);
        if (!$product) return 0;

        $avgDailySales = $this->db->query("
            SELECT COALESCE(SUM(td.quantity), 0) / 30 AS avg
            FROM transaction_details td
            JOIN transactions t ON t.id = td.transaction_id
            WHERE td.product_id = ?
              AND t.transaction_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
              AND t.status = 'completed'
        ", [$productId])->getRowArray()['avg'] ?? 0;

        return round((float)$avgDailySales * (int)$product['lead_time_days'], 2);
    }

    // ----------------------------------------------------------------
    // Cari atau gagal (throw exception)
    // ----------------------------------------------------------------
    public function findOrFail(int $id): array
    {
        $record = $this->find($id);
        if (!$record) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Produk ID {$id} tidak ditemukan.");
        }
        return $record;
    }
}
