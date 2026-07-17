<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\TransactionModel;
use App\Controllers\Web\PosController;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * TransactionApiController — RESTful API untuk transaksi
 */
class TransactionApiController extends BaseController
{
    protected TransactionModel $transactionModel;

    public function __construct()
    {
        $this->transactionModel = new TransactionModel();
    }

    // GET /api/transactions
    public function index(): ResponseInterface
    {
        $start  = $this->request->getGet('start_date') ?? date('Y-m-01');
        $end    = $this->request->getGet('end_date')   ?? date('Y-m-d');
        $status = $this->request->getGet('status')     ?? 'completed';
        $limit  = min((int)($this->request->getGet('limit') ?? 50), 200);

        $data = $this->transactionModel
            ->select('transactions.*, users.name as cashier_name')
            ->join('users', 'users.id = transactions.user_id')
            ->where('transaction_date >=', $start . ' 00:00:00')
            ->where('transaction_date <=', $end . ' 23:59:59')
            ->where('status', $status)
            ->orderBy('transaction_date', 'DESC')
            ->limit($limit)
            ->findAll();

        return $this->response->setJSON(['data' => $data]);
    }

    // GET /api/transactions/:id
    public function show(int $id): ResponseInterface
    {
        $trx = $this->transactionModel->getTransactionWithDetails($id);
        if (!$trx) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Transaksi tidak ditemukan.']);
        }
        return $this->response->setJSON(['data' => $trx]);
    }

    // POST /api/transactions/checkout
    // Delegate ke PosController::checkout() agar tidak duplikasi logika atomik
    public function checkout(): ResponseInterface
    {
        $posController = new PosController();
        return $posController->checkout();
    }
}
