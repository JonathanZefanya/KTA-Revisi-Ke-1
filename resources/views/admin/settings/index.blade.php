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

    <!-- KTA Template Section -->
    <div class="settings-section">
        <h5><i class="bi bi-card-image me-2"></i>Template Kartu Tanda Anggota (KTA)</h5>
        <p class="section-description">Upload template background KTA dan atur posisi serta ukuran elemen-elemen di dalamnya.</p>
        
        <div class="row g-4">
            <div class="col-lg-4">
                <form method="POST" action="{{ route('admin.settings.ktaTemplate') }}" enctype="multipart/form-data" class="small">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-dim small mb-1">Upload Template Background</label>
                        <input type="file" name="kta_template" accept="image/png,image/jpeg" class="form-control form-control-sm bg-dark border-secondary text-light" required>
                        <div class="form-text text-dim" style="font-size:0.7rem">Format: PNG/JPG, Rekomendasi: 1000x620px, Maks: 5MB</div>
                    </div>
                    @php($ktaTemplatePath = $settings['kta_template_path'] ?? 'img/kta_template.png')
                    @if($ktaTemplatePath)
                        <div class="mb-3">
                            <div class="small text-dim mb-2">Template Saat Ini:</div>
                            <img src="{{ asset(str_starts_with($ktaTemplatePath, 'storage/') ? $ktaTemplatePath : 'storage/'.$ktaTemplatePath) }}" 
                                 alt="KTA Template" 
                                 class="img-fluid border rounded" 
                                 style="max-width:100%;background:#fff"
                                 onerror="this.src='{{ asset($ktaTemplatePath) }}'">
                        </div>
                    @endif
                    <button class="btn btn-sm btn-success w-100">
                        <i class="bi bi-upload me-1"></i>Upload Template Baru
                    </button>
                </form>
            </div>

            <div class="col-lg-8">
                <div class="small text-dim mb-3">
                    <strong>Atur Posisi & Ukuran Elemen KTA:</strong>
                    <p class="mb-2" style="font-size:0.75rem">Klik tombol di bawah untuk membuka editor visual yang memungkinkan Anda mengatur posisi dan ukuran setiap elemen.</p>
                </div>
                
                <button type="button" class="btn btn-sm btn-primary mb-3" onclick="openLayoutEditor()">
                    <i class="bi bi-cursor me-1"></i>Buka Editor Drag & Drop
                </button>

                <div class="card bg-dark border-secondary">
                    <div class="card-body p-3">
                        <div class="small text-light">
                            <strong class="d-block mb-2">Cara Menggunakan Editor:</strong>
                            <ol class="mb-0" style="font-size:0.75rem;line-height:1.8">
                                <li>Klik tombol "Buka Editor Drag & Drop"</li>
                                <li><strong>Drag (geser)</strong> elemen untuk mengubah posisi</li>
                                <li><strong>Resize</strong> elemen dengan drag handle di pojok kanan bawah</li>
                                <li>Gunakan <strong>slider</strong> untuk mengatur ukuran font</li>
                                <li>Klik "Simpan" untuk menyimpan perubahan</li>
                            </ol>
                            <div class="alert alert-info mt-2 mb-0" style="font-size:0.7rem;padding:0.5rem">
                                <i class="bi bi-lightbulb me-1"></i>
                                <strong>Tips:</strong> Elemen yang dapat diatur: Nomor Anggota, Judul, Data Perusahaan, Masa Berlaku, Pas Foto, dan QR Code.
                            </div>
                        </div>
                    </div>
                </div>
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

// KTA Layout Editor with Drag & Drop
let ktaLayoutConfig = @json(json_decode($settings['kta_layout_config'] ?? '{}', true) ?: []);
let selectedElement = null;
let isDragging = false;
let isResizing = false;
let startX, startY, startLeft, startTop, startWidth, startHeight;

// Konstanta konversi cm ke px (1cm ≈ 37.795px pada 96 DPI)
const CM_TO_PX = 37.795;

function openLayoutEditor() {
    // Ensure config is an object with cm-based defaults for 29.7cm x 21.28cm (matching PDF defaults)
    if (!ktaLayoutConfig || typeof ktaLayoutConfig !== 'object' || Object.keys(ktaLayoutConfig).length === 0) {
        ktaLayoutConfig = {
            member_box: {left: 1.3, top: 1.2, fontSize: 14},
            title: {left: 11.5, top: 4.2, fontSize: 16},
            meta: {left: 7.5, top: 6.5, width: 16, fontSize: 12, labelWidth: 6},
            expiry: {left: 10.5, top: 15.8, fontSize: 11},
            photo: {left: 7.3, top: 14.5, width: 3.5, height: 4.8},
            qr: {right: 1, bottom: 1.8, width: 3.5, height: 3.5}
        };
    }
    
    const modal = document.getElementById('ktaLayoutModal');
    if (modal) {
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
        
        // Load template image
        setTimeout(() => {
            const bgImg = document.getElementById('canvas-bg');
            const templatePath = @json($ktaTemplatePath ?? 'img/kta_template.png');
            bgImg.src = '{{ asset("") }}' + (templatePath.startsWith('storage/') ? templatePath : 'storage/' + templatePath);
            bgImg.onerror = function() {
                this.src = '{{ asset("") }}' + templatePath;
            };
            
            initDragDrop();
            applyConfigToElements();
        }, 100);
    }
}

function initDragDrop() {
    const canvas = document.getElementById('kta-canvas');
    const elements = canvas.querySelectorAll('.draggable-elem');
    
    elements.forEach(elem => {
        // Click to select
        elem.addEventListener('click', (e) => {
            if (isResizing) return;
            selectElement(elem);
            e.stopPropagation();
        });
        
        // Drag element
        elem.addEventListener('mousedown', (e) => {
            if (e.target.classList.contains('resize-handle')) {
                startResize(e, elem);
            } else {
                startDrag(e, elem);
            }
        });
    });
    
    // Deselect on canvas click
    canvas.addEventListener('click', () => {
        if (selectedElement && !isDragging && !isResizing) {
            deselectElement();
        }
    });
    
    // Mouse move and up
    document.addEventListener('mousemove', onMouseMove);
    document.addEventListener('mouseup', onMouseUp);
}

function selectElement(elem) {
    deselectElement();
    selectedElement = elem;
    elem.classList.add('selected');
    
    const elemKey = elem.dataset.elem;
    const config = ktaLayoutConfig[elemKey];
    
    // Show font control for text elements
    const fontControl = document.getElementById('font-control');
    const elemName = document.getElementById('selected-elem-name');
    const fontSize = config?.fontSize || 14;
    
    if (['member_box', 'title', 'meta', 'expiry'].includes(elemKey)) {
        fontControl.style.display = 'block';
        elemName.textContent = getElementName(elemKey);
        
        const slider = document.getElementById('font-size-slider');
        const display = document.getElementById('font-size-display');
        slider.value = fontSize;
        display.textContent = fontSize + 'px';
        
        slider.oninput = function() {
            display.textContent = this.value + 'px';
            elem.querySelector('.elem-content').style.fontSize = this.value + 'px';
            if (!ktaLayoutConfig[elemKey]) ktaLayoutConfig[elemKey] = {};
            ktaLayoutConfig[elemKey].fontSize = parseInt(this.value);
        };
    } else {
        fontControl.style.display = 'none';
    }
    
    updatePositionInfo(elem);
}

function deselectElement() {
    if (selectedElement) {
        selectedElement.classList.remove('selected');
        selectedElement = null;
    }
}

function getElementName(key) {
    const names = {
        member_box: 'Nomor Anggota',
        title: 'Judul',
        meta: 'Data Perusahaan',
        expiry: 'Masa Berlaku',
        photo: 'Pas Foto',
        qr: 'QR Code'
    };
    return names[key] || key;
}

function startDrag(e, elem) {
    if (e.target.classList.contains('resize-handle')) return;
    
    isDragging = true;
    selectedElement = elem;
    selectElement(elem);
    
    startX = e.clientX;
    startY = e.clientY;
    startLeft = elem.offsetLeft;
    startTop = elem.offsetTop;
    
    elem.style.cursor = 'grabbing';
    e.preventDefault();
}

function startResize(e, elem) {
    isResizing = true;
    selectedElement = elem;
    selectElement(elem);
    
    startX = e.clientX;
    startY = e.clientY;
    startWidth = elem.offsetWidth;
    startHeight = elem.offsetHeight;
    
    e.preventDefault();
    e.stopPropagation();
}

function onMouseMove(e) {
    if (isDragging && selectedElement) {
        const dx = e.clientX - startX;
        const dy = e.clientY - startY;
        
        selectedElement.style.left = (startLeft + dx) + 'px';
        selectedElement.style.top = (startTop + dy) + 'px';
        
        updatePositionInfo(selectedElement);
    } else if (isResizing && selectedElement) {
        const dx = e.clientX - startX;
        const dy = e.clientY - startY;
        
        const newWidth = Math.max(50, startWidth + dx);
        const newHeight = Math.max(30, startHeight + dy);
        
        selectedElement.style.width = newWidth + 'px';
        selectedElement.style.height = newHeight + 'px';
        
        updatePositionInfo(selectedElement);
    }
}

function onMouseUp(e) {
    if (isDragging && selectedElement) {
        const elemKey = selectedElement.dataset.elem;
        const config = ktaLayoutConfig[elemKey] || {};
        
        config.left = selectedElement.offsetLeft;
        config.top = selectedElement.offsetTop;
        
        ktaLayoutConfig[elemKey] = config;
        selectedElement.style.cursor = 'move';
    } else if (isResizing && selectedElement) {
        const elemKey = selectedElement.dataset.elem;
        const config = ktaLayoutConfig[elemKey] || {};
        
        config.width = selectedElement.offsetWidth;
        config.height = selectedElement.offsetHeight;
        
        ktaLayoutConfig[elemKey] = config;
    }
    
    isDragging = false;
    isResizing = false;
}

function updatePositionInfo(elem) {
    const elemKey = elem.dataset.elem;
    const config = ktaLayoutConfig[elemKey] || {};
    
    // Convert px to cm for display
    const leftCm = (elem.offsetLeft / CM_TO_PX).toFixed(1);
    const topCm = (elem.offsetTop / CM_TO_PX).toFixed(1);
    const widthCm = (elem.offsetWidth / CM_TO_PX).toFixed(1);
    const heightCm = (elem.offsetHeight / CM_TO_PX).toFixed(1);
    
    const info = document.getElementById('position-info');
    info.innerHTML = `
        <strong>${getElementName(elemKey)}</strong><br>
        <small>
        Left: ${leftCm} cm (${elem.offsetLeft}px)<br>
        Top: ${topCm} cm (${elem.offsetTop}px)<br>
        Width: ${widthCm} cm (${elem.offsetWidth}px)<br>
        Height: ${heightCm} cm (${elem.offsetHeight}px)
        ${config.fontSize ? `<br>Font: ${config.fontSize}px` : ''}
        </small>
    `;
}

function applyConfigToElements() {
    const canvas = document.getElementById('kta-canvas');
    if (!canvas) return;
    
    const canvasWidth = canvas.offsetWidth; // 29.7cm dalam px
    const canvasHeight = canvas.offsetHeight; // 21.28cm dalam px
    
    Object.keys(ktaLayoutConfig).forEach(key => {
        const elem = document.getElementById('elem-' + key);
        if (!elem) return;
        
        const config = ktaLayoutConfig[key];
        
        // Special handling for QR code with right/bottom positioning
        if (key === 'qr' && config.right !== undefined && config.bottom !== undefined) {
            const rightPx = config.right * CM_TO_PX;
            const bottomPx = config.bottom * CM_TO_PX;
            const widthPx = (config.width || 3.5) * CM_TO_PX;
            const heightPx = (config.height || 3.5) * CM_TO_PX;
            
            elem.style.left = (canvasWidth - rightPx - widthPx) + 'px';
            elem.style.top = (canvasHeight - bottomPx - heightPx) + 'px';
            elem.style.width = widthPx + 'px';
            elem.style.height = heightPx + 'px';
        } else {
            // Normal left/top positioning for other elements
            if (config.left !== undefined) {
                elem.style.left = (config.left * CM_TO_PX) + 'px';
            }
            if (config.top !== undefined) {
                elem.style.top = (config.top * CM_TO_PX) + 'px';
            }
            // Only set width/height for photo element (resizable)
            if (key === 'photo') {
                if (config.width !== undefined) {
                    elem.style.width = (config.width * CM_TO_PX) + 'px';
                }
                if (config.height !== undefined) {
                    elem.style.height = (config.height * CM_TO_PX) + 'px';
                }
            }
            // For meta, only set width (let height adjust to content)
            if (key === 'meta' && config.width !== undefined) {
                elem.style.width = (config.width * CM_TO_PX) + 'px';
            }
        }
        
        // Apply font size for text elements
        if (config.fontSize !== undefined && elem.querySelector('.elem-content')) {
            elem.querySelector('.elem-content').style.fontSize = config.fontSize + 'px';
        }
    });
    
    console.log('Applied config to elements:', ktaLayoutConfig);
}

function resetLayout() {
    if (confirm('Reset semua elemen ke posisi default?')) {
        ktaLayoutConfig = {
            member_box: {left: 1.3, top: 1.2, fontSize: 14},
            title: {left: 11.5, top: 4.2, fontSize: 16},
            meta: {left: 7.5, top: 6.5, width: 16, fontSize: 12, labelWidth: 6},
            expiry: {left: 10.5, top: 15.8, fontSize: 11},
            photo: {left: 7.3, top: 14.5, width: 3.5, height: 4.8},
            qr: {right: 1, bottom: 1.8, width: 3.5, height: 3.5}
        };
        applyConfigToElements();
    }
}

function saveLayoutConfig() {
    // Update config from current element positions
    updateLayoutConfig();
    
    // Show loading state
    const saveBtn = event.target;
    const originalText = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Menyimpan...';
    
    fetch('{{ route("admin.settings.ktaLayout") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            layout_config: JSON.stringify(ktaLayoutConfig)
        })
    })
    .then(res => {
        if (!res.ok) {
            throw new Error('Network response was not ok');
        }
        return res.json();
    })
    .then(data => {
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalText;
        
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('ktaLayoutModal'));
        if (modal) {
            modal.hide();
        }
        
        // Show success message
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success alert-dismissible fade show small';
        alertDiv.innerHTML = `
            <i class="bi bi-check-circle me-2"></i>
            Konfigurasi layout berhasil disimpan! Halaman akan di-refresh untuk melihat perubahan.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        const mainContent = document.querySelector('.adm-main') || document.querySelector('main');
        if (mainContent && mainContent.firstChild) {
            mainContent.insertBefore(alertDiv, mainContent.firstChild);
        }
        
        // Reload after short delay
        setTimeout(() => {
            location.reload();
        }, 1500);
    })
    .catch(err => {
        console.error(err);
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalText;
        alert('Gagal menyimpan konfigurasi. Silakan coba lagi.');
    });
}

function updateLayoutConfig() {
    // Update config from current element positions in drag-drop canvas
    const canvas = document.getElementById('kta-canvas');
    if (!canvas) return;
    
    const canvasWidth = canvas.offsetWidth; // 29.7cm dalam px
    const canvasHeight = canvas.offsetHeight; // 21.28cm dalam px
    
    const elements = canvas.querySelectorAll('.draggable-elem');
    
    elements.forEach(elem => {
        const elemKey = elem.dataset.elem;
        if (!ktaLayoutConfig[elemKey]) {
            ktaLayoutConfig[elemKey] = {};
        }
        
        const config = ktaLayoutConfig[elemKey];
        
        // For QR code, use right/bottom positioning
        if (elemKey === 'qr') {
            config.right = Math.round(((canvasWidth - elem.offsetLeft - elem.offsetWidth) / CM_TO_PX) * 10) / 10;
            config.bottom = Math.round(((canvasHeight - elem.offsetTop - elem.offsetHeight) / CM_TO_PX) * 10) / 10;
            config.width = Math.round((elem.offsetWidth / CM_TO_PX) * 10) / 10;
            config.height = Math.round((elem.offsetHeight / CM_TO_PX) * 10) / 10;
            
            // Remove left/top if exists to avoid confusion
            delete config.left;
            delete config.top;
        } else {
            // For other elements, use left/top positioning
            config.left = Math.round((elem.offsetLeft / CM_TO_PX) * 10) / 10;
            config.top = Math.round((elem.offsetTop / CM_TO_PX) * 10) / 10;
            
            // Update size if applicable
            if (elem.offsetWidth) {
                config.width = Math.round((elem.offsetWidth / CM_TO_PX) * 10) / 10;
            }
            if (elem.offsetHeight) {
                config.height = Math.round((elem.offsetHeight / CM_TO_PX) * 10) / 10;
            }
        }
    });
    
    console.log('Updated config before save (in cm):', ktaLayoutConfig);
}
</script>
@endpush

<!-- KTA Layout Editor Modal -->
<div class="modal fade" id="ktaLayoutModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header border-secondary" style="background:#0d1218">
                <h5 class="modal-title"><i class="bi bi-cursor me-2"></i>Editor Layout KTA - Drag & Drop</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="background:#1a1f2e;padding:0;overflow:hidden">
                <div class="d-flex h-100">
                    <!-- Canvas Area -->
                    <div class="flex-grow-1 position-relative" style="overflow:auto;background:#2a2f3e">
                        <div class="d-flex align-items-center justify-content-center" style="min-height:100%;padding:2rem">
                            <div id="kta-canvas-wrapper" style="position:relative;width:29.7cm;max-width:100%">
                                <div id="kta-canvas" style="position:relative;width:29.7cm;height:21.28cm;background:#fff;box-shadow:0 8px 32px rgba(0,0,0,0.5);border-radius:8px;overflow:hidden">
                                    <!-- Template background will be loaded here -->
                                    <img id="canvas-bg" src="" alt="Template" style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;pointer-events:none;user-select:none">
                                    
                                    <!-- Draggable Elements -->
                                    <div style="position:absolute;top:0;left:0;width:100%;height:100%">
                                        <div id="elem-member_box" class="draggable-elem" data-elem="member_box">
                                            <div class="elem-content" style="color:#000">13/028/AB</div>
                                            <div class="resize-handle"></div>
                                        </div>
                                        
                                        <div id="elem-title" class="draggable-elem" data-elem="title">
                                            <div class="elem-content" style="color:#000">KARTU TANDA ANGGOTA</div>
                                            <div class="resize-handle"></div>
                                        </div>
                                        
                                        <div id="elem-meta" class="draggable-elem draggable-box" data-elem="meta">
                                            <div class="elem-content" style="font-size:12px;line-height:1.4;color:#000;font-weight:400;">
                                                <table style="border-collapse:collapse;width:100%;border:none;">
                                                    <tr>
                                                        <td style="font-weight:700;padding:0.2cm 0.15cm;vertical-align:top;">NAMA PERUSAHAAN</td>
                                                        <td style="width:0.3cm;text-align:center;padding:0.2cm 0.1cm;">:</td>
                                                        <td style="padding:0.2cm 0.15cm;vertical-align:top;">TESTa</td>
                                                    </tr>
                                                    <tr>    
                                                        <td style="font-weight:700;padding:0.2cm 0.15cm;vertical-align:top;">NAMA PIMPINAN</td>
                                                        <td style="width:0.3cm;text-align:center;padding:0.2cm 0.1cm;">:</td>
                                                        <td style="padding:0.2cm 0.15cm;vertical-align:top;">123421</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-weight:700;padding:0.2cm 0.15cm;vertical-align:top;">NO. NPWP</td>
                                                        <td style="width:0.3cm;text-align:center;padding:0.2cm 0.1cm;">:</td>
                                                        <td style="padding:0.2cm 0.15cm;vertical-align:top;">1234567</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="resize-handle"></div>
                                        </div>
                                        
                                        <div id="elem-expiry" class="draggable-elem" data-elem="expiry">
                                            <div class="elem-content" style="color:#000">BERLAKU SAMPAI DENGAN TANGGAL 31 DESEMBER 2025</div>
                                            <div class="resize-handle"></div>
                                        </div>
                                        
                                        <div id="elem-photo" class="draggable-elem draggable-box" data-elem="photo" style="background:#eee;border:1px solid #999">
                                            <div class="elem-content" style="display:flex;align-items:center;justify-content:center;height:100%;font-size:10px;color:#666">
                                                <i class="bi bi-person" style="font-size:40px"></i>
                                            </div>
                                            <div class="resize-handle"></div>
                                        </div>
                                        
                                        <div id="elem-qr" class="draggable-elem draggable-box" data-elem="qr" style="background:#fff;border:1px solid #999">
                                            <div class="elem-content" style="display:flex;align-items:center;justify-content:center;height:100%;color:#000">
                                                <i class="bi bi-qr-code" style="font-size:30px"></i>
                                            </div>
                                            <div class="resize-handle"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Control Panel -->
                    <div class="border-start border-secondary" style="width:320px;background:#16202b;overflow-y:auto;padding:1.5rem">
                        <h6 class="mb-3"><i class="bi bi-sliders me-2"></i>Kontrol Elemen</h6>
                        
                        <div id="element-controls">
                            <div class="alert alert-sm alert-info" style="font-size:0.75rem;padding:0.5rem">
                                <i class="bi bi-info-circle me-1"></i>
                                Klik elemen di canvas untuk mengatur ukuran font
                            </div>
                            
                            <div id="font-control" style="display:none">
                                <div class="card bg-secondary mb-3">
                                    <div class="card-body p-3">
                                        <label class="form-label small mb-2">
                                            <strong id="selected-elem-name">Elemen</strong> - Ukuran Font
                                        </label>
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="range" id="font-size-slider" class="form-range flex-grow-1" min="8" max="32" value="14">
                                            <span id="font-size-display" class="badge bg-primary" style="min-width:45px">14px</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card bg-secondary">
                                <div class="card-header small"><strong>Posisi Saat Ini</strong></div>
                                <div class="card-body p-3">
                                    <div id="position-info" style="font-size:0.75rem">
                                        Klik elemen untuk melihat posisi
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <button type="button" class="btn btn-sm btn-outline-warning w-100 mb-2" onclick="resetLayout()">
                                <i class="bi bi-arrow-counterclockwise me-1"></i>Reset ke Default
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-secondary" style="background:#0d1218">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Batal
                </button>
                <button type="button" class="btn btn-success" onclick="saveLayoutConfig()">
                    <i class="bi bi-save me-1"></i>Simpan Konfigurasi
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.draggable-elem {
    position: absolute;
    cursor: move;
    user-select: none;
    transition: box-shadow 0.2s;
    z-index: 10;
}
.draggable-elem:hover {
    box-shadow: 0 0 0 1px #3b82f6;
}
.draggable-elem.selected {
    box-shadow: 0 0 0 1px #ef4444 !important;
}
.draggable-elem .elem-content {
    pointer-events: none;
    font-weight: 700;
}
.draggable-elem .elem-content table {
    border: none !important;
}
.draggable-elem .elem-content table td {
    border: none !important;
}
.draggable-box {
    background: rgba(59, 130, 246, 0.05);
    border: 1px dashed #3b82f6;
}
.resize-handle {
    position: absolute;
    right: -4px;
    bottom: -4px;
    width: 10px;
    height: 10px;
    background: #3b82f6;
    border: 1px solid #fff;
    border-radius: 50%;
    cursor: nwse-resize;
    display: none;
}
.draggable-elem:hover .resize-handle,
.draggable-elem.selected .resize-handle {
    display: block;
}
</style>

                <div class="row g-4" style="display:none">
                    <!-- Nomor Anggota -->
                    <div class="col-md-6">
                        <div class="card bg-secondary">
                            <div class="card-header"><strong>Nomor Anggota</strong></div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <label class="form-label small">Left (px)</label>
                                    <input type="number" id="member_box_left" class="form-control form-control-sm" onchange="updateLayoutConfig(this, 'left')">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Top (px)</label>
                                    <input type="number" id="member_box_top" class="form-control form-control-sm" onchange="updateLayoutConfig(this, 'top')">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Font Size (px)</label>
                                    <input type="number" id="member_box_fontSize" class="form-control form-control-sm" onchange="updateLayoutConfig(this, 'fontSize')">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Judul -->
                    <div class="col-md-6">
                        <div class="card bg-secondary">
                            <div class="card-header"><strong>Judul</strong></div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <label class="form-label small">Left (px)</label>
                                    <input type="number" id="title_left" class="form-control form-control-sm" onchange="updateLayoutConfig(this, 'left')">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Top (px)</label>
                                    <input type="number" id="title_top" class="form-control form-control-sm" onchange="updateLayoutConfig(this, 'top')">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Font Size (px)</label>
                                    <input type="number" id="title_fontSize" class="form-control form-control-sm" onchange="updateLayoutConfig(this, 'fontSize')">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Perusahaan -->
                    <div class="col-md-6">
                        <div class="card bg-secondary">
                            <div class="card-header"><strong>Data Perusahaan</strong></div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <label class="form-label small">Left (px)</label>
                                    <input type="number" id="meta_left" class="form-control form-control-sm" onchange="updateLayoutConfig(this, 'left')">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Top (px)</label>
                                    <input type="number" id="meta_top" class="form-control form-control-sm" onchange="updateLayoutConfig(this, 'top')">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Width (px)</label>
                                    <input type="number" id="meta_width" class="form-control form-control-sm" onchange="updateLayoutConfig(this, 'width')">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Font Size (px)</label>
                                    <input type="number" id="meta_fontSize" class="form-control form-control-sm" onchange="updateLayoutConfig(this, 'fontSize')">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Label Width (px)</label>
                                    <input type="number" id="meta_labelWidth" class="form-control form-control-sm" onchange="updateLayoutConfig(this, 'labelWidth')">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Masa Berlaku -->
                    <div class="col-md-6">
                        <div class="card bg-secondary">
                            <div class="card-header"><strong>Masa Berlaku</strong></div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <label class="form-label small">Left (px)</label>
                                    <input type="number" id="expiry_left" class="form-control form-control-sm" onchange="updateLayoutConfig(this, 'left')">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Top (px)</label>
                                    <input type="number" id="expiry_top" class="form-control form-control-sm" onchange="updateLayoutConfig(this, 'top')">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Font Size (px)</label>
                                    <input type="number" id="expiry_fontSize" class="form-control form-control-sm" onchange="updateLayoutConfig(this, 'fontSize')">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pas Foto -->
                    <div class="col-md-6">
                        <div class="card bg-secondary">
                            <div class="card-header"><strong>Pas Foto</strong></div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <label class="form-label small">Left (px)</label>
                                    <input type="number" id="photo_left" class="form-control form-control-sm" onchange="updateLayoutConfig(this, 'left')">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Top (px)</label>
                                    <input type="number" id="photo_top" class="form-control form-control-sm" onchange="updateLayoutConfig(this, 'top')">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Width (px)</label>
                                    <input type="number" id="photo_width" class="form-control form-control-sm" onchange="updateLayoutConfig(this, 'width')">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Height (px)</label>
                                    <input type="number" id="photo_height" class="form-control form-control-sm" onchange="updateLayoutConfig(this, 'height')">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- QR Code -->
                    <div class="col-md-6">
                        <div class="card bg-secondary">
                            <div class="card-header"><strong>QR Code</strong></div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <label class="form-label small">Right (px)</label>
                                    <input type="number" id="qr_right" class="form-control form-control-sm" onchange="updateLayoutConfig(this, 'right')">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Bottom (px)</label>
                                    <input type="number" id="qr_bottom" class="form-control form-control-sm" onchange="updateLayoutConfig(this, 'bottom')">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Width (px)</label>
                                    <input type="number" id="qr_width" class="form-control form-control-sm" onchange="updateLayoutConfig(this, 'width')">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Height (px)</label>
                                    <input type="number" id="qr_height" class="form-control form-control-sm" onchange="updateLayoutConfig(this, 'height')">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveLayoutConfig()">
                    <i class="bi bi-save me-1"></i>Simpan Konfigurasi
                </button>
            </div>
        </div>
    </div>
</div>

@endsection