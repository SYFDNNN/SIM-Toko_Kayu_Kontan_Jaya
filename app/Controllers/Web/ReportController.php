<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\TransactionModel;

/**
 * ReportController — Laporan harian/bulanan, export CSV & PDF
 */
class ReportController extends BaseController
{
    protected TransactionModel $transactionModel;

    public function __construct()
    {
        $this->transactionModel = new TransactionModel();
    }

    // GET /reports
    public function index(): string
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate   = $this->request->getGet('end_date')   ?? date('Y-m-d');

        $salesSummary  = $this->transactionModel->getSalesSummary($startDate, $endDate);
        $salesByProduct = $this->transactionModel->getSalesByProduct($startDate, $endDate);

        // Total keseluruhan
        $grandTotal = array_sum(array_column($salesSummary, 'total_revenue'));
        $totalTrx   = array_sum(array_column($salesSummary, 'total_transactions'));

        return view('reports/index', compact('salesSummary', 'salesByProduct', 'grandTotal', 'totalTrx', 'startDate', 'endDate'));
    }

    // GET /reports/export-csv
    public function exportCsv()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate   = $this->request->getGet('end_date')   ?? date('Y-m-d');

        $data = $this->transactionModel->getSalesByProduct($startDate, $endDate);

        $filename = "laporan_{$startDate}_sd_{$endDate}.csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF"); // BOM UTF-8 untuk Excel

        fputcsv($out, ['SKU', 'Nama Produk', 'Kategori', 'Total Qty', 'Total Pendapatan']);
        foreach ($data as $row) {
            fputcsv($out, [
                $row['sku'], $row['product_name'], $row['category_name'],
                $row['total_qty'], $row['total_revenue'],
            ]);
        }
        fclose($out);
        exit;
    }

    // GET /reports/export-pdf
    // Menggunakan tampilan HTML yang dicetak ke PDF via browser print dialog
    // Untuk PDF server-side, install DOMPDF: composer require dompdf/dompdf
    public function exportPdf(): string
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate   = $this->request->getGet('end_date')   ?? date('Y-m-d');

        $salesByProduct = $this->transactionModel->getSalesByProduct($startDate, $endDate);
        $grandTotal     = array_sum(array_column($salesByProduct, 'total_revenue'));

        $settingModel = new \App\Models\SettingModel();
        $settings     = $settingModel->getAllAsArray();

        // Render view khusus PDF (printable HTML)
        return view('reports/pdf', compact('salesByProduct', 'grandTotal', 'startDate', 'endDate', 'settings'));
    }
}
