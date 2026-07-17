<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * StockMovementTest — Tests untuk Stock In, Out, ROP
 */
class StockMovementTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $refresh = true;
    protected $seed    = 'TestSeeder';

    // ----------------------------------------------------------------
    // Test 1: Stock In menambah stok dan membuat record movement
    // ----------------------------------------------------------------
    public function testStockInIncreasesStock(): void
    {
        $db = \Config\Database::connect();
        $db->query("UPDATE products SET stock = 10 WHERE id = 1");

        session()->set(['user_id' => 1, 'user_role' => 'owner']);

        $controller = new \App\Controllers\Web\InventoryController();
        $request    = \Config\Services::request();
        $request->setMethod('post');
        $request->setPost(['product_id' => 1, 'quantity' => 5, 'reference_no' => 'PO-001', 'notes' => 'Test stock in']);

        $controller->stockInProcess();

        $stock = $db->query("SELECT stock FROM products WHERE id = 1")->getRowArray()['stock'];
        $this->assertEquals(15, $stock, 'Stok harus bertambah dari 10 menjadi 15');

        $movement = $db->query(
            "SELECT * FROM stock_movements WHERE product_id = 1 AND type = 'IN' ORDER BY id DESC LIMIT 1"
        )->getRowArray();

        $this->assertNotNull($movement);
        $this->assertEquals(5, $movement['quantity']);
        $this->assertEquals(10, $movement['stock_before']);
        $this->assertEquals(15, $movement['stock_after']);
        $this->assertEquals('PO-001', $movement['reference_no']);
    }

    // ----------------------------------------------------------------
    // Test 2: Stock Out mengurangi stok dan membuat record movement
    // ----------------------------------------------------------------
    public function testStockOutDecreasesStock(): void
    {
        $db = \Config\Database::connect();
        $db->query("UPDATE products SET stock = 20 WHERE id = 1");

        session()->set(['user_id' => 1, 'user_role' => 'owner']);

        $controller = new \App\Controllers\Web\InventoryController();
        $request    = \Config\Services::request();
        $request->setMethod('post');
        $request->setPost(['product_id' => 1, 'quantity' => 8, 'type' => 'OUT', 'notes' => 'Test stock out']);

        $controller->stockOutProcess();

        $stock = $db->query("SELECT stock FROM products WHERE id = 1")->getRowArray()['stock'];
        $this->assertEquals(12, $stock);

        $movement = $db->query(
            "SELECT * FROM stock_movements WHERE product_id = 1 AND type = 'OUT' ORDER BY id DESC LIMIT 1"
        )->getRowArray();

        $this->assertEquals(-8, $movement['quantity']);
        $this->assertEquals(20, $movement['stock_before']);
        $this->assertEquals(12, $movement['stock_after']);
    }

    // ----------------------------------------------------------------
    // Test 3: Stock Out gagal jika stok tidak cukup
    // ----------------------------------------------------------------
    public function testStockOutFailsIfInsufficientStock(): void
    {
        $db = \Config\Database::connect();
        $db->query("UPDATE products SET stock = 3 WHERE id = 1");

        session()->set(['user_id' => 1, 'user_role' => 'owner']);

        $controller = new \App\Controllers\Web\InventoryController();
        $request    = \Config\Services::request();
        $request->setMethod('post');
        $request->setPost(['product_id' => 1, 'quantity' => 10, 'type' => 'OUT']);

        // Setelah gagal, stok tidak boleh berubah
        $controller->stockOutProcess();

        $stock = $db->query("SELECT stock FROM products WHERE id = 1")->getRowArray()['stock'];
        $this->assertEquals(3, $stock, 'Stok tidak boleh berubah jika OUT gagal');
    }

    // ----------------------------------------------------------------
    // Test 4: ROP dihitung dengan benar
    // Formula: avg_daily_sales × lead_time_days
    // ----------------------------------------------------------------
    public function testRopCalculation(): void
    {
        $db = \Config\Database::connect();

        // Set lead_time_days = 5 untuk produk 1
        $db->query("UPDATE products SET lead_time_days = 5 WHERE id = 1");

        // Insert 3 transaksi dengan total 30 unit terjual dalam 30 hari
        // avg_daily_sales = 30/30 = 1.0
        // ROP = 1.0 × 5 = 5.0
        $db->query("DELETE FROM transaction_details WHERE product_id = 1");
        $db->query("DELETE FROM transactions WHERE id > 100"); // Clear test transactions

        // Insert 3 transaksi: 10 unit, 10 unit, 10 unit
        for ($i = 0; $i < 3; $i++) {
            $db->table('transactions')->insert([
                'transaction_no'   => "TRX-TEST-ROP-{$i}",
                'user_id'          => 2,
                'subtotal'         => 100000,
                'discount'         => 0,
                'total'            => 100000,
                'payment_method'   => 'cash',
                'payment_amount'   => 100000,
                'change_amount'    => 0,
                'status'           => 'completed',
                'transaction_date' => date('Y-m-d H:i:s', strtotime("-{$i} days")),
                'created_at'       => NOW(),
                'updated_at'       => NOW(),
            ]);
            $tId = $db->insertID();
            $db->table('transaction_details')->insert([
                'transaction_id' => $tId,
                'product_id'     => 1,
                'quantity'       => 10,
                'unit_price'     => 10000,
                'subtotal'       => 100000,
            ]);
        }

        $productModel = new \App\Models\ProductModel();
        $rop = $productModel->calculateRop(1);

        // avg = 30/30 = 1.0, lead = 5, ROP = 5.0
        $this->assertEquals(5.0, $rop, 'ROP harus 5.0 (1 unit/hari × 5 hari lead time)');
    }

    // ----------------------------------------------------------------
    // Test 5: Low stock detection
    // ----------------------------------------------------------------
    public function testLowStockDetection(): void
    {
        $db = \Config\Database::connect();
        // Set produk 1: stock=2, stock_minimum=5 → LOW
        $db->query("UPDATE products SET stock=2, stock_minimum=5 WHERE id=1");
        // Set produk 2: stock=10, stock_minimum=5 → OK
        $db->query("UPDATE products SET stock=10, stock_minimum=5 WHERE id=2");

        $productModel = new \App\Models\ProductModel();
        $lowStock = $productModel->getLowStock();

        $ids = array_column($lowStock, 'id');
        $this->assertContains(1, $ids, 'Produk 1 harus terdeteksi sebagai low stock');
        $this->assertNotContains(2, $ids, 'Produk 2 tidak boleh terdeteksi sebagai low stock');
    }
}
