<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login — Toko Kayu Kontan Jaya</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    * { font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; }

    body {
      min-height: 100vh;
      background: #120600;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1rem;
    }

    /* ── ANIMATED BACKGROUND PARTICLES ── */
    .bg-canvas {
      position: fixed; inset: 0; z-index: 0; overflow: hidden;
    }
    .particle {
      position: absolute;
      border-radius: 50%;
      opacity: 0;
      animation: rise linear infinite;
    }
    @keyframes rise {
      0%   { transform: translateY(100vh) scale(0); opacity: 0; }
      10%  { opacity: 1; }
      90%  { opacity: 0.6; }
      100% { transform: translateY(-20vh) scale(1.2); opacity: 0; }
    }

    /* ── WOOD GRAIN LINES ── */
    .grain {
      position: fixed; inset: 0; z-index: 0;
      background-image:
        repeating-linear-gradient(88deg, transparent, transparent 3px, rgba(180,90,30,0.04) 3px, rgba(180,90,30,0.04) 6px),
        repeating-linear-gradient(92deg, transparent, transparent 5px, rgba(120,60,20,0.03) 5px, rgba(120,60,20,0.03) 8px);
      animation: grainShift 8s ease-in-out infinite alternate;
    }
    @keyframes grainShift {
      from { background-position: 0 0, 0 0; }
      to   { background-position: 20px 10px, -10px 5px; }
    }

    /* ── GLOW ORBS ── */
    .orb {
      position: fixed; border-radius: 50%;
      filter: blur(80px); pointer-events: none; z-index: 0;
      animation: orbFloat ease-in-out infinite alternate;
    }
    .orb-1 {
      width: 500px; height: 500px;
      background: radial-gradient(circle, rgba(122,74,45,0.35) 0%, transparent 70%);
      top: -150px; left: -100px;
      animation-duration: 7s;
    }
    .orb-2 {
      width: 400px; height: 400px;
      background: radial-gradient(circle, rgba(180,100,40,0.25) 0%, transparent 70%);
      bottom: -100px; right: -80px;
      animation-duration: 9s;
      animation-delay: -3s;
    }
    .orb-3 {
      width: 300px; height: 300px;
      background: radial-gradient(circle, rgba(244,195,80,0.12) 0%, transparent 70%);
      top: 40%; left: 50%;
      animation-duration: 11s;
      animation-delay: -5s;
    }
    @keyframes orbFloat {
      from { transform: translate(0, 0) scale(1); }
      to   { transform: translate(30px, -20px) scale(1.1); }
    }

    /* ── CARD ENTRANCE ── */
    .login-card {
      position: relative; z-index: 10;
      background: rgba(255,255,255,0.97);
      border-radius: 24px;
      box-shadow: 0 32px 80px rgba(0,0,0,0.5), 0 0 0 1px rgba(255,255,255,0.1);
      overflow: hidden;
      animation: cardIn 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) both;
    }
    @keyframes cardIn {
      from { opacity: 0; transform: translateY(40px) scale(0.95); }
      to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* ── BRAND SECTION ── */
    .brand-section {
      position: relative; z-index: 10;
      animation: fadeDown 0.6s ease both;
    }
    @keyframes fadeDown {
      from { opacity: 0; transform: translateY(-20px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* ── LOGO ── */
    .logo-ring {
      width: 76px; height: 76px;
      position: relative; margin: 0 auto 16px;
    }
    .logo-ring::before {
      content: '';
      position: absolute; inset: -4px;
      border-radius: 50%;
      background: conic-gradient(from 0deg, #F4C350, #9B5E30, #7A4A2D, #F4C350);
      animation: spin 4s linear infinite;
    }
    .logo-ring::after {
      content: '';
      position: absolute; inset: -2px;
      border-radius: 50%;
      background: #120600;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    .logo-inner {
      position: absolute; inset: 4px;
      border-radius: 50%;
      background: linear-gradient(135deg, #7A4A2D, #4A1F08);
      display: flex; align-items: center; justify-content: center;
      z-index: 1;
    }

    /* ── SHIMMER ON CARD TOP ── */
    .card-shine {
      position: absolute; top: 0; left: -100%;
      width: 60%; height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.06), transparent);
      animation: shine 3s ease-in-out infinite;
      z-index: 0; pointer-events: none;
    }
    @keyframes shine {
      0%   { left: -100%; }
      50%, 100% { left: 150%; }
    }

    /* ── INPUTS ── */
    .form-group { margin-bottom: 20px; animation: slideIn 0.5s ease both; }
    .form-group:nth-child(1) { animation-delay: 0.3s; }
    .form-group:nth-child(2) { animation-delay: 0.4s; }
    .form-group:nth-child(3) { animation-delay: 0.5s; }
    @keyframes slideIn {
      from { opacity: 0; transform: translateX(-16px); }
      to   { opacity: 1; transform: translateX(0); }
    }

    .field-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 8px; }

    .input-wrap { position: relative; }
    .input-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; color: #9CA3AF; transition: color 150ms; pointer-events: none; }
    .input-wrap:focus-within .input-icon { color: #7A4A2D; }

    .field-input {
      width: 100%; padding: 12px 16px 12px 42px;
      border: 1.5px solid #E5E7EB; border-radius: 12px;
      font-size: 14px; color: #111827; background: #F9FAFB;
      transition: all 200ms ease; outline: none;
    }
    .field-input:focus {
      border-color: #7A4A2D; background: white;
      box-shadow: 0 0 0 4px rgba(122,74,45,0.1);
      transform: scale(1.005);
    }
    .field-input::placeholder { color: #D1D5DB; }

    /* ── BUTTON ── */
    .btn-submit {
      width: 100%; padding: 13px; border: none; border-radius: 12px;
      background: linear-gradient(135deg, #7A4A2D 0%, #9B5E30 50%, #7A4A2D 100%);
      background-size: 200% 100%;
      color: white; font-weight: 700; font-size: 15px;
      cursor: pointer; position: relative; overflow: hidden;
      transition: all 250ms ease;
      animation: slideIn 0.5s 0.55s ease both;
      letter-spacing: 0.02em;
    }
    .btn-submit:hover {
      background-position: 100% 0;
      transform: translateY(-2px);
      box-shadow: 0 12px 32px rgba(122,74,45,0.45);
    }
    .btn-submit:active { transform: translateY(0) scale(0.99); }
    .btn-submit .ripple {
      position: absolute; border-radius: 50%;
      background: rgba(255,255,255,0.3);
      transform: scale(0); animation: ripple 0.6s linear;
      pointer-events: none;
    }
    @keyframes ripple {
      to { transform: scale(4); opacity: 0; }
    }

    /* ── DEMO SECTION ── */
    .demo-box {
      margin-top: 20px; padding: 14px; border-radius: 12px;
      border: 1.5px dashed #FCD34D; background: #FFFBEB;
      animation: slideIn 0.5s 0.6s ease both;
    }
    .demo-btn {
      width: 100%; text-align: left; padding: 8px 12px;
      border-radius: 8px; border: 1px solid #FDE68A;
      background: white; cursor: pointer;
      transition: all 150ms ease; margin-bottom: 6px;
      display: flex; align-items: center; gap: 8px;
    }
    .demo-btn:last-child { margin-bottom: 0; }
    .demo-btn:hover { background: #FEF3C7; border-color: #F59E0B; transform: translateX(4px); }
    .demo-badge {
      font-size: 10px; font-weight: 700; padding: 2px 8px;
      border-radius: 20px; text-transform: uppercase; letter-spacing: 0.05em;
    }

    /* ── FOOTER ── */
    .login-footer {
      position: relative; z-index: 10; text-align: center; margin-top: 20px;
      animation: fadeDown 0.5s 0.7s ease both;
    }
  </style>
</head>
<body>

<!-- Animated background -->
<div class="bg-canvas" id="bgCanvas"></div>
<div class="grain"></div>
<div class="orb orb-1"></div>
<div class="orb orb-2"></div>
<div class="orb orb-3"></div>

<div style="position:relative;z-index:10;width:100%;max-width:420px;">

  <!-- Brand -->
  <div class="brand-section text-center mb-7">
    <div class="logo-ring">
      <div class="logo-inner">
        <svg viewBox="0 0 40 40" fill="none" style="width:36px;height:36px;">
          <path d="M20 6 L34 32 H6 Z" fill="white" opacity="0.95"/>
          <rect x="14" y="28" width="12" height="3" rx="1.5" fill="#F4C350"/>
        </svg>
      </div>
    </div>
    <h1 style="font-size:22px;font-weight:800;color:white;letter-spacing:-0.03em;line-height:1.2;">
      Toko Kayu Kontan Jaya
    </h1>
    <p style="color:rgba(244,195,80,0.6);font-size:13px;margin-top:6px;font-weight:500;">
      Sistem Manajemen Stok & Penjualan
    </p>
  </div>

  <!-- Card -->
  <div class="login-card">
    <div class="card-shine"></div>

    <!-- Card header strip -->
    <div style="height:4px;background:linear-gradient(90deg,#7A4A2D,#F4C350,#9B5E30);"></div>

    <div style="padding:32px;">

      <!-- Error/Success -->
      <?php if (session('error')): ?>
      <div style="margin-bottom:20px;display:flex;align-items:center;gap:10px;background:#FEF2F2;border:1px solid #FECACA;color:#B91C1C;padding:12px 14px;border-radius:10px;font-size:13px;animation:slideIn 0.3s ease;">
        <svg style="width:16px;height:16px;flex-shrink:0;" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
        </svg>
        <?= esc(session('error')) ?>
      </div>
      <?php endif; ?>

      <?php if (session('success')): ?>
      <div style="margin-bottom:20px;display:flex;align-items:center;gap:10px;background:#F0FDF4;border:1px solid #BBF7D0;color:#15803D;padding:12px 14px;border-radius:10px;font-size:13px;">
        <svg style="width:16px;height:16px;flex-shrink:0;" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
        </svg>
        <?= esc(session('success')) ?>
      </div>
      <?php endif; ?>

      <form method="POST" action="<?= base_url('login') ?>" novalidate id="loginForm">
        <?= csrf_field() ?>

        <!-- Email -->
        <div class="form-group">
          <label class="field-label">Alamat Email</label>
          <div class="input-wrap">
            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <input type="email" name="email" id="email"
              value="<?= old('email') ?>"
              placeholder="nama@contoh.com"
              class="field-input" required autocomplete="email"/>
          </div>
          <?php if (isset($errors['email'])): ?>
          <p style="color:#EF4444;font-size:12px;margin-top:6px;"><?= esc($errors['email']) ?></p>
          <?php endif; ?>
        </div>

        <!-- Password -->
        <div class="form-group">
          <label class="field-label">Password</label>
          <div class="input-wrap">
            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <input type="password" name="password" id="password"
              placeholder="••••••••"
              class="field-input" style="padding-right:44px;" required autocomplete="current-password"/>
            <button type="button" onclick="togglePwd()" id="eyeBtn"
              style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9CA3AF;padding:4px;transition:color 150ms;"
              onmouseover="this.style.color='#7A4A2D'" onmouseout="this.style.color='#9CA3AF'">
              <svg id="eyeIcon" style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
            </button>
          </div>
          <?php if (isset($errors['password'])): ?>
          <p style="color:#EF4444;font-size:12px;margin-top:6px;"><?= esc($errors['password']) ?></p>
          <?php endif; ?>
        </div>

        <!-- Submit -->
        <div class="form-group" style="margin-bottom:0;">
          <button type="submit" class="btn-submit" onclick="addRipple(event, this)">
            Masuk ke Sistem 
          </button>
        </div>
      </form>

      <!-- Demo -->
      <?php if (ENVIRONMENT === 'development'): ?>
      <div class="demo-box">
        </p>
        <button class="demo-btn" onclick="fillDemo('admin@tokokayukontan.com')">
          <span class="demo-badge" style="background:#F3E8FF;color:#6D28D9;">Admin</span>
          <span style="font-size:12px;color:#78716C;">admin@tokokayukontan.com</span>
        </button>
        <button class="demo-btn" onclick="fillDemo('owner@tokokayukontan.com')">
          <span class="demo-badge" style="background:#FEF3C7;color:#92400E;">Owner</span>
          <span style="font-size:12px;color:#78716C;">owner@tokokayukontan.com</span>
        </button>
        <button class="demo-btn" onclick="fillDemo('kasir1@tokokayukontan.com')">
          <span class="demo-badge" style="background:#DBEAFE;color:#1D4ED8;">Kasir</span>
          <span style="font-size:12px;color:#78716C;">kasir1@tokokayukontan.com</span>
        </button>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Footer -->
  <div class="login-footer">
    <p style="color:rgba(255,255,255,0.2);font-size:12px;">© 2026 Toko Kayu Kontan Jaya · Sistem Internal</p>
  </div>

</div>

<script>
// ── FLOATING PARTICLES ──
const canvas = document.getElementById('bgCanvas');
const colors = ['rgba(122,74,45,0.6)','rgba(180,100,40,0.5)','rgba(244,195,80,0.4)','rgba(90,40,10,0.5)','rgba(200,120,60,0.4)'];
for (let i = 0; i < 25; i++) {
  const p = document.createElement('div');
  p.className = 'particle';
  const size = Math.random() * 8 + 3;
  p.style.cssText = `
    width:${size}px; height:${size}px;
    left:${Math.random()*100}%;
    background:${colors[Math.floor(Math.random()*colors.length)]};
    animation-duration:${Math.random()*10+8}s;
    animation-delay:-${Math.random()*10}s;
    filter:blur(${Math.random()*2}px);
  `;
  canvas.appendChild(p);
}

// ── TOGGLE PASSWORD ──
function togglePwd() {
  const p = document.getElementById('password');
  const icon = document.getElementById('eyeIcon');
  p.type = p.type === 'password' ? 'text' : 'password';
  icon.innerHTML = p.type === 'text'
    ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>'
    : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
}

// ── FILL DEMO ──
function fillDemo(email) {
  const e = document.getElementById('email');
  const p = document.getElementById('password');
  e.value = ''; p.value = '';
  let i = 0;
  const typeEmail = setInterval(() => {
    e.value += email[i++];
    if (i >= email.length) {
      clearInterval(typeEmail);
      let j = 0; const pass = 'password';
      const typePwd = setInterval(() => {
        p.value += pass[j++];
        if (j >= pass.length) clearInterval(typePwd);
      }, 60);
    }
  }, 40);
}

// ── RIPPLE EFFECT ──
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