@php($appName = config('app.name'))
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar | {{ $appName }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body{font-family:'Inter',sans-serif;background:linear-gradient(135deg,#f8fafc,#eef2f7);} 
        .auth-wrapper{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem;}
        .card{border:none;border-radius:28px;box-shadow:0 8px 24px -8px rgba(0,0,0,.08),0 12px 40px -12px rgba(0,0,0,.06);} 
        .brand-badge{display:inline-flex;align-items:center;gap:.6rem;font-weight:600;font-size:1.05rem;color:#0d6efd;text-decoration:none;}
        .form-control{border-radius:14px;padding:.8rem 1rem;border:1px solid #dbe0e6;} 
        .form-control:focus{box-shadow:0 0 0 .25rem rgba(13,110,253,.15);border-color:#0d6efd;} 
        .btn-brand{background:#0d6efd;border:none;border-radius:14px;padding:.85rem 1rem;font-weight:600;letter-spacing:.3px;}
        .btn-brand:hover{background:#0b5ed7;} 
        .link-hover{text-decoration:none;position:relative;} 
        .link-hover:after{content:'';position:absolute;left:0;bottom:-2px;height:2px;width:0;background:currentColor;transition:.35s;} 
        .link-hover:hover:after{width:100%;}
        .floating-shape{position:absolute;inset:0;pointer-events:none;overflow:hidden;border-radius:28px;} 
        .floating-shape:before{content:'';position:absolute;width:480px;height:480px;background:radial-gradient(circle at 30% 30%,rgba(13,110,253,.18),transparent 70%);top:-120px;left:-120px;filter:blur(10px);} 
        .floating-shape:after{content:'';position:absolute;width:380px;height:380px;background:radial-gradient(circle at 70% 70%,rgba(32,201,151,.18),transparent 70%);bottom:-120px;right:-100px;filter:blur(12px);} 
        @media (max-width:575.98px){.card{border-radius:22px;} .auth-side{display:none;}}
    </style>
</head>
<body>
<div class="auth-wrapper">
    <div class="container">
        <div class="row g-4 align-items-stretch justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card position-relative">
                    <div class="floating-shape"></div>
                    <div class="card-body p-4 p-md-5">
                        <a href="{{ route('home') }}" class="brand-badge mb-3">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 14 4-4"/><path d="M14 12V8"/><path d="M2 12h4"/><path d="M6 8V4"/><rect x="8" y="4" width="8" height="4" rx="1"/><rect x="4" y="12" width="8" height="4" rx="1"/><path d="M6 16v2"/><rect x="12" y="12" width="8" height="4" rx="1"/><path d="M18 16v2"/><rect x="8" y="20" width="8" height="4" rx="1" transform="rotate(-90 8 20)"/></svg>
                            <span>{{ $appName }}</span>
                        </a>
                        <h1 class="h4 fw-semibold mb-1">Registrasi Badan Usaha</h1>
                        <p class="text-secondary mb-4">Lengkapi data BU & dokumen pendukung.</p>
                        @if($errors->any())
                            <div class="alert alert-danger py-2 small mb-3">
                                {{ $errors->first() }}
                            </div>
                        @endif
                        <form method="POST" action="{{ route('register.attempt') }}" class="needs-validation" novalidate enctype="multipart/form-data" id="registerForm">
                            @csrf
                            <ul class="nav nav-pills mb-3" id="regTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="tab-data-bu" data-bs-toggle="pill" data-bs-target="#pane-data-bu" type="button" role="tab">Data Badan Usaha</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-files" data-bs-toggle="pill" data-bs-target="#pane-files" type="button" role="tab">Upload Dokumen</button>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="pane-data-bu" role="tabpanel">
                                    <div class="mb-3">
                                        <label class="form-label small fw-medium">Nama Badan Usaha</label>
                                        <input type="text" name="bu_name" value="{{ old('bu_name') }}" class="form-control" required>
                                        <div class="invalid-feedback">Wajib diisi.</div>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <label class="form-label small fw-medium">Bentuk BU</label>
                                            <select name="bentuk" class="form-select" required>
                                                <option value="">Pilih</option>
                                                @foreach(['PT','CV','Koperasi'] as $v)
                                                    <option value="{{ $v }}" @selected(old('bentuk')==$v)>{{ $v }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">Pilih bentuk.</div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label small fw-medium">Jenis BU</label>
                                            <select name="jenis" class="form-select" required>
                                                <option value="">Pilih</option>
                                                @foreach(['BUJKN','BUJKA','BUJKPMA'] as $v)
                                                    <option value="{{ $v }}" @selected(old('jenis')==$v)>{{ $v }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">Pilih jenis.</div>
                                        </div>
                                    </div>
                                    <div class="row g-3 mt-1">
                                        <div class="col-sm-6">
                                            <label class="form-label small fw-medium">Kualifikasi</label>
                                            <select name="kualifikasi" class="form-select" required>
                                                <option value="">Pilih</option>
                                                @foreach([
                                                    'Kecil / Spesialis 1',
                                                    'Menengah / Spesialis 2',
                                                    'Besar BUJKN / Spesialis 2',
                                                    'Besar PMA / Spesialis 2',
                                                    'BUJKA'
                                                ] as $v)
                                                    <option value="{{ $v }}" @selected(old('kualifikasi')==$v)>{{ $v }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">Pilih kualifikasi.</div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label small fw-medium">Penanggung Jawab (PJBU)</label>
                                            <input type="text" name="penanggung_jawab" value="{{ old('penanggung_jawab') }}" class="form-control" required>
                                            <div class="invalid-feedback">Isi PJBU.</div>
                                        </div>
                                    </div>
                                    <div class="mt-3 mb-3">
                                        <label class="form-label small fw-medium">NPWP Badan Usaha</label>
                                        <input type="text" name="npwp" value="{{ old('npwp') }}" class="form-control" required>
                                        <div class="invalid-feedback">Isi NPWP.</div>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <label class="form-label small fw-medium">Email BU</label>
                                            <input type="email" name="bu_email" value="{{ old('bu_email') }}" class="form-control" required>
                                            <div class="invalid-feedback">Email BU wajib.</div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label small fw-medium">No. Telepon BU</label>
                                            <input type="text" name="bu_phone" value="{{ old('bu_phone') }}" class="form-control" required>
                                            <div class="invalid-feedback">Telepon wajib.</div>
                                        </div>
                                    </div>
                                    <div class="row g-3 mt-1">
                                        <div class="col-sm-4">
                                            <label class="form-label small fw-medium">Kode Pos</label>
                                            <input type="text" name="postal_code" value="{{ old('postal_code') }}" class="form-control">
                                        </div>
                                        <div class="col-sm-8">
                                            <label class="form-label small fw-medium">Alamat</label>
                                            <input type="text" name="address" value="{{ old('address') }}" class="form-control" required>
                                            <div class="invalid-feedback">Alamat wajib.</div>
                                        </div>
                                    </div>
                                    <div class="row g-3 mt-1">
                                        <div class="col-sm-6">
                                            <label class="form-label small fw-medium">Provinsi</label>
                                            <select name="province_code" id="provinceSelect" class="form-select" required></select>
                                            <input type="hidden" name="province_name" id="provinceNameHidden" value="{{ old('province_name') }}">
                                            <div class="invalid-feedback">Pilih provinsi.</div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label small fw-medium">Kab / Kota</label>
                                            <select name="city_code" id="citySelect" class="form-select" required disabled></select>
                                            <input type="hidden" name="city_name" id="cityNameHidden" value="{{ old('city_name') }}">
                                            <div class="invalid-feedback">Pilih kota.</div>
                                        </div>
                                    </div>
                                    <div class="row g-3 mt-1">
                                        <div class="col-sm-6">
                                            <label class="form-label small fw-medium">Password BU</label>
                                            <input type="password" name="password" class="form-control" required minlength="8">
                                            <div class="form-text small">Min 8 karakter kombinasi.</div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label small fw-medium">Konfirmasi Password</label>
                                            <input type="password" name="password_confirmation" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end mt-4">
                                        <button type="button" class="btn btn-brand" id="nextToFiles">Lanjut &raquo;</button>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pane-files" role="tabpanel">
                                    <div class="mb-3">
                                        <label class="form-label small fw-medium">Photo PJBU (PNG/JPG/JPEG max 3MB)</label>
                                        <input type="file" name="photo_pjbu" accept="image/png,image/jpeg" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-medium">NPWP BU (PDF max 10MB)</label>
                                        <input type="file" name="npwp_bu_file" accept="application/pdf" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-medium">NIB (PDF max 10MB)</label>
                                        <input type="file" name="nib_file" accept="application/pdf" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-medium">KTP PJBU (PDF max 10MB)</label>
                                        <input type="file" name="ktp_pjbu_file" accept="application/pdf" class="form-control" required>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label small fw-medium">NPWP PJBU (PDF max 10MB)</label>
                                        <input type="file" name="npwp_pjbu_file" accept="application/pdf" class="form-control" required>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-outline-secondary" id="backToData">&laquo; Kembali</button>
                                        <button type="submit" class="btn btn-brand">Daftar</button>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-4">
                                <small class="text-secondary">Sudah punya akun? <a href="{{ route('login') }}" class="link-hover">Masuk</a></small>
                            </div>
                        </form>
                        <p class="mt-4 small text-secondary mb-0">Dengan mendaftar Anda menyetujui <a href="#" class="link-hover">Ketentuan</a> & <a href="#" class="link-hover">Privasi</a>.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 col-md-7 auth-side d-flex align-items-center">
                <div class="w-100 text-center px-lg-4">
                    <h2 class="fw-semibold mb-3">Akses Cepat & Aman</h2>
                    <p class="text-secondary mx-auto" style="max-width:420px">Data Anda dilindungi dengan enkripsi modern. Antarmuka responsif untuk semua perangkat dan usia.</p>
                    <div class="ratio ratio-16x9 rounded-4 overflow-hidden shadow-sm mt-4">
                        <img src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?q=80&w=800&auto=format&fit=crop" alt="Illustration" class="w-100 h-100 object-fit-cover">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Tabs navigation buttons
document.getElementById('nextToFiles').addEventListener('click', () => {
    const trigger = document.querySelector('#tab-files');
    new bootstrap.Tab(trigger).show();
});
document.getElementById('backToData').addEventListener('click', () => {
    const trigger = document.querySelector('#tab-data-bu');
    new bootstrap.Tab(trigger).show();
});

// Client validation
(() => {
    const form = document.getElementById('registerForm');
    form.addEventListener('submit', e => {
        if(!form.checkValidity()) { e.preventDefault(); e.stopPropagation(); }
        form.classList.add('was-validated');
    });
})();

// Fetch provinces & cities (robust with fallback APIs)
const provinceSelect = document.getElementById('provinceSelect');
const citySelect = document.getElementById('citySelect');
const provinceNameHidden = document.getElementById('provinceNameHidden');
const cityNameHidden = document.getElementById('cityNameHidden');

async function loadProvinces(){
    provinceSelect.innerHTML = '<option value="">Memuat...</option>';
    try {
        const res = await fetch("{{ url('api/wilayah/provinces') }}");
        const json = await res.json();
        const list = Array.isArray(json.data) ? json.data : [];
        if(!list.length){ throw new Error('empty provinces'); }
        provinceSelect.innerHTML = '<option value="">Pilih</option>' + list.map(p => `<option value="${p.code}">${p.name}</option>`).join('');
        if(provinceNameHidden.value){
            const opt = [...provinceSelect.options].find(o=>o.text===provinceNameHidden.value);
            if(opt){ provinceSelect.value = opt.value; }
        }
    } catch(err){
        console.error(err);
        provinceSelect.innerHTML = '<option value="">Gagal memuat provinsi</option>';
    }
}

provinceSelect.addEventListener('change', async (e) => {
    const code = e.target.value; citySelect.disabled = true; citySelect.innerHTML = '<option value="">Memuat...</option>';
    const name = provinceSelect.options[provinceSelect.selectedIndex]?.text || '';
    provinceNameHidden.value = name;
    if(!code){ citySelect.innerHTML='<option value="">Pilih provinsi dulu</option>'; return; }
        try {
            const res = await fetch(`{{ url('api/wilayah/regencies') }}/${code}`);
            const json = await res.json();
            const list = Array.isArray(json.data) ? json.data : [];
            if(!list.length) throw new Error('empty regencies');
            citySelect.innerHTML = '<option value="">Pilih</option>' + list.map(c => `<option value="${c.code}">${c.name}</option>`).join('');
            citySelect.disabled = false;
        } catch(err){
            console.error(err);
            citySelect.innerHTML = '<option value="">Gagal memuat</option>';
        }
});

citySelect.addEventListener('change', () => {
    const name = citySelect.options[citySelect.selectedIndex]?.text || '';
    cityNameHidden.value = name;
});

loadProvinces();
</script>
</body>
</html>