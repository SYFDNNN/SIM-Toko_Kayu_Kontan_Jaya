<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\CategoryModel;

/**
 * ProductController — CRUD Produk + Import/Export CSV
 */
class ProductController extends BaseController
{
    protected ProductModel $productModel;
    protected CategoryModel $categoryModel;

    public function __construct()
    {
        $this->productModel  = new ProductModel();
        $this->categoryModel = new CategoryModel();
    }

    // GET /products
    public function index(): string
    {
        $perPage  = 15;
        $search   = $this->request->getGet('search') ?? '';
        $category = $this->request->getGet('category') ?? '';

        $builder = $this->productModel
            ->select('products.*, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id');

        if ($search) {
            $builder->groupStart()
                ->like('products.name', $search)
                ->orLike('products.sku', $search)
            ->groupEnd();
        }
        if ($category) {
            $builder->where('products.category_id', $category);
        }

        $products   = $builder->paginate($perPage, 'default');
        $pager      = $this->productModel->pager;
        $db = \Config\Database::connect();
$categories = $db->query("
    SELECT c.*, COUNT(p.id) as product_count
    FROM categories c
    LEFT JOIN products p ON p.category_id = c.id
        AND p.is_active = 1
        AND p.deleted_at IS NULL
    GROUP BY c.id
    ORDER BY c.name ASC
")->getResultArray();

        return view('products/index', compact('products', 'pager', 'categories', 'search', 'category'));
    }

    // GET /products/create
    public function create(): string
    {
        $this->adminOnly();
        return view('products/form', [
            'title'      => 'Tambah Produk',
            'product'    => null,
            'categories' => $this->categoryModel->findAll(),
        ]);
    }

    // POST /products/store
    public function store()
    {
        $this->adminOnly();

        $rules = [
            'sku'           => 'required|min_length[3]|is_unique[products.sku]',
            'name'          => 'required|min_length[3]',
            'category_id'   => 'required|integer',
            'cost_price'    => 'required|decimal',
            'selling_price' => 'required|decimal',
            'stock'         => 'required|integer|greater_than_equal_to[0]',
            'stock_minimum' => 'required|integer|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();

        // Handle file upload gambar
        $image = $this->request->getFile('image');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $this->validateImageFile($image);
            $newName = $image->getRandomName();
            $image->move(WRITEPATH . '../public/uploads/products', $newName);
            $data['image'] = 'products/' . $newName;
        }

        unset($data['csrf_test_name'], $data['image_file']); // Bersihkan field extra

        $this->productModel->insert($data);

        return redirect()->to(base_url('products'))->with('success', 'Produk berhasil ditambahkan.');
    }

    // GET /products/:id/edit
    public function edit(int $id): string
    {
        $this->adminOnly();
        $product = $this->productModel->findOrFail($id);
        return view('products/form', [
            'title'      => 'Edit Produk',
            'product'    => $product,
            'categories' => $this->categoryModel->findAll(),
        ]);
    }

    // POST /products/:id/update
    public function update(int $id)
    {
        $this->adminOnly();

        $rules = [
            'sku'           => "required|min_length[3]|is_unique[products.sku,id,{$id}]",
            'name'          => 'required|min_length[3]',
            'category_id'   => 'required|integer',
            'cost_price'    => 'required|decimal',
            'selling_price' => 'required|decimal',
            'stock_minimum' => 'required|integer|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();

        $image = $this->request->getFile('image');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $this->validateImageFile($image);
            $newName = $image->getRandomName();
            $image->move(WRITEPATH . '../public/uploads/products', $newName);
            $data['image'] = 'products/' . $newName;
        }

        unset($data['csrf_test_name']);
        $this->productModel->update($id, $data);

        return redirect()->to(base_url('products'))->with('success', 'Produk berhasil diperbarui.');
    }

    // POST /products/:id/delete
    public function delete(int $id)
    {
        $this->adminOnly();
        $this->productModel->delete($id); // Soft delete
        return redirect()->to(base_url('products'))->with('success', 'Produk berhasil dihapus.');
    }

    // ----------------------------------------------------------------
    // GET /products/export-csv
    // Export semua produk ke CSV
    // ----------------------------------------------------------------
    public function exportCsv()
    {
        $this->adminOnly();
        $products = $this->productModel
            ->select('products.sku, products.name, categories.name as category, products.cost_price, products.selling_price, products.stock, products.stock_minimum, products.unit')
            ->join('categories', 'categories.id = products.category_id')
            ->findAll();

        $filename = 'products_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        // BOM untuk Excel agar bisa baca UTF-8
        fwrite($output, "\xEF\xBB\xBF");

        fputcsv($output, ['SKU', 'Nama Produk', 'Kategori', 'Harga Beli', 'Harga Jual', 'Stok', 'Stok Minimum', 'Satuan']);

        foreach ($products as $p) {
            fputcsv($output, [
                $p['sku'], $p['name'], $p['category'],
                $p['cost_price'], $p['selling_price'],
                $p['stock'], $p['stock_minimum'], $p['unit'],
            ]);
        }
        fclose($output);
        exit;
    }

    // ----------------------------------------------------------------
    // GET  /products/import     → form import
    // POST /products/import     → proses import CSV
    // ----------------------------------------------------------------
    public function importForm(): string
    {
        $this->adminOnly();
        return view('products/import', ['title' => 'Import Produk dari CSV']);
    }

    public function importProcess()
    {
        $this->adminOnly();

        $file = $this->request->getFile('csv_file');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid.');
        }

        if ($file->getClientExtension() !== 'csv') {
            return redirect()->back()->with('error', 'Hanya file CSV yang diterima.');
        }

        $handle  = fopen($file->getTempName(), 'r');
        $headers = fgetcsv($handle); // baca baris header

        // Mapping kolom CSV ke kolom database
        $colMap = [
            'sku'           => array_search('SKU', $headers),
            'name'          => array_search('Nama Produk', $headers),
            'category'      => array_search('Kategori', $headers),
            'cost_price'    => array_search('Harga Beli', $headers),
            'selling_price' => array_search('Harga Jual', $headers),
            'stock'         => array_search('Stok', $headers),
            'stock_minimum' => array_search('Stok Minimum', $headers),
            'unit'          => array_search('Satuan', $headers),
        ];

        $imported = 0;
        $errors   = [];

        while (($row = fgetcsv($handle)) !== false) {
            try {
                // Cari atau buat kategori
                $catName = trim($row[$colMap['category']] ?? '');
                $cat     = $this->categoryModel->where('name', $catName)->first();
                if (!$cat) {
                    $catId = $this->categoryModel->insert(['name' => $catName, 'slug' => url_title($catName, '-', true)]);
                } else {
                    $catId = $cat['id'];
                }

                $sku = trim($row[$colMap['sku']] ?? '');
                // Upsert: update jika SKU sudah ada
                $existing = $this->productModel->where('sku', $sku)->first();
                $productData = [
                    'category_id'   => $catId,
                    'sku'           => $sku,
                    'name'          => trim($row[$colMap['name']] ?? ''),
                    'cost_price'    => (float) ($row[$colMap['cost_price']] ?? 0),
                    'selling_price' => (float) ($row[$colMap['selling_price']] ?? 0),
                    'stock'         => (int)   ($row[$colMap['stock']] ?? 0),
                    'stock_minimum' => (int)   ($row[$colMap['stock_minimum']] ?? 5),
                    'unit'          => trim($row[$colMap['unit']] ?? 'pcs'),
                ];

                if ($existing) {
                    $this->productModel->update($existing['id'], $productData);
                } else {
                    $this->productModel->insert($productData);
                }
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Baris {$imported}: " . $e->getMessage();
            }
        }
        fclose($handle);

        $msg = "Berhasil mengimport {$imported} produk.";
        if ($errors) {
            $msg .= ' Terdapat ' . count($errors) . ' error.';
        }

        return redirect()->to('/products')->with('success', $msg);
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------
    private function adminOnly(): void
    {
        if (!session('user_id')) {
            redirect()->to(base_url('login'))->send();
            exit;
        }
        if (session('user_role') !== 'admin') {
            session()->setFlashdata('error', 'Akses ditolak.');
            redirect()->to(base_url('dashboard'))->send();
            exit;
        }
    }

    private function validateImageFile($file): void
    {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array(strtolower($file->getClientExtension()), $allowed)) {
            throw new \Exception('Tipe file gambar tidak didukung. Gunakan JPG, PNG, atau WebP.');
        }
        if ($file->getSizeByUnit('mb') > 2) {
            throw new \Exception('Ukuran gambar maksimal 2MB.');
        }
    }
}
