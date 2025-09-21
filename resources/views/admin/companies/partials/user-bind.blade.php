<div class="col-12"><h5 class="mb-2">Pemilik Akun / Penanggung Jawab</h5></div>
@php($mode = $mode ?? 'create')
@if($mode === 'create')
<div class="col-md-12">
  <div class="d-flex gap-3 align-items-center flex-wrap">
    <div class="form-check">
      <input class="form-check-input" type="radio" name="user_mode" id="modeExisting" value="existing" {{ old('user_mode','existing')==='existing'?'checked':'' }}>
      <label class="form-check-label small" for="modeExisting">Pilih Pengguna</label>
    </div>
    <div class="form-check">
      <input class="form-check-input" type="radio" name="user_mode" id="modeNew" value="new" {{ old('user_mode')==='new'?'checked':'' }}>
      <label class="form-check-label small" for="modeNew">Tambah Pengguna Baru</label>
    </div>
  </div>
</div>
@endif
<div class="col-md-8" id="existingUserWrap">
  <label class="form-label small text-dim">Pilih Pengguna</label>
  <select name="existing_user_id" class="form-select form-select-sm bg-dark border-secondary text-light">
    <option value="">- Pilih Pengguna -</option>
    @php($sel = old('existing_user_id', $selectedUserId ?? null))
    @foreach(($users ?? collect()) as $u)
      <option value="{{ $u->id }}" @selected($sel==$u->id)>{{ $u->name }} — {{ $u->email }}</option>
    @endforeach
  </select>
  <div class="form-text text-dim">Nama Penanggung Jawab akan mengikuti nama pengguna yang dipilih.</div>
</div>
@if($mode === 'create')
<div class="col-md-8" id="newUserWrap" style="display:none">
  <div class="row g-3">
    <div class="col-md-6">
      <label class="form-label small text-dim">Nama</label>
      <input name="user_name" value="{{ old('user_name') }}" class="form-control form-control-sm bg-dark border-secondary text-light">
    </div>
    <div class="col-md-6">
      <label class="form-label small text-dim">Email</label>
      <input type="email" name="user_email" value="{{ old('user_email') }}" class="form-control form-control-sm bg-dark border-secondary text-light">
    </div>
    <div class="col-md-6">
      <label class="form-label small text-dim">No. HP</label>
      <input name="user_phone" value="{{ old('user_phone') }}" class="form-control form-control-sm bg-dark border-secondary text-light">
    </div>
    <div class="col-md-3">
      <label class="form-label small text-dim">Password</label>
      <input type="password" name="user_password" class="form-control form-control-sm bg-dark border-secondary text-light" autocomplete="new-password">
    </div>
    <div class="col-md-3">
      <label class="form-label small text-dim">Konfirmasi</label>
      <input type="password" name="user_password_confirmation" class="form-control form-control-sm bg-dark border-secondary text-light" autocomplete="new-password">
    </div>
    <div class="col-12 small text-dim">Pengguna baru akan dibuat dan ditautkan ke perusahaan; nama ini akan menjadi Penanggung Jawab.</div>
  </div>
</div>
@endif
@if($errors->any())
<div class="col-12">
  <div class="alert alert-danger py-2 small mb-0">
    <ul class="mb-0">
      @foreach($errors->all() as $err)
        <li>{{ $err }}</li>
      @endforeach
    </ul>
  </div>
</div>
@endif
