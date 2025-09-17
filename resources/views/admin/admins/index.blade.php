@extends('admin.layout')
@section('title','Administrator')
@section('page_title','Administrator')
@section('breadcrumbs','Manajemen Admin')
@section('content')
    @if(session('success'))<div class="alert alert-success py-2 small mb-3">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger py-2 small mb-3">{{ session('error') }}</div>@endif
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="m-0">Daftar Administrator</h5>
        <a href="{{ route('admin.admins.create') }}" class="btn btn-sm btn-primary">Tambah Admin</a>
    </div>
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Dibuat</th><th style="width:150px">Aksi</th></tr></thead>
            <tbody>
                @forelse($admins as $a)
                    <tr>
                        <td>{{ $a->name }}</td>
                        <td>{{ $a->email }}</td>
                        <td>
                            @if($a->role==='superadmin')
                                <span class="badge bg-gradient" style="background:linear-gradient(120deg,#1e3a8a,#1d4ed8);">SUPERADMIN</span>
                            @else
                                <span class="badge bg-secondary">ADMIN</span>
                            @endif
                        </td>
                        <td class="text-dim">{{ $a->created_at?->format('d M Y H:i') }}</td>
                        <td class="d-flex flex-wrap gap-1">
                            <a href="{{ route('admin.admins.edit',$a) }}" class="btn btn-sm btn-outline-info">Edit</a>
                            @if(auth('admin')->id() !== $a->id)
                                <form method="POST" action="{{ route('admin.admins.destroy',$a) }}" onsubmit="return confirm('Hapus admin ini?');" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            @else
                                <span class="badge bg-secondary">Anda</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-dim py-4">Belum ada admin.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection