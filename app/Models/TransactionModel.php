<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * TransactionModel — Header transaksi POS
 */
class TransactionModel extends Model
{
    protected $table         = 'transactions';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'transaction_no', 'user_id', 'customer_name', 'subtotal',
        'discount', 'total', 'payment_method', 'payment_amount',
        'change_amount', 'notes', 'status', 'transaction_date',
    ];

    // ----------------------------------------------------------------
    // Ambil transaksi beserta detailnya (untuk invoice)
    // ----------------------------------------------------------------
    public function getTransactionWithDetails(int $id): ?array
    {
        $transaction = $this->find($id);
        if (!$transaction) return null;

        $details = $this->db->query("
            SELECT td.*, p.name as product_name, p.sku, p.unit
            FROM transaction_details td
            JOIN products p ON p.id = td.product_id
            WHERE td.transaction_id = ?
        ", [$id])->getResultArray();

        $transaction['details'] = $details;

        // Kasir yang memproses
        $user = $this->db->query("SELECT name, email FROM users WHERE id = ?", [$transaction['user_id']])->getRowArray();
        $transaction['cashier_name'] = $user['name'] ?? '-';

        return $transaction;
    }

    // ----------------------------------------------------------------
    // Summary penjualan untuk laporan
    // ----------------------------------------------------------------
    public function getSalesSummary(string $startDate, string $endDate): array
    {
        return $this->db->query("
            SELECT
                DATE(t.transaction_date) as date,
                COUNT(t.id) as total_transactions,
                SUM(t.total) as total_revenue,
                SUM(t.discount) as total_discount
            FROM transactions t
            WHERE t.transaction_date BETWEEN ? AND ?
              AND t.status = 'completed'
            GROUP BY DATE(t.transaction_date)
            ORDER BY date ASC
        ", [$startDate, $endDate . ' 23:59:59'])->getResultArray();
    }

    // ----------------------------------------------------------------
    // Penjualan per produk untuk laporan
    // ----------------------------------------------------------------
    public function getSalesByProduct(string $startDate, string $endDate): array
    {
        return $this->db->query("
            SELECT
                p.sku, p.name as product_name,
                c.name as category_name,
                SUM(td.quantity) as total_qty,
                SUM(td.subtotal) as total_revenue
            FROM transaction_details td
            JOIN products p ON p.id = td.product_id
            JOIN categories c ON c.id = p.category_id
            JOIN transactions t ON t.id = td.transaction_id
            WHERE t.transaction_date BETWEEN ? AND ?
              AND t.status = 'completed'
            GROUP BY td.product_id
            ORDER BY total_revenue DESC
        ", [$startDate, $endDate . ' 23:59:59'])->getResultArray();
    }
}
