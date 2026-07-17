<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\SettingModel;
use App\Models\UserModel;

class SettingController extends BaseController
{
    protected SettingModel $settingModel;
    protected UserModel    $userModel;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
        $this->userModel    = new UserModel();
    }

    // ----------------------------------------------------------------
    // GET /settings
    // ----------------------------------------------------------------
    public function index(): string
    {
        $this->adminOrOwner();

        $db       = \Config\Database::connect();
        $settings = $this->settingModel->getAllAsArray();

        // Auto-detect nama kolom total di tabel transactions
        $amountCol = 'grand_total';
        try {
            $cols     = $db->query("
                SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_NAME   = 'transactions'
                  AND TABLE_SCHEMA = DATABASE()
            ")->getResultArray();
            $colNames = array_column($cols, 'COLUMN_NAME');
            foreach (['total_amount', 'grand_total', 'total', 'amount', 'subtotal'] as $candidate) {
                if (in_array($candidate, $colNames)) {
                    $amountCol = $candidate;
                    break;
                }
            }
        } catch (\Exception $e) {}

        // Hitung stats — masing-masing dibungkus try-catch agar page tetap load
        $stats = [
            'total_products' => 0,
            'total_users'    => 0,
            'low_stock'      => 0,
            'today_trx'      => 0,
            'month_revenue'  => 0,
        ];

        try {
            $stats['total_products'] = $db->query("
                SELECT COUNT(*) as c FROM products
                WHERE is_active = 1 AND deleted_at IS NULL
            ")->getRowArray()['c'] ?? 0;
        } catch (\Exception $e) {}

        try {
            $stats['total_users'] = $db->query("
                SELECT COUNT(*) as c FROM users WHERE is_active = 1
            ")->getRowArray()['c'] ?? 0;
        } catch (\Exception $e) {}

        try {
            $stats['low_stock'] = $db->query("
                SELECT COUNT(*) as c FROM products
                WHERE stock <= stock_minimum AND is_active = 1 AND deleted_at IS NULL
            ")->getRowArray()['c'] ?? 0;
        } catch (\Exception $e) {}

        try {
            $stats['today_trx'] = $db->query("
                SELECT COUNT(*) as c FROM transactions
                WHERE DATE(created_at) = CURDATE()
            ")->getRowArray()['c'] ?? 0;
        } catch (\Exception $e) {}

        try {
            $stats['month_revenue'] = $db->query("
                SELECT COALESCE(SUM(`{$amountCol}`), 0) as r FROM transactions
                WHERE MONTH(created_at) = MONTH(NOW())
                  AND YEAR(created_at)  = YEAR(NOW())
            ")->getRowArray()['r'] ?? 0;
        } catch (\Exception $e) {}

        $users = $this->userModel->orderBy('name', 'ASC')->findAll();

        return view('settings/index', compact('settings', 'stats', 'users'));
    }

    // ----------------------------------------------------------------
    // POST /settings/store  (simpan profil toko & transaksi)
    // ----------------------------------------------------------------
    public function store()
    {
        $this->adminOrOwner();

        $keys = [
            'store_name', 'store_address', 'store_phone',
            'store_email', 'store_city',
            'invoice_prefix', 'tax_rate', 'auto_print',
            'currency', 'lead_time_days',
        ];

        foreach ($keys as $key) {
            $val = $this->request->getPost($key);
            if ($val !== null) {
                $this->settingModel->setValue($key, $val);
            }
        }

        return redirect()->to(base_url('settings'))
                         ->with('success', 'Pengaturan berhasil disimpan.');
    }

    // ----------------------------------------------------------------
    // POST /settings  (alias lama — redirect ke store)
    // ----------------------------------------------------------------
    public function update()
    {
        return $this->store();
    }

    // ----------------------------------------------------------------
    // GET /settings/users
    // ----------------------------------------------------------------
    public function users(): string
    {
        $this->ownerOnly();
        $users = $this->userModel->orderBy('name', 'ASC')->findAll();
        return view('settings/users', compact('users'));
    }

    // ----------------------------------------------------------------
    // POST /settings/user/store  (tambah / edit user)
    // ----------------------------------------------------------------
    public function storeUser()
    {
        $this->ownerOnly();

        $userId = (int) $this->request->getPost('user_id');
        $isEdit = $userId > 0;

        $rules = [
            'name' => 'required|min_length[3]',
            'role' => 'required|in_list[owner,admin,cashier]',
        ];

        if ($isEdit) {
            // Email unik kecuali milik user itu sendiri
            $rules['email'] = "required|valid_email|is_unique[users.email,id,{$userId}]";
        } else {
            $rules['email']    = 'required|valid_email|is_unique[users.email]';
            $rules['password'] = 'required|min_length[6]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                             ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'role'  => $this->request->getPost('role'),
        ];

        $password = $this->request->getPost('password');
        if ($password) {
            $data['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        if ($isEdit) {
            $this->userModel->update($userId, $data);
            $msg = 'User berhasil diperbarui.';
        } else {
            $data['is_active'] = 1;
            $this->userModel->insert($data);
            $msg = 'User berhasil ditambahkan.';
        }

        return redirect()->to(base_url('settings'))
                         ->with('success', $msg);
    }

    // ----------------------------------------------------------------
    // POST /settings/user/delete
    // ----------------------------------------------------------------
    public function deleteUser()
    {
        $this->ownerOnly();

        $id = (int) $this->request->getPost('user_id');

        if ($id && $id !== (int) session('user_id')) {
            $this->userModel->delete($id);
        }

        return redirect()->to(base_url('settings'))
                         ->with('success', 'User berhasil dihapus.');
    }

    // ----------------------------------------------------------------
    // POST /settings/users/:id/toggle
    // ----------------------------------------------------------------
    public function toggleUser(int $id)
    {
        $this->ownerOnly();

        $user = $this->userModel->find($id);
        if ($user && $user['id'] !== (int) session('user_id')) {
            $this->userModel->update($id, [
                'is_active' => $user['is_active'] ? 0 : 1,
            ]);
        }

        return redirect()->to(base_url('settings'))
                         ->with('success', 'Status user diperbarui.');
    }

    // ----------------------------------------------------------------
    // POST /settings/backup
    // ----------------------------------------------------------------
    public function backup()
    {
        $this->ownerOnly();
        // Implementasi backup opsional — redirect dengan info
        return redirect()->to(base_url('settings'))
                         ->with('success', 'Fitur backup sedang dalam pengembangan.');
    }

    // ----------------------------------------------------------------
    // Helper: admin atau owner boleh akses (pengaturan toko)
    // ----------------------------------------------------------------
    private function adminOrOwner(): void
    {
        if (!session('user_id')) {
            redirect()->to(base_url('login'))->send();
            exit;
        }
        if (!in_array(session('user_role'), ['admin', 'owner'])) {
            session()->setFlashdata('error', 'Akses ditolak.');
            redirect()->to(base_url('dashboard'))->send();
            exit;
        }
    }

    // ----------------------------------------------------------------
    // Helper: hanya owner yang boleh akses (manajemen user)
    // ----------------------------------------------------------------
    private function ownerOnly(): void
    {
        if (!session('user_id')) {
            redirect()->to(base_url('login'))->send();
            exit;
        }
        if (session('user_role') !== 'owner') {
            session()->setFlashdata('error', 'Akses ditolak. Hanya Owner yang dapat mengelola pengguna.');
            redirect()->to(base_url('settings'))->send();
            exit;
        }
    }
}