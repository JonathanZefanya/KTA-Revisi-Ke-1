<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>@yield('title', config('app.name'))</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --ui-bg:#f5f7fb;--ui-surface:#ffffff;--ui-surface-alt:#f9fafc;--ui-border:#e5e7eb;--ui-border-soft:#eef0f3;
            --ui-primary:#2563eb;--ui-primary-hover:#1d4ed8;--ui-danger:#dc2626;--ui-radius:14px;--ui-radius-sm:8px;
            --ui-shadow:0 4px 16px -4px rgba(0,0,0,.04),0 2px 6px -1px rgba(0,0,0,.04);
            --ui-shadow-hover:0 6px 28px -6px rgba(0,0,0,.08),0 4px 12px -2px rgba(0,0,0,.06);
            --ui-transition:.22s cubic-bezier(.4,0,.2,1);
        }
        [data-theme=dark] {
            --ui-bg:#0f141b;--ui-surface:#1b222c;--ui-surface-alt:#212b36;--ui-border:#2b3542;--ui-border-soft:#313d4a;
            --ui-primary:#3b82f6;--ui-primary-hover:#1d4ed8;--ui-danger:#ef4444;--ui-shadow:0 4px 20px -4px rgba(0,0,0,.55),0 2px 8px -2px rgba(0,0,0,.5);--ui-shadow-hover:0 6px 28px -6px rgba(0,0,0,.6),0 4px 12px -2px rgba(0,0,0,.5);
        }
        body {background:var(--ui-bg);font-family:'Inter',system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Ubuntu,sans-serif;color:#111;font-size:15px;}
        a {text-decoration:none;}
        .layout {display:flex;min-height:100dvh;}
        .sidebar {width:250px;background:var(--ui-surface);border-right:1px solid var(--ui-border);padding:1.25rem .95rem;display:flex;flex-direction:column;gap:1rem;position:fixed;inset:0 auto 0 0;z-index:1040;transition:transform var(--ui-transition),box-shadow var(--ui-transition);}        
        .brand {font-weight:600;font-size:1rem;letter-spacing:.5px;display:flex;align-items:center;gap:.5rem;color:var(--ui-primary)}
        .nav-links a {display:flex;align-items:center;gap:.65rem;padding:.55rem .8rem;border-radius:var(--ui-radius-sm);font-weight:500;color:#4b5563;font-size:.83rem;transition:background var(--ui-transition),color var(--ui-transition);}        
        .nav-links a.active {background:var(--ui-primary);color:#fff;box-shadow:0 0 0 1px rgba(255,255,255,.08) inset;}        
        .nav-links a:not(.active):hover {background:var(--ui-surface-alt);color:#111;}
        .main {flex:1;margin-left:250px;display:flex;flex-direction:column;min-height:100dvh;}
        .topbar {height:60px;background:var(--ui-surface);border-bottom:1px solid var(--ui-border);display:flex;align-items:center;justify-content:space-between;padding:0 1.25rem;position:sticky;top:0;z-index:1020;}
        .surface {background:var(--ui-surface);border:1px solid var(--ui-border-soft);border-radius:var(--ui-radius);box-shadow:var(--ui-shadow);transition:box-shadow var(--ui-transition),transform var(--ui-transition);}
        .surface:hover {box-shadow:var(--ui-shadow-hover);}
        .content-wrapper {padding:1.75rem 1.75rem 2.5rem;flex:1;}
        .flash-area .alert {border:none;border-radius:var(--ui-radius-sm);box-shadow:var(--ui-shadow);font-size:.8rem;padding:.6rem .9rem;margin-bottom:.75rem;}
        .btn-primary {background:var(--ui-primary);border-color:var(--ui-primary);} .btn-primary:hover {background:var(--ui-primary-hover);border-color:var(--ui-primary-hover);}        
        .hamburger{display:none;width:42px;height:42px;border:1px solid var(--ui-border);border-radius:12px;background:var(--ui-surface);align-items:center;justify-content:center;cursor:pointer;transition:var(--ui-transition);}        
        .hamburger span{width:20px;height:2px;background:#111;position:relative;display:block;transition:var(--ui-transition);}        
        .hamburger span:before,.hamburger span:after{content:"";position:absolute;left:0;width:100%;height:2px;background:#111;transition:var(--ui-transition);}        
        .hamburger span:before{top:-6px;} .hamburger span:after{top:6px;}        
        .hamburger.active span{background:transparent;} .hamburger.active span:before{top:0;transform:rotate(45deg);} .hamburger.active span:after{top:0;transform:rotate(-45deg);}        
        .overlay{position:fixed;inset:0;background:rgba(0,0,0,.35);backdrop-filter:blur(2px);z-index:1030;opacity:0;pointer-events:none;transition:var(--ui-transition);}        
        .overlay.show{opacity:1;pointer-events:auto;}        
        .status-badge {display:inline-flex;align-items:center;gap:.35rem;font-size:.65rem;font-weight:600;padding:.35rem .55rem;border-radius:999px;letter-spacing:.5px;text-transform:uppercase;background:#edf2ff;color:#1e3a8a;}
        .status-badge.success{background:#ecfdf5;color:#065f46;}
        .status-badge.warning{background:#fff7ed;color:#9a3412;}
        .status-badge.danger{background:#fef2f2;color:#991b1b;}
        .status-badge.info{background:#eff6ff;color:#1d4ed8;}
        .status-badge.neutral{background:#f1f5f9;color:#334155;}
        .table-modern thead th {font-size:.65rem;letter-spacing:.5px;font-weight:600;color:#475569;text-transform:uppercase;border-bottom:1px solid var(--ui-border);}
        .table-modern td {font-size:.72rem;vertical-align:middle;}
        .divider {height:1px;background:var(--ui-border-soft);margin:1.25rem 0;}
        .theme-toggle {border:1px solid var(--ui-border);background:var(--ui-surface);border-radius:10px;padding:.4rem .75rem;font-size:.7rem;display:inline-flex;align-items:center;gap:.4rem;cursor:pointer;}
        /* Skeleton & subtle animations */
        @keyframes shimmer {0%{background-position:0 0;}100%{background-position:400% 0;}}
        .skeleton-block {position:relative;overflow:hidden;background:linear-gradient(90deg,var(--ui-surface-alt) 25%,var(--ui-border-soft) 37%,var(--ui-surface-alt) 63%);background-size:400% 100%;animation:shimmer 1.1s ease-in-out infinite;border-radius:6px;min-height:.8rem;color:transparent;}
        .skeleton-line {height:.7rem;margin:.3rem 0;}
        .skeleton-rounded {border-radius:999px;}
        .skeleton-table td > .skeleton-block {height:.7rem;}
        body.loading .show-when-loaded {display:none !important;}
        body:not(.loading) .show-during-loading {display:none !important;}
        /* Surface entrance */
        @keyframes surfaceIn {0%{opacity:0;transform:translateY(6px) scale(.98);}100%{opacity:1;transform:translateY(0) scale(1);} }
        .surface-appear {animation:surfaceIn .55s var(--ui-transition);}
        @media (max-width: 1000px){
            .sidebar {transform:translateX(-100%);box-shadow:none;}
            .sidebar.open {transform:translateX(0);box-shadow:0 8px 32px -8px rgba(0,0,0,.35);}            
            .main {margin-left:0;}
            .hamburger{display:inline-flex;}
        }
    </style>
    @stack('head')
</head>
<body>
<div class="layout">
    <div class="sidebar" id="userSidebar">
        <div class="brand">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l9 4-9 4-9-4 9-4z"/><path d="M3 10l9 4 9-4"/><path d="M3 18l9 4 9-4"/></svg>
            <span>{{ config('app.name') }}</span>
        </div>
        <nav class="nav-links flex-grow-1">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12l9-9 9 9"/><path d="M9 21V9h6v12"/></svg>
                <span>Dashboard</span>
            </a>
            @auth
                @if(auth()->user()->approved_at)
                <a href="{{ route('pembayaran') }}" class="{{ request()->routeIs('pembayaran') ? 'active' : '' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M7 10h1"/><path d="M7 14h1"/><rect x="3" y="4" width="18" height="16" rx="2"/></svg>
                    <span>Pembayaran</span>
                </a>
                <a href="{{ route('kta') }}" class="{{ request()->routeIs('kta') ? 'active' : '' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="14" rx="2"/><path d="M8 10h.01"/><path d="M12 10h.01"/><path d="M16 10h.01"/><path d="M8 14h8"/></svg>
                    <span>KTA</span>
                </a>
                <a href="{{ route('profile') }}" class="{{ request()->routeIs('profile') ? 'active' : '' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                    <span>Profile</span>
                </a>

                @if(auth()->user()->membership_card_number && auth()->user()->isEligibleForRenewal())
                <a href="{{ route('kta.renew.form') }}" class="{{ request()->routeIs('kta.renew.form') ? 'active' : '' }}" title="Perpanjangan KTA">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 0-9 9"/><path d="M21 12a9 9 0 0 1-9 9"/><path d="M7 12a5 5 0 0 1 10 0"/></svg>
                    <span>Perpanjang</span>
                </a>
                @endif
                @endif
            @endauth
        </nav>
        <div class="mt-auto small text-secondary">&copy; {{ date('Y') }} • <button class="theme-toggle" id="themeToggle" type="button" aria-label="Toggle tema">🌙 <span class="d-none d-sm-inline">Tema</span></button></div>
    </div>
    <div class="overlay" id="sidebarOverlay" hidden></div>
    <div class="main">
        <div class="topbar">
            <div class="d-flex align-items-center gap-2">
                <button class="hamburger" id="sidebarToggle" aria-label="Toggle menu" aria-controls="userSidebar" aria-expanded="false"><span></span></button>
                <div class="d-flex flex-column justify-content-center">
                    @auth
                        <span class="fw-semibold" style="font-size:.8rem;">{{ auth()->user()->name }}</span>
                        <span class="text-secondary" style="font-size:.7rem;">{{ auth()->user()->email }}</span>
                    @endauth
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                @auth
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger">Logout</button>
                </form>
                @endauth
            </div>
        </div>
        <div class="content-wrapper">
            <div class="flash-area">
                @if(session('success'))<div class="alert alert-success" role="alert">{{ session('success') }}</div>@endif
                @if(session('error'))<div class="alert alert-danger" role="alert">{{ session('error') }}</div>@endif
                @if($errors->any())<div class="alert alert-danger" role="alert">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
            </div>
            @yield('content')
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function(){
  const root=document.documentElement;const current=localStorage.getItem('ui-theme');if(current){root.setAttribute('data-theme',current);}  
  const toggle=document.getElementById('themeToggle');
  toggle?.addEventListener('click',()=>{const now=root.getAttribute('data-theme')==='dark'?'light':'dark';root.setAttribute('data-theme',now);localStorage.setItem('ui-theme',now);});
  const sidebar=document.getElementById('userSidebar');const overlay=document.getElementById('sidebarOverlay');const burger=document.getElementById('sidebarToggle');
  function open(){sidebar.classList.add('open');overlay.classList.add('show');overlay.hidden=false;burger.classList.add('active');burger.setAttribute('aria-expanded','true');document.body.style.overflow='hidden';}
  function close(){sidebar.classList.remove('open');overlay.classList.remove('show');setTimeout(()=>overlay.hidden=true,250);burger.classList.remove('active');burger.setAttribute('aria-expanded','false');document.body.style.overflow='';}
  burger?.addEventListener('click',()=>sidebar.classList.contains('open')?close():open());
  overlay?.addEventListener('click',close);window.addEventListener('keydown',e=>{if(e.key==='Escape'&&sidebar.classList.contains('open'))close();});
})();
</script>
@stack('scripts')
</body>
</html>