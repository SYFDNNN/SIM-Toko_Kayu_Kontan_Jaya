<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardApiController extends BaseController
{
    /**
     * Banyak project POS memakai status berbeda-beda:
     * completed / selesai / done / paid
     * Jadi query dibuat toleran supaya data tidak hilang.
     */
    private function completedStatusSql(): string
    {
        return "LOWER(t.status) IN ('completed', 'selesai', 'done', 'paid')";
    }

    public function stats(): ResponseInterface
    {
        $db = \Config\Database::connect();

        // Hari ini
        $todayStart    = date('Y-m-d 00:00:00');
        $tomorrowStart = date('Y-m-d 00:00:00', strtotime('+1 day'));

        // Filter untuk payment summary (bisa custom)
        $psStart = $this->request->getGet('start_date');
        $psEnd   = $this->request->getGet('end_date');

        if (!$psStart || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $psStart)) {
            $psStart = date('Y-m-d', strtotime('-29 days'));
        }
        if (!$psEnd || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $psEnd)) {
            $psEnd = date('Y-m-d');
        }

        $periodStart = $psStart . ' 00:00:00';
        $periodEnd   = $psEnd   . ' 23:59:59';

        $todaySales = $db->query("
            SELECT COALESCE(SUM(total), 0) AS total, COUNT(*) AS cnt
            FROM transactions
            WHERE transaction_date >= ?
              AND transaction_date < ?
              AND LOWER(status) IN ('completed', 'selesai', 'done', 'paid')
        ", [$todayStart, $tomorrowStart])->getRowArray();

        $monthRevenue = $db->query("
            SELECT COALESCE(SUM(total), 0) AS total
            FROM transactions
            WHERE transaction_date >= ?
              AND transaction_date <= ?
              AND LOWER(status) IN ('completed', 'selesai', 'done', 'paid')
        ", [$periodStart, $periodEnd])->getRowArray();

        $totalProducts = $db->query("
            SELECT COUNT(*) AS cnt
            FROM products
            WHERE is_active = 1
              AND deleted_at IS NULL
        ")->getRowArray();

        $lowStock = $db->query("
            SELECT COUNT(*) AS cnt
            FROM products
            WHERE stock <= stock_minimum
              AND is_active = 1
              AND deleted_at IS NULL
        ")->getRowArray();

        // Laba hari ini
        $todayProfit = $db->query("
            SELECT COALESCE(SUM(
                COALESCE(
                    td.subtotal,
                    COALESCE(td.unit_price, 0) * COALESCE(td.quantity, 0)
                ) - (COALESCE(p.cost_price, 0) * COALESCE(td.quantity, 0))
            ), 0) AS profit
            FROM transaction_details td
            JOIN products p ON p.id = td.product_id
            JOIN transactions t ON t.id = td.transaction_id
            WHERE t.transaction_date >= ?
              AND t.transaction_date < ?
              AND LOWER(t.status) IN ('completed', 'selesai', 'done', 'paid')
        ", [$todayStart, $tomorrowStart])->getRowArray();

        // Laba 30 hari terakhir
        $monthProfit = $db->query("
            SELECT COALESCE(SUM(
                COALESCE(
                    td.subtotal,
                    COALESCE(td.unit_price, 0) * COALESCE(td.quantity, 0)
                ) - (COALESCE(p.cost_price, 0) * COALESCE(td.quantity, 0))
            ), 0) AS profit
            FROM transaction_details td
            JOIN products p ON p.id = td.product_id
            JOIN transactions t ON t.id = td.transaction_id
            WHERE t.transaction_date >= ?
              AND t.transaction_date <= ?
              AND LOWER(t.status) IN ('completed', 'selesai', 'done', 'paid')
        ", [$periodStart, $periodEnd])->getRowArray();

        // Metode pembayaran 30 hari terakhir
        $paymentStats = $db->query("
            SELECT
                CASE
                    WHEN LOWER(payment_method) IN ('cash', 'tunai', 'tunai_cash') THEN 'cash'
                    WHEN LOWER(payment_method) IN ('transfer', 'bank_transfer', 'bank transfer', 'tf') THEN 'transfer'
                    ELSE LOWER(payment_method)
                END AS method_key,
                COUNT(*) AS total_trx,
                COALESCE(SUM(total), 0) AS total_amount
            FROM transactions
            WHERE transaction_date >= ?
              AND transaction_date <= ?
              AND LOWER(status) IN ('completed', 'selesai', 'done', 'paid')
            GROUP BY method_key
        ", [$periodStart, $periodEnd])->getResultArray();

        $paymentSummary = [
            'cash' => ['trx' => 0, 'amount' => 0],
            'transfer' => ['trx' => 0, 'amount' => 0],
        ];

        foreach ($paymentStats as $p) {
            $method = $p['method_key'] ?? '';
            if (isset($paymentSummary[$method])) {
                $paymentSummary[$method]['trx'] = (int) ($p['total_trx'] ?? 0);
                $paymentSummary[$method]['amount'] = (float) ($p['total_amount'] ?? 0);
            }
        }

        $recentTxns = $db->query("
            SELECT
                t.id,
                t.transaction_no,
                t.customer_name,
                t.total,
                t.payment_method,
                t.transaction_date,
                u.name AS cashier_name
            FROM transactions t
            LEFT JOIN users u ON u.id = t.user_id
            WHERE LOWER(t.status) IN ('completed', 'selesai', 'done', 'paid')
            ORDER BY t.transaction_date DESC, t.id DESC
            LIMIT 10
        ")->getResultArray();

        return $this->response->setJSON([
            'today_sales'         => (float) ($todaySales['total'] ?? 0),
            'payment_summary'     => $paymentSummary,
            'today_count'         => (int) ($todaySales['cnt'] ?? 0),
            'month_revenue'       => (float) ($monthRevenue['total'] ?? 0),
            'total_products'      => (int) ($totalProducts['cnt'] ?? 0),
            'low_stock_count'     => (int) ($lowStock['cnt'] ?? 0),
            'today_profit'        => (float) ($todayProfit['profit'] ?? 0),
            'month_profit'        => (float) ($monthProfit['profit'] ?? 0),
            'recent_transactions' => $recentTxns,
        ]);
    }

    public function salesTrend(): ResponseInterface
    {
        $db = \Config\Database::connect();

        $startDate = $this->request->getGet('start_date');
        $endDate   = $this->request->getGet('end_date');

        if (!$startDate) {
            $startDate = date('Y-m-d', strtotime('-29 days'));
        }
        if (!$endDate) {
            $endDate = date('Y-m-d');
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
            $startDate = date('Y-m-d', strtotime('-29 days'));
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
            $endDate = date('Y-m-d');
        }

        $rows = $db->query("
            SELECT
                DATE(transaction_date) AS date,
                COALESCE(SUM(total), 0) AS total
            FROM transactions
            WHERE DATE(transaction_date) BETWEEN ? AND ?
              AND LOWER(status) IN ('completed', 'selesai', 'done', 'paid')
            GROUP BY DATE(transaction_date)
            ORDER BY date ASC
        ", [$startDate, $endDate])->getResultArray();

        $data = array_map(function ($row) {
            return [
                'date'  => $row['date'],
                'total' => (float) $row['total'],
            ];
        }, $rows);

        return $this->response->setJSON([
            'data'       => $data,
            'start_date' => $startDate,
            'end_date'   => $endDate,
        ]);
    }

    public function topProducts(): ResponseInterface
    {
        $db = \Config\Database::connect();

        $startDate = $this->request->getGet('start_date');
        $endDate   = $this->request->getGet('end_date');

        if (!$startDate || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
            $startDate = date('Y-m-d', strtotime('-29 days'));
        }
        if (!$endDate || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
            $endDate = date('Y-m-d');
        }

        $rows = $db->query("
            SELECT
                p.name AS product_name,
                SUM(td.quantity) AS total_qty,
                SUM(COALESCE(td.subtotal, 0)) AS total_revenue
            FROM transaction_details td
            JOIN products p ON p.id = td.product_id
            JOIN transactions t ON t.id = td.transaction_id
            WHERE DATE(t.transaction_date) BETWEEN ? AND ?
              AND LOWER(t.status) IN ('completed', 'selesai', 'done', 'paid')
            GROUP BY p.id, p.name
            ORDER BY total_qty DESC
            LIMIT 5
        ", [$startDate, $endDate])->getResultArray();

        $data = array_map(function ($row) {
            return [
                'product_name'  => $row['product_name'],
                'total_qty'     => (int) ($row['total_qty'] ?? 0),
                'total_revenue' => (float) ($row['total_revenue'] ?? 0),
            ];
        }, $rows);

        return $this->response->setJSON([
            'data'       => $data,
            'start_date' => $startDate,
            'end_date'   => $endDate,
        ]);
    }
}