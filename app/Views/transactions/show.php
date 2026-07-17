<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Detail Transaksi — <?= esc($transaction['transaction_no']) ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    * { font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; margin: 0; padding: 0; }
    :root { --wood:#7A4A2D;--wood-dk:#5C2D10;--gold:#F4C350;--bg:#F7F2EB;--surface:#fff;--border:#EDE5D8;--text:#1C1410;--muted:#9B8B77; }
    body { background: var(--bg); color: var(--text); min-height: 100vh; padding: 32px 24px; }
    .back-btn { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: var(--wood); text-decoration: none; margin-bottom: 20px; padding: 7px 14px; border: 1.5px solid var(--border); border-radius: 9px; background: var(--surface); transition: all 150ms; }
    .back-btn:hover { background: #FAF6EF; border-color: var(--wood); }
    .card { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 28px; max-width: 780px; margin: 0 auto; box-shadow: 0 4px 24px rgba(0,0,0,0.05); }
    .card-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid var(--border); }
    .trx-no { font-size: 20px; font-weight: 700; color: var(--wood); font-family: monospace; }
    .trx-date { font-size: 13px; color: var(--muted); margin-top: 4px; }
    .badge { display: inline-flex; align-items: center; font-size: 12px; font-weight: 600; padding: 4px 12px; border-radius: 20px; }
    .badge-completed { background: #DCFCE7; color: #15803D; }
    .badge-pending   { background: #FEF3C7; color: #92400E; }
    .badge-cancelled { background: #FEE2E2; color: #B91C1C; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 24px; }
    .info-item { background: #FAF6EF; border-radius: 10px; padding: 12px 14px; }
    .info-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted); margin-bottom: 4px; }
    .info-value { font-size: 14px; font-weight: 600; color: var(--text); }
    .items-title { font-size: 13px; font-weight: 700; color: var(--text); margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.04em; }
    .items-table { width: 100%; border-collapse: collapse; font-size: 13px; margin-bottom: 20px; }
    .items-table thead th { padding: 9px 12px; background: #FAF6EF; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--muted); text-align: left; border-bottom: 1px solid var(--border); }
    .items-table tbody tr { border-bottom: 1px solid #F5EFE5; }
    .items-table tbody tr:last-child { border: none; }
    .items-table td { padding: 11px 12px; }
    .totals { border-top: 2px solid var(--border); padding-top: 16px; }
    .total-row { display: flex; justify-content: space-between; font-size: 13px; color: var(--muted); margin-bottom: 8px; }
    .total-row.grand { font-size: 16px; font-weight: 700; color: var(--text); border-top: 1px solid var(--border); padding-top: 10px; margin-top: 4px; }
    .total-row.change { color: #15803D; font-weight: 600; }
    .print-btn { background: var(--wood); color: white; border: none; border-radius: 9px; padding: 9px 20px; font-size: 13px; font-weight: 600; cursor: pointer; font-family: inherit; display: inline-flex; align-items: center; gap: 6px; transition: all 180ms; }
    .print-btn:hover { background: var(--wood-dk); }
    @media print { .back-btn, .print-btn { display: none; } body { background: white; padding: 0; } .card { box-shadow: none; border: none; } }
  </style>
</head>
<body>
  <div style="max-width:780px;margin:0 auto;">
    <a href="<?= base_url('transactions') ?>" class="back-btn">
      <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      Kembali ke Daftar
    </a>

    <div class="card">
      <div class="card-header">
        <div>
          <div class="trx-no"><?= esc($transaction['transaction_no']) ?></div>
          <div class="trx-date"><?= date('d F Y, H:i', strtotime($transaction['transaction_date'])) ?></div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
          <?php
            $stClass = match($transaction['status']) {
              'completed' => 'badge-completed',
              'pending'   => 'badge-pending',
              default     => 'badge-cancelled',
            };
            $stLabel = match($transaction['status']) {
              'completed' => 'Selesai',
              'pending'   => 'Pending',
              default     => 'Dibatalkan',
            };
          ?>
          <span class="badge <?= $stClass ?>"><?= $stLabel ?></span>
          <button class="print-btn" onclick="window.print()">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak
          </button>
        </div>
      </div>

      <!-- Info Grid -->
      <div class="info-grid">
        <div class="info-item">
          <div class="info-label">Kasir</div>
          <div class="info-value"><?= esc($transaction['cashier_name']) ?></div>
        </div>
        <div class="info-item">
          <div class="info-label">Pelanggan</div>
          <div class="info-value"><?= esc($transaction['customer_name'] ?? '—') ?></div>
        </div>
        <div class="info-item">
          <div class="info-label">Metode Pembayaran</div>
          <div class="info-value"><?= ucfirst(esc($transaction['payment_method'])) ?></div>
        </div>
        <div class="info-item">
          <div class="info-label">Catatan</div>
          <div class="info-value"><?= esc($transaction['notes'] ?: '—') ?></div>
        </div>
      </div>

      <!-- Items -->
      <div class="items-title">Detail Item</div>
      <table class="items-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Produk</th>
            <th>SKU</th>
            <th style="text-align:center;">Qty</th>
            <th style="text-align:right;">Harga Satuan</th>
            <th style="text-align:right;">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($transaction['details'] as $i => $d): ?>
          <tr>
            <td style="color:var(--muted);font-size:12px;"><?= $i+1 ?></td>
            <td style="font-weight:500;"><?= esc($d['product_name']) ?></td>
            <td style="font-family:monospace;font-size:12px;color:var(--muted);"><?= esc($d['sku']) ?></td>
            <td style="text-align:center;"><?= $d['quantity'] ?> <?= esc($d['unit']??'') ?></td>
            <td style="text-align:right;">Rp <?= number_format($d['unit_price'],0,',','.') ?></td>
            <td style="text-align:right;font-weight:600;">Rp <?= number_format($d['subtotal'],0,',','.') ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <!-- Totals -->
      <div class="totals">
        <div class="total-row"><span>Subtotal</span><span>Rp <?= number_format($transaction['subtotal'],0,',','.') ?></span></div>
        <?php if ($transaction['discount'] > 0): ?>
        <div class="total-row"><span>Diskon</span><span>- Rp <?= number_format($transaction['discount'],0,',','.') ?></span></div>
        <?php endif; ?>
        <div class="total-row grand"><span>Total</span><span>Rp <?= number_format($transaction['total'],0,',','.') ?></span></div>
        <div class="total-row" style="margin-top:8px;"><span>Bayar</span><span>Rp <?= number_format($transaction['payment_amount'],0,',','.') ?></span></div>
        <div class="total-row change"><span>Kembalian</span><span>Rp <?= number_format($transaction['change_amount'],0,',','.') ?></span></div>
      </div>
    </div>
  </div>
</body>
</html>
