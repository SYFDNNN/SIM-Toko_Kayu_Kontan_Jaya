<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Kasir / POS — Toko Kayu Kontan Jaya</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    * { font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --wood: #7A4A2D; --wood-dk: #5C2D10; --wood-lt: #9B5E30;
      --gold: #F4C350; --bg: #F7F2EB; --surface: #FFFFFF;
      --border: #EDE5D8; --text: #1C1410; --muted: #9B8B77;
    }
    body { background: var(--bg); color: var(--text); overflow-x: hidden; }
    [x-cloak] { display: none !important; }

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
    #main { margin-left: 240px; transition: margin-left 250ms cubic-bezier(0.4,0,0.2,1); height: 100vh; display: flex; flex-direction: column; }
    #main.expanded { margin-left: 68px; }

    /* TOPBAR */
    #topbar { height: 58px; background: var(--surface); border-bottom: 1px solid var(--border); display: flex; align-items: center; padding: 0 24px; gap: 14px; flex-shrink: 0; box-shadow: 0 2px 16px rgba(0,0,0,0.04); z-index: 40; }
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

    /* POS LAYOUT */
    .pos-wrap {
  flex: 1;
  display: flex;
  overflow: hidden;
  min-height: 0;
}

    /* KIRI */
    .pos-left {
  flex: 1;
  display: flex;
  flex-direction: column;
  padding: 24px;
  overflow: hidden;
  background: var(--bg);
  min-height: 0;
}
.prod-grid {
  flex: 1;
  overflow-y: auto;
  min-height: 0;
  padding-right: 4px;
  padding-bottom: 16px;
}
    .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 20px; animation: headerIn 0.5s ease both; }
    @keyframes headerIn { from{opacity:0;transform:translateY(-12px)} to{opacity:1;transform:translateY(0)} }

    .search-wrap { position: relative; margin-bottom: 10px; }
    .search-input { width: 100%; padding: 10px 14px 10px 38px; border: 1.5px solid var(--border); border-radius: 12px; font-size: 13px; background: var(--surface); color: var(--text); outline: none; transition: all 200ms; font-family: inherit; box-shadow: 0 1px 4px rgba(0,0,0,0.04); }
    .search-input:focus { border-color: var(--wood); box-shadow: 0 0 0 3px rgba(122,74,45,0.08); }
    .search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--muted); pointer-events: none; }

    /* Search dropdown */
    .search-dropdown { position: absolute; z-index: 50; top: calc(100% + 6px); left: 0; right: 0; background: white; border: 1px solid var(--border); border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.1); max-height: 280px; overflow-y: auto; }
    .search-item { display: flex; align-items: center; gap: 12px; padding: 10px 14px; cursor: pointer; border-bottom: 1px solid #F5EFE5; transition: background 130ms; }
    .search-item:last-child { border: none; }
    .search-item:hover { background: #FAF6EF; }

    /* Shortcut keys */
    .shortcuts { display: flex; gap: 14px; margin-bottom: 16px; }
    .shortcut-key { font-size: 11px; color: var(--wood); display: flex; align-items: center; gap: 5px; }
    kbd { background: #FEF3C7; color: #92400E; padding: 2px 6px; border-radius: 5px; font-size: 10px; font-weight: 700; font-family: inherit; }

    /* Product grid */
    .prod-grid-inner {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 14px;
}
@media (min-width: 1024px) { .prod-grid-inner { grid-template-columns: repeat(3, 1fr); } }
@media (min-width: 1280px) { .prod-grid-inner { grid-template-columns: repeat(4, 1fr); } }

.prod-card {
  background: var(--surface);
  border: 2px solid transparent;
  border-radius: 16px;
  padding: 12px;
  cursor: pointer;
  transition: all 220ms cubic-bezier(0.34, 1.56, 0.64, 1);
  box-shadow: 0 1px 4px rgba(0,0,0,0.05);
  animation: cardIn 0.4s ease both;
  display: flex;
  flex-direction: column;
  position: relative;
  overflow: hidden;
  user-select: none;
}
.prod-card::after {
  content: '';
  position: absolute;
  inset: 0;
  background: rgba(255,255,255,0);
  transition: background 150ms;
  pointer-events: none;
}
.prod-card:hover {
  border-color: var(--wood-lt);
  box-shadow: 0 8px 24px rgba(122,74,45,0.15);
  transform: translateY(-4px) scale(1.01);
}
.prod-card:hover::after { background: rgba(255,255,255,0.05); }
.prod-card:active {
  transform: translateY(-1px) scale(0.98);
  box-shadow: 0 2px 8px rgba(122,74,45,0.1);
  transition: all 80ms ease;
}

@keyframes cardIn {
  from { opacity:0; transform:translateY(16px) scale(0.96); }
  to   { opacity:1; transform:translateY(0) scale(1); }
}

/* Gambar FIXED HEIGHT agar semua card sama */
.prod-card .img-wrap {
  width: 100%;
  height: 140px;
  border-radius: 10px;
  overflow: hidden;
  background: #F5EFE5;
  margin-bottom: 10px;
  flex-shrink: 0;
  position: relative;
}
.prod-card .img-wrap img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 300ms ease;
  display: block;
}
.prod-card:hover .img-wrap img { transform: scale(1.06); }

/* Badge "Tambah" muncul saat hover */
.prod-card .add-badge {
  position: absolute;
  top: 8px;
  right: 8px;
  width: 28px;
  height: 28px;
  background: var(--wood);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  font-weight: 700;
  opacity: 0;
  transform: scale(0.6);
  transition: all 200ms cubic-bezier(0.34, 1.56, 0.64, 1);
  box-shadow: 0 3px 10px rgba(122,74,45,0.3);
}
.prod-card:hover .add-badge {
  opacity: 1;
  transform: scale(1);
}

.prod-info { flex: 1; display: flex; flex-direction: column; }
.prod-name {
  font-weight: 700;
  font-size: 13px;
  color: var(--text);
  margin-bottom: 2px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-height: 1.4;
  min-height: 36px;
}
.prod-sku { font-size: 11px; color: var(--muted); margin-bottom: 8px; }
.prod-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: auto;
}
.prod-price { font-weight: 800; font-size: 13px; color: var(--wood); }
.stock-badge {
  font-size: 10px;
  font-weight: 700;
  padding: 3px 8px;
  border-radius: 20px;
  background: #DCFCE7;
  color: #15803D;
}
.stock-badge.low {
  background: #FEE2E2;
  color: #B91C1C;
  animation: stockPulse 2s ease-in-out infinite;
}
@keyframes stockPulse {
  0%,100% { opacity:1; }
  50%      { opacity:0.6; }
}

    /* KANAN: Cart */
    .pos-right { width: 360px; flex-shrink: 0; background: var(--surface); border-left: 1px solid var(--border); display: flex; flex-direction: column; box-shadow: -4px 0 16px rgba(0,0,0,0.04); }
    .cart-header { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
    .cart-title { font-size: 15px; font-weight: 700; color: var(--text); display: flex; align-items: center; gap: 8px; }
    .cart-count { background: var(--wood); color: white; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 20px; }
    .cart-body { flex: 1; overflow-y: auto; padding: 8px 16px; }
    .cart-empty { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px; color: var(--muted); }

    .cart-item { display: flex; align-items: flex-start; gap: 10px; padding: 12px 0; border-bottom: 1px solid #F5EFE5; }
    .cart-item:last-child { border: none; }
    .cart-item img { width: 44px; height: 44px; border-radius: 10px; object-fit: cover; background: #F5EFE5; flex-shrink: 0; border: 1px solid var(--border); }
    .cart-item-info { flex: 1; min-width: 0; }
    .cart-item-name { font-weight: 600; font-size: 13px; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 2px; }
    .cart-item-price { font-size: 11px; color: var(--muted); margin-bottom: 8px; }
    .qty-control { display: flex; align-items: center; gap: 8px; }
    .qty-btn { width: 28px; height: 28px; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; transition: all 150ms; background: #FEF3C7; color: var(--wood); }
    .qty-btn:hover { background: #FDE68A; }
    .qty-input { width: 48px; text-align: center; border: 1.5px solid var(--border); border-radius: 8px; padding: 4px; font-size: 13px; font-weight: 700; color: var(--text); outline: none; font-family: inherit; }
    .qty-input:focus { border-color: var(--wood); }
    .cart-item-total { text-align: right; flex-shrink: 0; }
    .cart-item-total-val { font-weight: 700; font-size: 13px; color: var(--wood); }
    .remove-btn { margin-top: 4px; color: #EF4444; background: none; border: none; cursor: pointer; padding: 0; transition: color 150ms; }
    .remove-btn:hover { color: #B91C1C; }

    /* Checkout panel */
    .checkout-panel { padding: 16px 20px; border-top: 1px solid var(--border); background: #FAF6EF; flex-shrink: 0; }
    .summary-row { display: flex; justify-content: space-between; font-size: 12px; color: var(--muted); margin-bottom: 4px; }
    .total-row { display: flex; justify-content: space-between; font-size: 16px; font-weight: 800; color: var(--text); margin-bottom: 14px; }
    .total-val { color: var(--wood); }
    .customer-input { width: 100%; padding: 8px 12px; border: 1.5px solid var(--border); border-radius: 10px; font-size: 13px; background: white; color: var(--text); outline: none; margin-bottom: 10px; font-family: inherit; transition: border-color 200ms; }
    .customer-input:focus { border-color: var(--wood); }
    .pay-methods { display: flex; gap: 8px; margin-bottom: 10px; }
    .pay-btn { flex: 1; padding: 8px; border-radius: 10px; font-size: 13px; font-weight: 600; border: 1.5px solid var(--border); background: white; cursor: pointer; transition: all 150ms; font-family: inherit; color: var(--muted); }
    .pay-btn.active { background: var(--wood); color: white; border-color: var(--wood); }
    .pay-btn:not(.active):hover { background: #FAF6EF; }
    .nominal-input { width: 100%; padding: 8px 12px; border: 1.5px solid var(--border); border-radius: 10px; font-size: 13px; background: white; color: var(--text); outline: none; margin-bottom: 6px; font-family: inherit; transition: border-color 200ms; }
    .nominal-input:focus { border-color: var(--wood); }
    .change-row { display: flex; justify-content: space-between; font-size: 13px; font-weight: 700; color: #15803D; margin-bottom: 10px; }
    .checkout-btn { width: 100%; padding: 12px; background: var(--wood); color: white; font-size: 14px; font-weight: 700; border-radius: 12px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 200ms; font-family: inherit; box-shadow: 0 4px 16px rgba(122,74,45,0.25); }
    .checkout-btn:hover:not(:disabled) { background: var(--wood-dk); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(122,74,45,0.35); }
    .checkout-btn:disabled { background: #D5CABC; cursor: not-allowed; box-shadow: none; }

    /* Modal */
    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 100; padding: 16px; backdrop-filter: blur(4px); }
    .modal-box { background: white; border-radius: 20px; box-shadow: 0 24px 64px rgba(0,0,0,0.2); max-width: 380px; width: 100%; padding: 32px; text-align: center; animation: modalIn 0.4s cubic-bezier(0.34,1.56,0.64,1); }
    @keyframes modalIn { from{opacity:0;transform:scale(0.85)} to{opacity:1;transform:scale(1)} }
    .modal-icon { width: 64px; height: 64px; background: #DCFCE7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }

    /* Toast */
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
      <a href="<?= base_url('logout') ?>" style="margin-left:auto;color:rgba(255,255,255,0.2);transition:all 200ms;" onmouseover="this.style.color='#EF4444';this.style.transform='translateX(2px)'" onmouseout="this.style.color='rgba(255,255,255,0.2)';this.style.transform=''">
        <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
        </svg>
      </a>
    </div>
  </div>
</aside>

<!-- MAIN -->
<div id="main">
  <!-- TOPBAR -->
  <header id="topbar">
    <div class="breadcrumb">Dashboard / <strong>Kasir</strong></div>
    <div class="tb-search">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
      </svg>
      <input placeholder="Cari produk, transaksi..." onkeypress="if(event.key==='Enter')location.href='<?= base_url('products') ?>?search='+this.value"/>
    </div>
    <div class="tb-actions">
      <?php if (in_array(session('user_role'), ['admin', 'cashier'])): ?>
      <a href="javascript:void(0)" onclick="newTransaction()" class="btn-new">
        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
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

  <!-- POS BODY -->
  <div class="pos-wrap" x-data="posApp()" x-init="init()">

    <!-- KIRI -->
    <div class="pos-left">
      <div class="page-header">
        <div>
          <h1 style="font-size:22px;font-weight:800;color:var(--text);letter-spacing:-0.02em;">Point of Sale</h1>
          <p style="font-size:13px;color:var(--muted);margin-top:3px;">Pilih produk atau cari berdasarkan nama / SKU</p>
        </div>
      </div>

      <!-- Search -->
      <div class="search-wrap">
        <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" x-model="searchQuery"
          @input.debounce.300ms="searchProducts()"
          @keydown.escape="searchResults = []"
          placeholder="Cari produk (nama/SKU)..."
          id="searchInput"
          class="search-input"/>
        <div x-show="isSearching" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);">
          <div style="width:16px;height:16px;border:2px solid #D5CABC;border-top-color:var(--wood);border-radius:50%;animation:spin 0.6s linear infinite;"></div>
        </div>
        <div x-show="searchResults.length > 0" x-cloak @click.outside="searchResults = []" class="search-dropdown">
          <template x-for="product in searchResults" :key="product.id">
            <div class="search-item" @click="addToCart(product); searchQuery = ''; searchResults = []">
              <img :src="product.image" style="width:40px;height:40px;border-radius:8px;object-fit:cover;background:#F5EFE5;flex-shrink:0;" :alt="product.name"/>
              <div style="flex:1;min-width:0;">
                <div style="font-weight:600;font-size:13px;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" x-text="product.name"></div>
                <div style="font-size:11px;color:var(--muted);" x-text="'SKU: ' + product.sku"></div>
              </div>
              <div style="text-align:right;flex-shrink:0;">
                <div style="font-weight:700;font-size:13px;color:var(--wood);" x-text="product.selling_price_formatted"></div>
                <div style="font-size:11px;color:var(--muted);" x-text="'Stok: ' + product.stock"></div>
              </div>
            </div>
          </template>
        </div>
      </div>

      <!-- Product Grid -->
      <div class="prod-grid">
        <p style="font-size:11px;color:var(--muted);font-weight:700;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:12px;">Produk Tersedia</p>
        <div class="prod-grid-inner">
<template x-for="(product, i) in quickProducts" :key="product.id">
  <div class="prod-card" @click="addToCart(product)" :style="`animation-delay:${i * 0.04}s`">
    <div class="img-wrap">
      <img :src="product.image" :alt="product.name"/>
      <div class="add-badge">+</div>
    </div>
    <div class="prod-info">
      <div class="prod-name" x-text="product.name"></div>
      <div class="prod-sku" x-text="product.sku"></div>
      <div class="prod-footer">
        <span class="prod-price" x-text="product.selling_price_formatted"></span>
        <span class="stock-badge" :class="product.stock <= 2 ? 'low' : ''" x-text="'Stok: ' + product.stock"></span>
      </div>
    </div>
  </div>
</template>        </div>
      </div>
    </div>

    <!-- KANAN: Cart -->
    <div class="pos-right">
      <div class="cart-header">
        <div class="cart-title">
          Keranjang
          <span class="cart-count" x-show="cart.length > 0" x-text="cart.length"></span>
        </div>
        <button x-show="cart.length > 0" @click="clearCart()"
          style="font-size:12px;font-weight:600;color:#EF4444;background:none;border:none;cursor:pointer;padding:4px 8px;border-radius:6px;transition:background 150ms;"
          onmouseover="this.style.background='#FEF2F2'" onmouseout="this.style.background='none'">
          Kosongkan
        </button>
      </div>

      <!-- Empty -->
      <div x-show="cart.length === 0" class="cart-empty">
        <svg style="width:56px;height:56px;color:#EDE5D8;margin-bottom:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <p style="font-size:13px;font-weight:500;">Keranjang kosong</p>
        <p style="font-size:12px;color:#C4B5A5;margin-top:4px;">Pilih produk untuk mulai</p>
      </div>

      <!-- Items -->
      <div class="cart-body" x-show="cart.length > 0">
        <template x-for="(item, index) in cart" :key="item.id">
          <div class="cart-item">
            <img :src="item.image" :alt="item.name"/>
            <div class="cart-item-info">
              <div class="cart-item-name" x-text="item.name"></div>
              <div class="cart-item-price" x-text="formatRupiah(item.unit_price)"></div>
              <div class="qty-control">
                <button class="qty-btn" @click="decreaseQty(index)">−</button>
                <input type="number" class="qty-input" x-model.number="item.quantity"
                  @change="updateQty(index, $event.target.value)" :max="item.stock" min="1"/>
                <button class="qty-btn" @click="increaseQty(index)">+</button>
              </div>
            </div>
            <div class="cart-item-total">
              <div class="cart-item-total-val" x-text="formatRupiah(item.unit_price * item.quantity)"></div>
              <button class="remove-btn" @click="removeFromCart(index)">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
              </button>
            </div>
          </div>
        </template>
      </div>

      <!-- Checkout -->
      <div class="checkout-panel" x-show="cart.length > 0">
        <div class="summary-row">
          <span>Subtotal (<span x-text="totalItems"></span> item)</span>
          <span x-text="formatRupiah(subtotal)"></span>
        </div>
        <div class="total-row">
          <span>Total</span>
          <span class="total-val" x-text="formatRupiah(subtotal)"></span>
        </div>
        <input type="text" class="customer-input" x-model="customerName" placeholder="Nama pelanggan (opsional)"/>
        <div class="pay-methods">
          <button class="pay-btn" :class="paymentMethod==='cash'?'active':''" @click="paymentMethod='cash'">Tunai</button>
          <button class="pay-btn" :class="paymentMethod==='transfer'?'active':''" @click="paymentMethod='transfer'">Transfer</button>
        </div>
        <div x-show="paymentMethod === 'cash'">
          <input type="number" class="nominal-input" x-model.number="paymentAmount" @input="calculateChange()" placeholder="Nominal pembayaran"/>
          <div class="change-row" x-show="change >= 0 && paymentAmount > 0">
            <span>Kembalian:</span>
            <span x-text="formatRupiah(change)"></span>
          </div>
        </div>
        <button class="checkout-btn" @click="checkout()" :disabled="isProcessing || cart.length === 0">
          <span x-show="!isProcessing">Checkout </span>
          <span x-show="isProcessing" style="display:flex;align-items:center;gap:8px;">
            <div style="width:16px;height:16px;border:2px solid rgba(255,255,255,0.4);border-top-color:white;border-radius:50%;animation:spin 0.6s linear infinite;"></div>
            Memproses...
          </span>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Sukses -->
<div x-data="posApp()" style="display:none;"></div>
<template x-if="false"><div></div></template>

<!-- Modal (global Alpine scope via event) -->
<div id="successModal" style="display:none;" class="modal-overlay">
  <div class="modal-box">
    <div class="modal-icon">
      <svg style="width:32px;height:32px;color:#16A34A;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
      </svg>
    </div>
    <h3 style="font-size:20px;font-weight:800;color:var(--text);margin-bottom:8px;">Transaksi Berhasil!</h3>
    <p style="font-size:13px;color:var(--muted);margin-bottom:4px;" id="modalTrxNo"></p>
    <p style="font-size:24px;font-weight:800;color:var(--wood);margin-bottom:24px;" id="modalTotal"></p>
    <div style="display:flex;gap:10px;">
      <button onclick="printInvoice()" style="flex:1;padding:12px;background:var(--wood);color:white;font-size:13px;font-weight:700;border-radius:10px;border:none;cursor:pointer;font-family:inherit;transition:background 150ms;" onmouseover="this.style.background='var(--wood-dk)'" onmouseout="this.style.background='var(--wood)'">Print Invoice</button>
      <button onclick="closeModal()" style="flex:1;padding:12px;background:#F5EFE5;color:var(--text);font-size:13px;font-weight:700;border-radius:10px;border:none;cursor:pointer;font-family:inherit;transition:background 150ms;" onmouseover="this.style.background='#EDE5D8'" onmouseout="this.style.background='#F5EFE5'">Transaksi Baru</button>
    </div>
  </div>
</div>

<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>

<script>
const BASE_URL = '<?= base_url() ?>';
let _invoiceUrl = '';
let _posApp = null;

function newTransaction() {
    if (_posApp) {
        _posApp.cart          = [];
        _posApp.customerName  = '';
        _posApp.paymentAmount = 0;
        _posApp.change        = 0;
        _posApp.searchQuery   = '';
        _posApp.searchResults = [];
        document.getElementById('searchInput').focus();
    } else {
        location.reload();
    }
}

function posApp() {
  return {
    searchQuery: '', searchResults: [], quickProducts: [], isSearching: false,
    cart: [], customerName: '', paymentMethod: 'cash', paymentAmount: 0,
    change: 0, isProcessing: false,

    async init() {
      _posApp = this;
      await this.loadQuickProducts();
      document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') { e.preventDefault(); document.getElementById('searchInput').focus(); }
        if (e.key === 'F8') { e.preventDefault(); if (this.cart.length > 0 && !this.isProcessing) this.checkout(); }
        if (e.key === 'Escape') this.searchResults = [];
      });
    },

    async loadQuickProducts() {
    try {
        const res = await fetch(BASE_URL + 'pos/products');
        if (res.ok) this.quickProducts = await res.json();
    } catch(e) { console.warn(e); }
},


    async searchProducts() {
      if (this.searchQuery.length < 2) { this.searchResults = []; return; }
      this.isSearching = true;
      try {
        const res = await fetch(BASE_URL + 'pos/search?q=' + encodeURIComponent(this.searchQuery));
        this.searchResults = await res.json();
      } catch(e) { console.error(e); } finally { this.isSearching = false; }
    },

    addToCart(product) {
      const existing = this.cart.find(i => i.id === product.id);
      if (existing) {
        if (existing.quantity < product.stock) existing.quantity++;
        else alert('Stok ' + product.name + ' tidak mencukupi');
      } else {
        this.cart.push({ id: product.id, name: product.name, sku: product.sku,
          unit_price: product.selling_price, unit: product.unit,
          stock: product.stock, image: product.image, quantity: 1 });
      }
    },

    increaseQty(i) { if (this.cart[i].quantity < this.cart[i].stock) this.cart[i].quantity++; },
    decreaseQty(i) { if (this.cart[i].quantity > 1) this.cart[i].quantity--; else this.removeFromCart(i); },
    updateQty(i, v) {
      const qty = parseInt(v);
      if (qty < 1) this.removeFromCart(i);
      else if (qty > this.cart[i].stock) this.cart[i].quantity = this.cart[i].stock;
      else this.cart[i].quantity = qty;
    },
    removeFromCart(i) { this.cart.splice(i, 1); },
    clearCart() { if (confirm('Kosongkan keranjang?')) this.cart = []; },

    get totalItems() { return this.cart.reduce((s, i) => s + i.quantity, 0); },
    get subtotal()   { return this.cart.reduce((s, i) => s + (i.unit_price * i.quantity), 0); },
    calculateChange() { this.change = Math.max(0, this.paymentAmount - this.subtotal); },

    async checkout() {
      if (this.cart.length === 0) return;
      if (this.paymentMethod === 'cash' && this.paymentAmount < this.subtotal) { alert('Pembayaran kurang!'); return; }
      this.isProcessing = true;
      try {
        const res = await fetch(BASE_URL + 'pos/checkout', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
          body: JSON.stringify({
            customer_name: this.customerName, payment_method: this.paymentMethod,
            payment_amount: this.paymentMethod === 'cash' ? this.paymentAmount : this.subtotal,
            cart: this.cart.map(i => ({ product_id: i.id, quantity: i.quantity, unit_price: i.unit_price }))
          })
        });
        const data = await res.json();
        if (data.success) {
          _invoiceUrl = data.invoice_url;
          document.getElementById('modalTrxNo').textContent = 'No: ' + data.transaction_no;
          document.getElementById('modalTotal').textContent = this.formatRupiah(this.subtotal);
          document.getElementById('successModal').style.display = 'flex';
          this.cart = []; this.customerName = ''; this.paymentAmount = 0; this.change = 0;
        } else { alert('Gagal: ' + data.message); }
      } catch(e) { alert('Error jaringan.'); } finally { this.isProcessing = false; }
    },

    formatRupiah(v) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(v); }
  };
}

function printInvoice() {
  if (_invoiceUrl) window.open(_invoiceUrl, '_blank');
  closeModal();
}
function closeModal() {
  document.getElementById('successModal').style.display = 'none';
  document.getElementById('searchInput').focus();
}

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
</script>
</body>
</html>