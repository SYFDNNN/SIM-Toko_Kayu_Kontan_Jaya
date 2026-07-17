<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\StockMovementModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * InventoryApiController — Stock In/Out via API
 */
class InventoryApiController extends BaseController
{
    // GET /api/inventory/movements
    public function movements(): ResponseInterface
    {
        $db        = \Config\Database::connect();
        $productId = $this->request->getGet('product_id');
        $type      = $this->request->getGet('type');
        $start     = $this->request->getGet('start_date') ?? date('Y-m-01');
        $end       = $this->request->getGet('end_date')   ?? date('Y-m-d');

        $builder = $db->table('stock_movements sm')
            ->select('sm.*, p.name as product_name, p.sku, u.name as user_name')
            ->join('products p', 'p.id = sm.product_id')
            ->join('users u', 'u.id = sm.user_id')
            ->where('sm.created_at >=', $start . ' 00:00:00')
            ->where('sm.created_at <=', $end . ' 23:59:59')
            ->orderBy('sm.created_at', 'DESC')
            ->limit(100);

        if ($productId) $builder->where('sm.product_id', $productId);
        if ($type)      $builder->where('sm.type', $type);

        return $this->response->setJSON(['data' => $builder->get()->getResultArray()]);
    }

    // POST /api/inventory/stock-in
    public function stockIn(): ResponseInterface
    {
        $data      = $this->request->getJSON(true);
        $productId = (int) ($data['product_id'] ?? 0);
        $quantity  = (int) ($data['quantity']   ?? 0);

        if (!$productId || $quantity <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'product_id dan quantity wajib diisi.']);
        }

        $db      = \Config\Database::connect();
        $product = $db->query("SELECT id, stock FROM products WHERE id = ? AND is_active = 1", [$productId])->getRowArray();

        if (!$product) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Produk tidak ditemukan.']);
        }

        $db->transBegin();
        try {
            $stockBefore = $product['stock'];
            $stockAfter  = $stockBefore + $quantity;
            $db->query("UPDATE products SET stock = ?, updated_at = NOW() WHERE id = ?", [$stockAfter, $productId]);
            $db->table('stock_movements')->insert([
                'product_id'   => $productId, 'user_id' => session('user_id'),
                'type' => 'IN', 'quantity' => $quantity,
                'stock_before' => $stockBefore, 'stock_after' => $stockAfter,
                'reference_type' => 'purchase_order', 'reference_no' => $data['reference_no'] ?? null,
                'notes' => $data['notes'] ?? null, 'created_at' => date('Y-m-d H:i:s'),
            ]);
            $db->transCommit();
            return $this->response->setJSON(['success' => true, 'stock_before' => $stockBefore, 'stock_after' => $stockAfter]);
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setStatusCode(500)->setJSON(['message' => $e->getMessage()]);
        }
    }

    // POST /api/inventory/stock-out
    public function stockOut(): ResponseInterface
    {
        $data      = $this->request->getJSON(true);
        $productId = (int) ($data['product_id'] ?? 0);
        $quantity  = (int) ($data['quantity']   ?? 0);
        $type      = in_array($data['type'] ?? '', ['OUT','ADJUSTMENT']) ? $data['type'] : 'OUT';

        if (!$productId || $quantity <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'product_id dan quantity wajib diisi.']);
        }

        $db      = \Config\Database::connect();
        $product = $db->query("SELECT id, name, stock FROM products WHERE id = ? FOR UPDATE", [$productId])->getRowArray();

        if (!$product || $product['stock'] < $quantity) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Stok tidak mencukupi.']);
        }

        $db->transBegin();
        try {
            $before = $product['stock'];
            $after  = $before - $quantity;
            $db->query("UPDATE products SET stock = ?, updated_at = NOW() WHERE id = ?", [$after, $productId]);
            $db->table('stock_movements')->insert([
                'product_id' => $productId, 'user_id' => session('user_id'),
                'type' => $type, 'quantity' => -$quantity,
                'stock_before' => $before, 'stock_after' => $after,
                'reference_type' => 'manual', 'notes' => $data['notes'] ?? null,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            $db->transCommit();
            return $this->response->setJSON(['success' => true, 'stock_before' => $before, 'stock_after' => $after]);
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setStatusCode(500)->setJSON(['message' => $e->getMessage()]);
        }
    }
}
