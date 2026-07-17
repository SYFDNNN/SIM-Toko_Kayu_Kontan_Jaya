<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= esc($title ?? 'Toko Kayu Kontan Jaya') ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    * { font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; }
    :root {
      --sb-w: 220px; --sb-w-mini: 60px; --tb-h: 52px;
      --wood: #7A4A2D; --wood-dk: #6B3F25; --wood-lt: #9B5E30;
      --bg: #F5F0E8; --surface: #FFFDF9; --border: #EDE5D8;
      --text: #1C1410; --muted: #9B8B77;
      --success: #16A34A; --warning: #D97706; --danger: #DC2626; --info: #1D4ED8;
    }
    body { background: var(--bg); color: var(--text); margin: 0; }

    /* SIDEBAR */
    #sidebar {
      position: fixed; inset-y: 0; left: 0; z-index: 50;
      width: var(--sb-w);
      background: linear-gradient(160deg, #2C1206 0%, #4A1F08 50%, #6B3412 100%);
      display: flex; flex-direction: column;
      transition: width 220ms cubic-bezier(.4,0,.2,1);
      box-shadow: 4px 0 20px rgba(0,0,0,0.12);
    }
    #sidebar.mini { width: var(--sb-w-mini); }
    #sidebar.mini .nav-label,
    #sidebar.mini .brand-text,
    #sidebar.mini .user-text,
    #sidebar.mini .sec-label { display: none; }
    #sidebar.mini .nav-link { justify-content: center; padding: 10px 0; margin: 1px 8px; }
    #sidebar.mini .sb-brand { padding: 14px 0; justify-content: center; }
    #sidebar.mini #toggle-btn { display: none; }

    .sb-brand { display: flex; align-items: center; gap: 10px; padding: 14px 14px 12px; border-bottom: 1px solid rgba(255,255,255,0.08); }
    .sb-logo { width: 32px; height: 32px; border-radius: 8px; background: rgba(244,195,80,0.2); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .brand-text .b1 { font-size: 13px; font-weight: 700; color: #fff; line-height: 1.2; }
    .brand-text .b2 { font-size: 10px; color: rgba(255,255,255,0.4); }
    #toggle-btn { margin-left: auto; background: none; border: none; cursor: pointer; color: rgba(255,255,255,0.3); padding: 4px; border-radius: 6px; transition: all 130ms; }
    #toggle-btn:hover { color: white; background: rgba(255,255,255,0.1); }
    #toggle-icon { transition: transform 220ms; }
    #sidebar.mini #toggle-icon { transform: rotate(180deg); }

    .sb-nav { flex: 1; padding: 10px 8px; overflow-y: auto; overflow-x: hidden; }
    .sec-label { font-size: 9px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: rgba(255,255,255,0.25); padding: 10px 10px 4px; white-space: nowrap; }
    .nav-link { display: flex; align-items: center; gap: 10px; padding: 9px 10px; border-radius: 8px; color: rgba(255,255,255,0.55); font-size: 13px; font-weight: 500; cursor: pointer; transition: all 130ms; margin-bottom: 1px; position: relative; text-decoration: none; white-space: nowrap; overflow: hidden; }
    .nav-link:hover { background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.85); }
    .nav-link.active { background: rgba(255,255,255,0.12); color: #fff; box-shadow: inset 0 0 0 1px rgba(255,255,255,0.1); }
    .nav-link.active::before { content: ''; position: absolute; left: 0; top: 25%; height: 50%; width: 3px; background: #F4C350; border-radius: 0 3px 3px 0; }
    .nav-icon { width: 16px; height: 16px; flex-shrink: 0; }

    .sb-footer { padding: 10px 8px 14px; border-top: 1px solid rgba(255,255,255,0.08); }
    .user-row { display: flex; align-items: center; gap: 9px; padding: 8px; border-radius: 8px; cursor: pointer; transition: background 130ms; text-decoration: none; }
    .user-row:hover { background: rgba(255,255,255,0.07); }
    .user-avatar { width: 28px; height: 28px; border-radius: 8px; background: rgba(244,195,80,0.25); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: #F4C350; flex-shrink: 0; }
    .user-text .un { font-size: 12px; font-weight: 600; color: white; }
    .user-text .ur { font-size: 10px; color: rgba(255,255,255,0.35); }
    .logout-btn { margin-left: auto; color: rgba(255,255,255,0.3); transition: color 130ms; }
    .logout-btn:hover { color: #FC8181; }

    /* TOPBAR */
    #topbar {
      position: fixed; top: 0; right: 0; z-index: 40;
      left: var(--sb-w); height: var(--tb-h);
      background: var(--surface); border-bottom: 1px solid var(--border);
      display: flex; align-items: center; padding: 0 20px; gap: 12px;
      transition: left 220ms cubic-bezier(.4,0,.2,1);
      box-shadow: 0 1px 6px rgba(0,0,0,0.04);
    }
    #topbar.mini { left: var(--sb-w-mini); }

    .breadcrumb { font-size: 12px; color: var(--muted); white-space: nowrap; }
    .breadcrumb strong { color: var(--text); font-weight: 600; }
    .tb-divider { width: 1px; height: 20px; background: var(--border); flex-shrink: 0; }
    .tb-search { position: relative; flex: 1; max-width: 280px; }
    .tb-search input { width: 100%; padding: 7px 12px 7px 32px; border-radius: 8px; border: 1px solid var(--border); background: #FAF6EF; font-size: 12px; color: var(--text); outline: none; transition: border-color 130ms; font-family: inherit; }
    .tb-search input:focus { border-color: var(--wood); box-shadow: 0 0 0 3px rgba(122,74,45,0.08); }
    .tb-search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: var(--muted); pointer-events: none; }

    .tb-right { margin-left: auto; display: flex; align-items: center; gap: 8px; }
    .btn-new-trx { background: var(--wood); color: white; font-size: 12px; font-weight: 600; padding: 7px 14px; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; gap: 5px; transition: all 130ms; font-family: inherit; text-decoration: none; }
    .btn-new-trx:hover { background: var(--wood-dk); transform: translateY(-1px); box-shadow: 0 3px 10px rgba(122,74,45,0.25); }
    .icon-btn { width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border); background: white; display: flex; align-items: center; justify-content: center; cursor: pointer; position: relative; transition: background 130ms; }
    .icon-btn:hover { background: #FAF6EF; }
    .notif-badge { position: absolute; top: 4px; right: 4px; width: 8px; height: 8px; border-radius: 50%; background: #E53E3E; border: 2px solid white; }
    .tb-avatar { width: 30px; height: 30px; border-radius: 8px; background: var(--wood); color: white; font-size: 11px; font-weight: 700; display: flex; align-items: center; justify-content: center; cursor: pointer; }

    /* MAIN */
    #main {
      margin-left: var(--sb-w); margin-top: var(--tb-h);
      min-height: calc(100vh - var(--tb-h));
      padding: 24px; transition: margin-left 220ms cubic-bezier(.4,0,.2,1);
    }
    #main.mini { margin-left: var(--sb-w-mini); }

    /* PAGE HEADER */
    .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
    .page-title { font-size: 20px; font-weight: 700; color: var(--text); }
    .page-sub { font-size: 13px; color: var(--muted); margin-top: 2px; }

    /* CARDS */
    .card { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; box-shadow: 0 1px 8px rgba(0,0,0,0.04); }
    .card-header { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-bottom: 1px solid var(--border); }
    .card-title { font-size: 13px; font-weight: 600; color: var(--text); }
    .card-body { padding: 18px; }

    /* METRIC CARDS */
    .metrics { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 14px; margin-bottom: 22px; }
    .metric { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 18px; transition: all 150ms; }
    .metric:hover { box-shadow: 0 6px 20px rgba(0,0,0,0.08); transform: translateY(-2px); }
    .metric-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
    .metric-label { font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; }
    .metric-ico { width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
    .ico-g { background: #DCFCE7; } .ico-a { background: #FEF3C7; }
    .ico-b { background: #DBEAFE; } .ico-r { background: #FEE2E2; }
    .metric-val { font-size: 22px; font-weight: 700; color: var(--text); margin-bottom: 5px; }
    .metric-foot { font-size: 11px; color: var(--muted); display: flex; align-items: center; gap: 6px; }
    .badge-up { background: #DCFCE7; color: #15803D; font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 20px; }
    .badge-dn { background: #FEE2E2; color: #B91C1C; font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 20px; }
    .badge-wn { background: #FEF3C7; color: #92400E; font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 20px; }

    /* BUTTONS */
    .btn { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; padding: 8px 16px; border-radius: 9px; border: none; cursor: pointer; transition: all 130ms; font-family: inherit; text-decoration: none; }
    .btn-primary { background: var(--wood); color: white; }
    .btn-primary:hover { background: var(--wood-dk); box-shadow: 0 3px 10px rgba(122,74,45,0.25); transform: translateY(-1px); }
    .btn-secondary { background: white; color: var(--text); border: 1px solid var(--border); }
    .btn-secondary:hover { background: var(--bg); border-color: #C8BAA8; }
    .btn-success { background: #F0FDF4; color: #15803D; border: 1px solid #BBF7D0; }
    .btn-success:hover { background: #DCFCE7; }
    .btn-danger { background: #FEF2F2; color: var(--danger); border: 1px solid #FECACA; }
    .btn-danger:hover { background: #FEE2E2; }
    .btn-sm { padding: 6px 12px; font-size: 12px; border-radius: 7px; }
    .btn-xs { padding: 4px 10px; font-size: 11px; border-radius: 6px; }

    /* TABLES */
    .tbl-wrap { border-radius: 14px; overflow: hidden; border: 1px solid var(--border); background: var(--surface); }
    .data-tbl { width: 100%; border-collapse: collapse; font-size: 13px; }
    .data-tbl thead th { padding: 11px 16px; background: #FAF6EF; text-align: left; font-size: 10px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.06em; border-bottom: 1px solid var(--border); }
    .data-tbl tbody tr { border-bottom: 1px solid #F5EFE5; transition: background 100ms; }
    .data-tbl tbody tr:last-child { border: none; }
    .data-tbl tbody tr:hover { background: #FAF6EF; }
    .data-tbl td { padding: 12px 16px; color: var(--text); }
    .td-mono { font-family: monospace; font-size: 12px; color: var(--wood-lt); font-weight: 600; }

    /* BADGES */
    .badge { display: inline-flex; align-items: center; font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 20px; }
    .bg-success { background: #DCFCE7; color: #15803D; }
    .bg-warning { background: #FEF3C7; color: #92400E; }
    .bg-danger  { background: #FEE2E2; color: #B91C1C; }
    .bg-info    { background: #DBEAFE; color: #1D4ED8; }
    .bg-neutral { background: #F3F4F6; color: #4B5563; }
    .bg-purple  { background: #EDE9FE; color: #6D28D9; }

    /* FORMS */
    .form-group { margin-bottom: 16px; }
    .form-label { display: block; font-size: 12px; font-weight: 600; color: #57453A; margin-bottom: 6px; }
    .form-input { width: 100%; padding: 9px 13px; border: 1.5px solid var(--border); border-radius: 9px; font-size: 13px; background: white; color: var(--text); transition: border-color 130ms; outline: none; font-family: inherit; }
    .form-input:focus { border-color: var(--wood); box-shadow: 0 0 0 3px rgba(122,74,45,0.08); }
    .form-select { width: 100%; padding: 9px 13px; border: 1.5px solid var(--border); border-radius: 9px; font-size: 13px; background: white; color: var(--text); outline: none; font-family: inherit; cursor: pointer; }
    .form-select:focus { border-color: var(--wood); }
    .form-textarea { width: 100%; padding: 9px 13px; border: 1.5px solid var(--border); border-radius: 9px; font-size: 13px; background: white; color: var(--text); outline: none; font-family: inherit; resize: vertical; }
    .form-textarea:focus { border-color: var(--wood); }

    /* TOAST */
    #toast { position: fixed; bottom: 24px; right: 24px; z-index: 9999; background: #1C1410; color: white; padding: 13px 18px; border-radius: 11px; font-size: 13px; font-weight: 500; box-shadow: 0 8px 30px rgba(0,0,0,0.2); display: flex; align-items: center; gap: 10px; animation: slideUp .3s ease; max-width: 380px; }
    #toast.t-success { border-left: 3px solid #22C55E; }
    #toast.t-error   { border-left: 3px solid #EF4444; }
    @keyframes slideUp { from { transform: translateY(16px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

    /* SCROLLBAR */
    ::-webkit-scrollbar { width: 5px; height: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #D4C9BF; border-radius: 3px; }
  </style>
  <?= $this->renderSection('styles') ?>
</head>
<body>

<!-- ── SIDEBAR ── -->
<aside id="sidebar">
  <div class="sb-brand">
    <div class="sb-logo">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
        <path d="M12 3L20 19H4L12 3Z" fill="#F4C350"/>
        <rect x="9" y="15" width="6" height="1.5" rx="0.75" fill="#4A1F08"/>
      </svg>
    </div>
    <div class="brand-text">
      <div class="b1">Toko Kayu</div>
      <div class="b2">Kontan Jaya</div>
    </div>
    <button id="toggle-btn" onclick="toggleSidebar()">
      <svg id="toggle-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
      </svg>
    </button>
  </div>

  <nav class="sb-nav">
    <div class="sec-label">Menu Utama</div>
    <?php
    $uri      = service('request')->uri->getPath();
    $userRole = session('user_role') ?? '';

    // Definisi menu dan role yang boleh melihatnya
    $navs = [
      [
        'url'   => 'dashboard',
        'label' => 'Dashboard',
        'match' => 'dashboard',
        'roles' => ['admin', 'owner', 'cashier'],
        'icon'  => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
      ],
      [
        'url'   => 'products',
        'label' => 'Produk',
        'match' => 'products',
        'roles' => ['admin', 'owner'],
        'icon'  => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
      ],
      [
        'url'   => 'inventori',
        'label' => 'Inventori',
        'match' => 'inventori',
        'roles' => ['admin', 'owner'],
        'icon'  => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
      ],
      [
        'url'   => 'pos',
        'label' => 'Kasir',
        'match' => 'pos',
        'roles' => ['admin', 'cashier'],
        'icon'  => 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z',
      ],
      [
        'url'   => 'transactions',
        'label' => 'Transaksi',
        'match' => 'transactions',
        'roles' => ['admin', 'owner'],
        'icon'  => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
      ],
      [
        'url'   => 'reports',
        'label' => 'Laporan',
        'match' => 'reports',
        'roles' => ['admin', 'owner'],
        'icon'  => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
      ],
    ];
    foreach ($navs as $n):
      if (!in_array($userRole, $n['roles'])) continue;
      $active = strpos($uri, $n['match']) !== false ? 'active' : '';
    ?>
    <a href="<?= base_url($n['url']) ?>" class="nav-link <?= $active ?>">
      <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="<?= $n['icon'] ?>"/>
      </svg>
      <span class="nav-label"><?= $n['label'] ?></span>
    </a>
    <?php endforeach; ?>

    <div class="sec-label" style="margin-top:10px;">Sistem</div>
    <?php if (in_array($userRole, ['admin', 'owner'])): ?>
    <a href="<?= base_url('settings') ?>" class="nav-link <?= strpos($uri,'settings')!==false?'active':'' ?>">
      <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      <span class="nav-label">Pengaturan</span>
    </a>
    <?php endif; ?>
  </nav>

  <div class="sb-footer">
    <div class="user-row">
      <div class="user-avatar"><?= strtoupper(substr(session('user_name') ?? 'U', 0, 1)) ?></div>
      <div class="user-text">
        <div class="un"><?= esc(session('user_name') ?? 'User') ?></div>
        <div class="ur"><?= ucfirst(session('user_role') ?? '') ?></div>
      </div>
      <a href="<?= base_url('logout') ?>" class="logout-btn" title="Logout">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
        </svg>
      </a>
    </div>
  </div>
</aside>

<!-- ── TOPBAR ── -->
<header id="topbar">
  <span class="breadcrumb">Toko Kayu &nbsp;/&nbsp; <strong><?= esc($title ?? 'Dashboard') ?></strong></span>
  <div class="tb-divider"></div>
  <div class="tb-search">
    <svg class="tb-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/>
    </svg>
    <input type="text" placeholder="Cari produk, transaksi..." id="global-search"
           onkeypress="if(event.key==='Enter'){location.href='<?= base_url('products') ?>?search='+this.value}"/>
  </div>
  <div class="tb-right">
    <?php if (in_array(session('user_role'), ['admin', 'cashier'])): ?>
    <a href="<?= base_url('pos') ?>" class="btn-new-trx">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" d="M12 4v16m8-8H4"/>
      </svg>
      Transaksi Baru
    </a>
    <?php endif; ?>
    <?php
    $db2 = \Config\Database::connect();
    $lowC = $db2->query("SELECT COUNT(*) as c FROM products WHERE stock<=stock_minimum AND is_active=1 AND deleted_at IS NULL")->getRowArray()['c'] ?? 0;
    ?>
    <a href="<?= base_url('inventori') ?>" class="icon-btn" title="<?= $lowC ?> produk stok rendah">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
      </svg>
      <?php if ($lowC > 0): ?>
      <span class="notif-badge"></span>
      <?php endif; ?>
    </a>
    <div class="tb-avatar"><?= strtoupper(substr(session('user_name') ?? 'U', 0, 1)) ?></div>
  </div>
</header>

<!-- ── MAIN ── -->
<main id="main">
  <?php if (session('success')): ?>
  <div id="toast" class="t-success">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22C55E" stroke-width="2.5"><path stroke-linecap="round" d="M5 13l4 4L19 7"/></svg>
    <?= esc(session('success')) ?>
  </div>
  <?php elseif (session('error')): ?>
  <div id="toast" class="t-error">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2.5"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
    <?= esc(session('error')) ?>
  </div>
  <?php endif; ?>

  <?= $this->renderSection('content') ?>
</main>

<script>
function toggleSidebar() {
  const sb = document.getElementById('sidebar');
  const main = document.getElementById('main');
  const tb = document.getElementById('topbar');
  sb.classList.toggle('mini');
  main.classList.toggle('mini');
  tb.classList.toggle('mini');
  localStorage.setItem('sb_mini', sb.classList.contains('mini'));
}
document.addEventListener('DOMContentLoaded', () => {
  if (localStorage.getItem('sb_mini') === 'true') {
    document.getElementById('sidebar').classList.add('mini');
    document.getElementById('main').classList.add('mini');
    document.getElementById('topbar').classList.add('mini');
  }
  const t = document.getElementById('toast');
  if (t) setTimeout(() => { t.style.transition='opacity .4s'; t.style.opacity='0'; setTimeout(()=>t.remove(),400); }, 3500);
});
</script>
<?= $this->renderSection('scripts') ?>
</body>
</html>