<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>@yield('title','Terjadi Kesalahan')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg:#0f141b; --surface:#1b222c; --surface-alt:#212b36; --border:#2b3542; --primary:#3b82f6; --primary-accent:#60a5fa; --danger:#ef4444; --radius:20px; --radius-sm:12px; --grad:linear-gradient(135deg,#2563eb 0%,#3b82f6 40%,#60a5fa 70%,#93c5fd 100%);} 
        [data-theme=light]{ --bg:#f5f7fb; --surface:#ffffff; --surface-alt:#f1f5f9; --border:#e2e8f0; --primary:#2563eb; --primary-accent:#1d4ed8; --grad:linear-gradient(135deg,#1d4ed8 0%,#2563eb 35%,#3b82f6 70%,#60a5fa 100%);}        
        body{margin:0;font-family:'Inter',system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Ubuntu,sans-serif;background:var(--bg);color:#fff;min-height:100dvh;display:flex;flex-direction:column;}
        .page-wrap{flex:1;display:flex;align-items:center;justify-content:center;padding:2.8rem 1.4rem;}
        .err-shell{position:relative;display:grid;grid-template-columns:1fr 1fr;gap:3rem;max-width:1150px;width:100%;background:var(--surface);border:1px solid var(--border);border-radius:calc(var(--radius)+6px);padding:3.3rem 3.6rem;overflow:hidden;box-shadow:0 12px 46px -12px rgba(0,0,0,.55),0 6px 26px -6px rgba(0,0,0,.4);}        
        @media (max-width: 980px){.err-shell{grid-template-columns:1fr;padding:2.4rem 1.7rem;}}
        h1.err-code{font-size:clamp(2.8rem,7.2vw,4.8rem);line-height:1.05;font-weight:700;margin:0;letter-spacing:-1px;background:var(--grad);-webkit-background-clip:text;background-clip:text;color:transparent;position:relative;}
        h1.err-code:after{content:attr(data-glow);position:absolute;inset:0;filter:blur(34px) brightness(1.3) saturate(1.45);opacity:.35;background:var(--grad);z-index:-1;}
        .lead{font-size:1.05rem;font-weight:500;color:#cbd5e1;margin:.85rem 0 1.35rem;}
        .glass{background:linear-gradient(150deg,rgba(255,255,255,.06),rgba(255,255,255,.02));border:1px solid rgba(255,255,255,.08);backdrop-filter:blur(6px);padding:1rem 1.25rem;border-radius:var(--radius-sm);font-size:.75rem;line-height:1.15rem;color:#cbd5e1;}
        .actions{display:flex;flex-wrap:wrap;gap:.9rem;margin-top:1.25rem;}
        .btn-soft{--btn-bg:var(--surface-alt);--btn-border:var(--border);background:var(--btn-bg);border:1px solid var(--btn-border);color:#f1f5f9;font-weight:600;font-size:.8rem;padding:.85rem 1.15rem;border-radius:14px;display:inline-flex;align-items:center;gap:.55rem;position:relative;overflow:hidden;transition:.35s cubic-bezier(.4,0,.2,1);}
        .btn-soft:before{content:"";position:absolute;inset:0;background:linear-gradient(90deg,rgba(255,255,255,.08),transparent 40%,transparent 60%,rgba(255,255,255,.08));background-size:220% 100%;transform:translateX(-40%);transition:1.2s;opacity:0;}
        .btn-soft:hover:before{transform:translateX(0);opacity:1;}
        .btn-soft:hover{color:#fff;box-shadow:0 6px 28px -10px rgba(0,0,0,.6),0 4px 14px -4px rgba(0,0,0,.45);}        
        .btn-primary-grad{background:var(--grad);border:none;color:#fff;}
        .illus{position:relative;display:flex;align-items:center;justify-content:center;}
        svg.decor{width:100%;max-width:360px;filter:drop-shadow(0 12px 32px rgba(0,0,0,.55));}
        @media (max-width:980px){.illus{order:-1;} svg.decor{max-width:250px;margin-bottom:1.3rem;}}
        @keyframes floaty {0%{transform:translateY(0);}50%{transform:translateY(-14px);}100%{transform:translateY(0);} }
        .float{animation:floaty 8s ease-in-out infinite;}
        .meta{font-size:.65rem;text-transform:uppercase;letter-spacing:.18em;font-weight:600;color:#64748b;margin-bottom:.85rem;display:flex;align-items:center;gap:.45rem;}
        .theme-toggle{position:fixed;top:14px;right:14px;border:1px solid var(--border);background:var(--surface-alt);color:#cbd5e1;padding:.45rem .8rem;border-radius:12px;font-size:.7rem;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:.4rem;}
        footer{padding:1.4rem 1rem 2rem;text-align:center;font-size:.7rem;color:#64748b;}
        .code-chip{background:#1e293b;color:#93c5fd;font-weight:600;font-size:.75rem;padding:.35rem .55rem;border-radius:8px;display:inline-flex;align-items:center;gap:.35rem;letter-spacing:.5px;}
    </style>
    @stack('error-head')
</head>
<body>
<button class="theme-toggle" id="themeToggle" type="button">🌙 <span class="d-none d-sm-inline">Tema</span></button>
<div class="page-wrap">
    <div class="err-shell surface-appear">
        @yield('illustration')
        <div>
            <div class="meta">
                <span class="code-chip">ERROR @yield('code')</span>
                <span>@yield('label')</span>
            </div>
            <h1 class="err-code" data-glow="@yield('code')">@yield('headline')</h1>
            <p class="lead">@yield('message')</p>
            @yield('extra')
            <div class="actions">
                <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="btn-soft btn-primary-grad" style="box-shadow:0 6px 22px -6px rgba(37,99,235,.55);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12L12 3l9 9"/><path d="M9 21V9h6v12"/></svg>
                    <span>Kembali {{ auth()->check() ? 'Dashboard' : 'Beranda' }}</span>
                </a>
                <a href="javascript:history.back()" class="btn-soft">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l-6-6 6-6"/><path d="M21 12H4"/></svg>
                    <span>Halaman Sebelumnya</span>
                </a>
                <a href="mailto:{{ config('mail.from.address') }}?subject=Permintaan%20Bantuan%20Error%20@yield('code')" class="btn-soft" style="border-color:#334155;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4z"/><path d="M4 4l8 8 8-8"/></svg>
                    <span>Hubungi Support</span>
                </a>
            </div>
        </div>
    </div>
</div>
<footer>&copy; {{ date('Y') }} {{ config('app.name') }} • Semua hak dilindungi.</footer>
<script>
(function(){
  const root=document.documentElement;const key='error-theme';
  const btn=document.getElementById('themeToggle');
  const saved=localStorage.getItem(key);if(saved){root.setAttribute('data-theme',saved);} else if(window.matchMedia('(prefers-color-scheme: dark)').matches){root.setAttribute('data-theme','dark');}
  btn?.addEventListener('click',()=>{const cur=root.getAttribute('data-theme')==='dark'?'light':'dark';root.setAttribute('data-theme',cur);localStorage.setItem(key,cur);});
})();
</script>
@stack('error-scripts')
</body>
</html>
