<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Riwayat Transaksi — Toko Kayu Kontan Jaya</title>
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
    .brand-wrap { display: flex; align-items: center; gap: 10px; padding: 18px 14px 14px; border-bottom: 1px solid rgba(255,255,255,0.06); }
    .brand-logo { width: 34px; height: 34px; border-radius: 10px; background: rgba(244,195,80,0.2); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .brand-name b { font-size: 13px; font-weight: 700; color: white; display: block; }
    .brand-name span { font-size: 10px; color: rgba(255,255,255,0.3); }
    .toggle-btn { margin-left: auto; background: none; border: none; cursor: pointer; color: rgba(255,255,255,0.25); padding: 4px; border-radius: 6px; transition: all 150ms; }
    .toggle-btn:hover { background: rgba(255,255,255,0.08); color: white; }
    .sb-nav { flex: 1; padding: 10px 8px; overflow-y: auto; }
    .section-label { font-size: 9px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: rgba(255,255,255,0.2); padding: 10px 10px 4px; }
    .nav-link { display: flex; align-items: center; gap: 10px; padding: 9px 10px; border-radius: 9px; color: rgba(255,255,255,0.45); font-size: 13px; font-weight: 500; cursor: pointer; transition: all 180ms ease; margin-bottom: 1px; text-decoration: none; position: relative; overflow: hidden; }
    .nav-link:hover { color: rgba(255,255,255,0.9); transform: translateX(3px); }
    .nav-link.active { background: rgba(255,255,255,0.1); color: white; box-shadow: inset 0 0 0 1px rgba(255,255,255,0.08); }
    .nav-link.active::before { content: ''; position: absolute; left: 0; top: 20%; height: 60%; width: 3px; background: var(--gold); border-radius: 0 3px 3px 0; }
    .nav-icon { width: 17px; height: 17px; flex-shrink: 0; }
    .sb-footer { padding: 10px 8px 16px; border-top: 1px solid rgba(255,255,255,0.06); }
    .user-row { display: flex; align-items: center; gap: 9px; padding: 8px 10px; border-radius: 9px; }
    .user-avatar { width: 30px; height: 30px; border-radius: 9px; background: rgba(244,195,80,0.2); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: var(--gold); flex-shrink: 0; }
    .user-info .uname { font-size: 12px; font-weight: 600; color: white; }
    .user-info .urole { font-size: 10px; color: rgba(255,255,255,0.28); text-transform: capitalize; }
    #main { margin-left: 240px; transition: margin-left 250ms cubic-bezier(0.4,0,0.2,1); min-height: 100vh; display: flex; flex-direction: column; }
    #main.expanded { margin-left: 68px; }
    #topbar { height: 58px; background: var(--surface); border-bottom: 1px solid var(--border); display: flex; align-items: center; padding: 0 24px; gap: 14px; position: sticky; top: 0; z-index: 40; box-shadow: 0 2px 16px rgba(0,0,0,0.04); }
    .breadcrumb { font-size: 13px; color: var(--muted); }
    .breadcrumb strong { color: var(--text); font-weight: 600; }
    .tb-search { position: relative; }
    .tb-search input { width: 240px; padding: 7px 14px 7px 34px; border: 1.5px solid var(--border); border-radius: 10px; background: #FAF6EF; font-size: 13px; color: var(--text); outline: none; font-family: inherit; }
    .tb-search svg { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 15px; height: 15px; color: var(--muted); }
    .tb-actions { margin-left: auto; display: flex; align-items: center; gap: 10px; }
    .btn-new { background: var(--wood); color: white; font-size: 13px; font-weight: 600; padding: 7px 16px; border-radius: 9px; border: none; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: all 200ms; text-decoration: none; }
    .btn-new:hover { background: var(--wood-dk); transform: translateY(-2px); }
    .icon-btn { width: 34px; height: 34px; border-radius: 9px; border: 1.5px solid var(--border); background: white; display: flex; align-items: center; justify-content: center; cursor: pointer; position: relative; transition: all 180ms; color: var(--muted); text-decoration: none; }
    .icon-btn:hover { background: #FAF6EF; border-color: #D5CABC; color: var(--wood); }
    .notif-dot { position: absolute; top: 4px; right: 4px; width: 8px; height: 8px; border-radius: 50%; background: #EF4444; border: 2px solid white; }
    .tb-avatar { width: 32px; height: 32px; border-radius: 9px; background: var(--wood); color: white; font-size: 12px; font-weight: 700; display: flex; align-items: center; justify-content: center; }
    .content { flex: 1; padding: 28px 24px; }
    .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 24px; }
    .page-title { font-size: 20px; font-weight: 700; color: var(--text); }
    .page-sub { font-size: 13px; color: var(--muted); margin-top: 3px; }
    .filter-card { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 18px 20px; margin-bottom: 20px; display: flex; align-items: flex-end; gap: 14px; flex-wrap: wrap; }
    .filter-group { display: flex; flex-direction: column; gap: 5px; }
    .filter-label { font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; }
    .filter-input { padding: 7px 12px; border: 1.5px solid var(--border); border-radius: 9px; font-size: 13px; background: #FAF6EF; color: var(--text); outline: none; font-family: inherit; }
    .filter-input:focus { border-color: var(--wood); }
    .btn-filter { background: var(--wood); color: white; font-size: 13px; font-weight: 600; padding: 8px 18px; border-radius: 9px; border: none; cursor: pointer; font-family: inherit; transition: all 180ms; }
    .btn-filter:hover { background: var(--wood-dk); }
    .table-wrap { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
    .data-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .data-table thead th { padding: 11px 16px; background: #FAF6EF; text-align: left; font-size: 10px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.06em; border-bottom: 1px solid var(--border); }
    .data-table tbody tr { border-bottom: 1px solid #F5EFE5; transition: background 100ms; }
    .data-table tbody tr:last-child { border: none; }
    .data-table tbody tr:hover { background: #FAF6EF; }
    .data-table td { padding: 12px 16px; }
    .td-mono { font-family: monospace; font-size: 12px; color: var(--wood-lt); font-weight: 600; }
    .badge { display: inline-flex; align-items: center; font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 20px; }
    .badge-completed { background: #DCFCE7; color: #15803D; }
    .badge-pending   { background: #FEF3C7; color: #92400E; }
    .badge-cancelled { background: #FEE2E2; color: #B91C1C; }
    .badge-cash     { background: #DBEAFE; color: #1D4ED8; }
    .badge-transfer { background: #EDE9FE; color: #6D28D9; }
    .badge-other    { background: #F3F4F6; color: #4B5563; }
    .btn-detail { background: #FAF6EF; color: var(--wood); border: 1.5px solid var(--border); font-size: 11px; font-weight: 600; padding: 4px 12px; border-radius: 7px; cursor: pointer; transition: all 150ms; text-decoration: none; }
    .btn-detail:hover { background: var(--wood); color: white; border-color: var(--wood); }
    .empty-state { text-align: center; padding: 60px 20px; color: var(--muted); }
    .empty-state svg { width: 48px; height: 48px; margin: 0 auto 14px; opacity: 0.3; }
    .pager { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-top: 1px solid var(--border); font-size: 12px; color: var(--muted); }
    .pager-links a, .pager-links span { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 7px; border: 1.5px solid var(--border); font-size: 12px; font-weight: 600; text-decoration: none; color: var(--text); margin: 0 2px; transition: all 150ms; }
    .pager-links a:hover { background: #FAF6EF; border-color: var(--wood); color: var(--wood); }
    .pager-links .active { background: var(--wood); color: white; border-color: var(--wood); }
    .summary-row { background: #FAF6EF; }
    .summary-row td { font-weight: 600; color: var(--text); font-size: 12px; }
  </style>
</head>
<body>

<!-- SIDEBAR -->
<aside id="sidebar">
  <div class="brand-wrap">
    <div class="brand-logo">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
        <path d="M12 3L20 19H4L12 3Z" fill="#F4C350"/>
        <rect x="9" y="15" width="6" height="1.5" rx="0.75" fill="#4A1F08"/>
      </svg>
    </div>
    <div class="brand-name"><b>Toko Kayu</b><span>Kontan Jaya</span></div>
    <button class="toggle-btn" onclick="toggleSidebar()">
      <svg id="toggle-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
      </svg>
    </button>
  </div>
  <nav class="sb-nav">
    <div class="section-label nav-label">Menu Utama</div>
    <?php
    $userRole = session('user_role') ?? '';
    $navItems = [
      ['url'=>'dashboard','label'=>'Dashboard','match'=>'dashboard','roles'=>['admin','owner','cashier'],'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>'],
      ['url'=>'products','label'=>'Produk','match'=>'products','roles'=>['admin','owner'],'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>'],
      ['url'=>'inventori','label'=>'Inventori','match'=>'inventori','roles'=>['admin','owner'],'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>'],
      ['url'=>'pos','label'=>'Kasir','match'=>'pos','roles'=>['admin','cashier'],'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>'],
      ['url'=>'transactions','label'=>'Transaksi','match'=>'transactions','roles'=>['admin','owner'],'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>'],
      ['url'=>'reports','label'=>'Laporan','match'=>'reports','roles'=>['admin','owner'],'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>'],
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
      <a href="<?= base_url('logout') ?>" style="margin-left:auto;color:rgba(255,255,255,0.2);transition:color 150ms;" onmouseover="this.style.color='#EF4444'" onmouseout="this.style.color='rgba(255,255,255,0.2)'">
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
    <div class="breadcrumb">Dashboard / <strong>Riwayat Transaksi</strong></div>
    <div class="tb-search">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
      <input placeholder="Cari produk, transaksi..."/>
    </div>
    <div class="tb-actions">
      <?php if (in_array(session('user_role'), ['admin', 'cashier'])): ?>
      <a href="<?= base_url('pos') ?>" class="btn-new">
        <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        Transaksi Baru
      </a>
      <?php endif; ?>
      <?php
        $db = \Config\Database::connect();
        $lowC = $db->query("SELECT COUNT(*) as c FROM products WHERE stock<=stock_minimum AND is_active=1 AND deleted_at IS NULL")->getRowArray()['c'] ?? 0;
      ?>
      <a href="<?= base_url('inventori') ?>" class="icon-btn" title="<?= $lowC ?> produk stok rendah">
        <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <?php if ($lowC > 0): ?><span class="notif-dot"></span><?php endif; ?>
      </a>
      <div class="tb-avatar"><?= strtoupper(substr(session('user_name')??'U',0,1)) ?></div>
    </div>
  </header>

  <div class="content">
    <!-- Page Header -->
    <div class="page-header">
      <div>
        <div class="page-title">Riwayat Transaksi</div>
        <div class="page-sub">Daftar semua transaksi penjualan</div>
      </div>
    </div>

    <!-- Filter -->
    <form method="GET" action="<?= base_url('transactions') ?>">
      <div class="filter-card">
        <div class="filter-group">
          <label class="filter-label">Dari Tanggal</label>
          <input type="date" name="start_date" class="filter-input" value="<?= esc($startDate) ?>"/>
        </div>
        <div class="filter-group">
          <label class="filter-label">Sampai Tanggal</label>
          <input type="date" name="end_date" class="filter-input" value="<?= esc($endDate) ?>"/>
        </div>
        <div class="filter-group">
          <label class="filter-label">Status</label>
          <select name="status" class="filter-input">
            <option value=""     <?= $status===''          ?'selected':'' ?>>Semua Status</option>
            <option value="completed"  <?= $status==='completed'  ?'selected':'' ?>>Completed</option>
            <option value="pending"    <?= $status==='pending'    ?'selected':'' ?>>Pending</option>
            <option value="cancelled"  <?= $status==='cancelled'  ?'selected':'' ?>>Cancelled</option>
          </select>
        </div>
        <button type="submit" class="btn-filter">Tampilkan</button>
        <a href="<?= base_url('transactions') ?>" style="font-size:13px;color:var(--muted);text-decoration:none;padding:8px 4px;">Reset</a>
      </div>
    </form>

    <!-- Table -->
    <div class="table-wrap">
      <?php if (empty($transactions)): ?>
      <div class="empty-state">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <p style="font-size:14px;font-weight:600;">Tidak ada transaksi</p>
        <p style="font-size:12px;margin-top:4px;">Coba ubah filter tanggal atau status</p>
      </div>
      <?php else: ?>
      <table class="data-table">
        <thead>
          <tr>
            <th>No. Transaksi</th>
            <th>Tanggal & Waktu</th>
            <th>Kasir</th>
            <th>Pelanggan</th>
            <th>Metode</th>
            <th style="text-align:right;">Total</th>
            <th style="text-align:center;">Status</th>
            <th style="text-align:center;">Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php
          $grandTotal = 0;
          foreach ($transactions as $i => $t):
            $grandTotal += $t['total'];
        ?>
          <tr style="animation:fadeRow 0.3s ease <?= $i * 0.03 ?>s both;">
            <td><span class="td-mono"><?= esc($t['transaction_no']) ?></span></td>
            <td style="color:var(--muted);font-size:12px;">
              <?= date('d/m/Y', strtotime($t['transaction_date'])) ?><br>
              <span style="font-size:11px;"><?= date('H:i', strtotime($t['transaction_date'])) ?></span>
            </td>
            <td style="font-size:13px;"><?= esc($t['cashier_name']) ?></td>
            <td style="font-size:13px;color:var(--muted);"><?= esc($t['customer_name'] ?? '—') ?></td>
            <td>
              <?php
                $pmClass = match($t['payment_method']) {
                  'cash'     => 'badge-cash',
                  'transfer' => 'badge-transfer',
                  default    => 'badge-other',
                };
                $pmLabel = match($t['payment_method']) {
                  'cash'     => 'Tunai',
                  'transfer' => 'Transfer',
                  default    => ucfirst($t['payment_method']),
                };
              ?>
              <span class="badge <?= $pmClass ?>"><?= $pmLabel ?></span>
            </td>
            <td style="text-align:right;font-weight:600;">Rp <?= number_format($t['total'],0,',','.') ?></td>
            <td style="text-align:center;">
              <?php
                $stClass = match($t['status']) {
                  'completed' => 'badge-completed',
                  'pending'   => 'badge-pending',
                  default     => 'badge-cancelled',
                };
                $stLabel = match($t['status']) {
                  'completed' => 'Selesai',
                  'pending'   => 'Pending',
                  default     => 'Dibatalkan',
                };
              ?>
              <span class="badge <?= $stClass ?>"><?= $stLabel ?></span>
            </td>
            <td style="text-align:center;">
              <a href="<?= base_url('transactions/'.$t['id']) ?>" class="btn-detail">Detail</a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr class="summary-row">
            <td colspan="5" style="padding:12px 16px;font-size:12px;color:var(--muted);">
              <?= count($transactions) ?> transaksi ditampilkan
            </td>
            <td style="padding:12px 16px;text-align:right;font-weight:700;color:var(--text);">
              Rp <?= number_format($grandTotal,0,',','.') ?>
            </td>
            <td colspan="2"></td>
          </tr>
        </tfoot>
      </table>

      <!-- Paginasi -->
      <?php if ($pager): ?>
      <div class="pager">
        <span><?= $pager->getTotal() ?> total transaksi</span>
        <div class="pager-links"><?= $pager->links() ?></div>
      </div>
      <?php endif; ?>
      <?php endif; ?>
    </div>
  </div><!-- /content -->
</div><!-- /main -->

<style>
  @keyframes fadeRow { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:translateY(0)} }
</style>
<script>
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('collapsed');
  document.getElementById('main').classList.toggle('expanded');
  localStorage.setItem('sb_collapsed', document.getElementById('sidebar').classList.contains('collapsed'));
}
document.addEventListener('DOMContentLoaded', () => {
  if (localStorage.getItem('sb_collapsed') === 'true') {
    document.getElementById('sidebar').classList.add('collapsed');
    document.getElementById('main').classList.add('expanded');
  }
});
</script>
</body>
</html>
