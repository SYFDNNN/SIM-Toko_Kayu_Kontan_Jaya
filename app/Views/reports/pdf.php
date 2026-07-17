<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <title>Laporan Penjualan — <?= esc($settings['store_name'] ?? 'Toko Kayu Kontan Jaya') ?></title>
  <style>
    body { font-family: Arial, sans-serif; font-size: 11px; color: #1a1a1a; margin: 20px; }
    h1 { font-size: 18px; color: #78350f; }
    .header { border-bottom: 2px solid #d97706; padding-bottom: 10px; margin-bottom: 16px; }
    .meta { font-size: 10px; color: #6b7280; margin-top: 4px; }
    table { width: 100%; border-collapse: collapse; margin-top: 12px; }
    th { background: #fef3c7; color: #78350f; padding: 8px 10px; text-align: left; font-size: 10px; text-transform: uppercase; border: 1px solid #e5e7eb; }
    td { padding: 7px 10px; border: 1px solid #e5e7eb; }
    tr:nth-child(even) { background: #fffbeb; }
    .total-row { font-weight: bold; background: #fef3c7 !important; }
    .amount { text-align: right; }
    .footer { margin-top: 20px; font-size: 10px; color: #9ca3af; text-align: center; }
    @media print {
      .no-print { display: none; }
      @page { margin: 15mm; }
    }
  </style>
</head>
<body>

<div class="header">
  <h1><?= esc($settings['store_name'] ?? 'Toko Kayu Kontan Jaya') ?></h1>
  <div class="meta">
    <?= esc($settings['store_address'] ?? '') ?> &nbsp;|&nbsp; Telp: <?= esc($settings['store_phone'] ?? '') ?>
  </div>
  <div style="margin-top:8px; font-size:13px; font-weight:bold; color:#374151;">
    Laporan Penjualan Per Produk
  </div>
  <div class="meta">Periode: <?= esc($startDate) ?> s/d <?= esc($endDate) ?></div>
  <div class="meta">Dicetak: <?= date('d/m/Y H:i:s') ?></div>
</div>

<table>
  <thead>
    <tr>
      <th>No</th>
      <th>SKU</th>
      <th>Nama Produk</th>
      <th>Kategori</th>
      <th style="text-align:right">Qty Terjual</th>
      <th style="text-align:right">Total Pendapatan</th>
    </tr>
  </thead>
  <tbody>
    <?php if (empty($salesByProduct)): ?>
    <tr>
      <td colspan="6" style="text-align:center; color:#9ca3af; padding:20px;">Tidak ada data untuk periode ini</td>
    </tr>
    <?php else: ?>
    <?php foreach ($salesByProduct as $i => $row): ?>
    <tr>
      <td><?= $i + 1 ?></td>
      <td><?= esc($row['sku']) ?></td>
      <td><?= esc($row['product_name']) ?></td>
      <td><?= esc($row['category_name']) ?></td>
      <td class="amount"><?= number_format($row['total_qty']) ?></td>
      <td class="amount">Rp <?= number_format($row['total_revenue'], 0, ',', '.') ?></td>
    </tr>
    <?php endforeach; ?>
    <tr class="total-row">
      <td colspan="4" style="text-align:right">TOTAL KESELURUHAN</td>
      <td class="amount"><?= number_format(array_sum(array_column($salesByProduct, 'total_qty'))) ?></td>
      <td class="amount">Rp <?= number_format($grandTotal, 0, ',', '.') ?></td>
    </tr>
    <?php endif; ?>
  </tbody>
</table>

<div class="footer">
  Laporan ini digenerate otomatis oleh Sistem Manajemen Stok &amp; Penjualan Toko Kayu Kontan Jaya
</div>

<div class="no-print" style="text-align:center; margin-top:24px;">
  <button onclick="window.print()" style="background:#78350f;color:white;border:none;padding:10px 28px;border-radius:8px;cursor:pointer;font-weight:bold;">
    🖨️ Print PDF
  </button>
  <a href="/reports" style="margin-left:12px;color:#78350f;">← Kembali ke Laporan</a>
</div>

</body>
</html>
