@extends('admin.layout')
@section('title','Pengaturan')
@section('page_title','Pengaturan')
@section('content')
@if(session('success'))<div class="alert alert-success small">{{ session('success') }}</div>@endif

<style>
.settings-tabs {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid var(--adm-accent);
    padding-bottom: 0;
}
.settings-tabs .tab-btn {
    background: transparent;
    border: none;
    padding: 0.75rem 1.5rem;
    color: var(--adm-text-dim);
    font-size: 0.95rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    border-radius: 6px 6px 0 0;
}
.settings-tabs .tab-btn:hover {
    color: var(--adm-text);
    background: rgba(13, 71, 154, 0.1);
}
.settings-tabs .tab-btn.active {
    color: var(--adm-text);
    background: var(--adm-accent);
}
.tab-content-wrapper {
    display: none;
}
.tab-content-wrapper.active {
    display: block;
    animation: fadeIn 0.3s ease;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.settings-section {
    background: var(--adm-card);
    border: 1px solid var(--adm-border);
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}
.settings-section h5 {
    color: var(--adm-text);
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--adm-border);
}
.settings-section .section-description {
    color: var(--adm-text-dim);
    font-size: 0.875rem;
    margin-bottom: 1rem;
}
</style>

<div class="settings-tabs">
    <button class="tab-btn active" onclick="switchTab('general')">
        <i class="bi bi-gear me-1"></i> Umum
    </button>
    <button class="tab-btn" onclick="switchTab('signature')">
        <i class="bi bi-pen me-1"></i> Tanda Tangan
    </button>
    <button class="tab-btn" onclick="switchTab('bank')">
        <i class="bi bi-bank me-1"></i> Rekening Bank
    </button>
    <button class="tab-btn" onclick="switchTab('rates')">
        <i class="bi bi-currency-dollar me-1"></i> Tarif
    </button>
</div>

<!-- Tab: Umum -->
<div id="tab-general" class="tab-content-wrapper active">
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="settings-section">
                <h5><i class="bi bi-globe me-2"></i>Informasi Website</h5>
                <p class="section-description">Atur nama website dan logo yang akan ditampilkan di sistem.</p>
                <form method="POST" action="{{ route('admin.settings.updateSite') }}" class="small" enctype="multipart/form-data">@csrf
                    <div class="mb-3">
                        <label class="form-label text-dim small mb-1">Nama Website</label>
                        <input type="text" name="site_name" value="{{ old('site_name',$site_name) }}" class="form-control form-control-sm bg-dark border-secondary text-light" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dim small mb-1">Logo Website (1:1 png/jpg maks 2MB)</label>
                        <input type="file" name="site_logo" accept="image/png,image/jpeg" class="form-control form-control-sm bg-dark border-secondary text-light">
                        @php($logo = $settings['site_logo_path'] ?? null)
                        @if($logo)
                            <div class="mt-2">
                                <img src="{{ asset('storage/'.$logo) }}" alt="Logo" class="border rounded" style="width:80px;height:80px;object-fit:cover;background:#fff">
                            </div>
                        @endif
                    </div>
                    <div class="text-end">
                        <button class="btn btn-sm btn-primary"><i class="bi bi-check-circle me-1"></i>Simpan Website</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="settings-section">
                <h5><i class="bi bi-envelope me-2"></i>Konfigurasi SMTP</h5>
                <p class="section-description">Atur konfigurasi email SMTP untuk pengiriman notifikasi. Kosongkan untuk menggunakan konfigurasi dari .env</p>
                <form method="POST" action="{{ route('admin.settings.updateSite') }}" class="small">@csrf
                    <input type="hidden" name="site_name" value="{{ $site_name }}">
                    <div class="row g-2">
                        <div class="col-7">
                            <label class="form-label text-dim small mb-1">SMTP Host</label>
                            <input placeholder="smtp.gmail.com" name="mail_host" value="{{ old('mail_host',$settings['mail_host'] ?? '') }}" class="form-control form-control-sm bg-dark border-secondary text-light">
                        </div>
                        <div class="col-3">
                            <label class="form-label text-dim small mb-1">Port</label>
                            <input placeholder="587" name="mail_port" value="{{ old('mail_port',$settings['mail_port'] ?? '') }}" class="form-control form-control-sm bg-dark border-secondary text-light">
                        </div>
                        <div class="col-2">
                            <label class="form-label text-dim small mb-1">Enkripsi</label>
                            <select name="mail_encryption" class="form-select form-select-sm bg-dark border-secondary text-light">
                                <option value="">-</option>
                                @foreach(['tls','ssl','starttls'] as $enc)
                                    <option value="{{ $enc }}" @selected(old('mail_encryption',$settings['mail_encryption'] ?? '')==$enc)>{{ strtoupper($enc) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-dim small mb-1">Username</label>
                            <input placeholder="your-email@gmail.com" name="mail_username" value="{{ old('mail_username',$settings['mail_username'] ?? '') }}" class="form-control form-control-sm bg-dark border-secondary text-light">
                        </div>
                        <div class="col-6">
                            <label class="form-label text-dim small mb-1">Password</label>
                            <input placeholder="••••••••" type="password" name="mail_password" value="{{ old('mail_password',$settings['mail_password'] ?? '') }}" class="form-control form-control-sm bg-dark border-secondary text-light">
                        </div>
                        <div class="col-6">
                            <label class="form-label text-dim small mb-1">From Address</label>
                            <input placeholder="noreply@example.com" name="mail_from_address" value="{{ old('mail_from_address',$settings['mail_from_address'] ?? '') }}" class="form-control form-control-sm bg-dark border-secondary text-light">
                        </div>
                        <div class="col-6">
                            <label class="form-label text-dim small mb-1">From Name</label>
                            <input placeholder="Sistem KTA" name="mail_from_name" value="{{ old('mail_from_name',$settings['mail_from_name'] ?? '') }}" class="form-control form-control-sm bg-dark border-secondary text-light">
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button class="btn btn-sm btn-primary"><i class="bi bi-check-circle me-1"></i>Simpan SMTP</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Tab: Tanda Tangan -->
<div id="tab-signature" class="tab-content-wrapper">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="settings-section">
                <h5><i class="bi bi-pen me-2"></i>Tanda Tangan Digital</h5>
                <p class="section-description">Gambar tanda tangan dalam format PNG akan disimpan dan dapat digunakan untuk dokumen resmi seperti invoice dan sertifikat KTA.</p>
                
                @if($signature_path)
                    <div class="mb-3 p-3 bg-dark rounded text-center">
                        <div class="small text-dim mb-2">Tanda Tangan Saat Ini:</div>
                        <img src="{{ asset('storage/'.$signature_path) }}" alt="Signature" style="max-width:100%;height:auto;border:1px solid #444;border-radius:6px;background:#fff;padding:.5rem">
                    </div>
                @endif
                
                <form method="POST" action="{{ route('admin.settings.storeSignature') }}" onsubmit="return saveSignature()" class="small">@csrf
                    <div class="mb-2">
                        <label class="form-label text-dim small mb-2">Buat Tanda Tangan Baru:</label>
                        <div class="d-flex justify-content-center">
                            <canvas id="sigPad" width="400" height="160" style="background:#fff;border:2px solid #444;border-radius:8px;touch-action:none;cursor:crosshair"></canvas>
                        </div>
                    </div>
                    <input type="hidden" name="signature" id="signatureInput">
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="button" onclick="clearSig()" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>Clear
                        </button>
                        <button class="btn btn-sm btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Simpan Tanda Tangan
                        </button>
                    </div>
                </form>
                
                <div class="alert alert-info small mt-3 mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    <strong>Tips:</strong> Gunakan mouse atau touchpad untuk menggambar tanda tangan. Untuk hasil terbaik, gunakan stylus atau perangkat touchscreen.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tab: Rekening Bank -->
<div id="tab-bank" class="tab-content-wrapper">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="settings-section">
                <h5><i class="bi bi-bank me-2"></i>Rekening Bank (Transfer)</h5>
                <p class="section-description">Kelola rekening bank yang tersedia untuk pembayaran invoice. Rekening ini akan ditampilkan kepada pengguna saat melakukan pembayaran.</p>
                
                <div class="bg-dark p-3 rounded mb-4">
                    <h6 class="text-light mb-3 small"><i class="bi bi-plus-circle me-1"></i>Tambah Rekening Baru</h6>
                    <form method="POST" action="{{ route('admin.settings.banks.store') }}" class="row g-2 align-items-end small">@csrf
                        <div class="col-md-3">
                            <label class="form-label text-dim small mb-1">Nama Bank</label>
                            <input name="bank_name" placeholder="BCA, Mandiri, BNI..." class="form-control form-control-sm bg-dark border-secondary text-light" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-dim small mb-1">No. Rekening</label>
                            <input name="account_number" placeholder="1234567890" class="form-control form-control-sm bg-dark border-secondary text-light" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-dim small mb-1">Atas Nama</label>
                            <input name="account_name" placeholder="PT. AABI" class="form-control form-control-sm bg-dark border-secondary text-light" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-dim small mb-1">Urutan</label>
                            <input type="number" name="sort" value="0" min="0" class="form-control form-control-sm bg-dark border-secondary text-light">
                        </div>
                        <div class="col-md-1 d-grid">
                            <button class="btn btn-sm btn-primary w-100"><i class="bi bi-plus"></i></button>
                        </div>
                    </form>
                </div>
                
                <h6 class="text-light mb-3 small"><i class="bi bi-list-ul me-1"></i>Daftar Rekening</h6>
                <div class="table-responsive small">
                    <table class="table table-sm table-dark table-hover align-middle mb-0">
                        <thead class="table-secondary">
                            <tr>
                                <th width="50">#</th>
                                <th>Bank</th>
                                <th>No. Rekening</th>
                                <th>Atas Nama</th>
                                <th width="80" class="text-center">Urutan</th>
                                <th width="100" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($bankAccounts as $i=>$b)
                            <tr>
                                <td class="text-dim">{{ $i+1 }}</td>
                                <td><strong>{{ $b->bank_name }}</strong></td>
                                <td class="font-monospace text-info">{{ $b->account_number }}</td>
                                <td>{{ $b->account_name }}</td>
                                <td class="text-center"><span class="badge bg-secondary">{{ $b->sort }}</span></td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('admin.settings.banks.delete',$b) }}" onsubmit="return confirm('Yakin hapus rekening {{ $b->bank_name }}?')" class="d-inline">@csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-4 text-dim"><i class="bi bi-inbox me-2"></i>Belum ada rekening bank terdaftar</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tab: Tarif -->
<div id="tab-rates" class="tab-content-wrapper">
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="settings-section">
                <h5><i class="bi bi-cash-coin me-2"></i>Tarif Registrasi Badan Usaha</h5>
                <p class="section-description">Atur tarif biaya pendaftaran untuk setiap jenis dan kualifikasi badan usaha. Tarif ini akan digunakan untuk invoice registrasi awal.</p>
                
                <form method="POST" action="{{ route('admin.settings.saveRates') }}" class="small">@csrf
                    <div class="table-responsive">
                        <table class="table table-sm table-dark table-hover align-middle mb-3">
                            <thead class="table-secondary">
                                <tr>
                                    <th width="35%">Jenis</th>
                                    <th width="35%">Kualifikasi</th>
                                    <th width="30%" class="text-end">Nominal (Rp)</th>
                                </tr>
                            </thead>
                            <tbody id="ratesBody">
                                @foreach($defaultJenis as $j)
                                    @foreach($defaultKual as $k)
                                        @php($rate = $rates->firstWhere('jenis',$j)?->where('kualifikasi',$k)->first())
                                        <tr>
                                            <td>
                                                <strong class="text-info">{{ $j }}</strong>
                                                <input type="hidden" name="amount[{{ $j.'_'.$k }}][jenis]" value="{{ $j }}">
                                            </td>
                                            <td>
                                                {{ $k }}
                                                <input type="hidden" name="amount[{{ $j.'_'.$k }}][kualifikasi]" value="{{ $k }}">
                                            </td>
                                            <td>
                                                <input type="text" name="amount[{{ $j.'_'.$k }}][amount]" value="{{ $rate?->amount ?? '0' }}" class="form-control form-control-sm bg-dark border-secondary text-light text-end amount-field" placeholder="0">
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end">
                        <button class="btn btn-sm btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Simpan Tarif Registrasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="settings-section">
                <h5><i class="bi bi-arrow-repeat me-2"></i>Tarif Perpanjangan KTA</h5>
                <p class="section-description">Atur tarif biaya perpanjangan Kartu Tanda Anggota untuk setiap jenis dan kualifikasi. Tarif ini digunakan untuk renewal KTA.</p>
                
                <form method="POST" action="{{ route('admin.settings.saveRenewalRates') }}" class="small">@csrf
                    <div class="table-responsive">
                        <table class="table table-sm table-dark table-hover align-middle mb-3">
                            <thead class="table-secondary">
                                <tr>
                                    <th width="35%">Jenis</th>
                                    <th width="35%">Kualifikasi</th>
                                    <th width="30%" class="text-end">Nominal (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($defaultJenis as $j)
                                @foreach($defaultKual as $k)
                                    @php($rRate = $renewalRates->firstWhere('jenis',$j)?->where('kualifikasi',$k)->first())
                                    <tr>
                                        <td>
                                            <strong class="text-info">{{ $j }}</strong>
                                            <input type="hidden" name="renewal_amount[{{ $j.'_'.$k }}][jenis]" value="{{ $j }}">
                                        </td>
                                        <td>
                                            {{ $k }}
                                            <input type="hidden" name="renewal_amount[{{ $j.'_'.$k }}][kualifikasi]" value="{{ $k }}">
                                        </td>
                                        <td>
                                            <input type="text" name="renewal_amount[{{ $j.'_'.$k }}][amount]" value="{{ $rRate?->amount ?? '0' }}" class="form-control form-control-sm bg-dark border-secondary text-light text-end amount-field" placeholder="0">
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end">
                        <button class="btn btn-sm btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Simpan Tarif Perpanjangan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="alert alert-info small">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Catatan:</strong> Tarif yang diatur di sini akan otomatis terapply pada invoice yang dibuat. Gunakan format angka tanpa titik atau koma, sistem akan memformat otomatis (contoh: ketik 500000 untuk Rp 500.000).
    </div>
</div>

@push('scripts')
<script>
// Tab switching functionality
function switchTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content-wrapper').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab
    document.getElementById('tab-' + tabName).classList.add('active');
    
    // Mark button as active
    event.target.closest('.tab-btn').classList.add('active');
}

// Signature pad functionality
(function(){
  const c=document.getElementById('sigPad'); 
  if(!c) return; 
  
  const ctx=c.getContext('2d'); 
  ctx.lineWidth=2.5; 
  ctx.lineCap='round'; 
  ctx.lineJoin='round';
  ctx.strokeStyle='#000'; 
  
  let drawing=false, last={x:0,y:0};
  
  function pos(e){ 
    if(e.touches){
      const r=c.getBoundingClientRect();
      return {x:e.touches[0].clientX-r.left, y:e.touches[0].clientY-r.top}; 
    } 
    const r=c.getBoundingClientRect(); 
    return {x:e.clientX-r.left, y:e.clientY-r.top}; 
  }
  
  function start(e){ 
    e.preventDefault();
    drawing=true; 
    const p=pos(e); 
    last=p; 
  }
  
  function move(e){ 
    if(!drawing) return; 
    e.preventDefault();
    const p=pos(e); 
    ctx.beginPath(); 
    ctx.moveTo(last.x, last.y); 
    ctx.lineTo(p.x, p.y); 
    ctx.stroke(); 
    last=p; 
  }
  
  function end(e){ 
    if(drawing) e.preventDefault();
    drawing=false; 
  }
  
  ['mousedown','touchstart'].forEach(ev=>c.addEventListener(ev, start, {passive: false}));
  ['mousemove','touchmove'].forEach(ev=>c.addEventListener(ev, move, {passive: false}));
  ['mouseup','mouseleave','touchend','touchcancel'].forEach(ev=>c.addEventListener(ev, end, {passive: false}));
  
  window.clearSig=function(){ 
    ctx.clearRect(0,0,c.width,c.height); 
  }
  
  window.saveSignature=function(){ 
    const data=c.toDataURL('image/png'); 
    document.getElementById('signatureInput').value=data; 
    if(data.length<100){ 
      alert('Tanda tangan kosong! Silakan buat tanda tangan terlebih dahulu.'); 
      return false;
    } 
    return true; 
  }
})();

// Currency formatting for amount fields
(function(){
    function formatNumber(v){ 
        v = (v||'').toString().replace(/[^0-9]/g,''); 
        if(!v) return ''; 
        return v.replace(/\B(?=(\d{3})+(?!\d))/g,'.'); 
    }
    
    function onInput(e){ 
        const element = e.target;
        const cursorPos = element.selectionStart;
        const oldValue = element.value;
        const oldLength = oldValue.length;
        
        element.value = formatNumber(oldValue);
        
        const newLength = element.value.length;
        const diff = newLength - oldLength;
        
        // Adjust cursor position
        element.setSelectionRange(cursorPos + diff, cursorPos + diff);
    }
    
    document.querySelectorAll('.amount-field').forEach(inp=>{
        inp.addEventListener('input', onInput);
        // Initial format
        inp.value = formatNumber(inp.value);
    });
})();
</script>
@endpush
@endsection
