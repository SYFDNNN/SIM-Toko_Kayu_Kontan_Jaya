<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * StockMovementModel — Audit trail semua pergerakan stok
 */
class StockMovementModel extends Model
{
    protected $table         = 'stock_movements';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps  = false; // Hanya created_at, tanpa updated_at

    protected $allowedFields = [
        'product_id', 'user_id', 'type', 'quantity',
        'stock_before', 'stock_after', 'reference_type',
        'reference_id', 'reference_no', 'notes', 'created_at',
    ];

    /**
     * Riwayat pergerakan untuk satu produk
     */
    public function getByProduct(int $productId, int $limit = 50): array
    {
        return $this
            ->select('stock_movements.*, users.name as user_name')
            ->join('users', 'users.id = stock_movements.user_id')
            ->where('product_id', $productId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Ringkasan IN vs OUT per produk (untuk dashboard)
     */
    public function getSummaryByProduct(string $startDate, string $endDate): array
    {
        return $this->db->query("
            SELECT
                p.id, p.name, p.sku,
                SUM(CASE WHEN sm.type = 'IN' THEN sm.quantity ELSE 0 END) as total_in,
                SUM(CASE WHEN sm.type = 'OUT' THEN ABS(sm.quantity) ELSE 0 END) as total_out
            FROM stock_movements sm
            JOIN products p ON p.id = sm.product_id
            WHERE sm.created_at BETWEEN ? AND ?
            GROUP BY sm.product_id
            ORDER BY total_out DESC
        ", [$startDate, $endDate . ' 23:59:59'])->getResultArray();
    }
}
