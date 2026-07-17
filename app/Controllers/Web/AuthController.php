<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\UserModel;

class AuthController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function loginForm()
{
    if (session('user_id')) {
        return redirect()->to(base_url('dashboard'));
    }
    return view('auth/login', ['title' => 'Login']);
}

public function loginProcess()
{
    $email    = $this->request->getPost('email');
    $password = $this->request->getPost('password');

    $user = $this->userModel
                 ->where('email', $email)
                 ->where('is_active', 1)
                 ->first();

    if (!$user || !password_verify($password, $user['password'])) {
        return redirect()->to(base_url('login'))
                         ->with('error', 'Email atau password salah, atau akun tidak aktif.');
    }

    session()->set([
        'user_id'   => $user['id'],
        'user_name' => $user['name'],
        'user_role' => $user['role'],
        'logged_in' => true,
    ]);

    return redirect()->to(base_url('dashboard'));
}

public function logout()
{
    session()->destroy();
    return redirect()->to(base_url('login'));
}
}