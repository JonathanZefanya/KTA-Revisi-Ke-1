@extends('admin.layout')
@section('title','Pengaturan')
@section('page_title','Pengaturan')
@section('content')
@if(session('success'))<div class="alert alert-success small">{{ session('success') }}</div>@endif
<div class="row g-4">
    <div class="col-lg-4">
        <div class="adm-card mb-4">
            <h5>Nama Website & Logo</h5>
            <form method="POST" action="{{ route('admin.settings.updateSite') }}" class="small d-grid gap-2" enctype="multipart/form-data">@csrf
                <label class="form-label text-dim small mb-1">Nama</label>
                <input type="text" name="site_name" value="{{ old('site_name',$site_name) }}" class="form-control form-control-sm bg-dark border-secondary text-light" required>
                <label class="form-label text-dim small mb-1 mt-2">Logo (1:1 png/jpg maks 2MB)</label>
                <input type="file" name="site_logo" accept="image/png,image/jpeg" class="form-control form-control-sm bg-dark border-secondary text-light">
                @php($logo = $settings['site_logo_path'] ?? null)
                @if($logo)
                    <div class="mt-2"><img src="{{ asset('storage/'.$logo) }}" alt="Logo" class="border rounded" style="width:80px;height:80px;object-fit:cover;background:#fff"></div>
                @endif
                <div class="border-top mt-3 pt-2 small">
                    <div class="text-dim mb-1">SMTP (kosongkan untuk pakai .env)</div>
                    <div class="row g-2">
                        <div class="col-6"><input placeholder="Host" name="mail_host" value="{{ old('mail_host',$settings['mail_host'] ?? '') }}" class="form-control form-control-sm bg-dark border-secondary text-light"></div>
                        <div class="col-3"><input placeholder="Port" name="mail_port" value="{{ old('mail_port',$settings['mail_port'] ?? '') }}" class="form-control form-control-sm bg-dark border-secondary text-light"></div>
                        <div class="col-3">
                            <select name="mail_encryption" class="form-select form-select-sm bg-dark border-secondary text-light">
                                <option value="">(enc)</option>
                                @foreach(['tls','ssl','starttls'] as $enc)
                                    <option value="{{ $enc }}" @selected(old('mail_encryption',$settings['mail_encryption'] ?? '')==$enc)>{{ strtoupper($enc) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6"><input placeholder="Username" name="mail_username" value="{{ old('mail_username',$settings['mail_username'] ?? '') }}" class="form-control form-control-sm bg-dark border-secondary text-light"></div>
                        <div class="col-6"><input placeholder="Password" type="password" name="mail_password" value="{{ old('mail_password',$settings['mail_password'] ?? '') }}" class="form-control form-control-sm bg-dark border-secondary text-light"></div>
                        <div class="col-6"><input placeholder="From Address" name="mail_from_address" value="{{ old('mail_from_address',$settings['mail_from_address'] ?? '') }}" class="form-control form-control-sm bg-dark border-secondary text-light"></div>
                        <div class="col-6"><input placeholder="From Name" name="mail_from_name" value="{{ old('mail_from_name',$settings['mail_from_name'] ?? '') }}" class="form-control form-control-sm bg-dark border-secondary text-light"></div>
                    </div>
                </div>
                <button class="btn btn-sm btn-primary mt-3">Simpan</button>
            </form>
        </div>
        <div class="adm-card mb-4">
            <h5>Tanda Tangan Digital</h5>
            <div class="small text-dim mb-2">Gambar PNG akan disimpan & dapat dipakai untuk dokumen.</div>
            @if($signature_path)
                <div class="mb-2"><img src="{{ asset('storage/'.$signature_path) }}" alt="Signature" style="max-width:100%;height:auto;border:1px solid #333;border-radius:6px;background:#fff;padding:.25rem"></div>
            @endif
            <form method="POST" action="{{ route('admin.settings.storeSignature') }}" onsubmit="return saveSignature()" class="small">@csrf
                <canvas id="sigPad" width="320" height="140" style="background:#fff;border:1px solid #444;border-radius:6px;touch-action:none"></canvas>
                <input type="hidden" name="signature" id="signatureInput">
                <div class="d-flex gap-2 mt-2">
                    <button type="button" onclick="clearSig()" class="btn btn-sm btn-outline-secondary">Clear</button>
                    <button class="btn btn-sm btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="adm-card mb-4">
            <h5>Rekening Bank (Transfer)</h5>
            <form method="POST" action="{{ route('admin.settings.banks.store') }}" class="row g-2 align-items-end mb-3 small">@csrf
                <div class="col-md-3">
                    <label class="form-label text-dim small mb-1">Bank</label>
                    <input name="bank_name" class="form-control form-control-sm bg-dark border-secondary text-light" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-dim small mb-1">No. Rekening</label>
                    <input name="account_number" class="form-control form-control-sm bg-dark border-secondary text-light" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-dim small mb-1">Atas Nama</label>
                    <input name="account_name" class="form-control form-control-sm bg-dark border-secondary text-light" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-dim small mb-1">Urut</label>
                    <input type="number" name="sort" value="0" class="form-control form-control-sm bg-dark border-secondary text-light">
                </div>
                <div class="col-md-1 d-grid">
                    <button class="btn btn-sm btn-primary w-100">Tambah</button>
                </div>
            </form>
            <div class="table-responsive small">
                <table class="table table-sm table-dark align-middle mb-0">
                    <thead><tr><th>#</th><th>Bank</th><th>No. Rekening</th><th>Atas Nama</th><th>Urut</th><th></th></tr></thead>
                    <tbody>
                    @forelse($bankAccounts as $i=>$b)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $b->bank_name }}</td>
                            <td class="font-monospace">{{ $b->account_number }}</td>
                            <td>{{ $b->account_name }}</td>
                            <td>{{ $b->sort }}</td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('admin.settings.banks.delete',$b) }}" onsubmit="return confirm('Hapus rekening?')" class="d-inline">@csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-3 text-dim">Belum ada rekening</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="adm-card mb-4">
            <h5>Tarif Registrasi Badan Usaha</h5>
            <form method="POST" action="{{ route('admin.settings.saveRates') }}" class="small">@csrf
                <div class="table-responsive">
                    <table class="table table-sm table-dark align-middle mb-2">
                        <thead><tr><th>Jenis</th><th>Kualifikasi</th><th>Nominal (Rp)</th></tr></thead>
                        <tbody id="ratesBody">
                            @foreach($defaultJenis as $j)
                                @foreach($defaultKual as $k)
                                    @php($rate = $rates->firstWhere('jenis',$j)?->where('kualifikasi',$k)->first())
                                    <tr>
                                        <td>{{ $j }}<input type="hidden" name="amount[{{ $j.'_'.$k }}][jenis]" value="{{ $j }}"></td>
                                        <td>{{ $k }}<input type="hidden" name="amount[{{ $j.'_'.$k }}][kualifikasi]" value="{{ $k }}"></td>
                                        <td><input type="text" name="amount[{{ $j.'_'.$k }}][amount]" value="{{ $rate?->amount ?? '0' }}" class="form-control form-control-sm bg-dark border-secondary text-light text-end amount-field"></td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="text-end"><button class="btn btn-sm btn-primary">Simpan Tarif Registrasi</button></div>
            </form>
        </div>
        <div class="adm-card mb-4">
            <h5>Tarif Perpanjangan Kartu Tanda Anggota</h5>
            <form method="POST" action="{{ route('admin.settings.saveRenewalRates') }}" class="small">@csrf
                <div class="table-responsive">
                    <table class="table table-sm table-dark align-middle mb-2">
                        <thead><tr><th>Jenis</th><th>Kualifikasi</th><th>Nominal (Rp)</th></tr></thead>
                        <tbody>
                        @foreach($defaultJenis as $j)
                            @foreach($defaultKual as $k)
                                @php($rRate = $renewalRates->firstWhere('jenis',$j)?->where('kualifikasi',$k)->first())
                                <tr>
                                    <td>{{ $j }}<input type="hidden" name="renewal_amount[{{ $j.'_'.$k }}][jenis]" value="{{ $j }}"></td>
                                    <td>{{ $k }}<input type="hidden" name="renewal_amount[{{ $j.'_'.$k }}][kualifikasi]" value="{{ $k }}"></td>
                                    <td><input type="text" name="renewal_amount[{{ $j.'_'.$k }}][amount]" value="{{ $rRate?->amount ?? '0' }}" class="form-control form-control-sm bg-dark border-secondary text-light text-end amount-field"></td>
                                </tr>
                            @endforeach
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="text-end"><button class="btn btn-sm btn-primary">Simpan Tarif Perpanjangan</button></div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
(function(){
  const c=document.getElementById('sigPad'); if(!c) return; const ctx=c.getContext('2d'); ctx.lineWidth=2; ctx.lineCap='round'; ctx.strokeStyle='#000'; let drawing=false,last={x:0,y:0};
  function pos(e){ if(e.touches){const r=c.getBoundingClientRect();return {x:e.touches[0].clientX-r.left,y:e.touches[0].clientY-r.top}; } const r=c.getBoundingClientRect(); return {x:e.clientX-r.left,y:e.clientY-r.top}; }
  function start(e){ drawing=true; const p=pos(e); last=p; }
  function move(e){ if(!drawing) return; const p=pos(e); ctx.beginPath(); ctx.moveTo(last.x,last.y); ctx.lineTo(p.x,p.y); ctx.stroke(); last=p; }
  function end(){ drawing=false; }
  ['mousedown','touchstart'].forEach(ev=>c.addEventListener(ev,start));
  ['mousemove','touchmove'].forEach(ev=>c.addEventListener(ev,move));
  ['mouseup','mouseleave','touchend','touchcancel'].forEach(ev=>c.addEventListener(ev,end));
  window.clearSig=function(){ ctx.clearRect(0,0,c.width,c.height); }
    window.saveSignature=function(){ const data=c.toDataURL('image/png'); document.getElementById('signatureInput').value=data; if(data.length<100){ alert('Tanda tangan kosong'); return false;} return true; }
    // Currency formatting
    function formatNumber(v){ v = (v||'').toString().replace(/[^0-9]/g,''); if(!v) return ''; return v.replace(/\B(?=(\d{3})+(?!\d))/g,'.'); }
    function onInput(e){ const caret = e.target.selectionStart; const rawBefore = e.target.value; e.target.value = formatNumber(rawBefore); }
    document.querySelectorAll('.amount-field').forEach(inp=>{
        inp.addEventListener('input', onInput);
        // initial format
        inp.value = formatNumber(inp.value);
    });
})();
</script>
@endpush
@endsection
