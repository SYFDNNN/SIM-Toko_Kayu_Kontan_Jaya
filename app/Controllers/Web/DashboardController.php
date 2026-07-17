<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;

/**
 * DashboardController — Metrik utama, grafik penjualan, low stock
 */
class DashboardController extends BaseController
{
    public function index(): string
    {
        $db   = \Config\Database::connect();
        $today = date('Y-m-d');
        $monthStart = date('Y-m-01');

        // Penjualan hari ini
        $todaySales = $db->query(
            "SELECT COALESCE(SUM(total),0) as total, COUNT(*) as count
             FROM transactions WHERE DATE(transaction_date)=? AND status='completed'",
            [$today]
        )->getRowArray();

        // Revenue bulan ini
        $monthRevenue = $db->query(
            "SELECT COALESCE(SUM(total),0) as total FROM transactions
             WHERE transaction_date >= ? AND status='completed'",
            [$monthStart]
        )->getRowArray();

        // Total produk aktif
        $totalProducts = $db->query("SELECT COUNT(*) as cnt FROM products WHERE is_active=1")->getRowArray()['cnt'];

        // Low stock count
        $lowStockCount = $db->query(
            "SELECT COUNT(*) as cnt FROM products WHERE stock <= stock_minimum AND is_active=1"
        )->getRowArray()['cnt'];

        // Tren penjualan 30 hari (untuk Chart.js)
        $salesTrend = $db->query(
            "SELECT DATE(transaction_date) as date, SUM(total) as total
             FROM transactions WHERE transaction_date >= DATE_SUB(NOW(), INTERVAL 30 DAY) AND status='completed'
             GROUP BY DATE(transaction_date) ORDER BY date ASC"
        )->getResultArray();

        // Top 5 produk terlaris bulan ini
        $topProducts = $db->query(
            "SELECT p.name, SUM(td.quantity) as total_qty, SUM(td.subtotal) as total_revenue
             FROM transaction_details td
             JOIN products p ON p.id = td.product_id
             JOIN transactions t ON t.id = td.transaction_id
             WHERE t.transaction_date >= ? AND t.status='completed'
             GROUP BY td.product_id ORDER BY total_qty DESC LIMIT 5",
            [$monthStart]
        )->getResultArray();

        // Transaksi terbaru
        $recentTransactions = $db->query(
            "SELECT t.*, u.name as cashier_name FROM transactions t
             JOIN users u ON u.id = t.user_id
             ORDER BY t.created_at DESC LIMIT 10"
        )->getResultArray();

        return view('dashboard/index', compact(
            'todaySales', 'monthRevenue', 'totalProducts', 'lowStockCount',
            'salesTrend', 'topProducts', 'recentTransactions'
        ));
    }
}
