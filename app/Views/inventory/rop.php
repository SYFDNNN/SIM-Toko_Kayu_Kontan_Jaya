<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Analisis ROP — Toko Kayu Kontan Jaya</title>
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

    #main { margin-left: 240px; transition: margin-left 250ms cubic-bezier(0.4,0,0.2,1); min-height: 100vh; display: flex; flex-direction: column; }
    #main.expanded { margin-left: 68px; }

    #topbar { height: 58px; background: var(--surface); border-bottom: 1px solid var(--border); display: flex; align-items: center; padding: 0 24px; gap: 14px; position: sticky; top: 0; z-index: 40; box-shadow: 0 2px 16px rgba(0,0,0,0.04); }
    .breadcrumb { font-size: 13px; color: var(--muted); }
    .breadcrumb strong { color: var(--text); font-weight: 600; }
    .tb-actions { margin-left: auto; display: flex; align-items: center; gap: 10px; }
    .tb-avatar { width: 32px; height: 32px; border-radius: 9px; background: var(--wood); color: white; font-size: 12px; font-weight: 700; display: flex; align-items: center; justify-content: center; }

    .content { flex: 1; padding: 28px 24px; }
    @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
    @keyframes rowIn { from{opacity:0;transform:translateX(-10px)} to{opacity:1;transform:translateX(0)} }

    .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 24px; animation: fadeUp 0.4s ease both; }

    /* INFO CARDS */
    .info-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; animation: fadeUp 0.4s 0.05s ease both; }
    .info-card { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 18px 20px; }
    .info-card-label { font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.07em; margin-bottom: 8px; }
    .info-card-val { font-size: 28px; font-weight: 800; letter-spacing: -0.03em; }
    .info-card-sub { font-size: 12px; color: var(--muted); margin-top: 4px; }

    /* FORMULA CARD */
    .formula-card { background: linear-gradient(135deg, #FFFBEB 0%, #FEF3C7 100%); border: 1.5px solid #FDE68A; border-radius: 16px; padding: 18px 20px; margin-bottom: 24px; animation: fadeUp 0.4s 0.1s ease both; }
    .formula-title { font-size: 13px; font-weight: 700; color: #92400E; margin-bottom: 10px; display: flex; align-items: center; gap: 8px; }
    .formula-box { background: white; border: 1px solid #FDE68A; border-radius: 10px; padding: 12px 16px; font-size: 14px; font-weight: 600; color: #78350F; font-family: monospace; text-align: center; }
    .formula-note { font-size: 11px; color: #92400E; margin-top: 10px; line-height: 1.6; }

    /* TABLE */
    .table-card { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; overflow: hidden; animation: fadeUp 0.5s 0.15s ease both; }
    .table-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--border); }
    .table-title { font-size: 14px; font-weight: 700; color: var(--text); }
    .data-tbl { width: 100%; border-collapse: collapse; font-size: 13px; }
    .data-tbl thead th { padding: 10px 16px; background: #FAF6EF; text-align: left; font-size: 10px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.07em; border-bottom: 1px solid var(--border); }
    .data-tbl tbody tr { border-bottom: 1px solid #F5EFE5; transition: all 180ms; animation: rowIn 0.4s ease both; }
    .data-tbl tbody tr:last-child { border: none; }
    .data-tbl tbody tr:hover { background: #FAF6EF; }
    .data-tbl td { padding: 12px 16px; color: var(--text); vertical-align: middle; }

    .badge { display: inline-flex; align-items: center; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; }
    .badge-danger { background: #FEE2E2; color: #B91C1C; }
    .badge-warning { background: #FEF3C7; color: #92400E; }
    .badge-ok { background: #DCFCE7; color: #15803D; }
    .sku-mono { font-family: monospace; font-size: 11px; color: var(--wood-lt); font-weight: 600; background: #FEF3C7; padding: 3px 8px; border-radius: 6px; }

    /* Progress bar for stock level */
    .stock-bar-wrap { width: 100px; height: 6px; background: #EDE5D8; border-radius: 3px; overflow: hidden; }
    .stock-bar-fill { height: 100%; border-radius: 3px; transition: width 0.5s ease; }

    .btn-primary { background: var(--wood); color: white; font-size: 13px; font-weight: 600; padding: 8px 16px; border-radius: 9px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 200ms; text-decoration: none; font-family: inherit; }
    .btn-primary:hover { background: var(--wood-dk); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(122,74,45,0.35); }
    .btn-ghost { background: white; color: var(--muted); font-size: 13px; font-weight: 600; padding: 8px 14px; border-radius: 9px; border: 1.5px solid var(--border); cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 200ms; text-decoration: none; font-family: inherit; }
    .btn-ghost:hover { border-color: #D5CABC; color: var(--text); }

    /* Filter bar */
    .filter-bar { display: flex; align-items: center; gap: 10px; }
    .filter-select { padding: 7px 12px; border: 1.5px solid var(--border); border-radius: 9px; background: #FAF6EF; font-size: 12px; font-weight: 600; color: var(--text); cursor: pointer; outline: none; font-family: inherit; }
    .filter-select:focus { border-color: var(--wood); }
  </style>
</head>
<body>

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

<div id="main">
  <header id="topbar">
    <div class="breadcrumb">
      <a href="<?= base_url('inventori') ?>" style="color:var(--muted);text-decoration:none;">Inventori</a>
      / <strong>Analisis ROP</strong>
    </div>
    <div class="tb-actions">
      <div class="tb-avatar"><?= strtoupper(substr(session('user_name')??'U',0,1)) ?></div>
    </div>
  </header>

  <div class="content">

    <div class="page-header">
      <div>
        <h1 style="font-size:22px;font-weight:800;color:var(--text);letter-spacing:-0.02em;">Analisis ROP</h1>
        <p style="font-size:13px;color:var(--muted);margin-top:3px;">Reorder Point — titik pemesanan ulang untuk setiap produk</p>
      </div>
      <a href="<?= base_url('inventori') ?>" class="btn-ghost">
        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali
      </a>
    </div>

    <?php
      // Compute summary stats from $products passed by controller
      // Variables from InventoryController::rop() — $products with fields: rop, stock, lead_time_days, avg_daily_sales, stock_minimum
      $total   = count($products ?? []);
      $critical = 0; $warning = 0; $safe = 0;
      foreach (($products ?? []) as $r) {
        if ($r['stock'] <= $r['stock_minimum']) $critical++;
        elseif ($r['stock'] <= ($r['rop'] ?? $r['stock_minimum'] * 2)) $warning++;
        else $safe++;
      }
    ?>

    <!-- Summary Cards -->
    <div class="info-grid">
      <div class="info-card" style="border-color:#FECACA;">
        <div class="info-card-label">Stok Kritis</div>
        <div class="info-card-val" style="color:#DC2626;"><?= $critical ?></div>
        <div class="info-card-sub">Di bawah stok minimum</div>
      </div>
      <div class="info-card" style="border-color:#FDE68A;">
        <div class="info-card-label">Perlu Perhatian</div>
        <div class="info-card-val" style="color:#D97706;"><?= $warning ?></div>
        <div class="info-card-sub">Mendekati titik ROP</div>
      </div>
      <div class="info-card" style="border-color:#BBF7D0;">
        <div class="info-card-label">Stok Aman</div>
        <div class="info-card-val" style="color:#15803D;"><?= $safe ?></div>
        <div class="info-card-sub">Di atas titik ROP</div>
      </div>
    </div>

    <!-- Formula explanation -->
    <div class="formula-card">
      <div class="formula-title">
        <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Rumus ROP (Reorder Point)
      </div>
      <div class="formula-box">
        ROP = (Rata-rata Permintaan Harian × Lead Time) + Safety Stock
      </div>
      <div class="formula-note">
        <strong>Rata-rata Permintaan Harian</strong>: total penjualan 30 hari terakhir ÷ 30 &nbsp;|&nbsp;
        <strong>Lead Time</strong>: estimasi hari dari order sampai barang datang (default: 7 hari) &nbsp;|&nbsp;
        <strong>Safety Stock</strong>: stok minimum yang ditetapkan per produk
      </div>
    </div>

    <!-- ROP Table -->
    <div class="table-card">
      <div class="table-header">
        <span class="table-title">Daftar ROP Semua Produk</span>
        <div class="filter-bar">
          <select class="filter-select" onchange="filterTable(this.value)">
            <option value="all">Semua Status</option>
            <option value="critical">Kritis</option>
            <option value="warning">Perlu Perhatian</option>
            <option value="safe">Aman</option>
          </select>
        </div>
      </div>
      <div style="overflow-x:auto;">
        <table class="data-tbl" id="ropTable">
          <thead>
            <tr>
              <th>SKU</th>
              <th>Produk</th>
              <th style="text-align:center;">Stok Saat Ini</th>
              <th style="text-align:center;">Stok Min</th>
              <th style="text-align:center;">Avg/Hari</th>
              <th style="text-align:center;">Lead Time</th>
              <th style="text-align:center;">ROP</th>
              <th style="text-align:center;">Level Stok</th>
              <th style="text-align:center;">Status</th>
              <th style="text-align:center;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($products)): ?>
            <tr>
              <td colspan="10" style="padding:48px;text-align:center;">
                <svg style="width:40px;height:40px;color:#EDE5D8;margin:0 auto 12px;display:block;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <p style="font-size:14px;font-weight:600;color:var(--muted);">Belum ada data produk</p>
              </td>
            </tr>
            <?php else: ?>
            <?php foreach ($products as $i => $r):
              $rop = $r['rop'] ?? round(($r['avg_daily_sales'] ?? 0) * ($r["lead_time_days"] ?? 7) + ($r['stock_minimum'] ?? 0));
              $unit = $r['unit'] ?? 'pcs';
              $pct = $r['stock'] > 0 ? min(100, round($r['stock'] / max($rop * 2, 1) * 100)) : 0;

              if ($r['stock'] <= $r['stock_minimum']) {
                $status = 'critical'; $badgeClass = 'badge-danger'; $statusLabel = 'Kritis';
                $barColor = '#EF4444';
              } elseif ($r['stock'] <= $rop) {
                $status = 'warning'; $badgeClass = 'badge-warning'; $statusLabel = 'Perhatian';
                $barColor = '#F59E0B';
              } else {
                $status = 'safe'; $badgeClass = 'badge-ok'; $statusLabel = 'Aman';
                $barColor = '#22C55E';
              }
            ?>
            <tr style="animation-delay:<?= $i * 0.03 ?>s;" data-status="<?= $status ?>">
              <td><span class="sku-mono"><?= esc($r['sku']) ?></span></td>
              <td style="font-weight:600;"><?= esc($r['name']) ?></td>
              <td style="text-align:center;font-weight:700;"><?= $r['stock'] ?> <span style="font-weight:400;color:var(--muted);font-size:11px;"><?= esc($unit) ?></span></td>
              <td style="text-align:center;color:var(--muted);"><?= $r['stock_minimum'] ?></td>
              <td style="text-align:center;color:var(--muted);"><?= number_format($r['avg_daily_sales'] ?? 0, 1) ?></td>
              <td style="text-align:center;color:var(--muted);"><?= $r["lead_time_days"] ?? 7 ?> hari</td>
              <td style="text-align:center;font-weight:700;color:var(--wood);"><?= $rop ?></td>
              <td style="text-align:center;">
                <div style="display:flex;align-items:center;gap:8px;justify-content:center;">
                  <div class="stock-bar-wrap">
                    <div class="stock-bar-fill" style="width:<?= $pct ?>%;background:<?= $barColor ?>;"></div>
                  </div>
                  <span style="font-size:11px;color:var(--muted);"><?= $pct ?>%</span>
                </div>
              </td>
              <td style="text-align:center;"><span class="badge <?= $badgeClass ?>"><?= $statusLabel ?></span></td>
              <td style="text-align:center;">
                <?php if ($status === 'critical' || $status === 'warning'): ?>
                <a href="<?= base_url('inventori/stock-in') ?>" class="btn-primary" style="padding:5px 12px;font-size:12px;border-radius:7px;">
                  <svg style="width:11px;height:11px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                  </svg>
                  Stock In
                </a>
                <?php else: ?>
                <span style="font-size:12px;color:var(--muted);">—</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
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

function filterTable(val) {
  const rows = document.querySelectorAll('#ropTable tbody tr[data-status]');
  rows.forEach(r => {
    r.style.display = (val === 'all' || r.dataset.status === val) ? '' : 'none';
  });
}
</script>
</body>
</html>