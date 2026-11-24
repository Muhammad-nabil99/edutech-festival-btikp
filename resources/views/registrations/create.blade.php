<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>EduTech Festival BTIKP 2026 — Inovasi Digital</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap">
<style>
:root {
  --bg: #eeeef2;
  --fg: #0a0b10;
  --muted: #4a4f5c;
  --primary: #3b5bdb;
  --accent-purple: #c04de0;
  --accent-orange: #f08c00;
}
* { box-sizing: border-box; }
html, body {
  margin: 0;
  padding: 0;
  width: 100%;
  background: var(--bg);
  color: var(--fg);
  font-family: Inter, system-ui, -apple-system, sans-serif;
  -webkit-font-smoothing: antialiased;
}
a { color: inherit; text-decoration: none; }

/* ====== Animasi robot ====== */
.rb-float { animation: rb-float 3.6s ease-in-out infinite; }
@keyframes rb-float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-9px); } }
.rb-antenna { transform-origin: 150px 62px; animation: rb-sway 2.8s ease-in-out infinite; }
@keyframes rb-sway { 0%,100% { transform: rotate(-6deg); } 50% { transform: rotate(6deg); } }
.rb-bulb { transform-box: fill-box; transform-origin: center; animation: rb-pulse 1.8s ease-in-out infinite; }
@keyframes rb-pulse { 0%,100% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.3); opacity: .75; } }
.rb-arm { transform-box: fill-box; transform-origin: 50% 12%; }
.rb-arm-l { animation: rb-arm-l 3.6s ease-in-out infinite; }
.rb-arm-r { animation: rb-arm-r 2.4s ease-in-out infinite; }
@keyframes rb-arm-l { 0%,100% { transform: rotate(4deg); } 50% { transform: rotate(-8deg); } }
@keyframes rb-arm-r { 0%,100% { transform: rotate(-4deg); } 50% { transform: rotate(16deg); } }
.rb-look { animation: rb-look 7s ease-in-out infinite; }
@keyframes rb-look { 0%,20% { transform: translateX(0); } 30%,45% { transform: translateX(-9px); } 55%,70% { transform: translateX(9px); } 80%,100% { transform: translateX(0); } }
.rb-eye { transform-box: fill-box; transform-origin: center; animation: rb-blink 4.5s infinite; }
@keyframes rb-blink { 0%,88%,92%,96%,100% { transform: scaleY(1); } 90% { transform: scaleY(.08); } 94% { transform: scaleY(.08); } }
.rb-badge { transform-box: fill-box; transform-origin: center; animation: rb-badge 2.4s ease-in-out infinite; }
@keyframes rb-badge { 0%,100% { transform: scale(1); } 50% { transform: scale(1.1); } }
@media (prefers-reduced-motion: reduce) {
  .rb-float, .rb-antenna, .rb-bulb, .rb-arm, .rb-look, .rb-eye, .rb-badge { animation: none; }
}

/* ========================================================
   1. DESKTOP VIEWPORT (Untuk Layar Lebar >= 1025px)
   ======================================================== */
@media (min-width: 1025px) {
  html, body {
    height: 100%;
    overflow: hidden;
  }
  .mobile-viewport { display: none !important; }
  .desktop-viewport {
    display: flex;
    width: 100vw;
    height: 100vh;
    overflow: hidden;
    align-items: center;
    justify-content: center;
    background: var(--bg);
  }
  #scaler {
    width: 1440px;
    height: 884px;
    transform-origin: center center;
    flex-shrink: 0;
  }
  .hero {
    position: relative;
    width: 1440px;
    height: 884px;
    background: #eeeef2;
    overflow: hidden;
    color: #0a0b10;
  }
  #dot-canvas-desktop {
    position: absolute;
    left: 0;
    top: 0;
    width: 1440px;
    height: 884px;
    pointer-events: none;
  }
  .abs { position: absolute; }
  h1.dt-h1 { position: absolute; margin: 0; font-size: 165px; font-weight: 800; line-height: 142px; letter-spacing: -.045em; }
  .pill { padding: 12px 22px; border-radius: 999px; font-weight: 600; font-size: 18px; }
}

/* ========================================================
   2. MOBILE & TABLET VIEWPORT (Untuk Layar <= 1024px)
   ======================================================== */
@media (max-width: 1024px) {
  html, body {
    min-height: 100%;
    height: auto;
    overflow-x: hidden;
    overflow-y: auto;
  }
  .desktop-viewport { display: none !important; }
  .mobile-viewport {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    width: 100%;
    position: relative;
    background: #eeeef2;
    overflow-x: hidden;
    padding: 20px 20px 48px;
  }
  #dot-canvas-mobile {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 0;
  }
  .m-content {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 680px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
  }
  /* Mobile Header */
  .m-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    margin-bottom: 24px;
  }
  .m-logo {
    width: 72px;
    height: 72px;
    object-fit: contain;
    filter: drop-shadow(0 4px 8px rgba(0,0,0,0.06));
  }
  .m-portal-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 999px;
    background: #0a0b10;
    color: #fff;
    font-weight: 600;
    font-size: 14px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.18);
    transition: transform 0.15s ease, background 0.15s ease;
  }
  .m-portal-btn:active {
    transform: scale(0.97);
  }

  /* Typography */
  .m-h1-left {
    margin: 0 0 16px 0;
    font-size: clamp(44px, 12vw, 84px);
    font-weight: 800;
    line-height: 0.94;
    letter-spacing: -0.04em;
    color: #0a0b10;
  }
  .m-h1-right {
    margin: 12px 0 20px 0;
    font-size: clamp(44px, 12vw, 84px);
    font-weight: 800;
    line-height: 0.94;
    letter-spacing: -0.04em;
    color: #0a0b10;
    text-align: right;
  }

  /* Stats Container */
  .m-stats-card {
    display: inline-flex;
    align-items: center;
    gap: 20px;
    padding: 12px 20px;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(10, 11, 16, 0.08);
    box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    margin-bottom: 24px;
    align-self: flex-start;
  }
  .m-stat-val {
    font-size: clamp(34px, 8vw, 44px);
    font-weight: 800;
    line-height: 1;
    color: #0a0b10;
  }
  .m-stat-lbl {
    font-size: 13px;
    color: #4a4f5c;
    margin-top: 3px;
    font-weight: 500;
  }
  .m-stat-divider {
    width: 1px;
    height: 42px;
    background: #9a9eae;
  }

  /* Interactive Robot Centerpiece */
  .m-robot-showcase {
    position: relative;
    width: 100%;
    height: 310px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 10px 0 20px;
  }
  .m-robot-svg {
    width: 220px;
    height: 270px;
    filter: drop-shadow(0 15px 30px rgba(59,91,219,0.22));
  }
  .m-pill {
    position: absolute;
    padding: 10px 18px;
    border-radius: 999px;
    font-weight: 700;
    font-size: clamp(13px, 3.8vw, 16px);
    white-space: nowrap;
    user-select: none;
    z-index: 2;
  }
  .m-pill-gim {
    left: 8%;
    top: 15px;
    transform: rotate(-7deg);
    background: linear-gradient(90deg, #3b5bdb, #c04de0);
    color: #fff;
    box-shadow: 0 8px 20px rgba(59,91,219,0.3);
  }
  .m-pill-mpi {
    right: 5%;
    top: 75px;
    transform: rotate(6deg);
    background: #fff;
    color: #0a0b10;
    border: 2px solid #0a0b10;
    box-shadow: 0 6px 16px rgba(0,0,0,0.08);
  }
  .m-pill-btikp {
    left: 12%;
    bottom: 25px;
    transform: rotate(-4deg);
    background: #f08c00;
    color: #0a0b10;
    font-weight: 800;
    box-shadow: 0 6px 16px rgba(240,140,0,0.3);
  }

  /* Description & Details */
  .m-desc-box {
    margin-bottom: 28px;
    space-y: 8px;
  }
  .m-desc-main {
    margin: 0 0 8px 0;
    font-size: clamp(16px, 4vw, 19px);
    line-height: 1.5;
    font-weight: 500;
    color: #0a0b10;
  }
  .m-desc-sub {
    margin: 0;
    font-size: 14px;
    color: #4a4f5c;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
  }
  .m-closed-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 999px;
    background: #ffe4e6;
    color: #e11d48;
    font-size: 12px;
    font-weight: 700;
  }

  /* Primary CTA */
  .m-cta-btn {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 16px 0 24px;
    height: 56px;
    border-radius: 18px;
    background: linear-gradient(90deg, #3b5bdb, #c04de0);
    color: #fff;
    font-weight: 700;
    font-size: clamp(15px, 4vw, 17px);
    box-shadow: 0 10px 25px rgba(59,91,219,0.35);
    margin-bottom: 32px;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    width: 100%;
  }
  .m-cta-btn:active {
    transform: scale(0.98);
  }
  .m-cta-icon {
    width: 36px;
    height: 36px;
    border-radius: 12px;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    shrink-0: 0;
  }

  /* Info Chips / Highlights */
  .m-chips-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px;
    padding-top: 20px;
    border-top: 1px solid rgba(10,11,16,0.12);
  }
  @media (min-width: 600px) {
    .m-chips-grid {
      grid-template-columns: repeat(3, 1fr);
    }
  }
  .m-chip-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 14px;
    border-radius: 16px;
    background: rgba(255,255,255,0.6);
    border: 1px solid rgba(10,11,16,0.06);
    box-shadow: 0 2px 6px rgba(0,0,0,0.03);
  }
  .m-chip-icon {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    background: #fff;
    border: 1px solid rgba(10,11,16,0.08);
    color: #3b5bdb;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 2px 5px rgba(0,0,0,0.04);
  }
  .m-chip-text {
    font-size: 13px;
    font-weight: 700;
    color: #2c3345;
  }
}
</style>
</head>
<body>

<!-- ========================================================
     1. DESKTOP VIEWPORT (Screen >= 1025px)
     Exact 1440x884 Fit & Full Proportional Scaler
     ======================================================== -->
<div class="desktop-viewport" id="viewport">
  <div id="scaler">
    <div class="hero" id="hero-desktop">
      <canvas id="dot-canvas-desktop"></canvas>

      <img draggable="false" class="abs" alt="Edutech Festival BTIKP 2026" src="{{ asset('logo.png') }}" style="left:40px;top:22px;width:100px;height:100px;object-fit:contain">

      <!-- Tombol Portal Masuk (Sudut Kanan Atas) -->
      <a href="{{ route('admin.login') }}" class="abs" style="right:40px;top:44px;padding:0 26px;height:42px;display:flex;align-items:center;gap:8px;border-radius:999px;background:#0a0b10;color:#fff;font-weight:600;font-size:15px;box-shadow:0 4px 14px rgba(0,0,0,0.15);transition:transform .15s ease,background .15s ease">
        <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
        </svg>
        <span>Portal Masuk</span>
      </a>

      <h1 class="dt-h1" style="left:40px;top:150px">INOVASI<br>DIGITAL</h1>
      <h1 class="dt-h1" style="right:38px;top:462px;text-align:right">EDUTECH<br>FESTIVAL</h1>

      <div class="abs" style="right:40px;top:118px;display:flex;align-items:center;gap:28px;text-align:right">
        <div><div style="font-size:60px;font-weight:800;line-height:1">17</div><div style="font-size:15px;color:#4a4f5c;margin-top:4px">kabupaten / kota</div></div>
        <div style="width:1px;height:64px;background:#9a9eae"></div>
        <div><div style="font-size:60px;font-weight:800;line-height:1">2</div><div style="font-size:15px;color:#4a4f5c;margin-top:4px">kategori lomba</div></div>
      </div>

      <!-- ===== ROBOT (versi beranimasi) ===== -->
      <svg class="rb" aria-hidden="true" viewBox="0 0 300 300" style="position:absolute;right:-2px;top:190px;width:252px;height:309px">
        <defs>
          <linearGradient id="rb-dt" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#3b5bdb"></stop>
            <stop offset="1" stop-color="#c04de0"></stop>
          </linearGradient>
        </defs>
        <g class="rb-float">
          <g class="rb-antenna">
            <line x1="150" y1="30" x2="150" y2="62" stroke="#0a0b10" stroke-width="8" stroke-linecap="round"></line>
            <circle class="rb-bulb" cx="150" cy="26" r="13" fill="#f08c00"></circle>
          </g>
          <rect class="rb-arm rb-arm-l" x="20" y="130" width="36" height="70" rx="16" fill="#0a0b10"></rect>
          <rect class="rb-arm rb-arm-r" x="244" y="130" width="36" height="70" rx="16" fill="#0a0b10"></rect>
          <rect x="44" y="60" width="212" height="180" rx="64" fill="url(#rb-dt)"></rect>
          <rect x="68" y="98" width="164" height="94" rx="42" fill="#0a0b10"></rect>
          <g class="rb-look">
            <ellipse class="rb-eye" cx="116" cy="145" rx="14" ry="18" fill="#6be3ff"></ellipse>
            <ellipse class="rb-eye" cx="184" cy="145" rx="14" ry="18" fill="#6be3ff"></ellipse>
          </g>
        </g>
      </svg>

      <div class="abs pill" style="left:780px;top:196px;transform:rotate(-6deg);background:linear-gradient(90deg,#3b5bdb,#c04de0);color:#fff;box-shadow:0 8px 20px rgba(59,91,219,.25)">Gim Edukasi</div>
      <div class="abs pill" style="left:850px;top:272px;transform:rotate(5deg);background:#fff;border:2px solid #0a0b10">Media Interaktif</div>
      <div class="abs pill" style="left:790px;top:348px;transform:rotate(-4deg);background:#f08c00;color:#0a0b10;font-weight:800">BTIKP 2026</div>

      <p class="abs" style="left:40px;top:540px;margin:0;width:520px;font-size:19px;line-height:1.4">Ajang apresiasi karya digital pendidikan: Gim Edukasi dan Media Pembelajaran Interaktif.</p>
      <p class="abs" style="left:40px;top:604px;margin:0;font-size:16px;color:#4a4f5c">
        Terbuka untuk 17 Kab/Kota Sulawesi Tenggara.
        @if (isset($setting) && !$setting->isOpen())
          <span style="display:inline-block;margin-left:8px;padding:3px 12px;border-radius:999px;background:#ffe4e6;color:#e11d48;font-size:12px;font-weight:600">{{ $setting->getStatusMessage() }}</span>
        @endif
      </p>

      <!-- Tombol Masuk Portal Panitia & Juri -->
      <a href="{{ route('admin.login') }}" class="abs" style="left:40px;top:648px;display:flex;align-items:center;gap:18px;padding:0 16px 0 24px;height:54px;border-radius:18px;background:linear-gradient(90deg,#3b5bdb,#c04de0);color:#fff;font-weight:600;font-size:18px;box-shadow:0 10px 25px rgba(59,91,219,.3)">
        <span>Masuk Portal Panitia &amp; Juri</span>
        <span style="width:34px;height:34px;border-radius:10px;background:#fff;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 6px rgba(0,0,0,0.15)">
          <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#3b5bdb" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
        </span>
      </a>

      <!-- Information Chips -->
      <div class="abs" style="left:40px;top:728px;width:720px;display:flex;align-items:center;gap:24px;padding-top:16px;border-top:1px solid rgba(10,11,16,0.12);font-size:14px;color:#2c3345">
        <div style="display:flex;align-items:center;gap:10px">
          <div style="padding:8px;border-radius:10px;background:#fff;border:1px solid rgba(10,11,16,0.08);color:#3b5bdb;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 5px rgba(0,0,0,0.04)">
            <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
            </svg>
          </div>
          <span style="font-weight:600">Terbuka 17 Kab/Kota</span>
        </div>

        <div style="display:flex;align-items:center;gap:10px">
          <div style="padding:8px;border-radius:10px;background:#fff;border:1px solid rgba(10,11,16,0.08);color:#3b5bdb;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 5px rgba(0,0,0,0.04)">
            <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
            </svg>
          </div>
          <span style="font-weight:600">2 Kategori Pilihan</span>
        </div>

        <div style="display:flex;align-items:center;gap:10px">
          <div style="padding:8px;border-radius:10px;background:#fff;border:1px solid rgba(10,11,16,0.08);color:#3b5bdb;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 5px rgba(0,0,0,0.04)">
            <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
          </div>
          <span style="font-weight:600">Sertifikat &amp; Tiket Resmi</span>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ========================================================
     2. MOBILE & TABLET VIEWPORT (Screen <= 1024px)
     Fluid, Touch-Friendly, Naturally Flowing Layout
     ======================================================== -->
<div class="mobile-viewport">
  <canvas id="dot-canvas-mobile"></canvas>

  <div class="m-content" id="hero-mobile">
    <!-- Top Header -->
    <header class="m-header">
      <img draggable="false" class="m-logo" alt="Edutech Festival BTIKP 2026" src="{{ asset('logo.png') }}">

      <a href="{{ route('admin.login') }}" class="m-portal-btn">
        <svg style="width:15px;height:15px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
        </svg>
        <span>Portal Masuk</span>
      </a>
    </header>

    <!-- First Heading -->
    <h1 class="m-h1-left">INOVASI<br>DIGITAL</h1>

    <!-- Stats Bar -->
    <div class="m-stats-card">
      <div>
        <div class="m-stat-val">17</div>
        <div class="m-stat-lbl">kabupaten / kota</div>
      </div>
      <div class="m-stat-divider"></div>
      <div>
        <div class="m-stat-val">2</div>
        <div class="m-stat-lbl">kategori lomba</div>
      </div>
    </div>

    <!-- Animated Robot & Floating Badges -->
    <div class="m-robot-showcase">
      <!-- Badge Gim Edukasi -->
      <div class="m-pill m-pill-gim">Gim Edukasi</div>
      
      <!-- Badge Media Interaktif -->
      <div class="m-pill m-pill-mpi">Media Interaktif</div>

      <!-- Animated Robot SVG -->
      <svg class="m-robot-svg" aria-hidden="true" viewBox="0 0 300 300">
        <defs>
          <linearGradient id="rb-mob" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#3b5bdb"></stop>
            <stop offset="1" stop-color="#c04de0"></stop>
          </linearGradient>
        </defs>
        <g class="rb-float">
          <g class="rb-antenna">
            <line x1="150" y1="30" x2="150" y2="62" stroke="#0a0b10" stroke-width="8" stroke-linecap="round"></line>
            <circle class="rb-bulb" cx="150" cy="26" r="13" fill="#f08c00"></circle>
          </g>
          <rect class="rb-arm rb-arm-l" x="20" y="130" width="36" height="70" rx="16" fill="#0a0b10"></rect>
          <rect class="rb-arm rb-arm-r" x="244" y="130" width="36" height="70" rx="16" fill="#0a0b10"></rect>
          <rect x="44" y="60" width="212" height="180" rx="64" fill="url(#rb-mob)"></rect>
          <rect x="68" y="98" width="164" height="94" rx="42" fill="#0a0b10"></rect>
          <g class="rb-look">
            <ellipse class="rb-eye" cx="116" cy="145" rx="14" ry="18" fill="#6be3ff"></ellipse>
            <ellipse class="rb-eye" cx="184" cy="145" rx="14" ry="18" fill="#6be3ff"></ellipse>
          </g>
        </g>
      </svg>

      <!-- Badge BTIKP 2026 -->
      <div class="m-pill m-pill-btikp">BTIKP 2026</div>
    </div>

    <!-- Second Heading -->
    <h1 class="m-h1-right">EDUTECH<br>FESTIVAL</h1>

    <!-- Description & Schedule Status -->
    <div class="m-desc-box">
      <p class="m-desc-main">Ajang apresiasi karya digital pendidikan: Gim Edukasi dan Media Pembelajaran Interaktif.</p>
      <p class="m-desc-sub">
        <span>Terbuka untuk 17 Kab/Kota Sulawesi Tenggara.</span>
        @if (isset($setting) && !$setting->isOpen())
          <span class="m-closed-badge">{{ $setting->getStatusMessage() }}</span>
        @endif
      </p>
    </div>

    <!-- Action Button (Masuk Portal Panitia & Juri) -->
    <a href="{{ route('admin.login') }}" class="m-cta-btn">
      <span>Masuk Portal Panitia &amp; Juri</span>
      <span class="m-cta-icon">
        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#3b5bdb" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
      </span>
    </a>

    <!-- Highlights / Info Chips -->
    <div class="m-chips-grid">
      <div class="m-chip-card">
        <div class="m-chip-icon">
          <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
          </svg>
        </div>
        <span class="m-chip-text">Terbuka 17 Kab/Kota</span>
      </div>

      <div class="m-chip-card">
        <div class="m-chip-icon">
          <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
          </svg>
        </div>
        <span class="m-chip-text">2 Kategori Pilihan</span>
      </div>

      <div class="m-chip-card">
        <div class="m-chip-icon">
          <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
          </svg>
        </div>
        <span class="m-chip-text">Sertifikat &amp; Tiket Resmi</span>
      </div>
    </div>
  </div>
</div>

<script>
(function () {
  /* ===== 1. DESKTOP FIT SCALER ===== */
  var sc = document.getElementById('scaler');
  function fit() {
    if (window.innerWidth >= 1025 && sc) {
      var s = Math.min(window.innerWidth / 1440, window.innerHeight / 884);
      sc.style.transform = 'scale(' + s + ')';
    }
  }
  window.addEventListener('resize', fit);
  fit();

  /* ===== 2. INTERACTIVE DOT GRID CANVAS (DESKTOP & MOBILE) ===== */
  function setupDotGrid(canvasId, containerId, isFixedScale) {
    var canvas = document.getElementById(canvasId);
    var container = document.getElementById(containerId);
    if (!canvas || !container) return;

    var ctx = canvas.getContext('2d');
    var dpr = Math.min(window.devicePixelRatio || 1, 2);

    var config = {
      gap: 28,
      baseRadius: 1.5,
      maxRadius: 2.8,
      influenceRadius: 170,
      pullStrength: 0.14,
      ease: 0.12,
      baseColor: 'rgba(148, 163, 184, 0.55)',
      activeColor: 'rgba(129, 140, 248, 0.95)'
    };

    var width, height, dots = [];

    function resize() {
      if (isFixedScale) {
        width = 1440;
        height = 884;
      } else {
        width = container.offsetWidth || window.innerWidth;
        height = container.offsetHeight || window.innerHeight;
      }

      canvas.width = Math.floor(width * dpr);
      canvas.height = Math.floor(height * dpr);
      ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

      dots = [];
      var cols = Math.ceil(width / config.gap) + 1;
      var rows = Math.ceil(height / config.gap) + 1;
      for (var r = 0; r < rows; r++) {
        for (var c = 0; c < cols; c++) {
          var ox = 14 + c * config.gap;
          var oy = 14 + r * config.gap;
          dots.push({ originX: ox, originY: oy, x: ox, y: oy, radius: config.baseRadius });
        }
      }
    }

    resize();
    window.addEventListener('resize', resize);

    var pointer = { x: -9999, y: -9999, active: false };

    function updatePointer(e) {
      var rect = container.getBoundingClientRect();
      var clientX = e.clientX;
      var clientY = e.clientY;

      if (e.touches && e.touches.length > 0) {
        clientX = e.touches[0].clientX;
        clientY = e.touches[0].clientY;
      }

      var scale = isFixedScale ? (rect.width / 1440 || 1) : 1;
      pointer.x = (clientX - rect.left) / scale;
      pointer.y = (clientY - rect.top) / scale;
      pointer.active = true;
    }

    container.addEventListener('pointermove', updatePointer, { passive: true });
    container.addEventListener('touchmove', updatePointer, { passive: true });
    container.addEventListener('touchstart', updatePointer, { passive: true });

    container.addEventListener('pointerleave', function () { pointer.active = false; });
    container.addEventListener('touchend', function () { pointer.active = false; });

    function animate() {
      // Hanya gambar jika canvas sedang terlihat di viewport
      if (canvas.offsetParent !== null) {
        ctx.clearRect(0, 0, width, height);
        for (var i = 0; i < dots.length; i++) {
          var dot = dots[i];
          var targetX = dot.originX;
          var targetY = dot.originY;
          var targetRadius = config.baseRadius;
          var isNear = false;

          if (pointer.active) {
            var dx = pointer.x - dot.originX;
            var dy = pointer.y - dot.originY;
            var dist = Math.hypot(dx, dy);
            if (dist < config.influenceRadius) {
              isNear = true;
              var falloff = 1 - dist / config.influenceRadius;
              var smooth = falloff * falloff;
              targetX = dot.originX + dx * config.pullStrength * smooth;
              targetY = dot.originY + dy * config.pullStrength * smooth;
              targetRadius = config.baseRadius + (config.maxRadius - config.baseRadius) * smooth;
            }
          }

          dot.x += (targetX - dot.x) * config.ease;
          dot.y += (targetY - dot.y) * config.ease;
          dot.radius += (targetRadius - dot.radius) * config.ease;

          ctx.beginPath();
          ctx.arc(dot.x, dot.y, dot.radius, 0, Math.PI * 2);
          ctx.fillStyle = isNear ? config.activeColor : config.baseColor;
          ctx.fill();
        }
      }
      requestAnimationFrame(animate);
    }
    animate();
  }

  setupDotGrid('dot-canvas-desktop', 'hero-desktop', true);
  setupDotGrid('dot-canvas-mobile', 'hero-mobile', false);
})();
</script>
</body>
</html>
