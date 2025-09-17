@php($user = auth()->user())
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpanjang KTA</title>
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
                <a class="nav-link" href="{{ route('kta') }}">KTA</a>
                <a class="nav-link active" href="{{ route('kta.renew.form') }}">Perpanjang KTA</a>
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
        <h1 class="h5 fw-semibold mb-4">Perpanjang KTA</h1>
        @if(session('success'))<div class="alert alert-success small">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="alert alert-danger small">{{ session('error') }}</div>@endif
        <div class="card border-0 shadow-sm mb-4" style="max-width:860px;">
            <div class="card-body small">
                <p class="text-secondary">Gunakan halaman ini untuk memperpanjang masa berlaku kartu anggota Anda selama 1 tahun ke depan.</p>
                @if(isset($pendingInvoice) && $pendingInvoice)
                    <div class="alert alert-warning d-flex flex-column gap-1">
                        <div class="small mb-0">Invoice perpanjangan sedang menunggu pembayaran / verifikasi.</div>
                        <div class="small mb-0">Nomor: <strong>{{ $pendingInvoice->number }}</strong> • Status: <span class="badge text-bg-{{ $pendingInvoice->status==='awaiting_verification'?'warning text-dark':($pendingInvoice->status==='unpaid'?'secondary':'success') }}">{{ strtoupper($pendingInvoice->status) }}</span></div>
                        <div><a class="btn btn-sm btn-outline-primary" href="{{ route('pembayaran',['invoice'=>$pendingInvoice->id]) }}">Lihat Invoice</a></div>
                    </div>
                @endif
                <div class="table-responsive mb-3" style="max-width:620px;">
                    <table class="table table-sm mb-0">
                        <tbody>
                            <tr><th class="text-secondary" style="width:220px;">Nama</th><td>{{ $user->name }}</td></tr>
                            <tr><th class="text-secondary">No KTA</th><td>{{ $user->membership_card_number ?? '-' }}</td></tr>
                            <tr><th class="text-secondary">Masa Berlaku Saat Ini</th><td>{{ $currentExpiry? $currentExpiry->format('d M Y') : '-' }}</td></tr>
                            <tr><th class="text-secondary">Masa Berlaku Setelah Perpanjangan</th><td>{{ $proposed->format('d M Y') }}</td></tr>
                            <tr><th class="text-secondary">Biaya Perpanjangan</th><td>Rp {{ number_format($amount,0,',','.') }}</td></tr>
                        </tbody>
                    </table>
                </div>
                @php($confirmMsg = "Perpanjang masa berlaku KTA sampai ".$proposed->format('d M Y')."?")
                @if(empty($pendingInvoice))
                    <form method="POST" action="{{ route('kta.renew.submit') }}" data-confirm="{{ $confirmMsg }}" onsubmit="return confirm(this.getAttribute('data-confirm'));" class="mb-3">
                        @csrf
                        <button class="btn btn-primary btn-sm">Perpanjang Sekarang</button>
                        <a href="{{ route('kta') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
                    </form>
                @else
                    <a href="{{ route('kta') }}" class="btn btn-outline-secondary btn-sm mb-3">Kembali</a>
                @endif
                <h6 class="fw-semibold mb-2">Riwayat Perpanjangan</h6>
                <div class="table-responsive" style="max-height:320px;">
                    <table class="table table-sm align-middle small">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Masa Berlaku Sebelumnya</th>
                                <th>Masa Berlaku Baru</th>
                                <th>Biaya (Rp)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($renewals as $r)
                                <tr>
                                    <td>{{ $r->created_at->format('d M Y H:i') }}</td>
                                    <td>{{ $r->previous_expires_at? $r->previous_expires_at->format('d M Y') : '-' }}</td>
                                    <td>{{ $r->new_expires_at->format('d M Y') }}</td>
                                    <td>{{ number_format($r->amount,0,',','.') }}</td>
                                    @php($inv = $r->invoice)
                                    <td>
                                        @if($inv)
                                            @switch($inv->status)
                                                @case('paid') <span class="badge text-bg-success">DITERIMA</span> @break
                                                @case('rejected') <span class="badge text-bg-danger">DITOLAK</span> @break
                                                @case('awaiting_verification') <span class="badge text-bg-warning text-dark">MENUNGGU</span> @break
                                                @default <span class="badge text-bg-secondary">UNPAID</span>
                                            @endswitch
                                        @else
                                            <span class="badge text-bg-light text-secondary">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-secondary">Belum ada perpanjangan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
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
