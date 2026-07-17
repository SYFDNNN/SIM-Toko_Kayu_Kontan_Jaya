<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pengaturan — Toko Kayu Kontan Jaya</title>
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
    .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 24px; animation: headerIn 0.5s ease both; }
    @keyframes headerIn { from{opacity:0;transform:translateY(-12px)} to{opacity:1;transform:translateY(0)} }
    @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
    @keyframes rowIn { from{opacity:0;transform:translateX(-10px)} to{opacity:1;transform:translateX(0)} }

    /* LAYOUT 2 COL */
    .settings-grid { display: grid; grid-template-columns: 220px 1fr; gap: 20px; margin-bottom: 22px; }

    /* MENU TABS */
    .settings-menu { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 8px; animation: fadeUp 0.5s ease both; }
    .menu-item { display: flex; align-items: center; gap: 9px; padding: 9px 12px; border-radius: 9px; font-size: 13px; font-weight: 500; color: var(--muted); cursor: pointer; transition: all 150ms; border: none; background: none; width: 100%; text-align: left; font-family: inherit; }
    .menu-item:hover { background: #FAF6EF; color: var(--text); }
    .menu-item.active { background: #FEF3C7; color: var(--wood); font-weight: 700; }
    .menu-item svg { width: 15px; height: 15px; flex-shrink: 0; }

    /* CARDS */
    .card { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; animation: fadeUp 0.5s 0.05s ease both; }
    .card-header { padding: 16px 20px; border-bottom: 1px solid var(--border); }
    .card-title { font-size: 14px; font-weight: 700; color: var(--text); }
    .card-desc  { font-size: 12px; color: var(--muted); margin-top: 2px; }
    .card-body  { padding: 20px; }

    /* FORM */
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .form-group { margin-bottom: 0; }
    .form-label { display: block; font-size: 12px; font-weight: 600; color: #4A3728; margin-bottom: 6px; }
    .form-input { width: 100%; padding: 9px 13px; border: 1.5px solid var(--border); border-radius: 9px; font-size: 13px; background: #FAFAF8; color: var(--text); outline: none; transition: all 200ms; font-family: inherit; }
    .form-input:focus { border-color: var(--wood); background: white; box-shadow: 0 0 0 3px rgba(122,74,45,0.08); }
    .form-select { width: 100%; padding: 9px 13px; border: 1.5px solid var(--border); border-radius: 9px; font-size: 13px; background: #FAFAF8; color: var(--text); outline: none; font-family: inherit; cursor: pointer; }
    .form-select:focus { border-color: var(--wood); }
    .form-textarea { width: 100%; padding: 9px 13px; border: 1.5px solid var(--border); border-radius: 9px; font-size: 13px; background: #FAFAF8; color: var(--text); outline: none; font-family: inherit; resize: vertical; min-height: 72px; }
    .form-textarea:focus { border-color: var(--wood); }
    .form-footer { padding: 16px 20px; border-top: 1px solid var(--border); background: #FAF6EF; display: flex; align-items: center; gap: 10px; }

    /* BUTTONS */
    .btn-primary  { background: var(--wood); color: white; font-size: 13px; font-weight: 700; padding: 9px 20px; border-radius: 9px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 200ms; font-family: inherit; text-decoration: none; }
    .btn-primary:hover  { background: var(--wood-dk); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(122,74,45,0.3); }
    .btn-secondary { background: white; color: var(--text); font-size: 13px; font-weight: 500; padding: 9px 16px; border-radius: 9px; border: 1.5px solid var(--border); cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 180ms; font-family: inherit; text-decoration: none; }
    .btn-secondary:hover { background: #FAF6EF; border-color: #D5CABC; }
    .btn-success  { background: #F0FDF4; color: #15803D; font-size: 13px; font-weight: 600; padding: 9px 16px; border-radius: 9px; border: 1.5px solid #BBF7D0; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 200ms; font-family: inherit; text-decoration: none; }
    .btn-success:hover  { background: #DCFCE7; transform: translateY(-2px); }
    .btn-danger   { background: #FEF2F2; color: #DC2626; font-size: 12px; font-weight: 600; padding: 5px 12px; border-radius: 7px; border: 1.5px solid #FECACA; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; transition: all 150ms; font-family: inherit; }
    .btn-danger:hover   { background: #FEE2E2; }
    .btn-edit     { background: #EFF6FF; color: #2563EB; font-size: 12px; font-weight: 600; padding: 5px 12px; border-radius: 7px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; transition: all 150ms; font-family: inherit; }
    .btn-edit:hover     { background: #DBEAFE; }

    /* METRICS GRID */
    .metrics-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; margin-bottom: 22px; animation: fadeUp 0.5s 0.1s ease both; }
    .metric-card { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 16px; transition: all 200ms; position: relative; overflow: hidden; }
    .metric-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; }
    .mc-blue::before  { background: linear-gradient(90deg,#2563EB,#93C5FD); }
    .mc-amber::before { background: linear-gradient(90deg,#D97706,#FCD34D); }
    .mc-red::before   { background: linear-gradient(90deg,#DC2626,#FCA5A5); }
    .mc-green::before { background: linear-gradient(90deg,#16A34A,#4ADE80); }
    .mc-purple::before{ background: linear-gradient(90deg,#7C3AED,#C4B5FD); }
    .metric-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.09); }
    .metric-label { font-size: 10px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 8px; }
    .metric-val   { font-size: 20px; font-weight: 800; color: var(--text); }
    .metric-val.amber { color: var(--wood); }
    .metric-val.red   { color: #DC2626; }

    /* TABLE */
    .data-tbl { width: 100%; border-collapse: collapse; font-size: 13px; }
    .data-tbl thead th { padding: 10px 16px; background: #FAF6EF; text-align: left; font-size: 10px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.07em; border-bottom: 1px solid var(--border); }
    .data-tbl tbody tr { border-bottom: 1px solid #F5EFE5; transition: background 150ms; animation: rowIn 0.4s ease both; }
    .data-tbl tbody tr:last-child { border: none; }
    .data-tbl tbody tr:hover { background: #FAF6EF; }
    .data-tbl td { padding: 12px 16px; vertical-align: middle; }
    .badge { display: inline-flex; align-items: center; font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 20px; }
    .badge-active   { background: #DCFCE7; color: #15803D; }
    .badge-inactive { background: #F3F4F6; color: #6B7280; }
    .role-owner  { background: #FEF3C7; color: #92400E; font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 20px; display: inline-flex; }
    .role-kasir  { background: #DBEAFE; color: #1D4ED8; font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 20px; display: inline-flex; }
    .role-admin  { background: #F3E8FF; color: #6D28D9; font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 20px; display: inline-flex; }

    /* TOGGLE */
    .toggle-wrap { display: flex; align-items: center; gap: 10px; }
    .toggle { position: relative; display: inline-block; width: 40px; height: 22px; }
    .toggle input { opacity: 0; width: 0; height: 0; }
    .toggle-slider { position: absolute; cursor: pointer; inset: 0; background: #D5CABC; border-radius: 22px; transition: 0.3s; }
    .toggle-slider:before { content: ''; position: absolute; width: 16px; height: 16px; left: 3px; bottom: 3px; background: white; border-radius: 50%; transition: 0.3s; }
    input:checked + .toggle-slider { background: var(--wood); }
    input:checked + .toggle-slider:before { transform: translateX(18px); }

    /* FLASH */
    .flash-success { display: flex; align-items: center; gap: 10px; background: #F0FDF4; border: 1px solid #BBF7D0; color: #15803D; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 20px; animation: fadeUp 0.3s ease; }
    .flash-error   { display: flex; align-items: center; gap: 10px; background: #FEF2F2; border: 1px solid #FECACA; color: #B91C1C; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 20px; animation: fadeUp 0.3s ease; }

    /* MODAL */
    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 100; padding: 16px; backdrop-filter: blur(4px); }
    .modal-box { background: white; border-radius: 16px; box-shadow: 0 24px 64px rgba(0,0,0,0.2); max-width: 460px; width: 100%; padding: 28px; animation: modalIn 0.3s cubic-bezier(0.34,1.56,0.64,1); }
    @keyframes modalIn { from{opacity:0;transform:scale(0.9)} to{opacity:1;transform:scale(1)} }

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
    <?php if ($userRole === 'admin'): ?>
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
    <div class="breadcrumb">Dashboard / <strong>Pengaturan</strong></div>
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
        <h1 style="font-size:22px;font-weight:800;color:var(--text);letter-spacing:-0.02em;">Pengaturan</h1>
        <p style="font-size:13px;color:var(--muted);margin-top:3px;">Kelola sistem dan konfigurasi toko</p>
      </div>
    </div>

    <!-- Flash -->
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

    <!-- RINGKASAN SISTEM -->
    <div class="metrics-grid">
      <div class="metric-card mc-blue">
        <div class="metric-label">Total Produk</div>
        <div class="metric-val"><?= number_format($stats['total_products']) ?></div>
      </div>
      <div class="metric-card mc-purple">
        <div class="metric-label">Total User</div>
        <div class="metric-val"><?= number_format($stats['total_users']) ?></div>
      </div>
      <div class="metric-card mc-red">
        <div class="metric-label">Stok Menipis</div>
        <div class="metric-val red"><?= number_format($stats['low_stock']) ?></div>
      </div>
      <div class="metric-card mc-green">
        <div class="metric-label">Transaksi Hari Ini</div>
        <div class="metric-val"><?= number_format($stats['today_trx']) ?></div>
      </div>
      <div class="metric-card mc-amber">
        <div class="metric-label">Omzet Bulan Ini</div>
        <div class="metric-val amber" style="font-size:15px;">Rp <?= number_format($stats['month_revenue'],0,',','.') ?></div>
      </div>
    </div>

    <!-- SETTINGS GRID (menu + detail) -->
    <div class="settings-grid">

      <!-- Menu Kiri -->
      <div class="settings-menu">
        <p style="font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.1em;padding:8px 12px 6px;">Menu Pengaturan</p>
        <button class="menu-item active" onclick="showTab('profil', this)">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
          Profil Toko
        </button>
        <?php if (session('user_role') === 'owner'): ?>
        <button class="menu-item" onclick="showTab('users', this)">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          Pengguna & Role
        </button>
        <?php endif; ?>
        <button class="menu-item" onclick="showTab('transaksi', this)">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
          Transaksi
        </button>
        <?php if (session('user_role') === 'owner'): ?>
        <button class="menu-item" onclick="showTab('backup', this)">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
          Backup & Export
        </button>
        <?php endif; ?>
      </div>

      <!-- Panel Kanan -->
      <div>

        <!-- TAB: Profil Toko -->
        <div id="tab-profil" class="card">
          <div class="card-header">
            <div class="card-title">Profil Toko</div>
            <div class="card-desc">Identitas dan informasi kontak toko</div>
          </div>
          <form method="POST" action="<?= base_url('settings/store') ?>">
            <?= csrf_field() ?>
            <div class="card-body">
              <div class="form-grid">
                <div class="form-group">
                  <label class="form-label">Nama Toko <span style="color:#EF4444;">*</span></label>
                  <input type="text" name="store_name" value="<?= esc($settings['store_name'] ?? 'Toko Kayu Kontan Jaya') ?>" class="form-input" required/>
                </div>
                <div class="form-group">
                  <label class="form-label">Email Toko</label>
                  <input type="email" name="store_email" value="<?= esc($settings['store_email'] ?? '') ?>" class="form-input" placeholder="toko@email.com"/>
                </div>
                <div class="form-group">
                  <label class="form-label">Nomor Telepon</label>
                  <input type="text" name="store_phone" value="<?= esc($settings['store_phone'] ?? '') ?>" class="form-input" placeholder="08xxxxxxxxxx"/>
                </div>
                <div class="form-group">
                  <label class="form-label">Kota / Lokasi</label>
                  <input type="text" name="store_city" value="<?= esc($settings['store_city'] ?? '') ?>" class="form-input" placeholder="Semarang"/>
                </div>
                <div class="form-group" style="grid-column:span 2;">
                  <label class="form-label">Alamat Lengkap</label>
                  <textarea name="store_address" class="form-textarea" placeholder="Jl. Raya Mebel No. 1..."><?= esc($settings['store_address'] ?? '') ?></textarea>
                </div>
              </div>
            </div>
            <div class="form-footer">
              <button type="submit" class="btn-primary">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Perubahan
              </button>
            </div>
          </form>
        </div>

        <!-- TAB: Pengguna -->
        <?php if (session('user_role') === 'owner'): ?>
        <div id="tab-users" class="card" style="display:none;">
          <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
            <div>
              <div class="card-title">Manajemen Pengguna</div>
              <div class="card-desc">Kelola akun kasir dan admin</div>
            </div>
            <button class="btn-primary" onclick="openUserModal()">
              <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
              Tambah User
            </button>
          </div>
          <div style="overflow-x:auto;">
            <table class="data-tbl">
              <thead>
                <tr>
                  <th>Nama</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th style="text-align:center;">Status</th>
                  <th style="text-align:center;">Last Login</th>
                  <th style="text-align:center;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($users)): ?>
                <tr><td colspan="6" style="padding:32px;text-align:center;color:var(--muted);">Belum ada pengguna</td></tr>
                <?php else: ?>
                <?php foreach ($users as $i => $u): ?>
                <tr style="animation-delay:<?= $i * 0.04 ?>s;">
                  <td style="font-weight:600;"><?= esc($u['name']) ?></td>
                  <td style="color:var(--muted);font-size:12px;"><?= esc($u['email']) ?></td>
                  <td>
                    <?php
                      $roleClass = match($u['role']) {
                        'owner'   => 'role-owner',
                        'admin'   => 'role-kasir',
                        'cashier' => 'role-admin',
                        default   => 'role-admin'
                      };
                      $roleLabel = match($u['role']) {
                        'owner'   => 'Owner',
                        'admin'   => 'Admin',
                        'cashier' => 'Kasir',
                        default   => ucfirst($u['role'])
                      };
                    ?>
                    <span class="<?= $roleClass ?>"><?= $roleLabel ?></span>
                  </td>
                  <td style="text-align:center;">
                    <span class="badge <?= $u['is_active'] ? 'badge-active' : 'badge-inactive' ?>">
                      <?= $u['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                    </span>
                  </td>
                  <td style="text-align:center;font-size:12px;color:var(--muted);">
                  <?= !empty($u['last_login_at']) ? date('d/m/Y H:i', strtotime($u['last_login_at'])) : '—' ?>                  </td>
                  <td style="text-align:center;">
                    <div style="display:flex;align-items:center;justify-content:center;gap:6px;">
                      <button class="btn-edit" onclick="editUser(<?= htmlspecialchars(json_encode($u)) ?>)">
                        <svg style="width:11px;height:11px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                      </button>
                      <?php if ($u['id'] != session('user_id')): ?>
                      <form method="POST" action="<?= base_url('settings/user/delete') ?>" onsubmit="return confirm('Hapus pengguna ini?')" style="display:inline;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>"/>
                        <button type="submit" class="btn-danger">
                          <svg style="width:11px;height:11px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                          Hapus
                        </button>
                      </form>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
        <?php endif; // end owner-only users tab ?>

        <!-- TAB: Transaksi -->
        <div id="tab-transaksi" class="card" style="display:none;">
          <div class="card-header">
            <div class="card-title">Pengaturan Transaksi</div>
            <div class="card-desc">Konfigurasi invoice, pajak, dan metode pembayaran</div>
          </div>
          <form method="POST" action="<?= base_url('settings/store') ?>">
            <?= csrf_field() ?>
            <div class="card-body">
              <div class="form-grid">
                <div class="form-group">
                  <label class="form-label">Prefix Invoice</label>
                  <input type="text" name="invoice_prefix" value="<?= esc($settings['invoice_prefix'] ?? 'INV-') ?>" class="form-input" placeholder="INV-"/>
                </div>
                <div class="form-group">
                  <label class="form-label">Pajak Default (%)</label>
                  <input type="number" name="tax_rate" value="<?= esc($settings['tax_rate'] ?? '0') ?>" class="form-input" min="0" max="100" placeholder="0"/>
                </div>
                <div class="form-group" style="grid-column:span 2;">
                  <label class="form-label" style="margin-bottom:10px;">Auto Print Invoice</label>
                  <div class="toggle-wrap">
                    <label class="toggle">
                      <input type="checkbox" name="auto_print" value="1" <?= ($settings['auto_print'] ?? '0') == '1' ? 'checked' : '' ?>/>
                      <span class="toggle-slider"></span>
                    </label>
                    <span style="font-size:13px;color:var(--muted);">Cetak invoice otomatis setelah transaksi selesai</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-footer">
              <button type="submit" class="btn-primary">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Pengaturan
              </button>
            </div>
          </form>
        </div>

        <!-- TAB: Backup -->
        <?php if (session('user_role') === 'owner'): ?>
        <div id="tab-backup" class="card" style="display:none;">
          <div class="card-header">
            <div class="card-title">Backup & Export</div>
            <div class="card-desc">Unduh data dan kelola backup sistem</div>
          </div>
          <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
              <a href="<?= base_url('reports/export-csv') ?>" class="btn-success" style="justify-content:center;padding:14px;">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export Transaksi CSV
              </a>
              <a href="<?= base_url('products/export-csv') ?>" class="btn-success" style="justify-content:center;padding:14px;">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export Produk CSV
              </a>
              <form method="POST" action="<?= base_url('settings/backup') ?>" style="grid-column:span 2;">
                <?= csrf_field() ?>
                <button type="submit" class="btn-primary" style="width:100%;justify-content:center;padding:14px;">
                  <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                  Backup Database
                </button>
              </form>
            </div>
          </div>
        </div>
        <?php endif; // end owner-only backup tab ?>

      </div>
    </div>

  </div>
</div>

<!-- Modal User — hanya untuk owner -->
<?php if (session('user_role') === 'owner'): ?>
<div id="userModal" style="display:none;" class="modal-overlay" onclick="if(event.target===this)closeUserModal()">
  <div class="modal-box">
    <h3 style="font-size:16px;font-weight:800;color:var(--text);margin-bottom:4px;" id="modalTitle">Tambah Pengguna</h3>
    <p style="font-size:12px;color:var(--muted);margin-bottom:20px;">Isi data pengguna baru</p>
    <form method="POST" action="<?= base_url('settings/user/store') ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="user_id" id="editUserId"/>
      <div style="display:grid;gap:14px;">
        <div>
          <label class="form-label">Nama Lengkap</label>
          <input type="text" name="name" id="editName" class="form-input" required/>
        </div>
        <div>
          <label class="form-label">Email</label>
          <input type="email" name="email" id="editEmail" class="form-input" required/>
        </div>
        <div>
          <label class="form-label">Role</label>
          <select name="role" id="editRole" class="form-select">
            <option value="cashier">Kasir</option>
            <option value="admin">Admin</option>
            <option value="owner">Owner</option>
          </select>
        </div>
        <div>
          <label class="form-label">Password <span style="font-size:11px;color:var(--muted);font-weight:400;">(kosongkan jika tidak diubah)</span></label>
          <input type="password" name="password" id="editPassword" class="form-input" placeholder="••••••••"/>
        </div>
      </div>
      <div style="display:flex;gap:10px;margin-top:20px;">
        <button type="submit" class="btn-primary" style="flex:1;justify-content:center;">Simpan</button>
        <button type="button" class="btn-secondary" onclick="closeUserModal()" style="flex:1;justify-content:center;">Batal</button>
      </div>
    </form>
  </div>
</div>
<?php endif; // end owner-only modal ?>

<?php if (session('success')): ?>
<div class="toast success" id="toast">
  <svg style="width:16px;height:16px;color:#22C55E;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
  </svg>
  <?= esc(session('success')) ?>
</div>
<?php endif; ?>

<script>
// Sidebar
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

// Tab switching
function showTab(name, btn) {
  ['profil','users','transaksi','backup'].forEach(t => {
    const el = document.getElementById('tab-' + t);
    if (el) el.style.display = 'none';
  });
  document.querySelectorAll('.menu-item').forEach(b => b.classList.remove('active'));
  const target = document.getElementById('tab-' + name);
  if (target) target.style.display = 'block';
  btn.classList.add('active');
}

// User modal
<?php if (session('user_role') === 'owner'): ?>
function openUserModal() {
  document.getElementById('modalTitle').textContent = 'Tambah Pengguna';
  document.getElementById('editUserId').value = '';
  document.getElementById('editName').value = '';
  document.getElementById('editEmail').value = '';
  document.getElementById('editRole').value = 'cashier';
  document.getElementById('editPassword').value = '';
  document.getElementById('userModal').style.display = 'flex';
}
function editUser(u) {
  document.getElementById('modalTitle').textContent = 'Edit Pengguna';
  document.getElementById('editUserId').value = u.id;
  document.getElementById('editName').value = u.name;
  document.getElementById('editEmail').value = u.email;
  document.getElementById('editRole').value = u.role;
  document.getElementById('editPassword').value = '';
  document.getElementById('userModal').style.display = 'flex';
  document.querySelectorAll('.menu-item').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.menu-item')[1].classList.add('active');
}
function closeUserModal() {
  document.getElementById('userModal').style.display = 'none';
}
<?php endif; ?>

// Auto open tab sesuai flash
<?php if (session('user_role') === 'owner' && session('success') && request()->getPost('user_id') !== null): ?>
showTab('users', document.querySelectorAll('.menu-item')[1]);
<?php endif; ?>

// Toast auto dismiss
const toast = document.getElementById('toast');
if (toast) setTimeout(() => {
  toast.style.opacity = '0'; toast.style.transform = 'translateY(8px)';
  toast.style.transition = 'all 0.3s'; setTimeout(() => toast.remove(), 300);
}, 3500);
</script>
</body>
</html>