@extends('admin.layout')
@section('title','Tambah Admin')
@section('page_title','Tambah Admin')
@section('breadcrumbs')<a href="{{ route('admin.admins.index') }}" class="text-decoration-none text-dim">Administrator</a> / Tambah @endsection
@section('content')
<div class="adm-card" style="max-width:640px;">
    <h5 class="mb-3">Form Tambah Admin</h5>
    @if($errors->any())
        <div class="alert alert-danger py-2 small">
            <ul class="m-0 ps-3">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('admin.admins.store') }}" class="small d-flex flex-column gap-3">
        @csrf
        <div>
            <label class="form-label mb-1">Nama</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control form-control-sm" required>
        </div>
        <div>
            <label class="form-label mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-sm" required>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label mb-1">Password</label>
                <input type="password" name="password" class="form-control form-control-sm" required>
                <div class="form-text">Min 6 karakter.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label mb-1">Role</label>
                <select name="role" class="form-select form-select-sm" required>
                    <option value="admin" @selected(old('role')==='admin')>Admin Biasa</option>
                    <option value="superadmin" @selected(old('role')==='superadmin')>Superadmin</option>
                </select>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary btn-sm">Simpan</button>
            <a href="{{ route('admin.admins.index') }}" class="btn btn-outline-secondary btn-sm">Batal</a>
        </div>
    </form>
</div>
@endsection