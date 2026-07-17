<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

/**
 * AuthFilter — Proteksi route yang memerlukan login dan/atau role tertentu.
 *
 * Cara pakai di Routes.php:
 *   $routes->get('...', '...', ['filter' => 'auth']);               // wajib login
 *   $routes->get('...', '...', ['filter' => 'auth:admin']);         // wajib admin
 *   $routes->get('...', '...', ['filter' => 'auth:admin,owner']);   // admin atau owner
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. Wajib login
        if (!session()->get('logged_in')) {
            session()->set('redirect_url', current_url());
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // 2. Cek role jika ada argumen
        if (!empty($arguments)) {
            $userRole = session('user_role');
            if (!in_array($userRole, $arguments)) {
                return redirect()->to('/dashboard')
                    ->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk halaman ini.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // tidak ada yang perlu dilakukan setelah request
    }
}
