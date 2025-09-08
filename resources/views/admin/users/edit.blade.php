@extends('admin.layout')
@section('title','Edit User')
@section('page_title','Edit User')
@section('content')
<div class="adm-card mb-4">
    @if(session('success'))
        <div class="alert alert-success py-2 small">{{ session('success') }}</div>
    @endif
    <form method="post" action="{{ route('admin.users.update',$user) }}" class="row g-3">
        @csrf @method('PUT')
        <div class="col-md-6">
            <label class="form-label small text-dim">Nama</label>
            <input name="name" value="{{ old('name',$user->name) }}" class="form-control form-control-sm bg-dark border-secondary text-light" required>
        </div>
        <div class="col-md-6">
            <label class="form-label small text-dim">Email</label>
            <input name="email" type="email" value="{{ old('email',$user->email) }}" class="form-control form-control-sm bg-dark border-secondary text-light" required>
        </div>
        <div class="col-md-4">
            <label class="form-label small text-dim">Telp</label>
            <input name="phone" value="{{ old('phone',$user->phone) }}" class="form-control form-control-sm bg-dark border-secondary text-light">
        </div>
        <div class="col-md-4">
            <label class="form-label small text-dim">Password (Kosongkan bila tidak ganti)</label>
            <input name="password" type="password" class="form-control form-control-sm bg-dark border-secondary text-light">
        </div>
        <div class="col-md-3 form-check ms-2 mt-2">
            <input type="checkbox" class="form-check-input" id="approve" name="approve" value="1" {{ old('approve')||$user->approved_at?'checked':'' }} {{ $user->approved_at ? 'disabled' : '' }}>
            <label for="approve" class="form-check-label small">Approved</label>
        </div>
        <div class="col-md-3 small text-dim">
            <label class="form-label small text-dim d-block">Status</label>
            @if($user->approved_at)
                <span class="badge bg-success">Approved {{ $user->approved_at->format('d/m/Y H:i') }}</span>
            @else
                <span class="badge bg-warning text-dark">Pending</span>
            @endif
        </div>
        <div class="col-12">
            <button class="btn btn-sm btn-primary">Simpan</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">Kembali</a>
        </div>
    </form>
    <hr class="border-secondary">
    <form action="{{ route('admin.users.destroy',$user) }}" method="POST" onsubmit="return confirm('Hapus user ini?')">
        @csrf @method('DELETE')
        <button class="btn btn-sm btn-outline-danger">Hapus User</button>
    </form>
</div>
@endsection
