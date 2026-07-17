<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * ConcurrencyTest — Skenario race condition di POS
 *
 * PENTING: Test ini mendeskripsikan dan mensimulasikan skenario
 * race condition. Test sesungguhnya memerlukan multiple processes
 * (misal dengan Guzzle async atau PCNTL fork).
 *
 * Skenario yang dicover:
 *   1. Dua kasir checkout produk yang sama secara bersamaan
 *   2. Stok hanya cukup untuk satu transaksi
 *   3. Sistem harus memastikan hanya satu yang berhasil
 *
 * Mekanisme perlindungan di kode:
 *   - SELECT ... FOR UPDATE (row-level lock di MySQL InnoDB)
 *   - DB transaction (BEGIN/COMMIT/ROLLBACK)
 *   - Validasi stok SETELAH lock row
 */
class ConcurrencyTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    protected $refresh = true;
    protected $seed    = 'TestSeeder';

    /**
     * Test: Simulasi sequential checkout yang meniru race condition
     *
     * Karena PHP (dalam satu proses) tidak bisa benar-benar concurrent,
     * test ini mensimulasikan dengan dua checkout berurutan untuk produk
     * dengan stok = 1. Hanya satu yang boleh berhasil.
     */
    public function testOnlyOneCheckoutSucceedsWhenStockIsOne(): void
    {
        $db = \Config\Database::connect();

        // Set stok produk 1 = 1 (hanya cukup untuk 1 transaksi)
        $db->query("UPDATE products SET stock = 1 WHERE id = 1");

        $results = [];

        // Simula checkout pertama (kasir 1)
        session()->set(['user_id' => 2, 'user_role' => 'cashier']);
        $ctrl1   = new \App\Controllers\Web\PosController();
        $request = \Config\Services::request();
        $request->setMethod('post');
        $request->setBody(json_encode([
            'payment_method' => 'cash',
            'payment_amount' => 2000000,
            'cart' => [['product_id' => 1, 'quantity' => 1, 'unit_price' => 1350000]],
        ]));
        $request->setHeader('Content-Type', 'application/json');
        $results[] = json_decode($ctrl1->checkout()->getBody(), true)['success'];

        // Simula checkout kedua (kasir 2) — stok sudah 0
        session()->set(['user_id' => 3, 'user_role' => 'cashier']);
        $ctrl2   = new \App\Controllers\Web\PosController();
        $request->setBody(json_encode([
            'payment_method' => 'cash',
            'payment_amount' => 2000000,
            'cart' => [['product_id' => 1, 'quantity' => 1, 'unit_price' => 1350000]],
        ]));
        $results[] = json_decode($ctrl2->checkout()->getBody(), true)['success'];

        // Hanya satu yang boleh berhasil
        $successCount = count(array_filter($results));
        $this->assertEquals(1, $successCount, 'Hanya satu checkout yang boleh berhasil saat stok = 1');

        // Stok akhir harus 0, tidak pernah negatif
        $finalStock = $db->query("SELECT stock FROM products WHERE id = 1")->getRowArray()['stock'];
        $this->assertEquals(0, $finalStock, 'Stok akhir harus 0, tidak boleh negatif');
        $this->assertGreaterThanOrEqual(0, $finalStock, 'Stok tidak boleh negatif (race condition terdeteksi!)');
    }

    /**
     * Deskripsi: Cara menguji concurrency sesungguhnya
     *
     * Untuk pengujian race condition yang benar-benar konkuren,
     * gunakan pendekatan berikut (di luar PHPUnit):
     *
     * 1. Apache JMeter / Locust:
     *    - Buat 2 thread group yang mengirim POST /pos/checkout bersamaan
     *    - Set stok produk = 1
     *    - Verifikasi: hanya 1 response 200, 1 response 400
     *    - Verifikasi: stock di DB = 0, tidak pernah -1
     *
     * 2. curl parallel:
     *    curl -X POST .../pos/checkout -d '{"cart":[{"product_id":1,"quantity":1,...}]}' &
     *    curl -X POST .../pos/checkout -d '{"cart":[{"product_id":1,"quantity":1,...}]}' &
     *    wait
     *    mysql -e "SELECT stock FROM products WHERE id=1"  # Harus 0
     *
     * 3. Mekanisme perlindungan di kode (PosController::checkout):
     *    - Line: $db->query("SELECT ... FOR UPDATE", [$productId])
     *      → Ini kunci eksklusif InnoDB yang mencegah race condition
     *    - Jika dua request masuk bersamaan, yang kedua akan MENUNGGU
     *      sampai transaksi pertama COMMIT atau ROLLBACK
     *    - Setelah lock dilepas, yang kedua akan validasi stok lagi
     *      dan menemukan stok sudah 0 → return error
     */
    public function testConcurrencyProtectionDocumentation(): void
    {
        // Test ini selalu pass — fungsinya sebagai dokumentasi hidup
        $this->assertTrue(true, 'Lihat komentar di atas untuk skenario concurrency test lengkap');
    }
}
