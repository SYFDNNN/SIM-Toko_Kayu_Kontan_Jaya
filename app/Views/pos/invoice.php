<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Invoice <?= esc($transaction['transaction_no']) ?></title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Courier New', monospace; font-size: 12px; color: #1a1a1a; background: white; }

    .invoice-wrapper { max-width: 320px; margin: 0 auto; padding: 20px 16px; }

    .header { text-align: center; border-bottom: 2px dashed #ccc; padding-bottom: 12px; margin-bottom: 12px; }
    .store-name { font-size: 16px; font-weight: bold; letter-spacing: 1px; }
    .store-info { font-size: 10px; color: #555; margin-top: 4px; line-height: 1.5; }

    .trx-info { margin-bottom: 10px; }
    .trx-info table { width: 100%; }
    .trx-info td { padding: 2px 0; font-size: 11px; }
    .trx-info td:last-child { text-align: right; }

    .divider { border: none; border-top: 1px dashed #ccc; margin: 10px 0; }

    .items-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    .items-table th { font-size: 10px; text-align: left; border-bottom: 1px solid #ccc; padding: 4px 0; text-transform: uppercase; }
    .items-table td { font-size: 11px; padding: 4px 0; vertical-align: top; }
    .items-table td:last-child { text-align: right; }

    .totals { margin-top: 6px; }
    .totals table { width: 100%; }
    .totals td { padding: 3px 0; font-size: 12px; }
    .totals td:last-child { text-align: right; font-weight: bold; }
    .total-row td { font-size: 14px; font-weight: bold; border-top: 2px solid #333; padding-top: 6px; }

    .payment-info { margin-top: 8px; font-size: 11px; }
    .change-row { font-size: 13px; font-weight: bold; margin-top: 4px; }

    .footer { text-align: center; margin-top: 16px; font-size: 10px; color: #777; border-top: 1px dashed #ccc; padding-top: 10px; }
    .thank-you { font-size: 13px; font-weight: bold; color: #333; margin-bottom: 4px; }

    @media print {
      body { background: white; }
      .no-print { display: none; }
      @page { margin: 5mm; size: 80mm auto; }
    }
  </style>
</head>
<body>
<div class="invoice-wrapper">

  <!-- HEADER -->
  <div class="header">
    <div class="store-name"><?= esc($settings['store_name'] ?? 'TOKO KAYU KONTAN JAYA') ?></div>
    <div class="store-info">
      <?= esc($settings['store_address'] ?? 'Jl. Raya Mebel No. 1') ?><br/>
      Telp: <?= esc($settings['store_phone'] ?? '08123456789') ?>
    </div>
  </div>

  <!-- TRANSACTION INFO -->
  <div class="trx-info">
    <table>
      <tr>
        <td>No. Transaksi</td>
        <td><?= esc($transaction['transaction_no']) ?></td>
      </tr>
      <tr>
        <td>Tanggal</td>
        <td><?= date('d/m/Y H:i', strtotime($transaction['transaction_date'])) ?></td>
      </tr>
      <tr>
        <td>Kasir</td>
        <td><?= esc($transaction['cashier_name']) ?></td>
      </tr>
      <?php if ($transaction['customer_name']): ?>
      <tr>
        <td>Pelanggan</td>
        <td><?= esc($transaction['customer_name']) ?></td>
      </tr>
      <?php endif; ?>
    </table>
  </div>

  <hr class="divider"/>

  <!-- ITEMS -->
  <table class="items-table">
    <thead>
      <tr>
        <th>Item</th>
        <th style="text-align:center">Qty</th>
        <th style="text-align:right">Harga</th>
        <th style="text-align:right">Total</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($transaction['details'] as $item): ?>
      <tr>
        <td><?= esc($item['product_name']) ?></td>
        <td style="text-align:center"><?= $item['quantity'] ?></td>
        <td style="text-align:right"><?= number_format($item['unit_price'], 0, ',', '.') ?></td>
        <td style="text-align:right"><?= number_format($item['subtotal'], 0, ',', '.') ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <hr class="divider"/>

  <!-- TOTALS -->
  <div class="totals">
    <table>
      <tr>
        <td>Subtotal</td>
        <td>Rp <?= number_format($transaction['subtotal'], 0, ',', '.') ?></td>
      </tr>
      <?php if ($transaction['discount'] > 0): ?>
      <tr>
        <td>Diskon</td>
        <td>- Rp <?= number_format($transaction['discount'], 0, ',', '.') ?></td>
      </tr>
      <?php endif; ?>
      <tr class="total-row">
        <td>TOTAL</td>
        <td>Rp <?= number_format($transaction['total'], 0, ',', '.') ?></td>
      </tr>
    </table>
  </div>

  <!-- PAYMENT -->
  <div class="payment-info">
    <div>Metode: <?= $transaction['payment_method'] === 'cash' ? 'Tunai' : 'Transfer' ?></div>
    <?php if ($transaction['payment_method'] === 'cash'): ?>
    <div>Bayar: Rp <?= number_format($transaction['payment_amount'], 0, ',', '.') ?></div>
    <div class="change-row">Kembali: Rp <?= number_format($transaction['change_amount'], 0, ',', '.') ?></div>
    <?php endif; ?>
  </div>

  <!-- FOOTER -->
  <div class="footer">
    <div class="thank-you">Terima Kasih!</div>
    <div>Simpan struk ini sebagai bukti pembelian.</div>
    <div style="margin-top:6px;">*Barang yang sudah dibeli tidak dapat dikembalikan*</div>
    <div style="margin-top:8px; font-size:9px; color:#aaa;">
      Dicetak: <?= date('d/m/Y H:i:s') ?>
    </div>
  </div>
</div>

<!-- Print Button (hilang saat print) -->
<div class="no-print" style="text-align:center; padding: 20px; background: #f3f4f6;">
  <button onclick="window.print()" style="
    background: #78350f; color: white; border: none; padding: 12px 32px;
    border-radius: 8px; font-size: 14px; font-weight: bold; cursor: pointer;
    margin-right: 8px;
  ">Print Invoice</button>
  <button onclick="window.close()" style="
    background: #e5e7eb; color: #374151; border: none; padding: 12px 32px;
    border-radius: 8px; font-size: 14px; cursor: pointer;
  ">Tutup</button>
</div>

<script>
  // Auto print jika dibuka sebagai popup
  if (window.opener) {
    window.onload = () => setTimeout(() => window.print(), 500);
  }
</script>
</body>
</html>
