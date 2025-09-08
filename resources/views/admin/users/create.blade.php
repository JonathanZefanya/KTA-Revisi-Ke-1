@extends('admin.layout')
@section('title','Buat User')
@section('page_title','Buat User')
@section('content')
<div class="adm-card mb-4">
    <form method="post" action="{{ route('admin.users.store') }}" class="row g-3">
        @csrf
        <div class="col-md-6">
            <label class="form-label small text-dim">Nama</label>
            <input name="name" value="{{ old('name') }}" class="form-control form-control-sm bg-dark border-secondary text-light" required>
        </div>
        <div class="col-md-6">
            <label class="form-label small text-dim">Email</label>
            <input name="email" type="email" value="{{ old('email') }}" class="form-control form-control-sm bg-dark border-secondary text-light" required>
        </div>
        <div class="col-md-4">
            <label class="form-label small text-dim">Telp</label>
            <input name="phone" value="{{ old('phone') }}" class="form-control form-control-sm bg-dark border-secondary text-light">
        </div>
        <div class="col-md-4">
            <label class="form-label small text-dim">Password</label>
            <input name="password" type="password" class="form-control form-control-sm bg-dark border-secondary text-light" required>
        </div>
        <div class="col-md-3 form-check ms-2 mt-2">
            <input type="checkbox" class="form-check-input" id="approve" name="approve" value="1" {{ old('approve')?'checked':'' }}>
            <label for="approve" class="form-check-label small">Approve Langsung</label>
        </div>
        <div class="col-12">
            <button class="btn btn-sm btn-primary">Simpan</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">Kembali</a>
        </div>
    </form>
</div>
@endsection
