<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= esc($title ?? 'Form Produk') ?> — Toko Kayu Kontan Jaya</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    * { font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; margin: 0; padding: 0; }
    :root { --wood:#7A4A2D;--wood-dk:#5C2D10;--gold:#F4C350;--bg:#F7F2EB;--surface:#FFFFFF;--border:#EDE5D8;--text:#1C1410;--muted:#9B8B77; }
    body { background: var(--bg); color: var(--text); overflow-x: hidden; }

    /* SIDEBAR */
    #sidebar { width:240px;flex-shrink:0;background:linear-gradient(180deg,#1E0B02 0%,#3A1608 40%,#5C2D10 100%);height:100vh;position:fixed;left:0;top:0;z-index:50;display:flex;flex-direction:column;transition:width 250ms cubic-bezier(0.4,0,0.2,1);box-shadow:4px 0 40px rgba(0,0,0,0.25); }
    #sidebar.collapsed { width:68px; }
    #sidebar.collapsed .nav-label,#sidebar.collapsed .brand-name,#sidebar.collapsed .user-info { opacity:0;width:0;overflow:hidden;white-space:nowrap; }
    #sidebar.collapsed .nav-link { justify-content:center;padding:10px 0; }
    #sidebar.collapsed .section-label { display:none; }
    #sidebar::before { content:'';position:absolute;inset:0;pointer-events:none;z-index:0;background:repeating-linear-gradient(88deg,transparent,transparent 4px,rgba(255,255,255,0.02) 4px,rgba(255,255,255,0.02) 6px);animation:grainMove 12s linear infinite; }
    @keyframes grainMove { from{background-position:0 0}to{background-position:0 200px} }
    .brand-wrap { display:flex;align-items:center;gap:10px;padding:18px 14px 14px;border-bottom:1px solid rgba(255,255,255,0.06);position:relative;z-index:1; }
    .brand-logo { width:34px;height:34px;border-radius:10px;background:rgba(244,195,80,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;animation:logoPulse 3s ease-in-out infinite; }
    @keyframes logoPulse { 0%,100%{box-shadow:0 0 0 0 rgba(244,195,80,0)}50%{box-shadow:0 0 0 6px rgba(244,195,80,0.12)} }
    .brand-name b { font-size:13px;font-weight:700;color:white;display:block; }
    .brand-name span { font-size:10px;color:rgba(255,255,255,0.3); }
    .toggle-btn { margin-left:auto;background:none;border:none;cursor:pointer;color:rgba(255,255,255,0.25);padding:4px;border-radius:6px;transition:all 150ms;position:relative;z-index:1; }
    .toggle-btn:hover { background:rgba(255,255,255,0.08);color:white; }
    .sb-nav { flex:1;padding:10px 8px;overflow-y:auto;position:relative;z-index:1; }
    .section-label { font-size:9px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:rgba(255,255,255,0.2);padding:10px 10px 4px; }
    .nav-link { display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:9px;color:rgba(255,255,255,0.45);font-size:13px;font-weight:500;cursor:pointer;transition:all 180ms ease;margin-bottom:1px;text-decoration:none;position:relative; }
    .nav-link:hover { color:rgba(255,255,255,0.9);transform:translateX(3px); }
    .nav-link.active { background:rgba(255,255,255,0.1);color:white; }
    .nav-link.active::before { content:'';position:absolute;left:0;top:20%;height:60%;width:3px;background:var(--gold);border-radius:0 3px 3px 0; }
    .nav-icon { width:17px;height:17px;flex-shrink:0;transition:transform 200ms; }
    .nav-link:hover .nav-icon { transform:scale(1.15); }
    .sb-footer { padding:10px 8px 16px;border-top:1px solid rgba(255,255,255,0.06);position:relative;z-index:1; }
    .user-row { display:flex;align-items:center;gap:9px;padding:8px 10px;border-radius:9px;cursor:pointer;transition:background 150ms; }
    .user-row:hover { background:rgba(255,255,255,0.05); }
    .user-avatar { width:30px;height:30px;border-radius:9px;background:rgba(244,195,80,0.2);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:var(--gold);flex-shrink:0; }
    .user-info .uname { font-size:12px;font-weight:600;color:white;white-space:nowrap; }
    .user-info .urole { font-size:10px;color:rgba(255,255,255,0.28);text-transform:capitalize; }

    /* MAIN */
    #main { margin-left:240px;transition:margin-left 250ms cubic-bezier(0.4,0,0.2,1);min-height:100vh;display:flex;flex-direction:column; }
    #main.expanded { margin-left:68px; }

    /* TOPBAR */
    #topbar { height:58px;background:var(--surface);border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 24px;gap:14px;position:sticky;top:0;z-index:40;box-shadow:0 2px 16px rgba(0,0,0,0.04); }
    .breadcrumb { font-size:13px;color:var(--muted); }
    .breadcrumb strong { color:var(--text);font-weight:600; }
    .tb-actions { margin-left:auto;display:flex;align-items:center;gap:10px; }
    .tb-avatar { width:32px;height:32px;border-radius:9px;background:var(--wood);color:white;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center; }

    /* CONTENT */
    .content { flex:1;padding:28px 24px; }

    /* PAGE HEADER */
    .page-header { display:flex;align-items:center;gap:14px;margin-bottom:28px;animation:headerIn 0.5s ease both; }
    @keyframes headerIn { from{opacity:0;transform:translateY(-12px)}to{opacity:1;transform:translateY(0)} }
    .back-btn { width:36px;height:36px;border-radius:10px;border:1.5px solid var(--border);background:white;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all 180ms;text-decoration:none;color:var(--muted); }
    .back-btn:hover { background:#FAF6EF;border-color:#D5CABC;color:var(--wood);transform:translateX(-2px); }

    /* FORM CARD */
    .form-card { background:var(--surface);border:1px solid var(--border);border-radius:16px;overflow:hidden;animation:cardIn 0.5s 0.1s cubic-bezier(0.34,1.56,0.64,1) both; }
    @keyframes cardIn { from{opacity:0;transform:translateY(20px) scale(0.98)}to{opacity:1;transform:translateY(0) scale(1)} }

    .form-section { padding:24px 28px;border-bottom:1px solid var(--border); }
    .form-section:last-child { border-bottom:none; }
    .section-title { font-size:14px;font-weight:700;color:var(--text);margin-bottom:4px;display:flex;align-items:center;gap:8px; }
    .section-desc { font-size:12px;color:var(--muted);margin-bottom:20px; }
    .section-icon { width:28px;height:28px;border-radius:8px;background:#FEF3C7;display:flex;align-items:center;justify-content:center; }

    .form-grid { display:grid;grid-template-columns:1fr 1fr;gap:18px; }
    .form-grid.cols-3 { grid-template-columns:1fr 1fr 1fr; }
    .form-group { animation:fieldIn 0.4s ease both; }
    .form-group:nth-child(1){animation-delay:0.15s}
    .form-group:nth-child(2){animation-delay:0.2s}
    .form-group:nth-child(3){animation-delay:0.25s}
    .form-group:nth-child(4){animation-delay:0.3s}
    .form-group:nth-child(5){animation-delay:0.35s}
    .form-group:nth-child(6){animation-delay:0.4s}
    @keyframes fieldIn { from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)} }

    .field-label { display:block;font-size:12px;font-weight:600;color:#4A3728;margin-bottom:7px;letter-spacing:0.02em; }
    .field-required { color:#EF4444;margin-left:2px; }
    .field-hint { font-size:11px;color:var(--muted);font-weight:400;margin-left:4px; }

    .field-input { width:100%;padding:9px 14px;border:1.5px solid var(--border);border-radius:10px;font-size:13px;color:var(--text);background:#FAFAF8;transition:all 200ms;outline:none; }
    .field-input:focus { border-color:var(--wood);background:white;box-shadow:0 0 0 3px rgba(122,74,45,0.08);transform:translateY(-1px); }
    .field-input::placeholder { color:#C4B5A5; }
    .field-input[readonly] { background:#F5EFE5;color:var(--muted);cursor:not-allowed; }

    .field-select { width:100%;padding:9px 14px;border:1.5px solid var(--border);border-radius:10px;font-size:13px;color:var(--text);background:#FAFAF8;transition:all 200ms;outline:none;cursor:pointer;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239B8B77'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center;background-size:15px; }
    .field-select:focus { border-color:var(--wood);background-color:white;box-shadow:0 0 0 3px rgba(122,74,45,0.08); }

    .field-textarea { width:100%;padding:9px 14px;border:1.5px solid var(--border);border-radius:10px;font-size:13px;color:var(--text);background:#FAFAF8;transition:all 200ms;outline:none;resize:vertical;min-height:80px; }
    .field-textarea:focus { border-color:var(--wood);background:white;box-shadow:0 0 0 3px rgba(122,74,45,0.08); }

    .field-error { font-size:11px;color:#EF4444;margin-top:5px;display:flex;align-items:center;gap:4px; }

    /* Image upload */
    .img-upload-area { border:2px dashed var(--border);border-radius:12px;padding:24px;text-align:center;cursor:pointer;transition:all 200ms;background:#FAFAF8;position:relative; }
    .img-upload-area:hover { border-color:var(--wood);background:#FEF9EF; }
    .img-upload-area input[type=file] { position:absolute;inset:0;opacity:0;cursor:pointer; }

    /* Current image preview */
    .img-preview { width:80px;height:80px;border-radius:12px;object-fit:cover;border:2px solid var(--border);margin-bottom:8px;transition:transform 200ms; }
    .img-preview:hover { transform:scale(1.08); }

    /* Form actions */
    .form-actions { padding:20px 28px;background:#FAF6EF;border-top:1px solid var(--border);display:flex;align-items:center;gap:12px; }
    .btn-submit { background:var(--wood);color:white;font-size:14px;font-weight:700;padding:10px 24px;border-radius:10px;border:none;cursor:pointer;display:flex;align-items:center;gap:8px;transition:all 200ms;position:relative;overflow:hidden; }
    .btn-submit:hover { background:var(--wood-dk);transform:translateY(-2px);box-shadow:0 8px 24px rgba(122,74,45,0.3); }
    .btn-submit:active { transform:translateY(0); }
    .btn-submit .ripple { position:absolute;border-radius:50%;background:rgba(255,255,255,0.3);transform:scale(0);animation:ripple 0.6s linear; }
    @keyframes ripple { to{transform:scale(4);opacity:0} }
    .btn-cancel { background:white;color:var(--muted);font-size:14px;font-weight:500;padding:10px 20px;border-radius:10px;border:1.5px solid var(--border);cursor:pointer;transition:all 180ms;text-decoration:none;display:flex;align-items:center;gap:6px; }
    .btn-cancel:hover { background:#FAF6EF;color:var(--text); }

    ::-webkit-scrollbar{width:5px;}
    ::-webkit-scrollbar-track{background:transparent;}
    ::-webkit-scrollbar-thumb{background:#D5CABC;border-radius:3px;}
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
    <a href="<?= base_url('settings') ?>" class="nav-link">
      <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/>
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
      <a href="<?= base_url('logout') ?>" style="margin-left:auto;color:rgba(255,255,255,0.2);transition:color 200ms;" onmouseover="this.style.color='#EF4444'" onmouseout="this.style.color='rgba(255,255,255,0.2)'">
        <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
      </a>
    </div>
  </div>
</aside>

<!-- MAIN -->
<div id="main">
  <header id="topbar">
    <div class="breadcrumb">Produk / <strong><?= esc($title ?? 'Form Produk') ?></strong></div>
    <div class="tb-actions">
      <div class="tb-avatar"><?= strtoupper(substr(session('user_name')??'U',0,1)) ?></div>
    </div>
  </header>

  <div class="content">
    <!-- Page Header -->
    <div class="page-header">
      <a href="<?= base_url('products') ?>" class="back-btn">
        <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
      </a>
      <div>
        <h1 style="font-size:22px;font-weight:800;color:var(--text);letter-spacing:-0.02em;"><?= esc($title ?? 'Form Produk') ?></h1>
        <p style="font-size:13px;color:var(--muted);margin-top:2px;"><?= $product ? 'Update informasi produk' : 'Tambahkan produk baru ke katalog' ?></p>
      </div>
    </div>

    <!-- Error Messages -->
    <?php if (session('errors')): ?>
    <div style="margin-bottom:20px;background:#FEF2F2;border:1px solid #FECACA;border-radius:12px;padding:14px 16px;animation:fadeUp 0.3s ease;">
      <p style="font-size:13px;font-weight:600;color:#B91C1C;margin-bottom:8px;">Terdapat kesalahan:</p>
      <?php foreach (session('errors') as $err): ?>
      <p style="font-size:12px;color:#DC2626;display:flex;align-items:center;gap:5px;margin-bottom:3px;">
        <svg style="width:12px;height:12px;flex-shrink:0;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/></svg>
        <?= esc($err) ?>
      </p>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Form Card -->
    <div class="form-card">
      <form method="POST"
            action="<?= $product ? base_url('products/'.$product['id'].'/update') : base_url('products/store') ?>"
            enctype="multipart/form-data" id="productForm">
        <?= csrf_field() ?>

        <!-- SECTION 1: Informasi Dasar -->
        <div class="form-section">
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
            <div class="section-icon">
              <svg style="width:14px;height:14px;color:#92400E;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="section-title">Informasi Dasar</div>
          </div>
          <div class="section-desc">Identitas dan kategori produk</div>

          <div class="form-grid">
            <div class="form-group">
              <label class="field-label">SKU <span class="field-required">*</span></label>
              <input type="text" name="sku" value="<?= esc(old('sku', $product['sku'] ?? '')) ?>"
                class="field-input" placeholder="Contoh: MJK-001" required/>
              <div class="field-error" style="<?= isset($errors['sku'])?'':'display:none;' ?>">
                <svg style="width:11px;height:11px;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"/></svg>
                <?= esc($errors['sku'] ?? '') ?>
              </div>
            </div>
            <div class="form-group">
              <label class="field-label">Nama Produk <span class="field-required">*</span></label>
              <input type="text" name="name" value="<?= esc(old('name', $product['name'] ?? '')) ?>"
                class="field-input" placeholder="Nama lengkap produk" required/>
            </div>
            <div class="form-group">
              <label class="field-label">Kategori <span class="field-required">*</span></label>
              <select name="category_id" class="field-select" required>
                <option value="">Pilih Kategori</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= old('category_id', $product['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>><?= esc($cat['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label class="field-label">Satuan</label>
              <select name="unit" class="field-select">
                <?php foreach (['pcs','set','unit','lusin','kg','meter'] as $u): ?>
                <option value="<?= $u ?>" <?= old('unit', $product['unit'] ?? 'pcs') === $u ? 'selected' : '' ?>><?= $u ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-group" style="margin-top:18px;">
            <label class="field-label">Deskripsi <span class="field-hint">(opsional)</span></label>
            <textarea name="description" class="field-textarea" placeholder="Deskripsi singkat produk..."><?= esc(old('description', $product['description'] ?? '')) ?></textarea>
          </div>
        </div>

        <!-- SECTION 2: Harga -->
        <div class="form-section">
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
            <div class="section-icon" style="background:#DCFCE7;">
              <svg style="width:14px;height:14px;color:#15803D;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 1v8m0 0v1"/></svg>
            </div>
            <div class="section-title">Harga</div>
          </div>
          <div class="section-desc">Harga beli dari supplier dan harga jual ke pelanggan</div>
          <div class="form-grid">
            <div class="form-group">
              <label class="field-label">Harga Beli (Rp) <span class="field-required">*</span></label>
              <div style="position:relative;">
                <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:12px;color:var(--muted);font-weight:600;">Rp</span>
                <input type="number" name="cost_price" value="<?= esc(old('cost_price', $product['cost_price'] ?? '')) ?>"
                  class="field-input" style="padding-left:36px;" placeholder="0" min="0" required/>
              </div>
            </div>
            <div class="form-group">
              <label class="field-label">Harga Jual (Rp) <span class="field-required">*</span></label>
              <div style="position:relative;">
                <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:12px;color:var(--muted);font-weight:600;">Rp</span>
                <input type="number" name="selling_price" value="<?= esc(old('selling_price', $product['selling_price'] ?? '')) ?>"
                  class="field-input" style="padding-left:36px;" placeholder="0" min="0" required/>
              </div>
            </div>
          </div>
          <!-- Profit indicator -->
          <div id="profitIndicator" style="margin-top:12px;padding:10px 14px;border-radius:10px;background:#F0FDF4;border:1px solid #BBF7D0;display:none;font-size:13px;color:#15803D;font-weight:600;">
            💰 Margin: <span id="profitAmount">Rp 0</span> (<span id="profitPct">0%</span>)
          </div>
        </div>

        <!-- SECTION 3: Stok -->
        <div class="form-section">
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
            <div class="section-icon" style="background:#DBEAFE;">
              <svg style="width:14px;height:14px;color:#2563EB;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div class="section-title">Stok & Inventori</div>
          </div>
          <div class="section-desc">Pengaturan stok dan reorder point</div>
          <div class="form-grid cols-3">
            <div class="form-group">
              <label class="field-label">Stok Awal <?= $product ? '<span class="field-hint">(readonly)</span>' : '' ?></label>
              <input type="number" name="stock" value="<?= esc(old('stock', $product['stock'] ?? 0)) ?>"
                class="field-input" placeholder="0" min="0" <?= $product ? 'readonly' : '' ?>/>
              <?php if ($product): ?>
              <p style="font-size:11px;color:var(--muted);margin-top:4px;">Ubah stok via menu Inventori</p>
              <?php endif; ?>
            </div>
            <div class="form-group">
              <label class="field-label">Stok Minimum</label>
              <input type="number" name="stock_minimum" value="<?= esc(old('stock_minimum', $product['stock_minimum'] ?? 5)) ?>"
                class="field-input" placeholder="5" min="0"/>
            </div>
            <div class="form-group">
              <label class="field-label">Lead Time <span class="field-hint">(hari)</span></label>
              <input type="number" name="lead_time_days" value="<?= esc(old('lead_time_days', $product['lead_time_days'] ?? 3)) ?>"
                class="field-input" placeholder="3" min="1"/>
            </div>
          </div>
        </div>

        <!-- SECTION 4: Gambar & Status -->
        <div class="form-section">
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
            <div class="section-icon" style="background:#F3E8FF;">
              <svg style="width:14px;height:14px;color:#7C3AED;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div class="section-title">Gambar & Status</div>
          </div>
          <div class="section-desc">Upload gambar produk dan atur status aktif</div>
          <div class="form-grid">
            <div class="form-group">
              <label class="field-label">Gambar Produk</label>
              <?php if (!empty($product['image'])): ?>
              <div style="margin-bottom:10px;">
                <img src="/toko-kayu-kontan-jaya/public/uploads/<?= $product['image'] ?>" class="img-preview" alt="Current"/>
                <p style="font-size:11px;color:var(--muted);">Gambar saat ini</p>
              </div>
              <?php endif; ?>
              <div class="img-upload-area" id="uploadArea">
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp" onchange="previewImage(this)"/>
                <div id="uploadPlaceholder">
                  <svg style="width:28px;height:28px;color:#C4B5A5;margin:0 auto 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                  <p style="font-size:13px;font-weight:500;color:var(--muted);">Klik atau drag gambar</p>
                  <p style="font-size:11px;color:#C4B5A5;margin-top:3px;">JPG, PNG, WebP — Maks 2MB</p>
                </div>
                <img id="imgPreview" style="display:none;max-height:120px;border-radius:8px;margin:0 auto;" alt="Preview"/>
              </div>
            </div>
            <div class="form-group">
              <label class="field-label">Status Produk</label>
              <select name="is_active" class="field-select">
                <option value="1" <?= old('is_active', $product['is_active'] ?? 1) == 1 ? 'selected' : '' ?>>✅ Aktif — Tersedia di POS</option>
                <option value="0" <?= old('is_active', $product['is_active'] ?? 1) == 0 ? 'selected' : '' ?>>⛔ Nonaktif — Disembunyikan</option>
              </select>
            </div>
          </div>
        </div>

        <!-- FORM ACTIONS -->
        <div class="form-actions">
          <button type="submit" class="btn-submit" onclick="addRipple(event,this)">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <?= $product ? 'Perbarui Produk' : 'Simpan Produk' ?>
          </button>
          <a href="<?= base_url('products') ?>" class="btn-cancel">
            <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            Batal
          </a>
        </div>

      </form>
    </div>
  </div>
</div>

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

// Image preview
function previewImage(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      const img = document.getElementById('imgPreview');
      const placeholder = document.getElementById('uploadPlaceholder');
      img.src = e.target.result;
      img.style.display = 'block';
      placeholder.style.display = 'none';
    };
    reader.readAsDataURL(input.files[0]);
  }
}

// Profit calculator
function calcProfit() {
  const buy = parseFloat(document.querySelector('[name=cost_price]').value) || 0;
  const sell = parseFloat(document.querySelector('[name=selling_price]').value) || 0;
  const indicator = document.getElementById('profitIndicator');
  if (buy > 0 && sell > 0) {
    const profit = sell - buy;
    const pct = ((profit / buy) * 100).toFixed(1);
    document.getElementById('profitAmount').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(profit);
    document.getElementById('profitPct').textContent = pct + '%';
    indicator.style.display = 'block';
    indicator.style.background = profit >= 0 ? '#F0FDF4' : '#FEF2F2';
    indicator.style.borderColor = profit >= 0 ? '#BBF7D0' : '#FECACA';
    indicator.style.color = profit >= 0 ? '#15803D' : '#B91C1C';
  } else {
    indicator.style.display = 'none';
  }
}
document.querySelector('[name=cost_price]').addEventListener('input', calcProfit);
document.querySelector('[name=selling_price]').addEventListener('input', calcProfit);
calcProfit();

// Ripple
function addRipple(e, btn) {
  const r = document.createElement('span');
  r.className = 'ripple';
  const rect = btn.getBoundingClientRect();
  const size = Math.max(rect.width, rect.height);
  r.style.cssText = `width:${size}px;height:${size}px;left:${e.clientX-rect.left-size/2}px;top:${e.clientY-rect.top-size/2}px;`;
  btn.appendChild(r);
  setTimeout(() => r.remove(), 600);
}
</script>
</body>
</html>