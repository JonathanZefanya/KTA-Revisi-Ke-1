@php($user = auth()->user())
@php($companies = $user->companies()->latest()->get())
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{background:#f5f7fb;font-family:system-ui,-apple-system,Segoe UI,Inter,Roboto,Ubuntu,sans-serif;}
        .sidebar{width:240px;background:#fff;position:fixed;top:0;bottom:0;left:0;border-right:1px solid #e5e7eb;padding:1.25rem 1rem;transition:transform .3s ease;z-index:1040;}
        .sidebar .nav-link{color:#555;border-radius:.5rem;font-weight:500;padding:.55rem .9rem;}
        .sidebar .nav-link.active{background:#0d6efd;color:#fff;}
        .main{margin-left:240px;padding:2rem;transition:margin-left .3s ease;}
        .topbar{background:#fff;border-bottom:1px solid #e5e7eb;padding:.75rem 1.25rem;margin:-2rem -2rem 2rem -2rem;display:flex;justify-content:space-between;align-items:center;}
        .table-sm td, .table-sm th{padding:.4rem .5rem;}
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
            <a class="nav-link active" href="{{ route('dashboard') }}">Dashboard</a>
            @if($user->approved_at)
                <a class="nav-link" href="{{ route('pembayaran') }}">Pembayaran</a>
                <a class="nav-link" href="{{ route('kta') }}">KTA</a>
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
        @if(session('success'))
            <div class="alert alert-success small">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger small">{{ session('error') }}</div>
        @endif
        @if(is_null($user->approved_at))
            <div class="alert alert-warning small d-flex align-items-start gap-2 mb-4">
                <div>
                    <strong>Akun Belum Terverifikasi.</strong><br>
                    Menunggu persetujuan admin. Jika perlu cepat, hubungi admin.
                </div>
            </div>
            <h1 class="h5 fw-semibold mb-4">Status Akun</h1>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-2">Menunggu Verifikasi</h5>
                            <p class="text-secondary small mb-3">Terima kasih telah mendaftar. Akun Anda sedang menunggu persetujuan admin sebelum dapat mengakses fitur penuh.</p>
                            <ul class="small text-secondary mb-3">
                                <li>Pastikan data & dokumen yang diunggah sudah benar.</li>
                                <li>Jika butuh percepatan, hubungi admin.</li>
                                <li>Anda akan melihat fitur lengkap setelah disetujui.</li>
                            </ul>
                            <span class="badge text-bg-warning text-dark">Pending Approval</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-2">Apa Selanjutnya?</h5>
                            <p class="text-secondary small mb-2">Sementara menunggu, Anda bisa:</p>
                            <ul class="small text-secondary mb-0">
                                <li>Mempersiapkan dokumen tambahan bila diperlukan.</li>
                                <li>Mencatat nomor NPWP / NIB untuk referensi.</li>
                                <li>Menghubungi support jika ada kesalahan input.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h1 class="h5 fw-semibold mb-0">Dashboard</h1>
                <span class="badge text-bg-success">Terverifikasi</span>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body small">
                            <div class="text-secondary text-uppercase fw-semibold mb-1" style="font-size:.65rem;letter-spacing:.5px;">Total Badan Usaha</div>
                            <div class="fs-4 fw-semibold">{{ $companies->count() }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body small">
                            <div class="text-secondary text-uppercase fw-semibold mb-2" style="font-size:.65rem;letter-spacing:.5px;">Info Akun</div>
                            <p class="mb-1">Terakhir login: <span class="text-secondary">{{ $user->updated_at?->format('d/m/Y H:i') }}</span></p>
                            <p class="mb-0">Email: <span class="text-secondary">{{ $user->email }}</span></p>
                        </div>
                    </div>
                </div>
            </div>
            <h2 class="h6 fw-semibold mb-3">Data Badan Usaha</h2>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr class="small">
                                    <th>Nama</th>
                                    <th>Jenis / Kualifikasi</th>
                                    <th>NPWP</th>
                                    <th>Wilayah</th>
                                    <th>Dokumen</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                @forelse($companies as $c)
                                    <tr>
                                        <td class="fw-semibold">{{ $c->name }}</td>
                                        <td>{{ $c->jenis ?? '-' }} / {{ $c->kualifikasi ?? '-' }}</td>
                                        <td>{{ $c->npwp ?? '-' }}</td>
                                        <td>{{ $c->city_name }}, {{ $c->province_name }}</td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                @if($c->photo_pjbu_path)<a class="badge rounded-pill text-bg-secondary text-decoration-none" target="_blank" href="{{ asset('storage/'.$c->photo_pjbu_path) }}">Foto PJBU</a>@endif
                                                @if($c->npwp_bu_path)<a class="badge rounded-pill text-bg-secondary text-decoration-none" target="_blank" href="{{ asset('storage/'.$c->npwp_bu_path) }}">NPWP BU</a>@endif
                                                @if($c->nib_file_path)<a class="badge rounded-pill text-bg-secondary text-decoration-none" target="_blank" href="{{ asset('storage/'.$c->nib_file_path) }}">NIB</a>@endif
                                                @if($c->ktp_pjbu_path)<a class="badge rounded-pill text-bg-secondary text-decoration-none" target="_blank" href="{{ asset('storage/'.$c->ktp_pjbu_path) }}">KTP PJBU</a>@endif
                                                @if($c->npwp_pjbu_path)<a class="badge rounded-pill text-bg-secondary text-decoration-none" target="_blank" href="{{ asset('storage/'.$c->npwp_pjbu_path) }}">NPWP PJBU</a>@endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-secondary">Belum ada data badan usaha.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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