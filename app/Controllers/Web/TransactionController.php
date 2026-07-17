<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\TransactionModel;

/**
 * TransactionController — Riwayat & Detail Transaksi
 */
class TransactionController extends BaseController
{
    protected TransactionModel $transactionModel;

    public function __construct()
    {
        $this->transactionModel = new TransactionModel();
    }

    // GET /transactions
    public function index(): string
    {
        $perPage   = 20;
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate   = $this->request->getGet('end_date')   ?? date('Y-m-d');
        $status    = $this->request->getGet('status')     ?? 'completed';

        $builder = $this->transactionModel
            ->select('transactions.*, users.name as cashier_name')
            ->join('users', 'users.id = transactions.user_id')
            ->where('transactions.transaction_date >=', $startDate . ' 00:00:00')
            ->where('transactions.transaction_date <=', $endDate . ' 23:59:59');

        if ($status) {
            $builder->where('transactions.status', $status);
        }

        $transactions = $builder->orderBy('transactions.transaction_date', 'DESC')->paginate($perPage);
        $pager        = $this->transactionModel->pager;

        return view('transactions/index', compact('transactions', 'pager', 'startDate', 'endDate', 'status'));
    }

    // GET /transactions/:id
    public function show(int $id): string
    {
        $transaction = $this->transactionModel->getTransactionWithDetails($id);
        if (!$transaction) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Transaksi tidak ditemukan.');
        }
        return view('transactions/show', compact('transaction'));
    }
}
