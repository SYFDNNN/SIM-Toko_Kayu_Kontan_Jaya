<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * AuthTest — Login, logout, RBAC
 */
class AuthTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    protected $refresh = true;
    protected $seed    = 'TestSeeder';

    public function testLoginSuccess(): void
    {
        $response = $this->post('/login', [
            'email'    => 'owner@tokokayukontan.com',
            'password' => 'password123',
        ]);
        $response->assertRedirectTo(base_url('dashboard'));
        $this->assertTrue(session()->get('logged_in'), 'Session harus set logged_in=true');
        $this->assertEquals('owner', session()->get('user_role'));
    }

    public function testLoginFailWrongPassword(): void
    {
        $response = $this->post('/login', [
            'email'    => 'owner@tokokayukontan.com',
            'password' => 'wrongpassword',
        ]);
        $this->assertFalse((bool) session()->get('logged_in'), 'Login harus gagal dengan password salah');
    }

    public function testLoginFailInvalidEmail(): void
    {
        $response = $this->post('/login', [
            'email'    => 'notanemail',
            'password' => 'password123',
        ]);
        $this->assertFalse((bool) session()->get('logged_in'));
    }

    public function testProtectedRouteRedirectsIfNotLoggedIn(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirectTo(base_url('login'));
    }

    public function testCashierCannotAccessOwnerRoutes(): void
    {
        session()->set(['user_id' => 2, 'user_role' => 'cashier', 'logged_in' => true]);
        $response = $this->get('/settings/users');
        $response->assertStatus(404); // ownerOnly() throws PageNotFoundException
    }
}

// ==============================================================

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\ProductModel;

/**
 * ProductTest — CRUD produk, validasi SKU unik
 */
class ProductTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    protected $refresh = true;
    protected $seed    = 'TestSeeder';

    protected ProductModel $model;

    public function setUp(): void
    {
        parent::setUp();
        $this->model = new ProductModel();
    }

    public function testCreateProductSuccess(): void
    {
        $id = $this->model->insert([
            'category_id'   => 1,
            'sku'           => 'TEST-001',
            'name'          => 'Produk Test',
            'cost_price'    => 100000,
            'selling_price' => 150000,
            'stock'         => 10,
            'stock_minimum' => 3,
            'unit'          => 'pcs',
        ]);
        $this->assertGreaterThan(0, $id, 'Produk harus berhasil dibuat');
        $product = $this->model->find($id);
        $this->assertEquals('TEST-001', $product['sku']);
    }

    public function testSkuMustBeUnique(): void
    {
        // SKU 'MJK-001' sudah ada dari seeder
        $result = $this->model->insert([
            'category_id'   => 1,
            'sku'           => 'MJK-001', // duplicate
            'name'          => 'Produk Duplikat',
            'cost_price'    => 100000,
            'selling_price' => 150000,
            'stock'         => 5,
            'stock_minimum' => 2,
            'unit'          => 'pcs',
        ]);
        $this->assertFalse((bool) $result, 'Insert dengan SKU duplikat harus gagal');
    }

    public function testSoftDeleteDoesNotRemoveFromDB(): void
    {
        $this->model->delete(1);
        // Tanpa withDeleted, tidak terlihat
        $product = $this->model->find(1);
        $this->assertNull($product, 'Soft deleted product tidak boleh ditemukan');
        // Dengan withDeleted, masih ada
        $productWithDeleted = $this->model->withDeleted()->find(1);
        $this->assertNotNull($productWithDeleted, 'Soft deleted product harus masih ada di DB');
        $this->assertNotNull($productWithDeleted['deleted_at']);
    }

    public function testRopCalculation(): void
    {
        // ROP = 0 jika tidak ada penjualan
        $rop = $this->model->calculateRop(1);
        $this->assertIsFloat($rop);
        $this->assertGreaterThanOrEqual(0, $rop);
    }
}
