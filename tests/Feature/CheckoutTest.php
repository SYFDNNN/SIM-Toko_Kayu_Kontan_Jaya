<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * CheckoutTest — Integration tests untuk alur checkout POS
 *
 * Jalankan: ./vendor/bin/phpunit tests/Feature/CheckoutTest.php
 */
class CheckoutTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $refresh = true;  // Reset DB setiap test
    protected $seed    = 'TestSeeder';

    // ----------------------------------------------------------------
    // Test 1: Checkout berhasil, stok berkurang, transaksi tersimpan
    // ----------------------------------------------------------------
    public function testCheckoutSuccess(): void
    {
        $db = \Config\Database::connect();

        // Stok awal produk ID 1 = 10
        $db->query("UPDATE products SET stock = 10 WHERE id = 1");

        $controller = new \App\Controllers\Web\PosController();

        // Simulasi POST request
        $request = \Config\Services::request();
        $request->setMethod('post');
        $request->setBody(json_encode([
            'customer_name'  => 'Test Customer',
            'payment_method' => 'cash',
            'payment_amount' => 500000,
            'cart' => [
                ['product_id' => 1, 'quantity' => 3, 'unit_price' => 100000],
            ],
        ]));
        $request->setHeader('Content-Type', 'application/json');

        // Set session sebagai kasir
        session()->set(['user_id' => 2, 'user_role' => 'cashier']);

        $response = $controller->checkout();
        $body     = json_decode($response->getBody(), true);

        // Assert berhasil
        $this->assertTrue($body['success'], 'Checkout harus berhasil: ' . ($body['message'] ?? ''));
        $this->assertArrayHasKey('transaction_no', $body);

        // Assert stok berkurang
        $product = $db->query("SELECT stock FROM products WHERE id = 1")->getRowArray();
        $this->assertEquals(7, $product['stock'], 'Stok harus berkurang dari 10 menjadi 7');

        // Assert transaksi tersimpan
        $trx = $db->query("SELECT * FROM transactions WHERE transaction_no = ?", [$body['transaction_no']])->getRowArray();
        $this->assertNotNull($trx, 'Transaksi harus tersimpan di DB');
        $this->assertEquals('completed', $trx['status']);
        $this->assertEquals(300000, $trx['total']); // 3 × 100000

        // Assert transaction_details tersimpan
        $details = $db->query("SELECT * FROM transaction_details WHERE transaction_id = ?", [$trx['id']])->getResultArray();
        $this->assertCount(1, $details);
        $this->assertEquals(3, $details[0]['quantity']);

        // Assert stock_movements (OUT) dibuat
        $movement = $db->query(
            "SELECT * FROM stock_movements WHERE reference_no = ? AND type = 'OUT'",
            [$body['transaction_no']]
        )->getRowArray();
        $this->assertNotNull($movement, 'stock_movements harus dibuat');
        $this->assertEquals(-3, $movement['quantity']);
        $this->assertEquals(10, $movement['stock_before']);
        $this->assertEquals(7, $movement['stock_after']);
    }

    // ----------------------------------------------------------------
    // Test 2: Checkout gagal jika stok tidak cukup → ROLLBACK
    // ----------------------------------------------------------------
    public function testCheckoutFailsInsufficientStock(): void
    {
        $db = \Config\Database::connect();
        $db->query("UPDATE products SET stock = 2 WHERE id = 1");

        // Simpan state sebelum
        $stockBefore = $db->query("SELECT stock FROM products WHERE id = 1")->getRowArray()['stock'];
        $trxCountBefore = $db->query("SELECT COUNT(*) as cnt FROM transactions")->getRowArray()['cnt'];

        $controller = new \App\Controllers\Web\PosController();
        $request    = \Config\Services::request();
        $request->setMethod('post');
        $request->setBody(json_encode([
            'payment_method' => 'cash',
            'payment_amount' => 1000000,
            'cart' => [
                ['product_id' => 1, 'quantity' => 5, 'unit_price' => 100000], // Minta 5, stok cuma 2
            ],
        ]));
        $request->setHeader('Content-Type', 'application/json');
        session()->set(['user_id' => 2, 'user_role' => 'cashier']);

        $response = $controller->checkout();
        $body     = json_decode($response->getBody(), true);

        // Assert gagal
        $this->assertFalse($body['success'], 'Checkout harus gagal karena stok tidak cukup');
        $this->assertStringContainsString('Stok tidak cukup', $body['message']);

        // Assert ROLLBACK: stok tidak berubah
        $stockAfter = $db->query("SELECT stock FROM products WHERE id = 1")->getRowArray()['stock'];
        $this->assertEquals($stockBefore, $stockAfter, 'Stok tidak boleh berubah jika checkout gagal (rollback)');

        // Assert tidak ada transaksi baru
        $trxCountAfter = $db->query("SELECT COUNT(*) as cnt FROM transactions")->getRowArray()['cnt'];
        $this->assertEquals($trxCountBefore, $trxCountAfter, 'Tidak ada transaksi baru jika checkout gagal');
    }

    // ----------------------------------------------------------------
    // Test 3: Checkout dengan cart kosong harus ditolak
    // ----------------------------------------------------------------
    public function testCheckoutFailsEmptyCart(): void
    {
        $controller = new \App\Controllers\Web\PosController();
        $request    = \Config\Services::request();
        $request->setMethod('post');
        $request->setBody(json_encode(['cart' => []]));
        $request->setHeader('Content-Type', 'application/json');
        session()->set(['user_id' => 2, 'user_role' => 'cashier']);

        $response = $controller->checkout();
        $body     = json_decode($response->getBody(), true);

        $this->assertFalse($body['success']);
        $this->assertStringContainsString('kosong', $body['message']);
    }

    // ----------------------------------------------------------------
    // Test 4: Checkout atomik — multi-item, partial failure → ROLLBACK total
    // ----------------------------------------------------------------
    public function testCheckoutRollbackOnPartialFailure(): void
    {
        $db = \Config\Database::connect();
        $db->query("UPDATE products SET stock = 5 WHERE id = 1");
        $db->query("UPDATE products SET stock = 1 WHERE id = 2");

        $controller = new \App\Controllers\Web\PosController();
        $request    = \Config\Services::request();
        $request->setMethod('post');
        $request->setBody(json_encode([
            'payment_method' => 'cash',
            'payment_amount' => 2000000,
            'cart' => [
                ['product_id' => 1, 'quantity' => 2, 'unit_price' => 100000], // OK: stok 5
                ['product_id' => 2, 'quantity' => 3, 'unit_price' => 200000], // FAIL: stok cuma 1
            ],
        ]));
        $request->setHeader('Content-Type', 'application/json');
        session()->set(['user_id' => 2, 'user_role' => 'cashier']);

        $response = $controller->checkout();
        $body     = json_decode($response->getBody(), true);

        $this->assertFalse($body['success']);

        // ROLLBACK: stok produk 1 harus tetap 5 (tidak berkurang)
        $stock1 = $db->query("SELECT stock FROM products WHERE id = 1")->getRowArray()['stock'];
        $this->assertEquals(5, $stock1, 'Stok produk 1 harus tetap 5 karena rollback');
    }

    // ----------------------------------------------------------------
    // Test 5: Nomor transaksi unik per eksekusi
    // ----------------------------------------------------------------
    public function testTransactionNumberIsUnique(): void
    {
        $db = \Config\Database::connect();
        $db->query("UPDATE products SET stock = 100 WHERE id = 1");

        session()->set(['user_id' => 2, 'user_role' => 'cashier']);

        $nos = [];
        for ($i = 0; $i < 3; $i++) {
            $controller = new \App\Controllers\Web\PosController();
            $request    = \Config\Services::request();
            $request->setMethod('post');
            $request->setBody(json_encode([
                'payment_method' => 'cash',
                'payment_amount' => 200000,
                'cart' => [['product_id' => 1, 'quantity' => 1, 'unit_price' => 100000]],
            ]));
            $request->setHeader('Content-Type', 'application/json');
            $body = json_decode($controller->checkout()->getBody(), true);
            $nos[] = $body['transaction_no'];
        }

        $this->assertCount(3, array_unique($nos), 'Semua nomor transaksi harus unik');
    }
}
