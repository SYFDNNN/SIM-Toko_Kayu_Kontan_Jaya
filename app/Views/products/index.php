<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Produk — Toko Kayu Kontan Jaya</title>
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
    .nav-icon { width: 17px; height: 17px; flex-shrink: 0; }
    .sb-footer { padding: 10px 8px 16px; border-top: 1px solid rgba(255,255,255,0.06); position: relative; z-index: 1; }
    .user-row { display: flex; align-items: center; gap: 9px; padding: 8px 10px; border-radius: 9px; }
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
    .icon-btn:hover { background: #FAF6EF; border-color: #D5CABC; color: var(--wood); }
    .notif-dot { position: absolute; top: 4px; right: 4px; width: 8px; height: 8px; border-radius: 50%; background: #EF4444; border: 2px solid white; }
    .tb-avatar { width: 32px; height: 32px; border-radius: 9px; background: var(--wood); color: white; font-size: 12px; font-weight: 700; display: flex; align-items: center; justify-content: center; }

    /* CONTENT */
    .content { flex: 1; padding: 24px; }

    @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
    @keyframes cardPop { from{opacity:0;transform:translateY(20px) scale(0.97)} to{opacity:1;transform:translateY(0) scale(1)} }

    /* PAGE HEADER */
    .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; animation: fadeUp 0.4s ease both; }
    .btn-primary { background: var(--wood); color: white; font-size: 13px; font-weight: 600; padding: 9px 18px; border-radius: 10px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 7px; transition: all 200ms; text-decoration: none; font-family: inherit; }
    .btn-primary:hover { background: var(--wood-dk); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(122,74,45,0.35); }
    .btn-outline { background: white; color: var(--text); font-size: 13px; font-weight: 600; padding: 9px 16px; border-radius: 10px; border: 1.5px solid var(--border); cursor: pointer; display: inline-flex; align-items: center; gap: 7px; transition: all 200ms; text-decoration: none; font-family: inherit; }
    .btn-outline:hover { background: #FAF6EF; border-color: #D5CABC; }

    /* SEARCH & FILTER BAR */
    .filter-bar { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 14px 18px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px; animation: fadeUp 0.4s 0.05s ease both; flex-wrap: wrap; }
    .search-wrap { position: relative; flex: 1; min-width: 200px; }
    .search-wrap input { width: 100%; padding: 10px 14px 10px 38px; border: 1.5px solid var(--border); border-radius: 10px; background: #FAF6EF; font-size: 13px; color: var(--text); outline: none; font-family: inherit; transition: all 200ms; }
    .search-wrap input:focus { border-color: var(--wood); background: white; box-shadow: 0 0 0 3px rgba(122,74,45,0.08); }
    .search-wrap svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--muted); }
    .filter-select { padding: 10px 14px; border: 1.5px solid var(--border); border-radius: 10px; background: #FAF6EF; font-size: 13px; color: var(--text); outline: none; font-family: inherit; cursor: pointer; min-width: 160px; }
    .filter-select:focus { border-color: var(--wood); }

    /* CATEGORY PILLS */
    .cat-pills { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px; animation: fadeUp 0.4s 0.08s ease both; }
    .cat-pill { padding: 7px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; cursor: pointer; border: 1.5px solid var(--border); background: white; color: var(--muted); transition: all 180ms; white-space: nowrap; }
    .cat-pill:hover { border-color: var(--wood-lt); color: var(--wood); background: #FEF3C7; }
    .cat-pill.active { background: var(--wood); color: white; border-color: var(--wood); box-shadow: 0 4px 12px rgba(122,74,45,0.3); }

    /* PRODUCT GRID — large cards like the reference */
    .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 18px; }

    /* PRODUCT CARD */
    .product-card { background: var(--surface); border-radius: 18px; overflow: hidden; border: 1px solid var(--border); transition: all 240ms cubic-bezier(0.4,0,0.2,1); animation: cardPop 0.5s ease both; cursor: pointer; position: relative; }
    .product-card:hover { transform: translateY(-6px); box-shadow: 0 20px 48px rgba(122,74,45,0.18), 0 4px 12px rgba(0,0,0,0.08); border-color: #D5CABC; }

    /* IMAGE AREA */
    .card-img-wrap { position: relative; width: 100%; height: 200px; overflow: hidden; background: #F0E8DC; }
    .card-img-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform 400ms ease; display: block; }
    .product-card:hover .card-img-wrap img { transform: scale(1.06); }
    .card-img-placeholder { width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; background: linear-gradient(135deg, #F0E8DC 0%, #EDE0CC 100%); }
    .card-img-placeholder svg { width: 48px; height: 48px; color: #C9B49A; margin-bottom: 8px; }
    .card-img-placeholder span { font-size: 12px; color: #C9B49A; font-weight: 500; }

    /* STATUS BADGE on image */
    .card-status { position: absolute; top: 10px; left: 10px; font-size: 10px; font-weight: 700; padding: 4px 10px; border-radius: 20px; backdrop-filter: blur(8px); }
    .status-active   { background: rgba(21,128,61,0.9); color: white; }
    .status-inactive { background: rgba(107,114,128,0.85); color: white; }

    /* STOCK BADGE on image */
    .card-stock-badge { position: absolute; top: 10px; right: 10px; font-size: 10px; font-weight: 700; padding: 4px 10px; border-radius: 20px; backdrop-filter: blur(8px); }
    .stock-low  { background: rgba(220,38,38,0.88); color: white; }
    .stock-ok   { background: rgba(0,0,0,0.45); color: white; }

    /* CATEGORY BADGE on image */
    .card-cat { position: absolute; bottom: 10px; left: 10px; font-size: 10px; font-weight: 700; padding: 4px 10px; border-radius: 20px; background: rgba(122,74,45,0.88); color: white; backdrop-filter: blur(8px); max-width: calc(100% - 20px); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    /* CARD BODY */
    .card-body { padding: 14px 16px 16px; }
    .card-name { font-size: 14px; font-weight: 700; color: var(--text); margin-bottom: 4px; line-height: 1.3; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
    .card-sku  { font-size: 11px; color: var(--muted); margin-bottom: 10px; font-family: monospace; }

    .card-prices { display: flex; align-items: baseline; gap: 8px; margin-bottom: 10px; }
    .price-sell { font-size: 16px; font-weight: 800; color: var(--wood); }
    .price-buy  { font-size: 11px; color: var(--muted); text-decoration: line-through; }

    .card-meta { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
    .stock-info { display: flex; align-items: center; gap: 5px; font-size: 12px; font-weight: 600; }
    .stock-info.low  { color: #DC2626; }
    .stock-info.ok   { color: #15803D; }
    .stock-info.zero { color: #DC2626; }

    /* CARD ACTIONS */
    .card-actions { display: flex; gap: 8px; }
    .btn-card-edit { flex: 1; padding: 8px 0; border-radius: 9px; font-size: 12px; font-weight: 700; cursor: pointer; border: 1.5px solid #DBEAFE; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; gap: 5px; transition: all 150ms; text-decoration: none; font-family: inherit; }
    .btn-card-edit:hover { background: #DBEAFE; transform: translateY(-1px); }
    .btn-card-del  { flex: 1; padding: 8px 0; border-radius: 9px; font-size: 12px; font-weight: 700; cursor: pointer; border: 1.5px solid #FECACA; background: #FEF2F2; color: #DC2626; display: flex; align-items: center; justify-content: center; gap: 5px; transition: all 150ms; font-family: inherit; }
    .btn-card-del:hover { background: #FEE2E2; transform: translateY(-1px); }

    /* EMPTY STATE */
    .empty-state { grid-column: 1/-1; text-align: center; padding: 80px 24px; animation: fadeUp 0.4s ease both; }
    .empty-state svg { width: 64px; height: 64px; color: #EDE5D8; margin: 0 auto 16px; display: block; }

    /* FLASH */
    .flash-success { display: flex; align-items: center; gap: 10px; background: #F0FDF4; border: 1px solid #BBF7D0; color: #15803D; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 20px; }
    .flash-error   { display: flex; align-items: center; gap: 10px; background: #FEF2F2; border: 1px solid #FECACA; color: #B91C1C; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 20px; }

    /* RESULT COUNT */
    .result-info { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; animation: fadeUp 0.4s 0.1s ease both; }
    .result-count { font-size: 13px; color: var(--muted); }
    .result-count strong { color: var(--text); font-weight: 700; }

    /* TOAST */
    .toast { position: fixed; bottom: 24px; right: 24px; z-index: 9999; background: #1C1410; color: white; padding: 13px 18px; border-radius: 12px; font-size: 13px; font-weight: 500; box-shadow: 0 8px 32px rgba(0,0,0,0.3); display: flex; align-items: center; gap: 10px; animation: toastIn 0.4s cubic-bezier(0.34,1.56,0.64,1); }
    .toast.success { border-left: 4px solid #22C55E; }
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
    <div class="breadcrumb">Dashboard / <strong>Produk</strong></div>
    <div class="tb-search">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
      </svg>
      <input placeholder="Cari produk..." onkeypress="if(event.key==='Enter')applySearch(this.value)"/>
    </div>
    <div class="tb-actions">
      <a href="<?= base_url('pos') ?>" class="btn-new">
        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        Transaksi Baru
      </a>
      <?php
        $db   = \Config\Database::connect();
        $lowC = 0;
        try { $lowC = $db->query("SELECT COUNT(*) as c FROM products WHERE stock<=stock_minimum AND is_active=1 AND deleted_at IS NULL")->getRowArray()['c'] ?? 0; } catch(\Exception $e){}
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
        <h1 style="font-size:22px;font-weight:800;letter-spacing:-0.02em;">Produk</h1>
        <p style="font-size:13px;color:var(--muted);margin-top:3px;">Kelola semua produk toko</p>
      </div>
      <div style="display:flex;gap:10px;">
        <a href="<?= base_url('products/import') ?>" class="btn-outline">
          <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
          </svg>
          Import CSV
        </a>
        <a href="<?= base_url('products/export-csv') ?>" class="btn-outline">
          <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
          </svg>
          Export CSV
        </a>
        <a href="<?= base_url('products/create') ?>" class="btn-primary">
          <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
          </svg>
          Tambah Produk
        </a>
      </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session('success')): ?>
    <div class="flash-success">
      <svg style="width:16px;height:16px;flex-shrink:0;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
      <?= esc(session('success')) ?>
    </div>
    <?php endif; ?>
    <?php if (session('error')): ?>
    <div class="flash-error">
      <svg style="width:16px;height:16px;flex-shrink:0;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/></svg>
      <?= esc(session('error')) ?>
    </div>
    <?php endif; ?>

    <!-- Search & Filter Bar -->
    <div class="filter-bar">
      <div class="search-wrap">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" id="searchInput" placeholder="Cari nama produk atau SKU..."
          value="<?= esc($search ?? '') ?>"
          onkeyup="filterCards()" oninput="filterCards()"/>
      </div>
      <select class="filter-select" id="statusFilter" onchange="filterCards()">
        <option value="">Semua Status</option>
        <option value="aktif" <?= ($status??'')==='aktif'?'selected':'' ?>>Aktif</option>
        <option value="nonaktif" <?= ($status??'')==='nonaktif'?'selected':'' ?>>Nonaktif</option>
      </select>
      <select class="filter-select" id="stockFilter" onchange="filterCards()">
        <option value="">Semua Stok</option>
        <option value="low">Stok Rendah</option>
        <option value="ok">Stok Aman</option>
        <option value="empty">Stok Habis</option>
      </select>
    </div>

    <!-- Category Pills -->
    <?php if (!empty($categories)): ?>
    <div class="cat-pills">
      <button class="cat-pill active" data-cat="all" onclick="filterByCategory('all', this)">
        Semua (<?= count($products ?? []) ?>)
      </button>
      <?php foreach ($categories as $cat): ?>
      <button class="cat-pill" data-cat="<?= $cat['id'] ?>" onclick="filterByCategory('<?= $cat['id'] ?>', this)">
        <?= esc($cat['name']) ?>
        <span style="opacity:0.6;">(<?= $cat['product_count'] ?? 0 ?>)</span>
      </button>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Result info -->
    <div class="result-info">
      <div class="result-count">
        Menampilkan <strong id="visibleCount"><?= count($products ?? []) ?></strong> produk
      </div>
    </div>

    <!-- Product Grid (Cards) -->
    <div class="product-grid" id="productGrid">

      <?php if (empty($products)): ?>
      <div class="empty-state">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>
        <p style="font-size:16px;font-weight:700;color:var(--muted);margin-bottom:6px;">Belum ada produk</p>
        <p style="font-size:13px;color:#C9B49A;margin-bottom:20px;">Tambahkan produk pertama kamu</p>
        <a href="<?= base_url('products/create') ?>" class="btn-primary">+ Tambah Produk</a>
      </div>

      <?php else: ?>
      <?php foreach ($products as $i => $p):
        $stock    = (int)($p['stock'] ?? 0);
        $stockMin = (int)($p['stock_minimum'] ?? 0);
        $unit     = $p['unit'] ?? 'pcs';
        $isLow    = $stock <= $stockMin && $stock > 0;
        $isEmpty  = $stock <= 0;
        $isActive = !empty($p['is_active']);

        // Image path
        $imgSrc = null;
        if (!empty($p['image'])) {
          $imgSrc = '/toko-kayu-kontan-jaya/public/uploads/' . $p['image'];

          echo '<!-- IMG PATH: ' . $imgSrc . ' -->';


        }

        $stockClass = $isEmpty ? 'zero' : ($isLow ? 'low' : 'ok');
        $stockLabel = $isEmpty ? 'Habis' : ($isLow ? 'Hampir habis' : 'Tersedia');
      ?>
      <div class="product-card"
        data-name="<?= strtolower(esc($p['name'])) ?>"
        data-sku="<?= strtolower(esc($p['sku'])) ?>"
        data-cat="<?= $p['category_id'] ?? '' ?>"
        data-status="<?= $isActive ? 'aktif' : 'nonaktif' ?>"
        data-stock="<?= $isEmpty ? 'empty' : ($isLow ? 'low' : 'ok') ?>"
        style="animation-delay:<?= $i * 0.04 ?>s;">

        <!-- Image -->
        <div class="card-img-wrap">
          <?php if ($imgSrc): ?>
          <img src="<?= $imgSrc ?>" alt="<?= esc($p['name']) ?>" loading="lazy"
            onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
          <div class="card-img-placeholder" style="display:none;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span>Belum ada foto</span>
          </div>
          <?php else: ?>
          <div class="card-img-placeholder">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span>Belum ada foto</span>
          </div>
          <?php endif; ?>

          <!-- Badges on image -->
          <span class="card-status <?= $isActive ? 'status-active' : 'status-inactive' ?>">
            <?= $isActive ? 'Aktif' : 'Nonaktif' ?>
          </span>
          <span class="card-stock-badge <?= ($isEmpty || $isLow) ? 'stock-low' : 'stock-ok' ?>">
            <?= $stock ?> <?= esc($unit) ?>
          </span>
          <?php if (!empty($p['category_name'])): ?>
          <span class="card-cat"><?= esc($p['category_name']) ?></span>
          <?php endif; ?>
        </div>

        <!-- Body -->
        <div class="card-body">
          <div class="card-name"><?= esc($p['name']) ?></div>
          <div class="card-sku">SKU: <?= esc($p['sku']) ?></div>

          <div class="card-prices">
            <span class="price-sell">Rp <?= number_format($p['price'] ?? $p['selling_price'] ?? 0, 0, ',', '.') ?></span>
            <?php if (!empty($p['cost_price']) || !empty($p['purchase_price'])): ?>
            <span class="price-buy">Rp <?= number_format($p['cost_price'] ?? $p['purchase_price'] ?? 0, 0, ',', '.') ?></span>
            <?php endif; ?>
          </div>

          <div class="card-meta">
            <div class="stock-info <?= $stockClass ?>">
              <?php if ($isEmpty): ?>
              <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
              <?php elseif ($isLow): ?>
              <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
              <?php else: ?>
              <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
              <?php endif; ?>
              <?= $stockLabel ?>
            </div>
            <span style="font-size:11px;color:var(--muted);">Min: <?= $stockMin ?> <?= esc($unit) ?></span>
          </div>

          <!-- Actions -->
          <div class="card-actions">
            <a href="<?= base_url('products/' . $p['id'] . '/edit') ?>" class="btn-card-edit">
              <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
              Edit
            </a>
            <form method="POST" action="<?= base_url('products/' . $p['id'] . '/delete') ?>"
              onsubmit="return confirm('Hapus produk <?= esc(addslashes($p['name'])) ?>?')" style="flex:1;display:flex;">
              <?= csrf_field() ?>
              <button type="submit" class="btn-card-del" style="width:100%;">
                <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Hapus
              </button>
            </form>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
      <?php endif; ?>

    </div><!-- end product-grid -->

  </div><!-- end content -->
</div><!-- end main -->

<?php if (session('success')): ?>
<div class="toast success" id="toast">
  <svg style="width:16px;height:16px;color:#22C55E;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
  </svg>
  <?= esc(session('success')) ?>
</div>
<?php endif; ?>

<script>
// Sidebar toggle
function toggleSidebar() {
  const sb   = document.getElementById('sidebar');
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

// Filter by category pill
let activeCat = 'all';
function filterByCategory(catId, btn) {
  activeCat = catId;
  document.querySelectorAll('.cat-pill').forEach(p => p.classList.remove('active'));
  btn.classList.add('active');
  filterCards();
}

// Live filter cards
function filterCards() {
  const search  = document.getElementById('searchInput').value.toLowerCase();
  const status  = document.getElementById('statusFilter').value;
  const stock   = document.getElementById('stockFilter').value;
  const cards   = document.querySelectorAll('.product-card');
  let visible   = 0;

  cards.forEach(card => {
    const name    = card.dataset.name   || '';
    const sku     = card.dataset.sku    || '';
    const cat     = card.dataset.cat    || '';
    const cardSt  = card.dataset.status || '';
    const cardStk = card.dataset.stock  || '';

    const matchSearch = !search || name.includes(search) || sku.includes(search);
    const matchCat    = activeCat === 'all' || cat === activeCat;
    const matchStatus = !status  || cardSt  === status;
    const matchStock  = !stock   || cardStk === stock;

    const show = matchSearch && matchCat && matchStatus && matchStock;
    card.style.display = show ? '' : 'none';
    if (show) visible++;
  });

  document.getElementById('visibleCount').textContent = visible;
}

// Toast
const toast = document.getElementById('toast');
if (toast) setTimeout(() => {
  toast.style.opacity = '0'; toast.style.transform = 'translateY(8px)';
  toast.style.transition = 'all 0.3s'; setTimeout(() => toast.remove(), 300);
}, 3500);
</script>
</body>
</html>