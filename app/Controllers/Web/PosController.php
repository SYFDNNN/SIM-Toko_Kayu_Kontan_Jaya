<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\TransactionModel;
use App\Models\StockMovementModel;
use CodeIgniter\HTTP\ResponseInterface;

class PosController extends BaseController
{
    protected ProductModel $productModel;
    protected TransactionModel $transactionModel;
    protected StockMovementModel $stockMovementModel;

    public function __construct()
    {
        $this->productModel       = new ProductModel();
        $this->transactionModel   = new TransactionModel();
        $this->stockMovementModel = new StockMovementModel();
    }

    public function index()
    {
        return view('pos/index', [
            'title' => 'Kasir / POS',
        ]);
    }

    public function search(): ResponseInterface
    {
        $q = $this->request->getGet('q');

        if (strlen($q) < 2) {
            return $this->response->setJSON([]);
        }

        $products = $this->productModel
            ->select('id, sku, name, selling_price, stock, unit, image')
            ->groupStart()
                ->like('name', $q)
                ->orLike('sku', $q)
            ->groupEnd()
            ->where('is_active', 1)
            ->where('stock >', 0)
            ->limit(10)
            ->findAll();

        $results = array_map(function ($p) {
            return [
                'id'                      => $p['id'],
                'sku'                     => $p['sku'],
                'name'                    => $p['name'],
                'selling_price'           => $p['selling_price'],
                'selling_price_formatted' => 'Rp ' . number_format($p['selling_price'], 0, ',', '.'),
                'stock'                   => $p['stock'],
                'unit'                    => $p['unit'],
                'image'                   => $p['image']
                    ? str_replace('index.php/', '', base_url('uploads/' . $p['image']))
                    : str_replace('index.php/', '', base_url('assets/images/product-placeholder.png')),
            ];
        }, $products);

        return $this->response->setJSON($results);
    }

    public function products(): ResponseInterface
    {
        $products = $this->productModel
            ->select('id, sku, name, selling_price, stock, unit, image')
            ->where('is_active', 1)
            ->where('stock >', 0)
            ->orderBy('name', 'ASC')
            ->limit(100)
            ->findAll();

        $results = array_map(function ($p) {
            return [
                'id'                      => $p['id'],
                'sku'                     => $p['sku'],
                'name'                    => $p['name'],
                'selling_price'           => $p['selling_price'],
                'selling_price_formatted' => 'Rp ' . number_format($p['selling_price'], 0, ',', '.'),
                'stock'                   => $p['stock'],
                'unit'                    => $p['unit'],
                'image'                   => $p['image']
                    ? str_replace('index.php/', '', base_url('uploads/' . $p['image']))
                    : str_replace('index.php/', '', base_url('assets/images/product-placeholder.png')),
            ];
        }, $products);

        return $this->response->setJSON($results);
    }

    public function checkout(): ResponseInterface
    {
        if (!$this->request->is('post')) {
            return $this->response->setStatusCode(405)->setJSON([
                'success' => false,
                'message' => 'Method not allowed',
            ]);
        }

        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $cart = $data['cart'] ?? [];
        if (empty($cart)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cart kosong.',
            ]);
        }

        $paymentAmount = (float) ($data['payment_amount'] ?? 0);
        $paymentMethod = $data['payment_method'] ?? 'cash';

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $subtotal       = 0;
            $processedItems = [];

            foreach ($cart as $item) {
                $productId = (int)   $item['product_id'];
                $quantity  = (int)   $item['quantity'];
                $unitPrice = (float) $item['unit_price'];

                if ($quantity <= 0) {
                    throw new \Exception("Quantity tidak valid untuk produk ID {$productId}.");
                }

                $product = $db->query(
                    "SELECT id, name, stock, selling_price FROM products WHERE id = ? AND is_active = 1 FOR UPDATE",
                    [$productId]
                )->getRowArray();

                if (!$product) {
                    throw new \Exception("Produk ID {$productId} tidak ditemukan atau tidak aktif.");
                }

                if ($product['stock'] < $quantity) {
                    throw new \Exception(
                        "Stok tidak cukup untuk produk '{$product['name']}'. " .
                        "Tersedia: {$product['stock']}, diminta: {$quantity}."
                    );
                }

                $itemSubtotal = $unitPrice * $quantity;
                $subtotal    += $itemSubtotal;

                $processedItems[] = [
                    'product_id'   => $productId,
                    'product_name' => $product['name'],
                    'quantity'     => $quantity,
                    'unit_price'   => $unitPrice,
                    'subtotal'     => $itemSubtotal,
                    'stock_before' => $product['stock'],
                    'stock_after'  => $product['stock'] - $quantity,
                ];
            }

            if ($paymentMethod === 'cash' && $paymentAmount < $subtotal) {
                throw new \Exception('Jumlah pembayaran kurang dari total belanja.');
            }

            $changeAmount  = max(0, $paymentAmount - $subtotal);
            $transactionNo = $this->generateTransactionNo($db);

            $db->table('transactions')->insert([
                'transaction_no'   => $transactionNo,
                'user_id'          => session('user_id'),
                'customer_name'    => $data['customer_name'] ?? null,
                'subtotal'         => $subtotal,
                'discount'         => 0,
                'total'            => $subtotal,
                'payment_method'   => $paymentMethod,
                'payment_amount'   => $paymentAmount,
                'change_amount'    => $changeAmount,
                'notes'            => $data['notes'] ?? null,
                'status'           => 'completed',
                'transaction_date' => date('Y-m-d H:i:s'),
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);
            $transactionId = $db->insertID();

            foreach ($processedItems as $item) {
                $db->table('transaction_details')->insert([
                    'transaction_id' => $transactionId,
                    'product_id'     => $item['product_id'],
                    'quantity'       => $item['quantity'],
                    'unit_price'     => $item['unit_price'],
                    'subtotal'       => $item['subtotal'],
                ]);

                $db->query(
                    "UPDATE products SET stock = stock - ?, updated_at = NOW() WHERE id = ?",
                    [$item['quantity'], $item['product_id']]
                );

                $db->table('stock_movements')->insert([
                    'product_id'     => $item['product_id'],
                    'user_id'        => session('user_id'),
                    'type'           => 'OUT',
                    'quantity'       => -$item['quantity'],
                    'stock_before'   => $item['stock_before'],
                    'stock_after'    => $item['stock_after'],
                    'reference_type' => 'transaction',
                    'reference_id'   => $transactionId,
                    'reference_no'   => $transactionNo,
                    'notes'          => 'POS Checkout',
                    'created_at'     => date('Y-m-d H:i:s'),
                ]);
            }

            $db->transCommit();

            $this->logAudit('transaction.checkout', 'transactions', $transactionId, null, [
                'transaction_no' => $transactionNo,
                'total'          => $subtotal,
            ]);

            return $this->response->setJSON([
                'success'        => true,
                'message'        => 'Transaksi berhasil!',
                'transaction_id' => $transactionId,
                'transaction_no' => $transactionNo,
                'invoice_url'    => base_url("pos/invoice/{$transactionId}"),
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'POS Checkout error: ' . $e->getMessage());

            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function invoice(int $id): string
    {
        $transaction = $this->transactionModel->getTransactionWithDetails($id);

        if (!$transaction) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Transaksi tidak ditemukan.");
        }

        $settingModel = new \App\Models\SettingModel();
        $settings     = $settingModel->getAllAsArray();

        return view('pos/invoice', [
            'title'       => 'Invoice ' . $transaction['transaction_no'],
            'transaction' => $transaction,
            'settings'    => $settings,
        ]);
    }

    private function generateTransactionNo(\CodeIgniter\Database\BaseConnection $db): string
    {
        $today  = date('Ymd');
        $prefix = "TRX-{$today}-";

        $count = $db->query(
            "SELECT COUNT(*) as cnt FROM transactions WHERE transaction_no LIKE ?",
            [$prefix . '%']
        )->getRowArray()['cnt'] ?? 0;

        $sequence = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
        return $prefix . $sequence;
    }

    private function logAudit(string $action, string $table, ?int $recordId, ?array $old, ?array $new): void
    {
        try {
            $db = \Config\Database::connect();
            $db->table('audit_logs')->insert([
                'user_id'    => session('user_id'),
                'action'     => $action,
                'table_name' => $table,
                'record_id'  => $recordId,
                'old_values' => $old ? json_encode($old) : null,
                'new_values' => $new ? json_encode($new) : null,
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Audit log error: ' . $e->getMessage());
        }
    }
}