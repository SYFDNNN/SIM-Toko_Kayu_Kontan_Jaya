<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\CategoryModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * ProductApiController — RESTful API untuk produk
 * Base URL: /api/products
 */
class ProductApiController extends BaseController
{
    protected ProductModel $productModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
    }

    // GET /api/products
    public function index(): ResponseInterface
    {
        $page     = (int) ($this->request->getGet('page') ?? 1);
        $limit    = min((int) ($this->request->getGet('limit') ?? 15), 100);
        $search   = $this->request->getGet('search') ?? '';
        $catId    = $this->request->getGet('category_id');

        $builder = $this->productModel
            ->select('products.*, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id');

        if ($search) {
            $builder->groupStart()->like('products.name', $search)->orLike('products.sku', $search)->groupEnd();
        }
        if ($catId) $builder->where('products.category_id', $catId);

        $offset   = ($page - 1) * $limit;
        $total    = $builder->countAllResults(false);
        $products = $builder->limit($limit, $offset)->findAll();

        return $this->response->setJSON([
            'data' => $products,
            'meta' => ['total' => $total, 'per_page' => $limit, 'current_page' => $page, 'last_page' => ceil($total / $limit)],
        ]);
    }

    // GET /api/products/:id
    public function show(int $id): ResponseInterface
    {
        $product = $this->productModel->getWithCategory($id);
        if (!$product) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Produk tidak ditemukan.']);
        }
        return $this->response->setJSON(['data' => $product]);
    }

    // POST /api/products
    public function create(): ResponseInterface
    {
        if (session('user_role') !== 'owner') {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'Akses ditolak.']);
        }
        $data = $this->request->getJSON(true);
        $id   = $this->productModel->insert($data);
        if (!$id) {
            return $this->response->setStatusCode(422)->setJSON(['errors' => $this->productModel->errors()]);
        }
        return $this->response->setStatusCode(201)->setJSON(['success' => true, 'id' => $id]);
    }

    // PUT /api/products/:id
    public function update(int $id): ResponseInterface
    {
        if (session('user_role') !== 'owner') {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'Akses ditolak.']);
        }
        $data = $this->request->getJSON(true);
        $ok   = $this->productModel->update($id, $data);
        if (!$ok) {
            return $this->response->setStatusCode(422)->setJSON(['errors' => $this->productModel->errors()]);
        }
        return $this->response->setJSON(['success' => true]);
    }

    // DELETE /api/products/:id
    public function delete(int $id): ResponseInterface
    {
        if (session('user_role') !== 'owner') {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'Akses ditolak.']);
        }
        $this->productModel->delete($id);
        return $this->response->setJSON(['success' => true]);
    }
}
