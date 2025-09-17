@php($user = auth()->user())
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KTA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{background:#f5f7fb;font-family:system-ui,-apple-system,Segoe UI,Inter,Roboto,Ubuntu,sans-serif;}
        .sidebar{width:240px;background:#fff;position:fixed;top:0;bottom:0;left:0;border-right:1px solid #e5e7eb;padding:1.25rem 1rem;transition:transform .3s ease;z-index:1040;}
        .sidebar .nav-link{color:#555;border-radius:.5rem;font-weight:500;padding:.55rem .9rem;}
        .sidebar .nav-link.active{background:#0d6efd;color:#fff;}
        .main{margin-left:240px;padding:2rem;transition:margin-left .3s ease;}
        .topbar{background:#fff;border-bottom:1px solid #e5e7eb;padding:.75rem 1.25rem;margin:-2rem -2rem 2rem -2rem;display:flex;justify-content:space-between;align-items:center;}
        .hamburger{display:none;align-items:center;justify-content:center;width:42px;height:42px;border:1px solid #e5e7eb;border-radius:12px;background:#fff;cursor:pointer;}
        .hamburger span{width:20px;height:2px;background:#111;position:relative;display:block;}
        .hamburger span:before,.hamburger span:after{content:"";position:absolute;left:0;width:100%;height:2px;background:#111;transition:.3s}
        .hamburger span:before{top:-6px;} .hamburger span:after{top:6px;}
        .hamburger.active span{background:transparent;} .hamburger.active span:before{top:0;transform:rotate(45deg);} .hamburger.active span:after{top:0;transform:rotate(-45deg);} 
        @media (max-width: 992px){
            .sidebar{transform:translateX(-100%);box-shadow:0 0 0 rgba(0,0,0,0);} 
            .sidebar.open{transform:translateX(0);box-shadow:0 8px 28px -6px rgba(0,0,0,.25);} 
            .main{margin-left:0;}
            .topbar{margin:-2rem -2rem 2rem -2rem;}
            .hamburger{display:inline-flex;}
            body.menu-open{overflow:hidden;}
            .overlay{position:fixed;inset:0;background:rgba(0,0,0,.35);backdrop-filter:blur(2px);z-index:1030;opacity:0;pointer-events:none;transition:.3s;}
            .overlay.show{opacity:1;pointer-events:auto;}
        }
    </style>
</head>
<body>
    <div class="sidebar" id="userSidebar" aria-label="Sidebar Navigasi">
        <div class="fw-semibold mb-3">{{ config('app.name') }}</div>
        <nav class="nav flex-column small mb-4">
            <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
            @if($user->approved_at)
                <a class="nav-link" href="{{ route('pembayaran') }}">Pembayaran</a>
                <a class="nav-link active" href="{{ route('kta') }}">KTA</a>
                <a class="nav-link" href="{{ route('kta.renew.form') }}">Perpanjang KTA</a>
            @endif
        </nav>
        <div class="small text-secondary">&copy; {{ date('Y') }}</div>
    </div>
    <div class="overlay" id="sidebarOverlay" hidden></div>
    <div class="main" id="mainContent">
        <div class="topbar">
            <div class="d-flex align-items-center gap-2">
                <button class="hamburger" id="sidebarToggle" aria-label="Toggle menu" aria-controls="userSidebar" aria-expanded="false"><span></span></button>
                <div class="small">
                <div class="fw-semibold">{{ $user->name }}</div>
                <div class="text-secondary">{{ $user->email }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button class="btn btn-outline-danger btn-sm">Logout</button>
            </form>
        </div>
        <h1 class="h5 fw-semibold mb-4">KTA</h1>
        @if($user->hasActiveMembershipCard())
            <div class="alert alert-success small d-flex justify-content-between align-items-center">
                <div>Kartu anggota aktif sampai {{ optional($user->membership_card_expires_at)->format('d M Y') }} (No: {{ $user->membership_card_number }})</div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('kta.card') }}" class="btn btn-sm btn-outline-primary">Preview</a>
                    <a href="{{ route('kta.pdf') }}" class="btn btn-sm btn-primary">Download PDF</a>
                </div>
            </div>
        @else
            <div class="alert alert-info small">Belum ada KTA diterbitkan. Kartu akan muncul otomatis setelah pembayaran pertama Anda terverifikasi (status invoice menjadi PAID).</div>
        @endif
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function(){
            const toggle=document.getElementById('sidebarToggle');
            const sidebar=document.getElementById('userSidebar');
            const overlay=document.getElementById('sidebarOverlay');
            function open(){sidebar.classList.add('open');overlay.classList.add('show');overlay.hidden=false;toggle.classList.add('active');toggle.setAttribute('aria-expanded','true');document.body.classList.add('menu-open');}
            function close(){sidebar.classList.remove('open');overlay.classList.remove('show');setTimeout(()=>overlay.hidden=true,300);toggle.classList.remove('active');toggle.setAttribute('aria-expanded','false');document.body.classList.remove('menu-open');}
            toggle?.addEventListener('click',()=>{sidebar.classList.contains('open')?close():open();});
            overlay?.addEventListener('click',close);
            window.addEventListener('keydown',e=>{if(e.key==='Escape' && sidebar.classList.contains('open')) close();});
        })();
    </script>
</body>
</html>
