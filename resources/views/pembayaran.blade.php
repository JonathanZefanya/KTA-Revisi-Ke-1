@php($user = auth()->user())
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran</title>
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
                <a class="nav-link active" href="{{ route('pembayaran') }}">Pembayaran</a>
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
        <h1 class="h5 fw-semibold mb-4">Pembayaran</h1>
        @php($invoices = $invoices ?? collect())
        @if($invoices->isEmpty())
            <div class="alert alert-info small">Belum ada tagihan pembayaran.<br>
                <ul class="mt-2 mb-0 ps-3">
                    <li>Akun mungkin belum disetujui admin.</li>
                    <li>Belum ada data perusahaan terhubung.</li>
                    <li>Invoice otomatis dibuat saat admin menyetujui user pertama kali.</li>
                </ul>
                <div class="mt-2">Jika Anda baru saja disetujui, <a href="" onclick="location.reload();return false;">refresh</a> halaman ini.</div>
            </div>
        @else
            <div class="row g-3 mb-4">
                @foreach($invoices as $inv)
                    <div class="col-md-6 col-lg-4">
                        <div class="p-3 border rounded bg-white h-100 d-flex flex-column @if(isset($selected) && $selected && $selected->id===$inv->id) border-primary @endif" style="border-color:#e5e7eb;">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-semibold">{{ $inv->number }}</span>
                                <span class="badge bg-{{ $inv->status==='paid'?'success':($inv->status==='cancelled'?'danger':'warning text-dark') }}">{{ strtoupper($inv->status) }}</span>
                            </div>
                            @php($due = $inv->due_date instanceof \Carbon\Carbon ? $inv->due_date : \Carbon\Carbon::parse($inv->due_date))
                            <div class="small text-secondary mb-2">{{ ucfirst($inv->type) }} • Jatuh tempo {{ $due->format('d M Y') }}</div>
                            <div class="mt-auto fw-semibold">Rp {{ number_format($inv->amount,0,',','.') }}</div>
                            <a href="{{ route('pembayaran',['invoice'=>$inv->id]) }}" class="btn btn-sm btn-outline-primary mt-2">{{ (isset($selected) && $selected && $selected->id===$inv->id)?'Sedang Dibuka':'Lihat' }}</a>
                            <a href="{{ route('invoices.pdf',$inv) }}" class="btn btn-sm btn-link mt-1 p-0">PDF</a>
                        </div>
                    </div>
                @endforeach
            </div>
            @if(isset($selected) && $selected)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between flex-wrap align-items-start mb-3">
                            <div>
                                <h5 class="mb-1">Invoice {{ $selected->number }}</h5>
                                <div class="small text-secondary">Status: <span class="badge bg-{{ $selected->status==='paid'?'success':($selected->status==='rejected'?'danger':($selected->status==='awaiting_verification'?'warning text-dark':'secondary')) }}">{{ strtoupper($selected->status) }}</span></div>
                            </div>
                            <div class="text-end small">
                                <div>Tanggal: {{ $selected->issued_date?->format('d M Y') }}</div>
                                <div>Jatuh Tempo: {{ $selected->due_date?->format('d M Y') }}</div>
                                @if($selected->paid_at)<div>Dibayar: {{ $selected->paid_at->format('d M Y H:i') }}</div>@endif
                            </div>
                        </div>
                        <table class="table table-sm">
                            <thead><tr><th>Deskripsi</th><th class="text-end" style="width:160px">Subtotal (Rp)</th></tr></thead>
                            <tbody>
                                <tr><td>Biaya {{ $selected->type==='registration'?'Registrasi':'Perpanjangan' }} Badan Usaha</td><td class="text-end">{{ number_format($selected->amount,0,',','.') }}</td></tr>
                                <tr class="fw-semibold"><td class="text-end" colspan="2">Total: {{ number_format($selected->amount,0,',','.') }}</td></tr>
                            </tbody>
                        </table>
                        <div class="mb-3 small text-secondary">Unggah bukti pembayaran setelah transfer ke rekening resmi. Pastikan nominal sesuai.</div>
                        <div class="mb-3 small">
                            <strong>Pilih Rekening Transfer</strong>
                            @php($banks = \App\Models\BankAccount::orderBy('sort')->orderBy('bank_name')->get())
                            <form method="POST" action="{{ route('invoices.selectBank',$selected) }}" class="d-flex flex-wrap gap-2 align-items-end mt-2">@csrf
                                <select name="bank_account_id" class="form-select form-select-sm" style="max-width:320px" @disabled($selected->payment_proof_path) required>
                                    <option value="">-- pilih bank --</option>
                                    @foreach($banks as $b)
                                        <option value="{{ $b->id }}" @selected($selected->bank_account_id==$b->id)>{{ $b->bank_name }} - {{ $b->account_number }} ({{ $b->account_name }})</option>
                                    @endforeach
                                </select>
                                @if(!$selected->payment_proof_path)
                                    <button class="btn btn-sm btn-outline-primary">Simpan</button>
                                @endif
                            </form>
                            @if($selected->bank_account_id)
                                <div class="mt-2 small text-success">Rekening dipilih: {{ $selected->bankAccount?->bank_name }} / {{ $selected->bankAccount?->account_number }}</div>
                            @endif
                        </div>
                        @if(session('success'))<div class="alert alert-success py-2 small">{{ session('success') }}</div>@endif
                        @if($selected->status==='unpaid' || $selected->status==='awaiting_verification')
                            <form method="POST" action="{{ route('invoices.uploadProof',$selected) }}" enctype="multipart/form-data" class="small d-flex flex-column gap-2 mb-3">
                                @csrf
                                <input type="file" name="payment_proof" accept="application/pdf,image/png,image/jpeg" class="form-control form-control-sm" required>
                                @if($selected->payment_proof_path)
                                    <div class="small">Bukti saat ini: <a href="{{ asset('storage/'.$selected->payment_proof_path) }}" target="_blank">Lihat</a></div>
                                @endif
                                <div class="text-muted small">Format: pdf/jpg/png, maks 10MB.</div>
                                <div><button class="btn btn-sm btn-primary">Unggah Bukti</button></div>
                            </form>
                        @endif
                        <a href="{{ route('invoices.pdf',$selected) }}" class="btn btn-outline-secondary btn-sm">Download PDF</a>
                    </div>
                </div>
            @endif
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
