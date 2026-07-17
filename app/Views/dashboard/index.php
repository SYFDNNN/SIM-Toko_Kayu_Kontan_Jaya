<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard — Toko Kayu Kontan Jaya</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    * { font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --wood: #7A4A2D;
      --wood-dk: #5C2D10;
      --wood-lt: #9B5E30;
      --gold: #F4C350;
      --bg: #F7F2EB;
      --surface: #FFFFFF;
      --border: #EDE5D8;
      --text: #1C1410;
      --muted: #9B8B77;
    }
    body { background: var(--bg); color: var(--text); overflow-x: hidden; }

    #sidebar {
      width: 240px;
      flex-shrink: 0;
      background: linear-gradient(180deg, #1E0B02 0%, #3A1608 40%, #5C2D10 100%);
      height: 100vh;
      position: fixed;
      left: 0;
      top: 0;
      z-index: 50;
      display: flex;
      flex-direction: column;
      transition: width 250ms cubic-bezier(0.4,0,0.2,1);
      box-shadow: 4px 0 40px rgba(0,0,0,0.25);
    }
    #sidebar.collapsed { width: 68px; }
    #sidebar.collapsed .nav-label,
    #sidebar.collapsed .brand-name,
    #sidebar.collapsed .user-info {
      opacity: 0;
      width: 0;
      overflow: hidden;
      white-space: nowrap;
    }
    #sidebar.collapsed .nav-link { justify-content: center; padding: 10px 0; }
    #sidebar.collapsed .section-label { display: none; }
    #sidebar::before {
      content: '';
      position: absolute;
      inset: 0;
      pointer-events: none;
      z-index: 0;
      background: repeating-linear-gradient(88deg, transparent, transparent 4px, rgba(255,255,255,0.02) 4px, rgba(255,255,255,0.02) 6px);
      animation: grainMove 12s linear infinite;
    }
    @keyframes grainMove { from{background-position:0 0} to{background-position:0 200px} }

    .brand-wrap {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 18px 14px 14px;
      border-bottom: 1px solid rgba(255,255,255,0.06);
      position: relative;
      z-index: 1;
    }
    .brand-logo {
      width: 34px;
      height: 34px;
      border-radius: 10px;
      background: rgba(244,195,80,0.2);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      animation: logoPulse 3s ease-in-out infinite;
    }
    @keyframes logoPulse { 0%,100%{box-shadow:0 0 0 0 rgba(244,195,80,0)} 50%{box-shadow:0 0 0 6px rgba(244,195,80,0.12)} }
    .brand-name b { font-size: 13px; font-weight: 700; color: white; display: block; }
    .brand-name span { font-size: 10px; color: rgba(255,255,255,0.3); }
    .toggle-btn {
      margin-left: auto;
      background: none;
      border: none;
      cursor: pointer;
      color: rgba(255,255,255,0.25);
      padding: 4px;
      border-radius: 6px;
      transition: all 150ms;
      position: relative;
      z-index: 1;
    }
    .toggle-btn:hover { background: rgba(255,255,255,0.08); color: white; }

    .sb-nav {
      flex: 1;
      padding: 10px 8px;
      overflow-y: auto;
      position: relative;
      z-index: 1;
    }
    .section-label {
      font-size: 9px;
      font-weight: 700;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      color: rgba(255,255,255,0.2);
      padding: 10px 10px 4px;
    }
    .nav-link {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 9px 10px;
      border-radius: 9px;
      color: rgba(255,255,255,0.45);
      font-size: 13px;
      font-weight: 500;
      cursor: pointer;
      transition: all 180ms ease;
      margin-bottom: 1px;
      text-decoration: none;
      position: relative;
      overflow: hidden;
    }
    .nav-link::after {
      content: '';
      position: absolute;
      inset: 0;
      background: rgba(255,255,255,0);
      transition: background 150ms;
    }
    .nav-link:hover { color: rgba(255,255,255,0.9); transform: translateX(3px); }
    .nav-link:hover::after { background: rgba(255,255,255,0.05); }
    .nav-link.active {
      background: rgba(255,255,255,0.1);
      color: white;
      box-shadow: inset 0 0 0 1px rgba(255,255,255,0.08);
    }
    .nav-link.active::before {
      content: '';
      position: absolute;
      left: 0;
      top: 20%;
      height: 60%;
      width: 3px;
      background: var(--gold);
      border-radius: 0 3px 3px 0;
      animation: activeBar 0.3s ease;
    }
    @keyframes activeBar { from{transform:scaleY(0)} to{transform:scaleY(1)} }
    .nav-icon { width: 17px; height: 17px; flex-shrink: 0; transition: transform 200ms; }
    .nav-link:hover .nav-icon { transform: scale(1.15); }

    .sb-footer {
      padding: 10px 8px 16px;
      border-top: 1px solid rgba(255,255,255,0.06);
      position: relative;
      z-index: 1;
    }
    .user-row {
      display: flex;
      align-items: center;
      gap: 9px;
      padding: 8px 10px;
      border-radius: 9px;
      cursor: pointer;
      transition: background 150ms;
    }
    .user-row:hover { background: rgba(255,255,255,0.05); }
    .user-avatar {
      width: 30px;
      height: 30px;
      border-radius: 9px;
      background: rgba(244,195,80,0.2);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      font-weight: 700;
      color: var(--gold);
      flex-shrink: 0;
    }
    .user-info .uname { font-size: 12px; font-weight: 600; color: white; white-space: nowrap; }
    .user-info .urole { font-size: 10px; color: rgba(255,255,255,0.28); text-transform: capitalize; }

    #main {
      margin-left: 240px;
      transition: margin-left 250ms cubic-bezier(0.4,0,0.2,1);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    #main.expanded { margin-left: 68px; }

    #topbar {
      height: 58px;
      background: var(--surface);
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      padding: 0 24px;
      gap: 14px;
      position: sticky;
      top: 0;
      z-index: 40;
      box-shadow: 0 2px 16px rgba(0,0,0,0.04);
    }
    .breadcrumb { font-size: 13px; color: var(--muted); }
    .breadcrumb strong { color: var(--text); font-weight: 600; }
    .tb-search { position: relative; }
    .tb-search input {
      width: 240px;
      padding: 7px 14px 7px 34px;
      border: 1.5px solid var(--border);
      border-radius: 10px;
      background: #FAF6EF;
      font-size: 13px;
      color: var(--text);
      transition: all 200ms;
      outline: none;
    }
    .tb-search input:focus {
      border-color: var(--wood);
      width: 280px;
      background: white;
      box-shadow: 0 0 0 3px rgba(122,74,45,0.08);
    }
    .tb-search svg {
      position: absolute;
      left: 10px;
      top: 50%;
      transform: translateY(-50%);
      width: 15px;
      height: 15px;
      color: var(--muted);
    }
    .tb-actions { margin-left: auto; display: flex; align-items: center; gap: 10px; }
    .btn-new {
      background: var(--wood);
      color: white;
      font-size: 13px;
      font-weight: 600;
      padding: 7px 16px;
      border-radius: 9px;
      border: none;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 6px;
      transition: all 200ms;
      text-decoration: none;
    }
    .btn-new:hover { background: var(--wood-dk); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(122,74,45,0.35); }
    .icon-btn {
      width: 34px;
      height: 34px;
      border-radius: 9px;
      border: 1.5px solid var(--border);
      background: white;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      position: relative;
      transition: all 180ms;
      color: var(--muted);
      text-decoration: none;
    }
    .icon-btn:hover { background: #FAF6EF; border-color: #D5CABC; color: var(--wood); transform: translateY(-1px); }
    .notif-dot {
      position: absolute;
      top: 4px;
      right: 4px;
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: #EF4444;
      border: 2px solid white;
      animation: ping 1.5s ease-in-out infinite;
    }
    @keyframes ping { 0%,100%{transform:scale(1);opacity:1} 50%{transform:scale(1.4);opacity:0.7} }
    .tb-avatar {
      width: 32px;
      height: 32px;
      border-radius: 9px;
      background: var(--wood);
      color: white;
      font-size: 12px;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: transform 150ms;
    }
    .tb-avatar:hover { transform: scale(1.08); }

    .content { flex: 1; padding: 28px 24px; }

    .metrics-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 14px; }
    .profit-grid  { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; margin-bottom: 24px; }

    .metric-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 20px;
      position: relative;
      overflow: hidden;
      transition: all 220ms cubic-bezier(0.34,1.56,0.64,1);
      animation: cardSlideUp 0.6s cubic-bezier(0.34,1.56,0.64,1) both;
    }
    .metric-card:hover { transform: translateY(-5px) scale(1.01); box-shadow: 0 16px 40px rgba(0,0,0,0.12); }
    .metric-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(90deg, #5C2D10 0%, #8B5E34 28%, #C58A52 50%, #8B5E34 72%, #5C2D10 100%);
      background-size: 220% 100%;
      animation: brownGlow 3.2s linear infinite;
      box-shadow: 0 0 12px rgba(197, 138, 82, 0.35);
    }
    .metric-card::after {
      content: '';
      position: absolute;
      inset: -30% -60%;
      background: linear-gradient(120deg, transparent 38%, rgba(255,255,255,0.22) 50%, transparent 62%);
      transform: translateX(-30%) rotate(8deg);
      animation: cardShine 4.8s linear infinite;
      pointer-events: none;
      mix-blend-mode: screen;
      opacity: 0.55;
    }
    .metric-card:hover::after { opacity: 0.8; }
    .mc-brown .mc-icon {
      background: linear-gradient(145deg, rgba(122,74,45,0.16), rgba(197,138,82,0.18)) !important;
      box-shadow: inset 0 0 0 1px rgba(122,74,45,0.08);
    }
    .mc-brown .mc-value { color: var(--wood-dk); }
    .mc-brown .mc-label { color: #8F7057; }
    .mc-brown .mc-sub, .mc-brown .mc-sub a { color: #9B8B77; }
    .mc-brown .mc-sub a:hover { color: var(--wood); }
    .metric-card:nth-child(1) { animation-delay: 0.05s; }
    .metric-card:nth-child(2) { animation-delay: 0.12s; }
    .metric-card:nth-child(3) { animation-delay: 0.19s; }
    .metric-card:nth-child(4) { animation-delay: 0.26s; }
    @keyframes cardSlideUp { from{opacity:0;transform:translateY(28px) scale(0.96)} to{opacity:1;transform:translateY(0) scale(1)} }
    @keyframes brownGlow { 0%{background-position:0% 50%} 100%{background-position:220% 50%} }
    @keyframes cardShine { 0%{transform:translateX(-55%) rotate(8deg)} 100%{transform:translateX(55%) rotate(8deg)} }

    .mc-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
    .mc-label { font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.06em; }
    .mc-icon {
      width: 36px;
      height: 36px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: transform 200ms;
    }
    .metric-card:hover .mc-icon { transform: rotate(10deg) scale(1.1); }
    .mc-value { font-size: 22px; font-weight: 800; color: var(--text); margin-bottom: 6px; }
    .mc-sub { font-size: 12px; color: var(--muted); }

    .trend-preset {
      font-size: 11px;
      font-weight: 700;
      padding: 4px 10px;
      border-radius: 7px;
      border: 1.5px solid var(--border);
      background: white;
      color: var(--muted);
      cursor: pointer;
      transition: all 150ms;
      font-family: inherit;
    }
    .trend-preset:hover { background: #FAF6EF; border-color: var(--wood); color: var(--wood); }
    .trend-preset.active { background: var(--wood); color: white; border-color: var(--wood); }
    .trend-date-input {
      padding: 5px 10px;
      border: 1.5px solid var(--border);
      border-radius: 8px;
      font-size: 12px;
      color: var(--text);
      background: #FAF6EF;
      outline: none;
      transition: all 200ms;
      font-family: inherit;
    }
    .trend-date-input:focus { border-color: var(--wood); background: white; box-shadow: 0 0 0 3px rgba(122,74,45,0.08); }
    .trend-apply-btn {
      font-size: 12px;
      font-weight: 600;
      padding: 5px 12px;
      border-radius: 8px;
      background: var(--wood);
      color: white;
      border: none;
      cursor: pointer;
      transition: all 150ms;
      font-family: inherit;
    }
    .trend-apply-btn:hover { background: var(--wood-dk); }

    .charts-grid {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 14px;
      margin-bottom: 24px;
      align-items: stretch;
    }
    .chart-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 22px;
      animation: cardSlideUp 0.6s 0.3s cubic-bezier(0.34,1.56,0.64,1) both;
      transition: box-shadow 200ms;
    }
    .chart-card:hover { box-shadow: 0 8px 32px rgba(0,0,0,0.08); }
    .chart-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; }
    .chart-title { font-size: 14px; font-weight: 700; color: var(--text); }
    .chart-pill { font-size: 11px; color: var(--muted); background: #F5EFE5; border-radius: 20px; padding: 4px 12px; font-weight: 500; white-space: nowrap; }
    .chart-card-main { position: relative; overflow: hidden; }
    .chart-card-main::before {
      content: '';
      position: absolute;
      inset: 0;
      z-index: 0;
      pointer-events: none;
      background-image: radial-gradient(circle, rgba(122,74,45,0.04) 1px, transparent 1px);
      background-size: 20px 20px;
      animation: dotFloat 8s ease-in-out infinite alternate;
    }
    @keyframes dotFloat { from{background-position:0 0} to{background-position:10px 10px} }

    .chart-right-col {
      display: flex;
      flex-direction: column;
      gap: 14px;
      height: 100%;
      align-self: stretch;
    }

    .top-products-wrap {
      display: flex;
      align-items: stretch;
      position: relative;
      min-height: 180px;
    }
    .top-product-labels {
      width: 126px;
      flex-shrink: 0;
      position: relative;
      min-height: 180px;
    }
    .top-products-canvas { flex: 1; min-width: 0; }
    .top-product-label {
      position: absolute;
      left: 0;
      right: 4px;
      height: 20px;
      overflow: hidden;
      font-size: 11px;
      color: #9B8B77;
      display: flex;
      align-items: center;
    }
    .top-product-label:not(.is-marquee) .marquee-inner {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      display: block;
      width: 100%;
    }
    .top-product-label.is-marquee { cursor: pointer; }
    .top-product-label.is-marquee:not(:hover) .marquee-inner {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      display: block;
      width: 100%;
    }
    .top-product-label.is-marquee:hover .marquee-inner {
      display: inline-block;
      white-space: nowrap;
      animation: topProductMarquee var(--marquee-duration, 10s) linear infinite;
    }
    @keyframes topProductMarquee {
      0%,15%{transform:translateX(0)}
      85%,100%{transform:translateX(var(--marquee-distance, -100px))}
    }

    .payment-card {
      display: flex;
      flex-direction: column;
      flex: 1;
      min-height: 0;
    }
    .payment-card-body {
      display: grid;
      grid-template-columns: 112px 1fr;
      gap: 16px;
      align-items: center;
      flex: 1;
      min-height: 0;
    }
    .payment-chart-box {
      width: 112px;
      height: 112px;
      flex-shrink: 0;
      margin: 0 auto;
      position: relative;
    }
    .payment-chart-box canvas {
      width: 100% !important;
      height: 100% !important;
    }
    .payment-legend {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }
    .payment-item {
      border: 1px solid var(--border);
      border-radius: 14px;
      padding: 10px 12px;
      background: #FFFDF9;
    }
    .payment-item-top {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      margin-bottom: 8px;
    }
    .payment-name {
      display: flex;
      align-items: center;
      gap: 8px;
      min-width: 0;
    }
    .payment-dot {
      width: 10px;
      height: 10px;
      border-radius: 3px;
      flex-shrink: 0;
    }
    .payment-title {
      font-size: 12px;
      font-weight: 700;
      color: var(--text);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .payment-pct {
      font-size: 12px;
      font-weight: 800;
    }
    .payment-meta {
      display: flex;
      justify-content: space-between;
      gap: 8px;
      font-size: 11px;
      color: var(--muted);
      margin-bottom: 8px;
    }
    .payment-bar-track {
      height: 4px;
      background: var(--border);
      border-radius: 999px;
      overflow: hidden;
    }
    .payment-bar-fill {
      height: 100%;
      border-radius: 999px;
      transition: width 800ms cubic-bezier(0.34,1.56,0.64,1);
    }
    .payment-summary {
      margin-top: 10px;
      padding-top: 10px;
      border-top: 1px dashed var(--border);
      display: flex;
      justify-content: space-between;
      gap: 10px;
      font-size: 11px;
      color: var(--muted);
    }

    .table-card { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; overflow: hidden; animation: cardSlideUp 0.6s 0.4s cubic-bezier(0.34,1.56,0.64,1) both; }
    .table-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--border); }
    .table-title { font-size: 14px; font-weight: 700; color: var(--text); }
    table.txn { width: 100%; border-collapse: collapse; font-size: 13px; }
    table.txn thead th { padding: 10px 20px; background: #FAF6EF; text-align: left; font-size: 10px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.07em; border-bottom: 1px solid var(--border); }
    table.txn tbody tr { border-bottom: 1px solid #F5EFE5; transition: all 150ms; }
    table.txn tbody tr:last-child { border: none; }
    table.txn tbody tr:hover { background: #FAF6EF; }
    table.txn td { padding: 12px 20px; color: var(--text); vertical-align: middle; }
    .txn-no { font-family: monospace; font-size: 11px; color: var(--wood); font-weight: 600; text-decoration: none; }
    .txn-no:hover { text-decoration: underline; }
    .status-pill { display: inline-flex; font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 20px; }
    .sp-done { background: #DCFCE7; color: #15803D; }
    .method-cell { text-align: center; vertical-align: middle; }
    .method-cell-inner {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      min-height: 100%;
    }
    .method-pill {
      min-width: 88px;
      height: 30px;
      padding: 0 16px;
      border-radius: 999px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      box-sizing: border-box;
      white-space: nowrap;
      line-height: 1;
      background: linear-gradient(180deg, #efe2d4, #e6d2be);
      color: #6d3f22;
      font-size: 11px;
      font-weight: 600;
      border: 1px solid rgba(122,74,45,0.10);
      box-shadow: inset 0 1px 0 rgba(255,255,255,0.68);
      position: relative;
      overflow: hidden;
    }
    .method-pill::before {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(120deg, transparent 35%, rgba(255,255,255,0.35) 50%, transparent 65%);
      transform: translateX(-120%);
      animation: methodShine 4.8s linear infinite;
      pointer-events: none;
      mix-blend-mode: screen;
      opacity: 0.7;
    }
    @keyframes methodShine { 0%{transform:translateX(-120%)} 100%{transform:translateX(120%)} }
    .txn-row { animation: rowIn 0.4s ease both; }
    @keyframes rowIn { from{opacity:0;transform:translateX(-12px)} to{opacity:1;transform:translateX(0)} }

    .skeleton {
      background: linear-gradient(90deg,#F0EAE0 25%,#E5DDD3 50%,#F0EAE0 75%);
      background-size:200% 100%;
      animation:shimmer 1.4s infinite;
      border-radius:8px;
      display:inline-block;
    }
    @keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

    .toast {
      position: fixed;
      bottom: 24px;
      right: 24px;
      z-index: 9999;
      background: #1C1410;
      color: white;
      padding: 13px 18px;
      border-radius: 12px;
      font-size: 13px;
      font-weight: 500;
      box-shadow: 0 8px 32px rgba(0,0,0,0.3);
      display: flex;
      align-items: center;
      gap: 10px;
      animation: toastIn 0.4s cubic-bezier(0.34,1.56,0.64,1);
    }
    .toast.success { border-left: 4px solid #22C55E; }
    @keyframes toastIn { from{transform:translateY(24px) scale(0.9);opacity:0} to{transform:translateY(0) scale(1);opacity:1} }

    .particle-canvas { position: fixed; inset: 0; pointer-events: none; z-index: 0; overflow: hidden; }
    .particle { position: absolute; border-radius: 50%; opacity: 0; animation: floatUp linear infinite; }
@keyframes floatUp { 0%{transform:translateY(100vh);opacity:0} 5%{opacity:1} 95%{opacity:0.3} 100%{transform:translateY(-10vh);opacity:0} }



    .page-header { animation: headerIn 0.5s ease both; }
    @keyframes headerIn { from{opacity:0;transform:translateY(-12px)} to{opacity:1;transform:translateY(0)} }

    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #D5CABC; border-radius: 3px; }

    @media (max-width: 1280px) {
      .metrics-grid { grid-template-columns: repeat(2, 1fr); }
      .profit-grid { grid-template-columns: 1fr; }
      .charts-grid { grid-template-columns: 1fr; }
      .payment-card-body { grid-template-columns: 1fr; }
      .payment-chart-box { width: 132px; height: 132px; }
    }
    @media (max-width: 768px) {
      #sidebar { transform: translateX(-100%); }
      #main { margin-left: 0; }
      .content { padding: 18px 14px; }
      #topbar { padding: 0 14px; }
      .tb-search input { width: 170px; }
      .tb-search input:focus { width: 200px; }
      .metrics-grid { grid-template-columns: 1fr; }
      .payment-card-body { grid-template-columns: 1fr; }
      .payment-chart-box { width: 132px; height: 132px; }
      .top-products-wrap { flex-direction: column; }
      .top-product-labels { width: 100%; min-height: 0; height: auto !important; }
      .top-products-canvas { width: 100%; }
    }
  </style>

<style>
  :root {
    --wood: #7A4A2D;
    --wood-dk: #5C2D10;
    --wood-lt: #A86A3A;
    --gold: #F2C86D;
    --bg: #F4EDE3;
    --surface: rgba(255, 250, 244, 0.94);
    --border: rgba(122, 74, 45, 0.14);
    --text: #201712;
    --muted: #8A725C;
  }

  body {
    background:
      radial-gradient(circle at top left, rgba(242, 200, 109, 0.18), transparent 28%),
      radial-gradient(circle at top right, rgba(122, 74, 45, 0.10), transparent 26%),
      linear-gradient(180deg, #faf4ea 0%, #f4ede3 48%, #efe4d7 100%);
    color: var(--text);
    overflow-x: hidden;
    position: relative;
  }

  body::before {
    content: '';
    position: fixed;
    inset: 0;
    pointer-events: none;
    background:
      radial-gradient(circle at 20% 12%, rgba(255,255,255,0.65), transparent 24%),
      radial-gradient(circle at 80% 8%, rgba(255,255,255,0.35), transparent 18%),
      linear-gradient(120deg, transparent 0%, rgba(255,255,255,0.04) 50%, transparent 100%);
    mix-blend-mode: screen;
    opacity: .55;
    z-index: 0;
  }

  #sidebar {
    width: 240px;
    flex-shrink: 0;
    background:
      linear-gradient(180deg, rgba(22, 10, 5, 0.98) 0%, rgba(49, 21, 9, 0.98) 40%, rgba(103, 55, 26, 0.98) 100%);
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    z-index: 50;
    display: flex;
    flex-direction: column;
    transition: width 250ms cubic-bezier(0.4,0,0.2,1);
    box-shadow: 8px 0 42px rgba(0,0,0,0.26);
    border-right: 1px solid rgba(255,255,255,0.06);
  }
  #sidebar::after {
    content: '';
    position: absolute;
    inset: 0;
    pointer-events: none;
    background:
      radial-gradient(circle at 22% 0%, rgba(242,200,109,0.22), transparent 18%),
      linear-gradient(180deg, rgba(255,255,255,0.04), transparent 18%, transparent 82%, rgba(0,0,0,0.18));
  }
  #sidebar .brand-wrap,
  #sidebar .sb-nav,
  #sidebar .sb-footer { position: relative; z-index: 1; }
  #sidebar .brand-wrap { border-bottom: 1px solid rgba(255,255,255,0.07); }
  #sidebar .nav-link.active {
    background: linear-gradient(90deg, rgba(255,255,255,0.12), rgba(255,255,255,0.04));
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.09), 0 10px 26px rgba(0,0,0,0.16);
  }
  #sidebar .nav-link.active::before {
    width: 4px;
    background: linear-gradient(180deg, #f5d88a, #b97b49);
    box-shadow: 0 0 12px rgba(242,200,109,0.35);
  }
  .brand-logo {
    background: radial-gradient(circle at 30% 30%, rgba(242,200,109,0.32), rgba(122,74,45,0.28));
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.08), 0 8px 24px rgba(0,0,0,0.18);
  }
  .brand-name b { letter-spacing: 0.01em; }
  .toggle-btn:hover { background: rgba(255,255,255,0.10); color: white; box-shadow: 0 0 0 1px rgba(255,255,255,0.07) inset; }

  #main {
    margin-left: 240px;
    transition: margin-left 250ms cubic-bezier(0.4,0,0.2,1);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    position: relative;
    z-index: 1;
  }

  #topbar {
    height: 62px;
    background: rgba(255, 250, 244, 0.88);
    backdrop-filter: blur(14px) saturate(1.2);
    -webkit-backdrop-filter: blur(14px) saturate(1.2);
    border-bottom: 1px solid rgba(122,74,45,0.08);
    box-shadow: 0 10px 30px rgba(64, 37, 18, 0.06);
  }
  .breadcrumb strong { color: var(--wood-dk); }
  .tb-search input {
    background: rgba(255,255,255,0.8);
    border-color: rgba(122,74,45,0.14);
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.7);
  }
  .tb-search input:focus {
    box-shadow: 0 0 0 4px rgba(122,74,45,0.10), inset 0 1px 0 rgba(255,255,255,0.85);
  }
  .btn-new {
    background: linear-gradient(180deg, #9a6137 0%, #7a4a2d 100%);
    box-shadow: 0 10px 22px rgba(122,74,45,0.24), inset 0 1px 0 rgba(255,255,255,0.16);
    position: relative;
    overflow: hidden;
  }
  .btn-new::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(120deg, transparent 0%, rgba(255,255,255,0.22) 45%, transparent 55%);
    transform: translateX(-120%);
    transition: transform 0s;
  }
  .btn-new:hover::before { transform: translateX(120%); transition: transform 0.8s ease; }
  .btn-new:hover { background: linear-gradient(180deg, #8a532e 0%, #62361c 100%); box-shadow: 0 14px 28px rgba(122,74,45,0.30); }
  .icon-btn, .tb-avatar {
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.7), 0 8px 18px rgba(64,37,18,0.06);
  }
  .icon-btn:hover {
    background: rgba(249,243,236,0.95);
    border-color: rgba(122,74,45,0.20);
    box-shadow: 0 10px 20px rgba(122,74,45,0.08);
  }
  .tb-avatar {
    background: linear-gradient(180deg, #8a532e 0%, #6a3e22 100%);
  }

  .content { flex: 1; padding: 28px 24px; }

  .metric-card,
  .chart-card,
  .table-card {
    background: linear-gradient(180deg, rgba(255,255,255,0.90) 0%, rgba(250,245,238,0.92) 100%);
    border: 1px solid rgba(122,74,45,0.12);
    border-radius: 20px;
    box-shadow:
      0 16px 38px rgba(74, 44, 24, 0.08),
      inset 0 1px 0 rgba(255,255,255,0.80);
    position: relative;
    overflow: hidden;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
  }

  .metric-card {
    padding: 20px;
    transition: transform 260ms cubic-bezier(0.34,1.56,0.64,1), box-shadow 260ms ease, border-color 260ms ease;
    animation: cardSlideUp 0.6s cubic-bezier(0.34,1.56,0.64,1) both;
    transform-style: preserve-3d;
  }
  .metric-card:hover {
    transform: translateY(-8px) scale(1.015) perspective(1000px) rotateX(1deg);
    box-shadow: 0 22px 48px rgba(74, 44, 24, 0.16), inset 0 1px 0 rgba(255,255,255,0.92);
    border-color: rgba(122,74,45,0.18);
  }
  .metric-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #5C2D10 0%, #8B5E34 24%, #C99558 50%, #8B5E34 76%, #5C2D10 100%);
    background-size: 250% 100%;
    animation: luxuryTopBar 4.2s linear infinite;
    box-shadow: 0 0 16px rgba(197, 138, 82, 0.38);
  }
  .metric-card::after {
    content: '';
    position: absolute;
    inset: -40% -55%;
    background: linear-gradient(118deg, transparent 39%, rgba(255,255,255,0.30) 50%, transparent 61%);
    transform: translateX(-36%) rotate(9deg);
    animation: luxuryShine 5.8s linear infinite;
    pointer-events: none;
    mix-blend-mode: screen;
    opacity: 0.7;
  }
  .metric-card:hover::after { opacity: 0.95; }
  .mc-brown .mc-icon {
    background: linear-gradient(145deg, rgba(122,74,45,0.16), rgba(242,200,109,0.18)) !important;
    box-shadow: inset 0 0 0 1px rgba(122,74,45,0.10), 0 8px 20px rgba(122,74,45,0.10);
  }
  .mc-brown .mc-icon svg { color: #8A532E !important; }
  .mc-brown .mc-label {
    color: #9a7b5c;
    letter-spacing: 0.08em;
  }
  .mc-brown .mc-value {
    color: #4d2d1a;
    text-shadow: 0 1px 0 rgba(255,255,255,0.6);
  }
  .mc-brown .mc-sub,
  .mc-brown .mc-sub a {
    color: #8a725c;
  }
  .mc-brown .mc-sub a { transition: color 160ms ease; }
  .mc-brown .mc-sub a:hover { color: #5c2d10; }

  .metric-card:nth-child(1) { animation-delay: 0.05s; }
  .metric-card:nth-child(2) { animation-delay: 0.12s; }
  .metric-card:nth-child(3) { animation-delay: 0.19s; }
  .metric-card:nth-child(4) { animation-delay: 0.26s; }
  @keyframes cardSlideUp { from{opacity:0;transform:translateY(28px) scale(0.96)} to{opacity:1;transform:translateY(0) scale(1)} }
  @keyframes luxuryTopBar { 0%{background-position:0% 50%} 100%{background-position:250% 50%} }
  @keyframes luxuryShine { 0%{transform:translateX(-56%) rotate(9deg)} 100%{transform:translateX(56%) rotate(9deg)} }

  .mc-header { margin-bottom: 16px; }
  .mc-label { text-shadow: 0 1px 0 rgba(255,255,255,0.6); }
  .mc-icon {
    width: 40px;
    height: 40px;
    border-radius: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 220ms ease, box-shadow 220ms ease;
  }
  .metric-card:hover .mc-icon {
    transform: translateY(-2px) rotate(8deg) scale(1.08);
    box-shadow: inset 0 0 0 1px rgba(122,74,45,0.12), 0 10px 24px rgba(122,74,45,0.12);
  }
  .mc-value { font-size: 24px; letter-spacing: -0.03em; }

  .chart-card {
    padding: 24px;
    transition: transform 240ms ease, box-shadow 240ms ease;
  }
  .chart-card:hover { transform: translateY(-3px); box-shadow: 0 18px 42px rgba(74,44,24,0.10), inset 0 1px 0 rgba(255,255,255,0.86); }
  .chart-header { margin-bottom: 18px; }
  .chart-title { font-size: 15px; letter-spacing: -0.01em; }
  .chart-pill {
    background: linear-gradient(180deg, #fff8ef 0%, #f4e8da 100%);
    color: #7a4a2d;
    border: 1px solid rgba(122,74,45,0.10);
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.82);
  }
  .chart-card-main::before {
    background-image: radial-gradient(circle, rgba(122,74,45,0.05) 1px, transparent 1px);
    background-size: 22px 22px;
  }

  .top-product-label { color: #8f7057; }

  .payment-item {
    border: 1px solid rgba(122,74,45,0.10);
    border-radius: 14px;
    padding: 11px 12px;
    background: linear-gradient(180deg, rgba(255,255,255,0.95), rgba(248,242,235,0.97));
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.88);
  }
  .payment-title { color: #4d2d1a; }
  .payment-pct { color: #7a4a2d; }
  .payment-bar-track { background: rgba(122,74,45,0.10); }
  .payment-bar-fill { box-shadow: 0 0 12px rgba(122,74,45,0.20); }

  .table-card { overflow: hidden; }
  .table-header {
    padding: 18px 20px;
    background: linear-gradient(180deg, rgba(255,255,255,0.40), rgba(248,242,235,0.85));
    border-bottom: 1px solid rgba(122,74,45,0.10);
  }
  .table-title { font-size: 15px; }
  table.txn thead th {
    background: linear-gradient(180deg, #fbf6ef, #f6ede2);
    color: #8f7057;
  }
  table.txn tbody tr:hover { background: rgba(250,245,238,0.88); }
  .txn-no { color: #7a4a2d; }
  .status-pill {
    background: linear-gradient(180deg, rgba(122,74,45,0.10), rgba(122,74,45,0.06));
    color: #7a4a2d;
    border: 1px solid rgba(122,74,45,0.08);
  }
  .sp-done { background: linear-gradient(180deg, #e7f8eb, #d9f1de); color: #0f7a37; }
  .method-pill {
    min-width: 88px;
    height: 30px;
    padding: 0 16px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;
    white-space: nowrap;
    line-height: 1;
    background: linear-gradient(180deg, #efe2d4, #e6d2be);
    color: #6d3f22;
    font-size: 11px;
    font-weight: 600;
    border: 1px solid rgba(122,74,45,0.10);
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.68);
    position: relative;
    overflow: hidden;
  }

  .skeleton {
    background: linear-gradient(90deg,#ece1d2 25%,#ddd0c1 50%,#ece1d2 75%);
    background-size:200% 100%;
    animation:shimmer 1.5s infinite;
  }

  .toast {
    background: linear-gradient(180deg, #2b1a12, #1c120d);
    box-shadow: 0 16px 36px rgba(0,0,0,0.32);
    border: 1px solid rgba(255,255,255,0.08);
  }

  @keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

  ::-webkit-scrollbar-thumb { background: linear-gradient(180deg, #c8ad8f, #9d7c5d); border-radius: 3px; }

  @media (max-width: 768px) {
    .metric-card:hover { transform: translateY(-4px) scale(1.01); }
  }
</style>

</head>
<body>

<div class="particle-canvas" id="particleCanvas"></div>

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

<div id="main">
  <header id="topbar">
    <div class="breadcrumb">Dashboard / <strong>Ringkasan</strong></div>
    <div class="tb-search">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
      </svg>
      <input placeholder="Cari produk, transaksi..." onkeypress="if(event.key==='Enter')location.href='<?= base_url('products') ?>?search='+this.value"/>
    </div>
    <div class="tb-actions">
      <?php if (in_array(session('user_role'), ['admin', 'cashier'])): ?>
      <a href="<?= base_url('pos') ?>" class="btn-new">
        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        Transaksi Baru
      </a>
      <?php endif; ?>
      <a href="<?= base_url('inventori') ?>" class="icon-btn">
        <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <div class="notif-dot" id="notifDot" style="display:none;"></div>
      </a>
      <div class="tb-avatar"><?= strtoupper(substr(session('user_name')??'U',0,1)) ?></div>
    </div>
  </header>

  <div class="content">
    <div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;">
      <div>
        <h1 style="font-size:22px;font-weight:800;color:var(--text);letter-spacing:-0.02em;">Dashboard</h1>
        <p style="font-size:13px;color:var(--muted);margin-top:3px;">
          Selamat datang, <strong style="color:var(--wood);"><?= esc(session('user_name')) ?></strong>
        </p>
      </div>
      <div style="background:white;border:1px solid var(--border);border-radius:10px;padding:8px 14px;font-size:12px;color:var(--muted);display:flex;align-items:center;gap:6px;">
        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        <span id="currentDate"></span>
      </div>
    </div>

    <div class="metrics-grid">
      <div class="metric-card mc-brown">
        <div class="mc-header">
          <span class="mc-label">Penjualan Hari Ini</span>
          <div class="mc-icon" style="background:linear-gradient(145deg, rgba(122,74,45,0.12), rgba(197,138,82,0.12));">
            <svg style="width:18px;height:18px;color:#7A4A2D;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 1v8m0 0v1"/></svg>
          </div>
        </div>
        <div class="mc-value" id="todaySales"><span class="skeleton" style="height:26px;width:110px;"></span></div>
        <div class="mc-sub" id="todayCount"><span class="skeleton" style="height:13px;width:70px;"></span></div>
      </div>
      <div class="metric-card mc-brown">
        <div class="mc-header">
          <span class="mc-label">Revenue Bulan Ini</span>
          <div class="mc-icon" style="background:linear-gradient(145deg, rgba(122,74,45,0.12), rgba(197,138,82,0.12));">
            <svg style="width:18px;height:18px;color:#7A4A2D;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
          </div>
        </div>
        <div class="mc-value" id="monthRevenue"><span class="skeleton" style="height:26px;width:120px;"></span></div>
        <div class="mc-sub" style="color:var(--muted);">Bulan ini</div>
      </div>
      <div class="metric-card mc-brown">
        <div class="mc-header">
          <span class="mc-label">Total Produk</span>
          <div class="mc-icon" style="background:linear-gradient(145deg, rgba(122,74,45,0.12), rgba(197,138,82,0.12));">
            <svg style="width:18px;height:18px;color:#7A4A2D;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
          </div>
        </div>
        <div class="mc-value" id="totalProducts"><span class="skeleton" style="height:26px;width:60px;"></span></div>
        <div class="mc-sub" style="color:var(--muted);">Produk aktif</div>
      </div>
      <div class="metric-card mc-brown">
        <div class="mc-header">
          <span class="mc-label">Stok Rendah</span>
          <div class="mc-icon" style="background:linear-gradient(145deg, rgba(122,74,45,0.12), rgba(197,138,82,0.12));">
            <svg style="width:18px;height:18px;color:#7A4A2D;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
          </div>
        </div>
        <div class="mc-value" id="lowStockCount" style="color:var(--wood-dk);"><span class="skeleton" style="height:26px;width:50px;"></span></div>
        <div class="mc-sub">
          <a href="<?= base_url('inventori') ?>" style="color:var(--wood);font-size:12px;text-decoration:none;font-weight:500;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Lihat detail </a>
        </div>
      </div>
    </div>

    <div class="profit-grid">
      <div class="metric-card mc-brown" style="animation-delay:0.3s;">
        <div class="mc-header">
          <span class="mc-label">Laba Kotor Hari Ini</span>
          <div class="mc-icon" style="background:linear-gradient(145deg, rgba(122,74,45,0.12), rgba(197,138,82,0.12));">
            <svg style="width:18px;height:18px;color:#7A4A2D;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
          </div>
        </div>
        <div class="mc-value" id="todayProfit" style="color:var(--wood-dk);"><span class="skeleton" style="height:26px;width:110px;"></span></div>
        <div class="mc-sub" id="todayProfitSub" style="color:var(--muted);">Keuntungan hari ini</div>
      </div>
      <div class="metric-card mc-brown" style="animation-delay:0.37s;">
        <div class="mc-header">
          <span class="mc-label">Laba Kotor Bulan Ini</span>
          <div class="mc-icon" style="background:linear-gradient(145deg, rgba(122,74,45,0.12), rgba(197,138,82,0.12));">
            <svg style="width:18px;height:18px;color:#7A4A2D;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
          </div>
        </div>
        <div class="mc-value" id="monthProfit" style="color:var(--wood-dk);"><span class="skeleton" style="height:26px;width:120px;"></span></div>
        <div class="mc-sub" id="profitMargin" style="color:var(--muted);">Keuntungan bulan ini</div>
      </div>
    </div>

    <div class="charts-grid">
      <div class="chart-card chart-card-main">
        <div class="chart-header" style="flex-wrap:wrap;gap:10px;">
          <span class="chart-title">Tren Penjualan</span>
          <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            <div style="display:flex;gap:4px;">
              <button onclick="setTrendPreset(7)"   class="trend-preset" id="preset-7">7H</button>
              <button onclick="setTrendPreset(30)"  class="trend-preset active" id="preset-30">30H</button>
              <button onclick="setTrendPreset(90)"  class="trend-preset" id="preset-90">3B</button>
              <button onclick="setTrendPreset(365)" class="trend-preset" id="preset-365">1T</button>
            </div>
            <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
              <input type="date" id="trendStart" class="trend-date-input"/>
              <span style="font-size:11px;color:var(--muted);">—</span>
              <input type="date" id="trendEnd" class="trend-date-input"/>
              <button onclick="applyTrendFilter()" class="trend-apply-btn">Terapkan</button>
            </div>
            <span class="chart-pill" id="trendPill">30 Hari</span>
          </div>
        </div>
        <div style="position:relative;z-index:1;" id="trendWrap">
          <canvas id="salesTrendChart" height="180"></canvas>
        </div>
      </div>

      <div class="chart-right-col">
        <div class="chart-card">
          <div class="chart-header">
            <span class="chart-title">Top Produk</span>
            <div style="display:flex;align-items:center;gap:5px;flex-wrap:wrap;">
              <button class="trend-preset active" onclick="setChartPreset('top',7,this)">7H</button>
              <button class="trend-preset" onclick="setChartPreset('top',30,this)">30H</button>
              <button class="trend-preset" onclick="setChartPresetMonth('top',this)">Bln ini</button>
              <button class="trend-preset" id="topCustomBtn" onclick="toggleCustomChart('top')">Custom</button>
            </div>
          </div>
          <div id="topCustomRange" style="display:none;padding:0 16px 10px;display:none;align-items:center;gap:6px;flex-wrap:wrap;">
            <input type="date" id="topStart" class="trend-date-input"/>
            <span style="font-size:11px;color:var(--muted);">—</span>
            <input type="date" id="topEnd" class="trend-date-input"/>
            <button onclick="applyChartFilter('top')" class="trend-apply-btn">Terapkan</button>
          </div>
          <div id="topProductsWrap" class="top-products-wrap">
            <div id="topProductLabels" class="top-product-labels"></div>
            <div class="top-products-canvas">
              <canvas id="topProductsChart" height="180"></canvas>
            </div>
          </div>
        </div>

        <div class="chart-card payment-card">
          <div class="chart-header">
            <span class="chart-title">Metode Pembayaran</span>
            <div style="display:flex;align-items:center;gap:5px;flex-wrap:wrap;">
              <button class="trend-preset active" onclick="setChartPreset('pay',7,this)">7H</button>
              <button class="trend-preset" onclick="setChartPreset('pay',30,this)">30H</button>
              <button class="trend-preset" onclick="setChartPresetMonth('pay',this)">Bln ini</button>
              <button class="trend-preset" id="payCustomBtn" onclick="toggleCustomChart('pay')">Custom</button>
            </div>
          </div>
          <div id="payCustomRange" style="display:none;padding:0 16px 10px;align-items:center;gap:6px;flex-wrap:wrap;">
            <input type="date" id="payStart" class="trend-date-input"/>
            <span style="font-size:11px;color:var(--muted);">—</span>
            <input type="date" id="payEnd" class="trend-date-input"/>
            <button onclick="applyChartFilter('pay')" class="trend-apply-btn">Terapkan</button>
          </div>
          <div id="paymentContent" style="flex:1;display:flex;min-height:0;">
            <div class="payment-card-body">
              <div class="payment-chart-box">
                <canvas id="paymentChart"></canvas>
              </div>
              <div id="paymentLegend" class="payment-legend">
                <div class="skeleton" style="height:13px;width:100%;margin-bottom:8px;"></div>
                <div class="skeleton" style="height:13px;width:80%;"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="table-card">
      <div class="table-header">
        <span class="table-title">Transaksi Terbaru</span>
        <a href="<?= base_url('reports') ?>" style="font-size:12px;color:var(--wood);font-weight:500;text-decoration:none;">Lihat semua </a>
      </div>
      <div style="overflow-x:auto;">
        <table class="txn">
          <thead>
            <tr>
              <th style="width:160px;">No. Transaksi</th>
            <th>Pelanggan</th>
            <th>Kasir</th>
            <th style="text-align:right;width:130px;">Total</th>
            <th style="text-align:center;width:110px;">Metode</th>
            <th style="width:130px;">Waktu</th>
            <th style="text-align:center;width:90px;">Status</th>
            </tr>
          </thead>
          <tbody id="txnTbody">
            <tr><td colspan="7" style="padding:32px;text-align:center;">
              <div class="skeleton" style="height:14px;width:180px;margin:0 auto;"></div>
            </td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php if (session('success')): ?>
<div class="toast success" id="toast">
  <svg style="width:16px;height:16px;color:#22C55E;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
  <?= esc(session('success')) ?>
</div>
<?php endif; ?>

<script>
const BASE_URL = '<?= base_url() ?>';
const formatRp = v => 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(v ?? 0));

document.getElementById('currentDate').textContent =
  new Date().toLocaleDateString('id-ID', {weekday:'long',year:'numeric',month:'long',day:'numeric'});

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

const toast = document.getElementById('toast');
if (toast) setTimeout(() => {
  toast.style.opacity = '0';
  toast.style.transform = 'translateY(8px)';
  toast.style.transition = 'all 0.3s';
  setTimeout(() => toast.remove(), 300);
}, 3500);

const pCanvas = document.getElementById('particleCanvas');
for (let i = 0; i < 8; i++) {
  const p = document.createElement('div');
  p.className = 'particle';
  const s = Math.random() * 4 + 2;
  p.style.cssText = `width:${s}px;height:${s}px;left:${Math.random()*100}%;background:rgba(122,74,45,${Math.random()*0.08+0.03});animation-duration:${Math.random()*15+12}s;animation-delay:-${Math.random()*15}s;filter:blur(1px);`;
  pCanvas.appendChild(p);
}


function animateCount(el, end, isRp) {
  const dur = 900, t0 = performance.now();
  const tick = now => {
    const p = Math.min((now - t0) / dur, 1);
    const e = 1 - Math.pow(1 - p, 3);
    el.textContent = isRp ? formatRp(Math.round(end * e)) : Math.round(end * e);
    if (p < 1) requestAnimationFrame(tick);
  };
  requestAnimationFrame(tick);
}

let trendChartInstance = null;
function renderTrend(data, startDate, endDate) {
  const days = {};
  const start = new Date(startDate);
  const end   = new Date(endDate);

  for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
    days[d.toISOString().slice(0, 10)] = 0;
  }

  (data || []).forEach(d => {
    const key = d.date ? d.date.slice(0, 10) : null;
    if (key && Object.prototype.hasOwnProperty.call(days, key)) days[key] = parseFloat(d.total) || 0;
  });

  const totalDays = Object.keys(days).length;
  const labels = Object.keys(days).map(k => {
    const d = new Date(k);
    return totalDays <= 90
      ? d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })
      : d.toLocaleDateString('id-ID', { month: 'short', year: '2-digit' });
  });
  const values = Object.values(days);
  const hasData = values.some(v => v > 0);

  if (trendChartInstance) {
    trendChartInstance.destroy();
    trendChartInstance = null;
  }
  document.getElementById('trendWrap').innerHTML = '<canvas id="salesTrendChart" height="180"></canvas>';

  const ctx = document.getElementById('salesTrendChart').getContext('2d');
  const g = ctx.createLinearGradient(0, 0, 0, 220);
  g.addColorStop(0, 'rgba(122,74,45,0.25)');
  g.addColorStop(1, 'rgba(122,74,45,0)');

  trendChartInstance = new Chart(ctx, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        data: values,
        borderColor: '#7A4A2D',
        backgroundColor: g,
        borderWidth: 2.5,
        pointRadius: values.map(v => v > 0 ? 4 : 0),
        pointHoverRadius: 7,
        pointBackgroundColor: '#7A4A2D',
        pointHoverBackgroundColor: '#7A4A2D',
        pointHoverBorderColor: 'white',
        pointHoverBorderWidth: 2,
        fill: true,
        tension: 0.4,
      }]
    },
    options: {
      responsive: true,
      animation: { duration: 800, easing: 'easeInOutQuart' },
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#1C1410',
          titleColor: '#F4C350',
          bodyColor: 'white',
          padding: 10,
          cornerRadius: 8,
          callbacks: {
            label: c => formatRp(c.raw),
            title: t => t[0].label
          }
        }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: {
            font: { size: 11 },
            color: '#9B8B77',
            maxTicksLimit: totalDays <= 14 ? totalDays : 7
          }
        },
        y: {
          beginAtZero: true,
          grid: { color: 'rgba(0,0,0,0.04)' },
          ticks: {
            font: { size: 11 },
            color: '#9B8B77',
            callback: v => v === 0 ? '0' : new Intl.NumberFormat('id-ID', {notation:'compact',compactDisplay:'short'}).format(v)
          }
        }
      },
      interaction: { mode: 'index', intersect: false },
    }
  });

  const pill = document.getElementById('trendPill');
  if (!hasData) {
    pill.textContent = 'Belum ada data';
    pill.style.background = '#FEE2E2';
    pill.style.color = '#B91C1C';
  } else {
    pill.textContent = totalDays + ' Hari';
    pill.style.background = '#F5EFE5';
    pill.style.color = '#9B8B77';
  }
}

function initTrendDates() {
  const end = new Date(), start = new Date();
  start.setDate(start.getDate() - 29);
  document.getElementById('trendStart').value = start.toISOString().slice(0, 10);
  document.getElementById('trendEnd').value = end.toISOString().slice(0, 10);
}

function setTrendPreset(days) {
  document.querySelectorAll('.trend-preset').forEach(b => b.classList.remove('active'));
  const btn = document.getElementById('preset-' + days);
  if (btn) btn.classList.add('active');
  const end = new Date(), start = new Date();
  start.setDate(start.getDate() - (days - 1));
  document.getElementById('trendStart').value = start.toISOString().slice(0, 10);
  document.getElementById('trendEnd').value = end.toISOString().slice(0, 10);
  loadTrend();
}

function applyTrendFilter() {
  document.querySelectorAll('.trend-preset').forEach(b => b.classList.remove('active'));
  loadTrend();
}

async function loadTrend() {
  const s = document.getElementById('trendStart').value;
  const e = document.getElementById('trendEnd').value;
  if (!s || !e) return;
  if (s > e) {
    alert('Tanggal mulai tidak boleh lebih besar dari tanggal akhir.');
    return;
  }
  try {
    const res = await fetch(`${BASE_URL}api/dashboard/sales-trend?start_date=${s}&end_date=${e}`);
    const trend = await res.json();
    renderTrend(trend.data ?? [], s, e);
  } catch (err) {
    console.error('Trend error:', err);
    renderTrend([], s, e);
  }
}

let topProductsChartInstance = null;
let topProductsResizeObserver = null;

function escapeHtml(str) {
  return String(str)
    .replace(/&/g,'&amp;')
    .replace(/</g,'&lt;')
    .replace(/"/g,'&quot;');
}

function buildTopProductLabels(data) {
  const c = document.getElementById('topProductLabels');
  c.innerHTML = data.map(d => `
    <div class="top-product-label" title="${escapeHtml(d.product_name)}">
      <span class="marquee-inner">${escapeHtml(d.product_name)}</span>
    </div>
  `).join('');
}

function positionTopProductLabels(chart) {
  const c = document.getElementById('topProductLabels');
  c.style.height = chart.canvas.clientHeight + 'px';
  const meta = chart.getDatasetMeta(0);
  const labels = c.querySelectorAll('.top-product-label');
  meta.data.forEach((bar, i) => {
    if (!labels[i]) return;
    labels[i].style.top = (bar.y - 10) + 'px';
  });
}

function initTopProductMarquees() {
  const rm = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  document.querySelectorAll('.top-product-label').forEach(label => {
    const inner = label.querySelector('.marquee-inner');
    label.classList.remove('is-marquee');
    if (rm) return;
    if (inner.scrollWidth > label.clientWidth) {
      label.classList.add('is-marquee');
      const dist = inner.scrollWidth - label.clientWidth;
      inner.style.setProperty('--marquee-distance', '-' + dist + 'px');
      inner.style.setProperty('--marquee-duration', (8 + inner.textContent.length * 0.15) + 's');
    }
  });
}

function syncTopProductLabels() {
  if (!topProductsChartInstance) return;
  positionTopProductLabels(topProductsChartInstance);
  initTopProductMarquees();
}

function renderTops(data) {
  const wrap = document.getElementById('topProductsWrap');
  const canvas = document.getElementById('topProductsChart');
  const labelsEl = document.getElementById('topProductLabels');
  const canvasWrap = wrap.querySelector('.top-products-canvas');

  if (topProductsChartInstance) {
    topProductsChartInstance.destroy();
    topProductsChartInstance = null;
  }

  canvasWrap.querySelector('.top-products-empty')?.remove();
  canvas.style.display = '';
  labelsEl.innerHTML = '';
  labelsEl.style.display = '';

  if (!data || data.length === 0) {
    canvas.style.display = 'none';
    labelsEl.style.display = 'none';
    canvasWrap.insertAdjacentHTML('beforeend', `
      <div class="top-products-empty" style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:140px;color:#C4B5A5;">
        <svg style="width:28px;height:28px;margin-bottom:8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        <p style="font-size:12px;">Belum ada data penjualan</p>
      </div>
    `);
    return;
  }

  buildTopProductLabels(data);
  const ctx = canvas.getContext('2d');
  topProductsChartInstance = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: data.map(d => d.product_name),
      datasets: [{
        data: data.map(d => d.total_qty),
        backgroundColor: ['#7A4A2D','#9B5E30','#B87848','#D4A06A','#EAC898'],
        borderRadius: 6,
        borderSkipped: false
      }]
    },
    options: {
      indexAxis: 'y',
      responsive: true,
      animation: {
        duration: 1200,
        easing: 'easeInOutQuart',
        delay: ctx => ctx.dataIndex * 80,
        onComplete: () => syncTopProductLabels()
      },
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#1C1410',
          titleColor: '#F4C350',
          bodyColor: 'white',
          padding: 10,
          cornerRadius: 8
        }
      },
      scales: {
        x: { beginAtZero: true, grid:{ color:'rgba(0,0,0,0.04)' }, ticks:{ font:{size:11}, color:'#9B8B77' } },
        y: { grid:{ display:false }, ticks:{ display:false } }
      }
    }
  });

  if (topProductsResizeObserver) topProductsResizeObserver.disconnect();
  topProductsResizeObserver = new ResizeObserver(() => {
    if (topProductsChartInstance) {
      topProductsChartInstance.resize();
      syncTopProductLabels();
    }
  });
  topProductsResizeObserver.observe(wrap);
  requestAnimationFrame(() => syncTopProductLabels());
}

let paymentChartInstance = null;
function renderPayment(summary) {
  const cash = summary?.cash ?? { trx: 0, amount: 0 };
  const transfer = summary?.transfer ?? { trx: 0, amount: 0 };
  const totalTrx = cash.trx + transfer.trx;
  const canvas = document.getElementById('paymentChart');
  const legend = document.getElementById('paymentLegend');
  const ctx = canvas.getContext('2d');

  if (paymentChartInstance) {
    paymentChartInstance.destroy();
    paymentChartInstance = null;
  }

  if (totalTrx === 0) {
    legend.innerHTML = `<p style="font-size:12px;color:var(--muted);text-align:center;">Belum ada transaksi bulan ini</p>`;
    return;
  }

  paymentChartInstance = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Tunai', 'Transfer'],
      datasets: [{
        data: [cash.trx, transfer.trx],
        backgroundColor: ['#7A4A2D', '#93C5FD'],
        borderColor: ['#7A4A2D', '#93C5FD'],
        borderWidth: 0,
        hoverOffset: 4,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '72%',
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#1C1410',
          titleColor: '#F4C350',
          bodyColor: 'white',
          padding: 8,
          cornerRadius: 8,
          callbacks: {
            label: c => ` ${c.label}: ${c.raw} transaksi`
          }
        }
      }
    }
  });

  const cashPct = Math.round((cash.trx / totalTrx) * 100);
  const transferPct = Math.round((transfer.trx / totalTrx) * 100);
  const totalAmount = cash.amount + transfer.amount;

  legend.innerHTML = `
    <div class="payment-legend">
      <div class="payment-item">
        <div class="payment-item-top">
          <div class="payment-name">
            <span class="payment-dot" style="background:#7A4A2D;"></span>
            <span class="payment-title">Tunai</span>
          </div>
          <span class="payment-pct" style="color:var(--wood);">${cashPct}%</span>
        </div>
        <div class="payment-meta">
          <span>${cash.trx} transaksi</span>
          <span>${formatRp(cash.amount)}</span>
        </div>
        <div class="payment-bar-track">
          <div class="payment-bar-fill" style="width:${cashPct}%;background:#7A4A2D;"></div>
        </div>
      </div>

      <div class="payment-item">
        <div class="payment-item-top">
          <div class="payment-name">
            <span class="payment-dot" style="background:#93C5FD;"></span>
            <span class="payment-title">Transfer</span>
          </div>
          <span class="payment-pct" style="color:#2563EB;">${transferPct}%</span>
        </div>
        <div class="payment-meta">
          <span>${transfer.trx} transaksi</span>
          <span>${formatRp(transfer.amount)}</span>
        </div>
        <div class="payment-bar-track">
          <div class="payment-bar-fill" style="width:${transferPct}%;background:#93C5FD;"></div>
        </div>
      </div>

      <div class="payment-summary">
        <span>Total transaksi: ${totalTrx}</span>
        <span>Total omzet: ${formatRp(totalAmount)}</span>
      </div>
    </div>
  `;
}

function renderTxns(txns) {
  const tbody = document.getElementById('txnTbody');
  if (!txns.length) {
    tbody.innerHTML = '<tr><td colspan="7" style="padding:40px;text-align:center;color:#9B8B77;font-size:13px;">Belum ada transaksi</td></tr>';
    return;
  }
  tbody.innerHTML = txns.map((t, i) => `
    <tr class="txn-row" style="animation-delay:${i * 0.06}s;">
      <td><a class="txn-no" href="${BASE_URL}pos/invoice/${t.id}">${t.transaction_no}</a></td>
      <td style="color:#4A3728;">${t.customer_name || '<span style="color:#C4B5A5;">—</span>'}</td>
      <td style="color:#9B8B77;font-size:12px;">${t.cashier_name}</td>
      <td style="text-align:right;font-weight:700;">${formatRp(t.total)}</td>
      <td class="method-cell"><div class="method-cell-inner"><span class="method-pill">${t.payment_method === 'cash' ? 'Tunai' : 'Transfer'}</span></div></td>
      <td style="color:#9B8B77;font-size:12px;">${new Date(t.transaction_date).toLocaleString('id-ID',{day:'numeric',month:'short',hour:'2-digit',minute:'2-digit'})}</td>
      <td style="text-align:center;"><span class="status-pill sp-done">Selesai</span></td>
    </tr>
  `).join('');
}

// ── Filter untuk Top Produk & Metode Pembayaran ──────────────────

function initChartDates(type) {
  const end = new Date(), start = new Date();
  start.setDate(start.getDate() - 6); // default 7 hari
  document.getElementById(type + 'Start').value = start.toISOString().slice(0, 10);
  document.getElementById(type + 'End').value   = end.toISOString().slice(0, 10);
}

function setChartPreset(type, days, btn) {
  document.querySelectorAll('#' + type + 'CustomBtn').forEach(b => b.classList.remove('active'));
  btn.closest('.chart-header').querySelectorAll('.trend-preset').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');

  const end = new Date(), start = new Date();
  start.setDate(start.getDate() - (days - 1));
  document.getElementById(type + 'Start').value = start.toISOString().slice(0, 10);
  document.getElementById(type + 'End').value   = end.toISOString().slice(0, 10);

  document.getElementById(type + 'CustomRange').style.display = 'none';
  if (type === 'top') loadTopProducts();
  else loadPayment();
}

function setChartPresetMonth(type, btn) {
  btn.closest('.chart-header').querySelectorAll('.trend-preset').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');

  const now = new Date();
  const start = new Date(now.getFullYear(), now.getMonth(), 1);
  document.getElementById(type + 'Start').value = start.toISOString().slice(0, 10);
  document.getElementById(type + 'End').value   = now.toISOString().slice(0, 10);

  document.getElementById(type + 'CustomRange').style.display = 'none';
  if (type === 'top') loadTopProducts();
  else loadPayment();
}

function toggleCustomChart(type) {
  const rangeEl = document.getElementById(type + 'CustomRange');
  const isVisible = rangeEl.style.display === 'flex';
  rangeEl.style.display = isVisible ? 'none' : 'flex';
  const btn = document.getElementById(type + 'CustomBtn');
  if (!isVisible) {
    btn.closest('.chart-header').querySelectorAll('.trend-preset').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
  }
}

function applyChartFilter(type) {
  const s = document.getElementById(type + 'Start').value;
  const e = document.getElementById(type + 'End').value;
  if (!s || !e) return;
  if (s > e) { alert('Tanggal mulai tidak boleh lebih dari tanggal akhir.'); return; }
  if (type === 'top') loadTopProducts();
  else loadPayment();
}

async function loadTopProducts() {
  const s = document.getElementById('topStart').value;
  const e = document.getElementById('topEnd').value;
  if (!s || !e) return;
  try {
    const res = await fetch(`${BASE_URL}api/dashboard/top-products?start_date=${s}&end_date=${e}`);
    const tops = await res.json();
    renderTops(tops.data ?? []);
  } catch (err) {
    console.error('Top products error:', err);
    renderTops([]);
  }
}

async function loadPayment() {
  const s = document.getElementById('payStart').value;
  const e = document.getElementById('payEnd').value;
  if (!s || !e) return;
  try {
    const res = await fetch(`${BASE_URL}api/dashboard/stats?start_date=${s}&end_date=${e}`);
    const stats = await res.json();
    renderPayment(stats.payment_summary ?? null);
  } catch (err) {
    console.error('Payment error:', err);
    renderPayment(null);
  }
}

async function loadDashboard() {
  try {
    const sR = await fetch(BASE_URL + 'api/dashboard/stats');
    const stats = await sR.json();

    animateCount(document.getElementById('todaySales'),    stats.today_sales     ?? 0, true);
    animateCount(document.getElementById('monthRevenue'),  stats.month_revenue   ?? 0, true);
    animateCount(document.getElementById('totalProducts'), stats.total_products  ?? 0, false);
    animateCount(document.getElementById('lowStockCount'), stats.low_stock_count ?? 0, false);
    animateCount(document.getElementById('todayProfit'),   stats.today_profit    ?? 0, true);
    animateCount(document.getElementById('monthProfit'),   stats.month_profit    ?? 0, true);

    document.getElementById('todayCount').textContent = (stats.today_count ?? 0) + ' transaksi';
    if ((stats.low_stock_count ?? 0) > 0) document.getElementById('notifDot').style.display = 'block';

    const rev = stats.month_revenue ?? 0;
    const profit = stats.month_profit ?? 0;
    if (rev > 0) {
      document.getElementById('profitMargin').textContent = 'Margin ' + ((profit / rev) * 100).toFixed(1) + '% dari omzet';
    }

    const todayRev = stats.today_sales ?? 0;
    const todayPrf = stats.today_profit ?? 0;
    if (todayRev > 0) {
      document.getElementById('todayProfitSub').textContent = 'Margin ' + ((todayPrf / todayRev) * 100).toFixed(1) + '% dari penjualan';
    }

    renderTxns(stats.recent_transactions ?? []);
  } catch (e) {
    console.error('Stats error:', e);
    ['todaySales','monthRevenue','todayProfit','monthProfit'].forEach(id => document.getElementById(id).textContent = 'Rp 0');
    ['totalProducts','lowStockCount'].forEach(id => document.getElementById(id).textContent = '0');
    document.getElementById('todayCount').textContent = '0 transaksi';
    renderTxns([]);
  }

  initTrendDates();
  await loadTrend();

  // Init filter tanggal untuk top produk & payment (default 7 hari)
  initChartDates('top');
  initChartDates('pay');
  await loadTopProducts();
  await loadPayment();
}

loadDashboard();
</script>
</body>
</html>