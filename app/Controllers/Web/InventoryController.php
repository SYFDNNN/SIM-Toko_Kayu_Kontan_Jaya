<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\StockMovementModel;

class InventoryController extends BaseController
{
    protected $productModel;
    protected $stockMovementModel;

    public function __construct()
    {
        $this->productModel       = new ProductModel();
        $this->stockMovementModel = new StockMovementModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();

        // Produk dengan stok rendah
        $lowStockProducts = $db->query("
            SELECT p.*, c.name as category_name
            FROM products p
            JOIN categories c ON c.id = p.category_id
            WHERE p.stock <= p.stock_minimum
            AND p.is_active = 1
            AND p.deleted_at IS NULL
        ")->getResultArray();

        // Riwayat pergerakan stok terbaru
        $recentMovements = $db->query("
            SELECT sm.*, p.name as product_name, p.sku, u.name as user_name
            FROM stock_movements sm
            JOIN products p ON p.id = sm.product_id
            JOIN users u ON u.id = sm.user_id
            ORDER BY sm.created_at DESC
            LIMIT 50
        ")->getResultArray();

        return view('inventory/index', compact('lowStockProducts', 'recentMovements'));
    }

    public function stockInForm()
    {
        $products = $this->productModel->where('is_active', 1)->findAll();
        return view('inventory/stock_in', ['title' => 'Stock In', 'products' => $products]);
    }

    public function stockInProcess()
    {
        $productId = $this->request->getPost('product_id');
        $quantity  = (int) $this->request->getPost('quantity');
        $refNo     = $this->request->getPost('reference_no');
        $notes     = $this->request->getPost('notes');

        if (!$productId || $quantity <= 0) {
            return redirect()->back()->with('error', 'Data tidak valid.');
        }

        $db      = \Config\Database::connect();
        $product = $db->query("SELECT * FROM products WHERE id = ? AND is_active = 1", [$productId])->getRowArray();

        if (!$product) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan.');
        }

        $db->transBegin();
        try {
            $stockBefore = $product['stock'];
            $stockAfter  = $stockBefore + $quantity;

            $db->query("UPDATE products SET stock = ?, updated_at = NOW() WHERE id = ?", [$stockAfter, $productId]);

            $db->table('stock_movements')->insert([
                'product_id'     => $productId,
                'user_id'        => session('user_id'),
                'type'           => 'IN',
                'quantity'       => $quantity,
                'stock_before'   => $stockBefore,
                'stock_after'    => $stockAfter,
                'reference_type' => 'purchase_order',
                'reference_no'   => $refNo,
                'notes'          => $notes,
                'created_at'     => date('Y-m-d H:i:s'),
            ]);

            $db->transCommit();
            return redirect()->to(base_url('inventori'))->with('success', "Stock In berhasil. Stok {$product['name']} sekarang: {$stockAfter}.");
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function stockOutForm()
    {
        $products = $this->productModel->where('is_active', 1)->where('stock >', 0)->findAll();
        return view('inventory/stock_out', ['title' => 'Stock Out', 'products' => $products]);
    }

    public function stockOutProcess()
    {
        $productId = $this->request->getPost('product_id');
        $quantity  = (int) $this->request->getPost('quantity');
        $type      = $this->request->getPost('type') === 'ADJUSTMENT' ? 'ADJUSTMENT' : 'OUT';
        $notes     = $this->request->getPost('notes');

        $db      = \Config\Database::connect();
        $product = $db->query("SELECT * FROM products WHERE id = ?", [$productId])->getRowArray();

        if (!$product || $product['stock'] < $quantity) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi.');
        }

        $db->transBegin();
        try {
            $stockBefore = $product['stock'];
            $stockAfter  = $stockBefore - $quantity;

            $db->query("UPDATE products SET stock = ?, updated_at = NOW() WHERE id = ?", [$stockAfter, $productId]);

            $db->table('stock_movements')->insert([
                'product_id'     => $productId,
                'user_id'        => session('user_id'),
                'type'           => $type,
                'quantity'       => -$quantity,
                'stock_before'   => $stockBefore,
                'stock_after'    => $stockAfter,
                'reference_type' => 'manual',
                'notes'          => $notes,
                'created_at'     => date('Y-m-d H:i:s'),
            ]);

            $db->transCommit();
            return redirect()->to(base_url('inventori'))->with('success', "Stock Out berhasil. Stok {$product['name']} sekarang: {$stockAfter}.");
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function rop()
    {
        $db       = \Config\Database::connect();
        $products = $db->query("
            SELECT
                p.id, p.sku, p.name, p.stock, p.stock_minimum, p.lead_time_days,
                COALESCE(SUM(td.quantity) / 30, 0) AS avg_daily_sales,
                COALESCE(SUM(td.quantity) / 30, 0) * p.lead_time_days AS rop
            FROM products p
            LEFT JOIN transaction_details td ON td.product_id = p.id
            LEFT JOIN transactions t ON t.id = td.transaction_id
                AND t.transaction_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                AND t.status = 'completed'
            WHERE p.is_active = 1
            GROUP BY p.id
            ORDER BY p.name
        ")->getResultArray();

        return view('inventory/rop', ['title' => 'Reorder Point (ROP)', 'products' => $products]);
    }
}