<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Inventori — Toko Kayu Kontan Jaya</title>
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
    .tb-search { position: relative; }
    .tb-search input { width: 240px; padding: 7px 14px 7px 34px; border: 1.5px solid var(--border); border-radius: 10px; background: #FAF6EF; font-size: 13px; color: var(--text); transition: all 200ms; outline: none; font-family: inherit; }
    .tb-search input:focus { border-color: var(--wood); width: 280px; background: white; box-shadow: 0 0 0 3px rgba(122,74,45,0.08); }
    .tb-search svg { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 15px; height: 15px; color: var(--muted); }
    .tb-actions { margin-left: auto; display: flex; align-items: center; gap: 10px; }
    .btn-new { background: var(--wood); color: white; font-size: 13px; font-weight: 600; padding: 7px 16px; border-radius: 9px; border: none; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: all 200ms; text-decoration: none; font-family: inherit; }
    .btn-new:hover { background: var(--wood-dk); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(122,74,45,0.35); }
    .icon-btn { width: 34px; height: 34px; border-radius: 9px; border: 1.5px solid var(--border); background: white; display: flex; align-items: center; justify-content: center; cursor: pointer; position: relative; transition: all 180ms; color: var(--muted); text-decoration: none; }
    .icon-btn:hover { background: #FAF6EF; border-color: #D5CABC; color: var(--wood); transform: translateY(-1px); }
    .notif-dot { position: absolute; top: 4px; right: 4px; width: 8px; height: 8px; border-radius: 50%; background: #EF4444; border: 2px solid white; }
    .tb-avatar { width: 32px; height: 32px; border-radius: 9px; background: var(--wood); color: white; font-size: 12px; font-weight: 700; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: transform 150ms; }
    .tb-avatar:hover { transform: scale(1.08); }

    /* CONTENT */
    .content { flex: 1; padding: 28px 24px; }

    /* PAGE HEADER */
    .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; animation: headerIn 0.5s ease both; }
    @keyframes headerIn { from{opacity:0;transform:translateY(-12px)} to{opacity:1;transform:translateY(0)} }

    /* BUTTONS */
    .btn-primary { background: var(--wood); color: white; font-size: 13px; font-weight: 600; padding: 8px 16px; border-radius: 9px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 200ms; text-decoration: none; font-family: inherit; }
    .btn-primary:hover { background: var(--wood-dk); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(122,74,45,0.35); }
    .btn-success { background: #F0FDF4; color: #15803D; font-size: 13px; font-weight: 600; padding: 8px 16px; border-radius: 9px; border: 1.5px solid #BBF7D0; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 200ms; text-decoration: none; font-family: inherit; }
    .btn-success:hover { background: #DCFCE7; transform: translateY(-2px); box-shadow: 0 4px 14px rgba(21,128,61,0.15); }
    .btn-danger { background: #FEF2F2; color: #DC2626; font-size: 13px; font-weight: 600; padding: 8px 16px; border-radius: 9px; border: 1.5px solid #FECACA; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 200ms; text-decoration: none; font-family: inherit; }
    .btn-danger:hover { background: #FEE2E2; transform: translateY(-2px); box-shadow: 0 4px 14px rgba(220,38,38,0.15); }
    .btn-warning { background: #FFFBEB; color: #92400E; font-size: 13px; font-weight: 600; padding: 8px 16px; border-radius: 9px; border: 1.5px solid #FDE68A; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 200ms; text-decoration: none; font-family: inherit; }
    .btn-warning:hover { background: #FEF3C7; transform: translateY(-2px); }

    /* ALERT CARD */
    .alert-card { background: var(--surface); border: 1.5px solid #FECACA; border-radius: 16px; overflow: hidden; margin-bottom: 24px; animation: fadeUp 0.5s ease both; }
    .alert-card-header { display: flex; align-items: center; gap: 10px; padding: 14px 20px; background: #FEF2F2; border-bottom: 1px solid #FECACA; }
    .alert-title { font-size: 14px; font-weight: 700; color: #B91C1C; }
    .alert-badge { background: #EF4444; color: white; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 20px; }

    /* TABLE CARD */
    .table-card { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; overflow: hidden; animation: fadeUp 0.5s 0.1s ease both; }
    .table-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--border); }
    .table-title { font-size: 14px; font-weight: 700; color: var(--text); }
    .table-count { font-size: 12px; color: var(--muted); background: #F5EFE5; padding: 3px 10px; border-radius: 20px; }

    @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }

    /* DATA TABLE */
    .data-tbl { width: 100%; border-collapse: collapse; font-size: 13px; }
    .data-tbl thead th { padding: 10px 16px; background: #FAF6EF; text-align: left; font-size: 10px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.07em; border-bottom: 1px solid var(--border); }
    .data-tbl tbody tr { border-bottom: 1px solid #F5EFE5; transition: all 180ms; animation: rowIn 0.4s ease both; }
    .data-tbl tbody tr:last-child { border: none; }
    .data-tbl tbody tr:hover { background: #FAF6EF; }
    .data-tbl td { padding: 12px 16px; color: var(--text); vertical-align: middle; }
    @keyframes rowIn { from{opacity:0;transform:translateX(-10px)} to{opacity:1;transform:translateX(0)} }

    /* BADGES */
    .badge { display: inline-flex; align-items: center; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; }
    .badge-in      { background: #DCFCE7; color: #15803D; }
    .badge-out     { background: #FEE2E2; color: #B91C1C; }
    .badge-adj     { background: #FEF3C7; color: #92400E; }
    .badge-low     { background: #FEE2E2; color: #B91C1C; }
    .badge-ok      { background: #DCFCE7; color: #15803D; }
    .sku-mono      { font-family: monospace; font-size: 11px; color: var(--wood-lt); font-weight: 600; background: #FEF3C7; padding: 3px 8px; border-radius: 6px; }
    .qty-pos       { font-weight: 700; color: #15803D; }
    .qty-neg       { font-weight: 700; color: #DC2626; }

    /* FLASH */
    .flash-success { display: flex; align-items: center; gap: 10px; background: #F0FDF4; border: 1px solid #BBF7D0; color: #15803D; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 20px; animation: fadeUp 0.3s ease; }
    .flash-error   { display: flex; align-items: center; gap: 10px; background: #FEF2F2; border: 1px solid #FECACA; color: #B91C1C; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 20px; animation: fadeUp 0.3s ease; }

    /* TOAST */
    .toast { position: fixed; bottom: 24px; right: 24px; z-index: 9999; background: #1C1410; color: white; padding: 13px 18px; border-radius: 12px; font-size: 13px; font-weight: 500; box-shadow: 0 8px 32px rgba(0,0,0,0.3); display: flex; align-items: center; gap: 10px; animation: toastIn 0.4s cubic-bezier(0.34,1.56,0.64,1); }
    .toast.success { border-left: 4px solid #22C55E; }
    .toast.error   { border-left: 4px solid #EF4444; }
    @keyframes toastIn { from{transform:translateY(24px) scale(0.9);opacity:0} to{transform:translateY(0) scale(1);opacity:1} }

    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #D5CABC; border-radius: 3px; }
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
        onmouseover="this.style.color='#EF4444';this.style.transform='translateX(2px)'"
        onmouseout="this.style.color='rgba(255,255,255,0.2)';this.style.transform=''">
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
    <div class="breadcrumb">Dashboard / <strong>Inventori</strong></div>
    <div class="tb-search">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
      </svg>
      <input placeholder="Cari produk, transaksi..." onkeypress="if(event.key==='Enter')location.href='<?= base_url('products') ?>?search='+this.value"/>
    </div>
    <div class="tb-actions">
      <a href="<?= base_url('pos') ?>" class="btn-new">
        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        Transaksi Baru
      </a>
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
        <h1 style="font-size:22px;font-weight:800;color:var(--text);letter-spacing:-0.02em;">Inventori</h1>
        <p style="font-size:13px;color:var(--muted);margin-top:3px;">Kelola pergerakan stok dan pantau kondisi inventori</p>
      </div>
      <div style="display:flex;gap:10px;">
        <a href="<?= base_url('inventori/stock-in') ?>" class="btn-success">
          <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
          </svg>
          Stock In
        </a>
        <a href="<?= base_url('inventori/stock-out') ?>" class="btn-danger">
          <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/>
          </svg>
          Stock Out
        </a>
        <a href="<?= base_url('inventori/rop') ?>" class="btn-warning">
          <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
          </svg>
          Analisis ROP
        </a>
      </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session('success')): ?>
    <div class="flash-success">
      <svg style="width:16px;height:16px;flex-shrink:0;" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
      </svg>
      <?= esc(session('success')) ?>
    </div>
    <?php endif; ?>
    <?php if (session('error')): ?>
    <div class="flash-error">
      <svg style="width:16px;height:16px;flex-shrink:0;" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
      </svg>
      <?= esc(session('error')) ?>
    </div>
    <?php endif; ?>

    <!-- LOW STOCK ALERT -->
    <?php if (!empty($lowStockProducts)): ?>
    <div class="alert-card">
      <div class="alert-card-header">
        <svg style="width:18px;height:18px;color:#EF4444;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <span class="alert-title">Peringatan Stok Rendah</span>
        <span class="alert-badge"><?= count($lowStockProducts) ?> produk</span>
      </div>
      <div style="overflow-x:auto;">
        <table class="data-tbl">
          <thead>
            <tr>
              <th>SKU</th>
              <th>Nama Produk</th>
              <th>Kategori</th>
              <th style="text-align:center;">Stok Saat Ini</th>
              <th style="text-align:center;">Stok Minimum</th>
              <th style="text-align:center;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($lowStockProducts as $i => $p): ?>
            <tr style="animation-delay:<?= $i * 0.04 ?>s;">
              <td><span class="sku-mono"><?= esc($p['sku']) ?></span></td>
              <td style="font-weight:600;"><?= esc($p['name']) ?></td>
              <td style="color:var(--muted);"><?= esc($p['category_name']) ?></td>
              <td style="text-align:center;">
                <span class="badge badge-low"><?= $p['stock'] ?> <?= esc($p['unit'] ?? 'pcs') ?></span>
              </td>
              <td style="text-align:center;color:var(--muted);"><?= $p['stock_minimum'] ?></td>
              <td style="text-align:center;">
                <a href="<?= base_url('inventori/stock-in') ?>" class="btn-success" style="padding:5px 12px;font-size:12px;border-radius:7px;">
                  <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                  </svg>
                  Stock In
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>

    <!-- RIWAYAT PERGERAKAN STOK -->
    <div class="table-card">
      <div class="table-header">
        <span class="table-title">Riwayat Pergerakan Stok</span>
        <span class="table-count">50 terakhir</span>
      </div>
      <div style="overflow-x:auto;">
        <table class="data-tbl">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Produk</th>
              <th>SKU</th>
              <th style="text-align:center;">Tipe</th>
              <th style="text-align:center;">Qty</th>
              <th style="text-align:center;">Sebelum</th>
              <th style="text-align:center;">Sesudah</th>
              <th>Referensi</th>
              <th>User</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($recentMovements)): ?>
            <tr>
              <td colspan="9" style="padding:48px;text-align:center;">
                <svg style="width:40px;height:40px;color:#EDE5D8;margin:0 auto 12px;display:block;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p style="font-size:14px;font-weight:600;color:var(--muted);">Belum ada pergerakan stok</p>
              </td>
            </tr>
            <?php else: ?>
            <?php foreach ($recentMovements as $i => $m): ?>
            <tr style="animation-delay:<?= $i * 0.03 ?>s;">
              <td style="font-size:12px;color:var(--muted);white-space:nowrap;">
                <?= date('d/m/Y H:i', strtotime($m['created_at'])) ?>
              </td>
              <td style="font-weight:600;"><?= esc($m['product_name']) ?></td>
              <td><span class="sku-mono"><?= esc($m['sku']) ?></span></td>
              <td style="text-align:center;">
                <?php
                  $badgeClass = match($m['type']) {
                    'IN'  => 'badge-in',
                    'OUT' => 'badge-out',
                    default => 'badge-adj'
                  };
                ?>
                <span class="badge <?= $badgeClass ?>"><?= esc($m['type']) ?></span>
              </td>
              <td style="text-align:center;">
                <span class="<?= $m['quantity'] > 0 ? 'qty-pos' : 'qty-neg' ?>">
                  <?= $m['quantity'] > 0 ? '+' : '' ?><?= $m['quantity'] ?>
                </span>
              </td>
              <td style="text-align:center;color:var(--muted);"><?= $m['stock_before'] ?></td>
              <td style="text-align:center;font-weight:700;"><?= $m['stock_after'] ?></td>
              <td style="font-size:12px;color:var(--muted);"><?= esc($m['reference_no'] ?? '—') ?></td>
              <td style="font-size:12px;color:var(--muted);"><?= esc($m['user_name']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<?php if (session('success')): ?>
<div class="toast success" id="toast">
  <svg style="width:16px;height:16px;color:#22C55E;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
  </svg>
  <?= esc(session('success')) ?>
</div>
<?php endif; ?>

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
const toast = document.getElementById('toast');
if (toast) setTimeout(() => { toast.style.opacity='0'; toast.style.transform='translateY(8px)'; toast.style.transition='all 0.3s'; setTimeout(()=>toast.remove(),300); }, 3500);
</script>
</body>
</html>