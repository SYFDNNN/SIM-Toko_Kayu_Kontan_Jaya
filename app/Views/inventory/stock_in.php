<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Stock In — Toko Kayu Kontan Jaya</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    * { font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --wood: #7A4A2D; --wood-dk: #5C2D10; --wood-lt: #9B5E30;
      --gold: #F4C350; --bg: #F7F2EB; --surface: #FFFFFF;
      --border: #EDE5D8; --text: #1C1410; --muted: #9B8B77;
    }
    body { background: var(--bg); color: var(--text); overflow-x: hidden; }

    /* SIDEBAR */
    #sidebar {
      width: 240px; flex-shrink: 0;
      background: linear-gradient(180deg, #1E0B02 0%, #3A1608 40%, #5C2D10 100%);
      height: 100vh; position: fixed; left: 0; top: 0; z-index: 50;
      display: flex; flex-direction: column;
      transition: width 250ms cubic-bezier(0.4,0,0.2,1);
      box-shadow: 4px 0 40px rgba(0,0,0,0.25);
    }
    #sidebar.collapsed { width: 68px; }
    #sidebar.collapsed .nav-label, #sidebar.collapsed .brand-name,
    #sidebar.collapsed .user-info { opacity: 0; width: 0; overflow: hidden; white-space: nowrap; }
    #sidebar.collapsed .nav-link { justify-content: center; padding: 10px 0; }
    #sidebar.collapsed .section-label { display: none; }
    #sidebar::before {
      content: ''; position: absolute; inset: 0; pointer-events: none; z-index: 0;
      background: repeating-linear-gradient(88deg, transparent, transparent 4px, rgba(255,255,255,0.02) 4px, rgba(255,255,255,0.02) 6px);
      animation: grainMove 12s linear infinite;
    }
    @keyframes grainMove { from{background-position:0 0} to{background-position:0 200px} }

    .brand-wrap { display: flex; align-items: center; gap: 10px; padding: 18px 14px 14px; border-bottom: 1px solid rgba(255,255,255,0.06); position: relative; z-index: 1; }
    .brand-logo { width: 34px; height: 34px; border-radius: 10px; background: rgba(244,195,80,0.2); display: flex; align-items: center; justify-content: center; flex-shrink: 0; animation: logoPulse 3s ease-in-out infinite; }
    @keyframes logoPulse { 0%,100%{box-shadow:0 0 0 0 rgba(244,195,80,0)} 50%{box-shadow:0 0 0 6px rgba(244,195,80,0.12)} }
    .brand-name b { font-size: 13px; font-weight: 700; color: white; display: block; }
    .brand-name span { font-size: 10px; color: rgba(255,255,255,0.3); }
    .toggle-btn { margin-left: auto; background: none; border: none; cursor: pointer; color: rgba(255,255,255,0.25); padding: 4px; border-radius: 6px; transition: all 150ms; position: relative; z-index: 1; }
    .toggle-btn:hover { background: rgba(255,255,255,0.08); color: white; }

    .sb-nav { flex: 1; padding: 10px 8px; overflow-y: auto; position: relative; z-index: 1; }
    .section-label { font-size: 9px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: rgba(255,255,255,0.2); padding: 10px 10px 4px; }
    .nav-link { display: flex; align-items: center; gap: 10px; padding: 9px 10px; border-radius: 9px; color: rgba(255,255,255,0.45); font-size: 13px; font-weight: 500; cursor: pointer; transition: all 180ms ease; margin-bottom: 1px; text-decoration: none; position: relative; overflow: hidden; }
    .nav-link:hover { color: rgba(255,255,255,0.9); transform: translateX(3px); }
    .nav-link.active { background: rgba(255,255,255,0.1); color: white; box-shadow: inset 0 0 0 1px rgba(255,255,255,0.08); }
    .nav-link.active::before { content: ''; position: absolute; left: 0; top: 20%; height: 60%; width: 3px; background: var(--gold); border-radius: 0 3px 3px 0; }
    .nav-icon { width: 17px; height: 17px; flex-shrink: 0; transition: transform 200ms; }
    .nav-link:hover .nav-icon { transform: scale(1.15); }

    .sb-footer { padding: 10px 8px 16px; border-top: 1px solid rgba(255,255,255,0.06); position: relative; z-index: 1; }
    .user-row { display: flex; align-items: center; gap: 9px; padding: 8px 10px; border-radius: 9px; cursor: pointer; transition: background 150ms; }
    .user-row:hover { background: rgba(255,255,255,0.05); }
    .user-avatar { width: 30px; height: 30px; border-radius: 9px; background: rgba(244,195,80,0.2); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: var(--gold); flex-shrink: 0; }
    .user-info .uname { font-size: 12px; font-weight: 600; color: white; white-space: nowrap; }
    .user-info .urole { font-size: 10px; color: rgba(255,255,255,0.28); text-transform: capitalize; }

    /* MAIN */
    #main { margin-left: 240px; transition: margin-left 250ms cubic-bezier(0.4,0,0.2,1); min-height: 100vh; display: flex; flex-direction: column; }
    #main.expanded { margin-left: 68px; }

    /* TOPBAR */
    #topbar { height: 58px; background: var(--surface); border-bottom: 1px solid var(--border); display: flex; align-items: center; padding: 0 24px; gap: 14px; position: sticky; top: 0; z-index: 40; box-shadow: 0 2px 16px rgba(0,0,0,0.04); }
    .breadcrumb { font-size: 13px; color: var(--muted); }
    .breadcrumb strong { color: var(--text); font-weight: 600; }
    .tb-actions { margin-left: auto; display: flex; align-items: center; gap: 10px; }
    .tb-avatar { width: 32px; height: 32px; border-radius: 9px; background: var(--wood); color: white; font-size: 12px; font-weight: 700; display: flex; align-items: center; justify-content: center; }

    /* CONTENT */
    .content { flex: 1; padding: 28px 24px; max-width: 760px; }

    /* FORM CARD */
    .form-card { background: var(--surface); border: 1px solid var(--border); border-radius: 20px; overflow: hidden; animation: fadeUp 0.5s ease both; }
    @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
    .form-card-header { padding: 20px 24px; border-bottom: 1px solid var(--border); background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%); display: flex; align-items: center; gap: 12px; }
    .form-card-icon { width: 40px; height: 40px; border-radius: 12px; background: #15803D; display: flex; align-items: center; justify-content: center; }
    .form-card-body { padding: 28px 24px; }

    /* FORM ELEMENTS */
    .form-group { margin-bottom: 20px; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 7px; }
    .form-label span { color: #EF4444; margin-left: 2px; }
    .form-control { width: 100%; padding: 10px 14px; border: 1.5px solid var(--border); border-radius: 10px; font-size: 13px; color: var(--text); background: #FAF6EF; transition: all 200ms; outline: none; font-family: inherit; }
    .form-control:focus { border-color: #15803D; background: white; box-shadow: 0 0 0 3px rgba(21,128,61,0.1); }
    .form-control::placeholder { color: var(--muted); }
    select.form-control { cursor: pointer; }
    textarea.form-control { resize: vertical; min-height: 90px; }
    .form-hint { font-size: 11px; color: var(--muted); margin-top: 5px; }

    /* STOCK PREVIEW */
    .stock-preview { background: #F0FDF4; border: 1.5px solid #BBF7D0; border-radius: 12px; padding: 14px 16px; margin-bottom: 20px; display: none; }
    .stock-preview.show { display: block; animation: fadeUp 0.3s ease; }
    .stock-preview-row { display: flex; justify-content: space-between; align-items: center; font-size: 13px; margin-bottom: 6px; }
    .stock-preview-row:last-child { margin: 0; }
    .stock-preview-label { color: #166534; font-weight: 500; }
    .stock-preview-val { font-weight: 700; color: #15803D; }
    .stock-preview-arrow { color: #22C55E; font-size: 18px; margin: 4px auto; display: block; text-align: center; }

    /* BUTTONS */
    .btn-success { background: #15803D; color: white; font-size: 14px; font-weight: 700; padding: 11px 24px; border-radius: 10px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 200ms; font-family: inherit; }
    .btn-success:hover { background: #166534; transform: translateY(-2px); box-shadow: 0 8px 24px rgba(21,128,61,0.3); }
    .btn-ghost { background: white; color: var(--muted); font-size: 14px; font-weight: 600; padding: 11px 20px; border-radius: 10px; border: 1.5px solid var(--border); cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 200ms; text-decoration: none; font-family: inherit; }
    .btn-ghost:hover { border-color: #D5CABC; color: var(--text); }

    .flash-error { display: flex; align-items: center; gap: 10px; background: #FEF2F2; border: 1px solid #FECACA; color: #B91C1C; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 20px; }
    .err-list { margin: 0; padding-left: 18px; font-size: 13px; color: #B91C1C; }

    .page-header { margin-bottom: 24px; animation: fadeUp 0.4s ease both; }
  </style>
</head>
<body>

<!-- SIDEBAR -->
<aside id="sidebar">
  <div class="brand-wrap">
    <div class="brand-logo">
      <svg viewBox="0 0 24 24" fill="none" style="width:18px;height:18px;">
        <path d="M12 3L20 19H4L12 3Z" fill="#F4C350" opacity="0.95"/>
        <rect x="8" y="15" width="8" height="2" rx="1" fill="#4A1F08"/>
      </svg>
    </div>
    <div class="brand-name"><b>Toko Kayu</b><span>Kontan Jaya</span></div>
    <button class="toggle-btn" onclick="toggleSidebar()">
      <svg id="toggleIcon" style="width:16px;height:16px;transition:transform 250ms;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
      </svg>
    </button>
  </div>
  <nav class="sb-nav">
    <div class="section-label nav-label">Menu Utama</div>
    <?php
    $userRole = session('user_role') ?? '';
    $navItems = [
      [
        'url'   => 'dashboard',
        'label' => 'Dashboard',
        'match' => 'dashboard',
        'roles' => ['admin', 'owner', 'cashier'],
        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
      ],
      [
        'url'   => 'products',
        'label' => 'Produk',
        'match' => 'products',
        'roles' => ['admin', 'owner'],
        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
      ],
      [
        'url'   => 'inventori',
        'label' => 'Inventori',
        'match' => 'inventori',
        'roles' => ['admin', 'owner'],
        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>',
      ],
      [
        'url'   => 'pos',
        'label' => 'Kasir',
        'match' => 'pos',
        'roles' => ['admin', 'cashier'],
        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>',
      ],
      [
        'url'   => 'transactions',
        'label' => 'Transaksi',
        'match' => 'transactions',
        'roles' => ['admin', 'owner'],
        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>',
      ],
      [
        'url'   => 'reports',
        'label' => 'Laporan',
        'match' => 'reports',
        'roles' => ['admin', 'owner'],
        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
      ],
    ];
    $cp = service('request')->getPath();
    foreach ($navItems as $n):
      if (!in_array($userRole, $n['roles'])) continue;
      $active = strpos($cp, $n['match']) !== false;
    ?>
    <a href="<?= base_url($n['url']) ?>" class="nav-link <?= $active?'active':'' ?>">
      <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?= $n['icon'] ?></svg>
      <span class="nav-label"><?= $n['label'] ?></span>
    </a>
    <?php endforeach; ?>
    <?php if (in_array($userRole, ['admin', 'owner'])): ?>
    <div class="section-label nav-label" style="margin-top:10px;">Sistem</div>
    <a href="<?= base_url('settings') ?>" class="nav-link <?= strpos($cp,'settings')!==false?'active':'' ?>">
      <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
        <circle cx="12" cy="12" r="3"/>
      </svg>
      <span class="nav-label">Pengaturan</span>
    </a>
    <?php endif; ?>
  </nav>
  <div class="sb-footer">
    <div class="user-row">
      <div class="user-avatar"><?= strtoupper(substr(session('user_name')??'U',0,1)) ?></div>
      <div class="user-info">
        <div class="uname"><?= esc(session('user_name')??'User') ?></div>
        <div class="urole"><?= esc(session('user_role')??'') ?></div>
      </div>
      <a href="<?= base_url('logout') ?>" style="margin-left:auto;color:rgba(255,255,255,0.2);transition:all 200ms;"
        onmouseover="this.style.color='#EF4444'" onmouseout="this.style.color='rgba(255,255,255,0.2)'">
        <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
        </svg>
      </a>
    </div>
  </div>
</aside>

<!-- MAIN -->
<div id="main">
  <header id="topbar">
    <div class="breadcrumb">
      <a href="<?= base_url('inventori') ?>" style="color:var(--muted);text-decoration:none;">Inventori</a>
      / <strong>Stock In</strong>
    </div>
    <div class="tb-actions">
      <div class="tb-avatar"><?= strtoupper(substr(session('user_name')??'U',0,1)) ?></div>
    </div>
  </header>

  <div class="content">

    <div class="page-header">
      <h1 style="font-size:22px;font-weight:800;color:var(--text);letter-spacing:-0.02em;">Stock In</h1>
      <p style="font-size:13px;color:var(--muted);margin-top:3px;">Tambah stok masuk ke gudang</p>
    </div>

    <?php if (session('errors')): ?>
    <div class="flash-error" style="margin-bottom:20px;">
      <svg style="width:16px;height:16px;flex-shrink:0;" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
      </svg>
      <div>
        <ul class="err-list">
          <?php foreach ((array)session('errors') as $e): ?>
          <li><?= esc($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
    <?php endif; ?>

    <div class="form-card">
      <div class="form-card-header">
        <div class="form-card-icon">
          <svg style="width:20px;height:20px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
          </svg>
        </div>
        <div>
          <div style="font-size:15px;font-weight:700;color:#166534;">Tambah Stok Masuk</div>
          <div style="font-size:12px;color:#16a34a;margin-top:1px;">Isi form di bawah untuk mencatat penerimaan barang</div>
        </div>
      </div>
      <div class="form-card-body">
        <form action="<?= base_url('inventori/stock-in') ?>" method="POST">
          <?= csrf_field() ?>

          <div class="form-group">
            <label class="form-label">Produk <span>*</span></label>
            <select name="product_id" class="form-control" id="productSelect" onchange="updateStockPreview()" required>
              <option value="">— Pilih Produk —</option>
              <?php foreach ($products as $p): ?>
              <option value="<?= $p['id'] ?>"
                data-stock="<?= $p['stock'] ?>"
                data-unit="<?= esc($p['unit'] ?? 'pcs') ?>"
                data-sku="<?= esc($p['sku']) ?>"
                <?= old('product_id') == $p['id'] ? 'selected' : '' ?>>
                [<?= esc($p['sku']) ?>] <?= esc($p['name']) ?> — Stok: <?= $p['stock'] ?> <?= esc($p['unit'] ?? 'pcs') ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Stock preview -->
          <div class="stock-preview" id="stockPreview">
            <div class="stock-preview-row">
              <span class="stock-preview-label">Stok Saat Ini</span>
              <span class="stock-preview-val" id="prevStock">—</span>
            </div>
            <div class="stock-preview-row">
              <span class="stock-preview-label">Qty Masuk</span>
              <span class="stock-preview-val" id="prevQty" style="color:#15803D;">+0</span>
            </div>
            <div style="border-top:1px dashed #BBF7D0;margin:8px 0;"></div>
            <div class="stock-preview-row">
              <span class="stock-preview-label" style="font-weight:700;">Stok Setelah</span>
              <span class="stock-preview-val" id="prevAfter" style="font-size:16px;">—</span>
            </div>
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
              <label class="form-label">Jumlah (Qty) <span>*</span></label>
              <input type="number" name="quantity" class="form-control" id="qtyInput"
                placeholder="0" min="1" value="<?= old('quantity') ?>"
                onchange="updateStockPreview()" oninput="updateStockPreview()" required>
              <div class="form-hint">Masukkan jumlah barang yang diterima</div>
            </div>
            <div class="form-group">
              <label class="form-label">No. Referensi / No. PO</label>
              <input type="text" name="reference_no" class="form-control"
                placeholder="cth: PO-2024-001" value="<?= old('reference_no') ?>">
              <div class="form-hint">Nomor surat jalan atau purchase order</div>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Catatan</label>
            <textarea name="notes" class="form-control" placeholder="Catatan tambahan (opsional)..."><?= old('notes') ?></textarea>
          </div>

          <div style="display:flex;gap:12px;justify-content:flex-end;padding-top:8px;border-top:1px solid var(--border);">
            <a href="<?= base_url('inventori') ?>" class="btn-ghost">
              <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
              </svg>
              Batal
            </a>
            <button type="submit" class="btn-success">
              <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
              </svg>
              Simpan Stock In
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>

<script>
function toggleSidebar() {
  const sb = document.getElementById('sidebar');
  const main = document.getElementById('main');
  const icon = document.getElementById('toggleIcon');
  sb.classList.toggle('collapsed');
  main.classList.toggle('expanded');
  const c = sb.classList.contains('collapsed');
  icon.style.transform = c ? 'rotate(180deg)' : '';
  localStorage.setItem('sb_c', c);
}
if (localStorage.getItem('sb_c') === 'true') {
  document.getElementById('sidebar').classList.add('collapsed');
  document.getElementById('main').classList.add('expanded');
  document.getElementById('toggleIcon').style.transform = 'rotate(180deg)';
}

function updateStockPreview() {
  const sel = document.getElementById('productSelect');
  const qty = parseInt(document.getElementById('qtyInput').value) || 0;
  const opt = sel.options[sel.selectedIndex];
  if (!opt || !opt.value) {
    document.getElementById('stockPreview').classList.remove('show');
    return;
  }
  const curStock = parseInt(opt.dataset.stock) || 0;
  const unit = opt.dataset.unit || 'pcs';
  document.getElementById('prevStock').textContent = curStock + ' ' + unit;
  document.getElementById('prevQty').textContent = '+' + qty + ' ' + unit;
  document.getElementById('prevAfter').textContent = (curStock + qty) + ' ' + unit;
  document.getElementById('stockPreview').classList.add('show');
}
</script>
</body>
</html>