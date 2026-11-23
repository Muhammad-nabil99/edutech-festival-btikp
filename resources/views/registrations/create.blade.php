<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>EduTech Festival BTIKP 2026 — Inovasi Digital</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap">
<style>
:root { --bg: #eeeef2; --fg: #0a0b10; --muted: #4a4f5c; }
* { box-sizing: border-box; }
html, body {
  margin: 0;
  padding: 0;
  width: 100%;
  height: 100%;
  overflow: hidden;
  background: var(--bg);
  color: var(--fg);
  font-family: Inter, system-ui, sans-serif;
}
a { color: inherit; text-decoration: none; }
.viewport {
  width: 100vw;
  height: 100vh;
  overflow: hidden;
  display: flex;
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

/* ====== Hero Full & Fit ====== */
.hero {
  position: relative;
  width: 1440px;
  height: 884px;
  background: #eeeef2;
  overflow: hidden;
  color: #0a0b10;
}
#dot-canvas {
  position: absolute;
  left: 0;
  top: 0;
  width: 1440px;
  height: 884px;
  pointer-events: none;
}
.abs { position: absolute; }
.logo { left: 40px; top: 22px; width: 92px; height: 80px; border-radius: 18px; background: #fff; display: flex; flex-direction: column; align-items: center; justify-content: center; line-height: 1; }
h1 { position: absolute; margin: 0; font-size: 165px; font-weight: 800; line-height: 142px; letter-spacing: -.045em; }
.pill { padding: 12px 22px; border-radius: 999px; font-weight: 600; font-size: 18px; }

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
</style>
</head>
<body>

<div class="viewport" id="viewport">
<div id="scaler">
<div class="hero" id="hero">
  <canvas id="dot-canvas"></canvas>

  <img draggable="false" class="abs" alt="Edutech Festival BTIKP 2026" src="{{ asset('logo.png') }}" style="left:40px;top:22px;width:100px;height:100px;object-fit:contain">

  <!-- Tombol Portal Masuk (Sudut Kanan Atas) -->
  <a href="{{ route('admin.login') }}" class="abs" style="right:40px;top:44px;padding:0 26px;height:42px;display:flex;align-items:center;gap:8px;border-radius:999px;background:#0a0b10;color:#fff;font-weight:600;font-size:15px;box-shadow:0 4px 14px rgba(0,0,0,0.15);transition:transform .15s ease,background .15s ease">
    <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
    </svg>
    <span>Portal Masuk</span>
  </a>

  <h1 style="left:40px;top:150px">INOVASI<br>DIGITAL</h1>
  <h1 style="right:38px;top:462px;text-align:right">EDUTECH<br>FESTIVAL</h1>

  <div class="abs" style="right:40px;top:118px;display:flex;align-items:center;gap:28px;text-align:right">
    <div><div style="font-size:60px;font-weight:800;line-height:1">17</div><div style="font-size:15px;color:#4a4f5c;margin-top:4px">kabupaten / kota</div></div>
    <div style="width:1px;height:64px;background:#9a9eae"></div>
    <div><div style="font-size:60px;font-weight:800;line-height:1">2</div><div style="font-size:15px;color:#4a4f5c;margin-top:4px">kategori lomba</div></div>
  </div>

  <!-- ===== ROBOT (versi beranimasi) ===== -->
  <svg class="rb" aria-hidden="true" viewBox="0 0 300 300" style="position:absolute;right:-2px;top:190px;width:252px;height:309px">
    <defs>
      <linearGradient id="rb" x1="0" y1="0" x2="1" y2="1">
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
      <rect x="44" y="60" width="212" height="180" rx="64" fill="url(#rb)"></rect>
      <rect x="68" y="98" width="164" height="94" rx="42" fill="#0a0b10"></rect>
      <g class="rb-look">
        <ellipse class="rb-eye" cx="116" cy="145" rx="14" ry="18" fill="#6be3ff"></ellipse>
        <ellipse class="rb-eye" cx="184" cy="145" rx="14" ry="18" fill="#6be3ff"></ellipse>
      </g>
      <g class="rb-badge">
        <rect x="196" y="204" width="46" height="26" rx="8" fill="#fff"></rect>
        <text x="219" y="223" text-anchor="middle" font-family="Inter,sans-serif" font-weight="800" font-size="16" fill="#3b5bdb">AI</text>
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

<script>
(function () {
  var vp = document.getElementById('viewport');
  var sc = document.getElementById('scaler');
  function fit() {
    var s = Math.min(window.innerWidth / 1440, window.innerHeight / 884);
    sc.style.transform = 'scale(' + s + ')';
  }
  window.addEventListener('resize', fit);
  fit();

  /* ===== Dot grid interaktif (canvas) ===== */
  (function () {
    var hero = document.getElementById('hero');
    var canvas = document.getElementById('dot-canvas');
    var ctx = canvas.getContext('2d');
    var width = 1440, height = 884;
    var dpr = Math.min(window.devicePixelRatio || 1, 2);

    var config = {
      gap: 28,
      baseRadius: 1.5,
      maxRadius: 2.6,
      influenceRadius: 180,
      pullStrength: 0.14,
      ease: 0.12,
      baseColor: 'rgba(148, 163, 184, 0.65)',
      activeColor: 'rgba(129, 140, 248, 0.95)'
    };

    canvas.width = Math.floor(width * dpr);
    canvas.height = Math.floor(height * dpr);
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

    var dots = [];
    var cols = Math.ceil(width / config.gap) + 1;
    var rows = Math.ceil(height / config.gap) + 1;
    for (var r = 0; r < rows; r++) {
      for (var c = 0; c < cols; c++) {
        var ox = 14 + c * config.gap;
        var oy = 14 + r * config.gap;
        dots.push({ originX: ox, originY: oy, x: ox, y: oy, radius: config.baseRadius });
      }
    }

    var pointer = { x: -9999, y: -9999, active: false };

    hero.addEventListener('pointermove', function (e) {
      var rect = hero.getBoundingClientRect();
      var scale = rect.width / hero.offsetWidth || 1;
      pointer.x = (e.clientX - rect.left) / scale;
      pointer.y = (e.clientY - rect.top) / scale;
      pointer.active = true;
    });
    hero.addEventListener('pointerleave', function () { pointer.active = false; });

    function animate() {
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
      requestAnimationFrame(animate);
    }
    animate();
  })();
})();
</script>
</body>
</html>
