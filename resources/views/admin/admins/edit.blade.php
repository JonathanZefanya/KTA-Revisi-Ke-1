@extends('admin.layout')
@section('title','Edit Admin')
@section('page_title','Edit Admin')
@section('breadcrumbs')<a href="{{ route('admin.admins.index') }}" class="text-decoration-none text-dim">Administrator</a> / Edit @endsection
@section('content')
<div class="adm-card" style="max-width:640px;">
    <h5 class="mb-3">Edit Administrator</h5>
    @if(session('error'))<div class="alert alert-danger py-2 small mb-2">{{ session('error') }}</div>@endif
    @if($errors->any())
        <div class="alert alert-danger py-2 small"><ul class="m-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    <form method="POST" action="{{ route('admin.admins.update',$admin) }}" class="small d-flex flex-column gap-3">
        @csrf @method('PUT')
        <div>
            <label class="form-label mb-1">Nama</label>
            <input type="text" name="name" value="{{ old('name',$admin->name) }}" class="form-control form-control-sm" required>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email',$admin->email) }}" class="form-control form-control-sm" required>
            </div>
            <div class="col-md-6">
                <label class="form-label mb-1">Role</label>
                <select name="role" class="form-select form-select-sm" required>
                    <option value="admin" @selected(old('role',$admin->role)==='admin')>Admin Biasa</option>
                    <option value="superadmin" @selected(old('role',$admin->role)==='superadmin')>Superadmin</option>
                </select>
            </div>
        </div>
        <div>
            <label class="form-label mb-1">Password (kosongkan jika tidak ganti)</label>
            <input type="password" name="password" class="form-control form-control-sm" placeholder="••••••">
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-primary">Simpan Perubahan</button>
            <a href="{{ route('admin.admins.index') }}" class="btn btn-sm btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection