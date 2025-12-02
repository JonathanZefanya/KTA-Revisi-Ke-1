@php($c = $company ?? null)
<div class="col-md-6">
    <label class="form-label small text-dim">Nama BU</label>
    <input name="name" value="{{ old('name', $c?->name) }}" class="form-control form-control-sm bg-dark border-secondary text-light" required>
</div>
<div class="col-md-3">
    <label class="form-label small text-dim">Bentuk</label>
    <select name="bentuk" class="form-select form-select-sm bg-dark border-secondary text-light" required>
        <option value="">Pilih</option>
        @foreach(['PT','CV','Koperasi'] as $b)
            <option value="{{ $b }}" @selected(old('bentuk', $c?->bentuk)==$b)>{{ $b }}</option>
        @endforeach
    </select>
</div>
<div class="col-md-3">
    <label class="form-label small text-dim">Jenis</label>
    <select name="jenis" class="form-select form-select-sm bg-dark border-secondary text-light">
        <option value="">-</option>
        @foreach(['BUJKN','BUJKA','BUJKPMA'] as $j)
            <option value="{{ $j }}" @selected(old('jenis', $c?->jenis)==$j)>{{ $j }}</option>
        @endforeach
    </select>
</div>
<div class="col-md-3">
    <label class="form-label small text-dim">Kualifikasi</label>
    <select name="kualifikasi" class="form-select form-select-sm bg-dark border-secondary text-light">
        <option value="">-</option>
        @foreach([
            'Kecil / Spesialis 1',
            'Menengah / Spesialis 2',
            'Besar BUJKN / Spesialis 2',
            'Besar PMA / Spesialis 2',
            'BUJKA'
        ] as $k)
            <option value="{{ $k }}" @selected(old('kualifikasi', $c?->kualifikasi)==$k)>{{ $k }}</option>
        @endforeach
    </select>
</div>
<div class="col-md-3">
    <label class="form-label small text-dim">Status Anggota</label>
    <select name="membership_type" class="form-select form-select-sm bg-dark border-secondary text-light">
        <option value="AB" @selected(old('membership_type', $c?->membership_type ?? 'AB')=='AB')>AB - Anggota Biasa</option>
        <option value="ALB" @selected(old('membership_type', $c?->membership_type)=='ALB')>ALB - Anggota Luar Biasa</option>
    </select>
</div>
<div class="col-md-6">
    <label class="form-label small text-dim">Penanggung Jawab</label>
    @if(!$c)
        <input name="penanggung_jawab" value="{{ old('penanggung_jawab', $c?->penanggung_jawab) }}" class="form-control form-control-sm bg-dark border-secondary text-light" placeholder="Otomatis dari pengguna terpilih" readonly>
        <div class="form-text text-dim">Diisi otomatis dari pengguna yang dipilih.</div>
    @else
        <input name="penanggung_jawab" value="{{ old('penanggung_jawab', $c?->penanggung_jawab) }}" class="form-control form-control-sm bg-dark border-secondary text-light" readonly>
    @endif
    @error('penanggung_jawab')<div class="text-danger small">{{ $message }}</div>@enderror
</div>
<div class="col-md-3">
    <label class="form-label small text-dim">NPWP</label>
    <input type="text" name="npwp" value="{{ old('npwp', $c?->npwp) }}" class="form-control form-control-sm bg-dark border-secondary text-light" placeholder="Masukkan NPWP Badan Usaha">
    @error('npwp')<div class="text-danger small">{{ $message }}</div>@enderror
</div>
<div class="col-md-3">
    <label class="form-label small text-dim">Telp</label>
    <input name="phone" value="{{ old('phone', $c?->phone) }}" class="form-control form-control-sm bg-dark border-secondary text-light">
</div>
<div class="col-md-6">
    <label class="form-label small text-dim">Email</label>
    <input type="email" name="email" value="{{ old('email', $c?->email) }}" class="form-control form-control-sm bg-dark border-secondary text-light">
</div>
<div class="col-md-6">
    <label class="form-label small text-dim">Alamat</label>
    <input name="address" value="{{ old('address', $c?->address) }}" class="form-control form-control-sm bg-dark border-secondary text-light">
</div>
<div class="col-md-6">
    <label class="form-label small text-dim">Alamat Lokasi Asphalt Mixing Plant <span class="text-secondary">(Opsional)</span></label>
    <input name="asphalt_mixing_plant_address" value="{{ old('asphalt_mixing_plant_address', $c?->asphalt_mixing_plant_address) }}" class="form-control form-control-sm bg-dark border-secondary text-light" placeholder="Masukkan alamat lengkap lokasi AMP jika ada">
</div>
<div class="col-md-6">
    <label class="form-label small text-dim">Alamat Lokasi Concrete Batching Plant <span class="text-secondary">(Opsional)</span></label>
    <input name="concrete_batching_plant_address" value="{{ old('concrete_batching_plant_address', $c?->concrete_batching_plant_address) }}" class="form-control form-control-sm bg-dark border-secondary text-light" placeholder="Masukkan alamat lengkap lokasi CBP jika ada">
</div>
<div class="col-md-3">
    <label class="form-label small text-dim">Provinsi</label>
    <select id="admProvinceSelect" name="province_code" class="form-select form-select-sm bg-dark border-secondary text-light" data-current-code="{{ old('province_code', $c?->province_code) }}">
        <option value="">Memuat...</option>
    </select>
    <input type="hidden" id="admProvinceName" name="province_name" value="{{ old('province_name', $c?->province_name) }}">
    <div class="form-text text-dim">Pilih provinsi untuk memuat daftar kota/kabupaten.</div>
</div>
<div class="col-md-3">
    <label class="form-label small text-dim">Kota/Kabupaten</label>
    <select id="admCitySelect" name="city_code" class="form-select form-select-sm bg-dark border-secondary text-light" data-current-code="{{ old('city_code', $c?->city_code) }}" disabled>
        <option value="">Pilih provinsi dulu</option>
    </select>
    <input type="hidden" id="admCityName" name="city_name" value="{{ old('city_name', $c?->city_name) }}">
 </div>
<div class="col-md-3">
    <label class="form-label small text-dim">Kode Pos</label>
    <input name="postal_code" value="{{ old('postal_code', $c?->postal_code) }}" class="form-control form-control-sm bg-dark border-secondary text-light">
</div>
<div class="col-md-3">
    <label class="form-label small text-dim">Foto PJBU</label>
    <input type="file" name="photo_pjbu" class="form-control form-control-sm bg-dark border-secondary text-light">
    @if($c && $c->photo_pjbu_path)<a target="_blank" class="small" href="{{ asset('storage/'.$c->photo_pjbu_path) }}">Lihat</a>@endif
</div>
<div class="col-md-3">
    <label class="form-label small text-dim">NPWP BU (PDF)</label>
    <input type="file" name="npwp_bu_file" class="form-control form-control-sm bg-dark border-secondary text-light">
    @if($c && $c->npwp_bu_path)<a target="_blank" class="small" href="{{ asset('storage/'.$c->npwp_bu_path) }}">Lihat</a>@endif
</div>
<div class="col-md-3">
    <label class="form-label small text-dim">Akte BU (PDF)</label>
    <input type="file" name="akte_bu_file" class="form-control form-control-sm bg-dark border-secondary text-light">
    @if($c && $c->akte_bu_path)<a target="_blank" class="small" href="{{ asset('storage/'.$c->akte_bu_path) }}">Lihat</a>@endif
</div>
<div class="col-md-3">
    <label class="form-label small text-dim">NIB (PDF)</label>
    <input type="file" name="nib_file" class="form-control form-control-sm bg-dark border-secondary text-light">
    @if($c && $c->nib_file_path)<a target="_blank" class="small" href="{{ asset('storage/'.$c->nib_file_path) }}">Lihat</a>@endif
</div>
<div class="col-md-3">
    <label class="form-label small text-dim">KTP PJBU (PDF)</label>
    <input type="file" name="ktp_pjbu_file" class="form-control form-control-sm bg-dark border-secondary text-light">
    @if($c && $c->ktp_pjbu_path)<a target="_blank" class="small" href="{{ asset('storage/'.$c->ktp_pjbu_path) }}">Lihat</a>@endif
</div>
<div class="col-md-3">
    <label class="form-label small text-dim">NPWP PJBU (PDF)</label>
    <input type="file" name="npwp_pjbu_file" class="form-control form-control-sm bg-dark border-secondary text-light">
    @if($c && $c->npwp_pjbu_path)<a target="_blank" class="small" href="{{ asset('storage/'.$c->npwp_pjbu_path) }}">Lihat</a>@endif
</div>