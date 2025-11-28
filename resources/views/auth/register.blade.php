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
    /* Cropper modal tweaks (portrait 3:4) */
        .cropper-modal-backdrop{background:rgba(15,23,42,.6)!important;backdrop-filter:blur(3px);}        
        .cropper-container{font-family:inherit;}
        .cropper-view-box, .cropper-face{border-radius:10px;}
        .modal-crop .modal-content{border-radius:22px;overflow:hidden;}
        .preview-box{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:.5rem;}
    .preview-box .preview{overflow:hidden;width:160px;aspect-ratio:3/4;height:auto;max-height:215px;border-radius:10px;background:#f1f5f9;}
    </style>
    <link  href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css" rel="stylesheet" />
</head>
<body>
<div class="auth-wrapper">
    <div class="container">
        <div class="row g-4 align-items-stretch justify-content-center">
            <div class="col-lg-8 col-md-10">
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
                                    <div class="mt-3">
                                        <label class="form-label small fw-medium">Alamat Lokasi Asphalt Mixing Plant <span class="text-secondary">(Opsional)</span></label>
                                        <input type="text" name="asphalt_mixing_plant_address" value="{{ old('asphalt_mixing_plant_address') }}" class="form-control" placeholder="Masukkan alamat lengkap lokasi AMP jika ada">
                                    </div>
                                    <div class="mt-3">
                                        <label class="form-label small fw-medium">Alamat Lokasi Concrete Batching Plant <span class="text-secondary">(Opsional)</span></label>
                                        <input type="text" name="concrete_batching_plant_address" value="{{ old('concrete_batching_plant_address') }}" class="form-control" placeholder="Masukkan alamat lengkap lokasi CBP jika ada">
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
                                        <label class="form-label small fw-medium">Photo PJBU (PNG/JPG/JPEG max 3MB) <span class="text-secondary">(Rasio wajib 3:4)</span></label>
                                        <input type="file" name="photo_pjbu" id="photoPjbuInput" accept="image/png,image/jpeg" class="form-control" required data-requires-crop>
                                        <div class="form-text small">Pilih foto kemudian lakukan crop sesuai bingkai 3:4 (portrait).</div>
                                        <input type="hidden" name="photo_pjbu_cropped" id="photoPjbuCroppedMeta" value=""> <!-- optional meta -->
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-medium">NPWP BU (PDF max 10MB)</label>
                                        <input type="file" name="npwp_bu_file" accept="application/pdf" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-medium">Akte Badan Usaha (PDF max 10MB)</label>
                                        <input type="file" name="akte_bu_file" accept="application/pdf" class="form-control" required>
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
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>
<!-- Crop Modal -->
<div class="modal fade modal-crop" id="cropperModal" tabindex="-1" aria-hidden="true" aria-labelledby="cropperModalLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-semibold" id="cropperModalLabel">Crop Foto PJBU (3 : 4)</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <div class="row g-4">
                        <div class="col-md-8">
                                <div class="bg-light rounded-4 overflow-hidden position-relative" style="min-height:320px;aspect-ratio:3/4;">
                                        <img id="cropImage" alt="Crop preview" class="w-100 h-100" style="object-fit:contain;">
                                        <div class="position-absolute top-0 start-0 w-100 h-100" style="pointer-events:none;">
                                            <div style="position:absolute;inset:0;display:grid;grid-template-columns:repeat(3,1fr);grid-template-rows:repeat(4,1fr);">
                                                @for($i=0;$i<12;$i++)
                                                    <div style="border:1px solid rgba(255,255,255,.35);mix-blend-mode:overlay;"></div>
                                                @endfor
                                            </div>
                                            <div style="position:absolute;left:50%;top:50%;width:55%;height:55%;transform:translate(-50%,-50%);border:2px dashed rgba(255,255,255,.55);border-radius:18px;mix-blend-mode:overlay;"></div>
                                        </div>
                                </div>
                        </div>
                        <div class="col-md-4">
                                <div class="preview-box mb-3">
                                        <div class="small fw-semibold mb-2">Pratinjau (3:4)</div>
                                        <div class="preview" id="cropPreview"></div>
                                </div>
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="rotateLeft">Rotate -90°</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="rotateRight">Rotate +90°</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="resetCrop">Reset</button>
                                </div>
                                <div class="small text-secondary mb-2">Pastikan wajah / objek utama berada di tengah.</div>
                        </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <div class="d-flex w-100 justify-content-between align-items-center">
                        <div class="small text-secondary" id="cropInfo">&nbsp;</div>
                        <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="button" id="applyCrop" class="btn btn-brand">Simpan Crop</button>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
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

// =============== Cropper Logic ===============
(function(){
  const fileInput = document.getElementById('photoPjbuInput');
  const croppedMeta = document.getElementById('photoPjbuCroppedMeta');
  const form = document.getElementById('registerForm');
  const modalEl = document.getElementById('cropperModal');
  const imgEl = document.getElementById('cropImage');
  const previewEl = document.getElementById('cropPreview');
  const infoEl = document.getElementById('cropInfo');
  let cropper = null; let currentFile = null; let cropConfirmed = false; let modalInstance = null; 

  function revoke(){ if(imgEl.dataset.blobUrl){ URL.revokeObjectURL(imgEl.dataset.blobUrl); delete imgEl.dataset.blobUrl; } }

  fileInput.addEventListener('change', (e)=>{
     const f = e.target.files?.[0]; cropConfirmed = false; croppedMeta.value='';
     if(!f){ return; }
     if(!/^image\//.test(f.type)){ alert('File harus gambar.'); fileInput.value=''; return; }
     if(f.size > 3*1024*1024){ alert('Ukuran foto maksimal 3MB'); fileInput.value=''; return; }
     currentFile = f; revoke();
     const url = URL.createObjectURL(f); imgEl.src = url; imgEl.dataset.blobUrl = url;
     if(!modalInstance){ modalInstance = new bootstrap.Modal(modalEl,{backdrop:'static'}); }
     modalInstance.show();
  });

  modalEl.addEventListener('shown.bs.modal', ()=>{
      if(cropper){ cropper.destroy(); }
      cropper = new Cropper(imgEl, {
         aspectRatio: 3/4,
         viewMode: 1,
         dragMode: 'move',
         autoCropArea: 1,
         preview: previewEl,
         responsive: true,
         background: false,
         movable: true,
         rotatable: true,
         zoomOnWheel: true,
         ready(){ updateInfo(); }
      });
  });
  modalEl.addEventListener('hidden.bs.modal', ()=>{
      if(!cropConfirmed){ // user closed without confirming => reset input
          fileInput.value=''; revoke(); if(cropper){ cropper.destroy(); cropper=null; }
      }
  });

  function updateInfo(){
      if(!cropper) return; const data = cropper.getData(true); // true => rounded
    infoEl.textContent = `Crop: ${data.width} x ${data.height}px (rasio ${(data.width/data.height).toFixed(2)})`; // target ~0.75
  }

  document.getElementById('rotateLeft').addEventListener('click', ()=>{ cropper?.rotate(-90); updateInfo(); });
  document.getElementById('rotateRight').addEventListener('click', ()=>{ cropper?.rotate(90); updateInfo(); });
  document.getElementById('resetCrop').addEventListener('click', ()=>{ cropper?.reset(); updateInfo(); });

  document.getElementById('applyCrop').addEventListener('click', ()=>{
      if(!cropper) return;
    // Export to canvas with a target size preserving 3:4; choose 600x800 for good portrait quality
    const canvas = cropper.getCroppedCanvas({ width: 600, height: 800, fillColor:'#fff' });
      canvas.toBlob(blob => {
          if(!blob){ alert('Gagal membuat gambar.'); return; }
          const fileName = currentFile ? currentFile.name.replace(/\.(\w+)$/,'-cropped.$1') : 'foto-pjbu-cropped.jpg';
          const croppedFile = new File([blob], fileName, { type: blob.type });
          // Replace file input's FileList using DataTransfer
          const dt = new DataTransfer(); dt.items.add(croppedFile); fileInput.files = dt.files;
          croppedMeta.value = '1';
          cropConfirmed = true;
          modalInstance.hide();
          revoke(); if(cropper){ cropper.destroy(); cropper=null; }
      }, 'image/jpeg', 0.9);
  });

  // Prevent submit if user selected file but not confirmed crop
  form.addEventListener('submit', (e)=>{
      const requires = fileInput.hasAttribute('data-requires-crop');
      if(requires && fileInput.files.length && !cropConfirmed){
          e.preventDefault(); e.stopPropagation();
          alert('Mohon lakukan crop foto PJBU terlebih dahulu.');
          if(!modalInstance){ modalInstance = new bootstrap.Modal(modalEl,{backdrop:'static'}); }
          modalInstance.show();
      }
  });
})();
</script>
</body>
</html>