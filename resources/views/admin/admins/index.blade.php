@extends('admin.layout')
@section('title','Administrator')
@section('page_title','Administrator')
@section('breadcrumbs','Manajemen Admin')
@section('content')
<style>
.admin-header {
    background: linear-gradient(135deg, #312e81, #4338ca, #6366f1);
    border-radius: 8px;
    padding: 1.5rem;
    color: white;
    margin-bottom: 1.5rem;
}
.admin-header .stat-value {
    font-size: 2rem;
    font-weight: 700;
}
.admin-header .stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
}
</style>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-2 small mb-3" role="alert">
        <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show py-2 small mb-3" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i>{{ session('error') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Header Stats -->
<div class="admin-header">
    <div class="row align-items-center">
        <div class="col-md-6">
            <div class="d-flex align-items-center gap-3">
                <div>
                    <i class="bi bi-shield-lock" style="font-size: 3rem; opacity: 0.8;"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $admins->count() }}</div>
                    <div class="stat-label"><i class="bi bi-people me-1"></i>Total Administrator</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.admins.create') }}" class="btn btn-light">
                <i class="bi bi-plus-circle me-1"></i>Tambah Admin
            </a>
        </div>
    </div>
</div>

<!-- Table Section -->
<div class="adm-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-0"><i class="bi bi-table me-2"></i>Daftar Administrator</h6>
    </div>

    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th width="150">Role</th>
                    <th width="150">Dibuat</th>
                    <th width="200">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($admins as $a)
                    <tr>
                        <td>
                            <div class="fw-semibold text-light">{{ $a->name }}</div>
                            @if(auth('admin')->id() === $a->id)
                                <span class="badge bg-info small">Anda</span>
                            @endif
                        </td>
                        <td class="small">{{ $a->email }}</td>
                        <td>
                            @if($a->role==='superadmin')
                                <span class="badge bg-gradient" style="background:linear-gradient(120deg,#1e3a8a,#1d4ed8);">
                                    <i class="bi bi-star-fill me-1"></i>SUPERADMIN
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="bi bi-person-badge me-1"></i>ADMIN
                                </span>
                            @endif
                        </td>
                        <td class="text-dim small">{{ $a->created_at?->format('d M Y H:i') }}</td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap">
                                <a href="{{ route('admin.admins.edit',$a) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </a>
                                @if(auth('admin')->id() !== $a->id)
                                    <form method="POST" action="{{ route('admin.admins.destroy',$a) }}" onsubmit="return confirm('Hapus admin ini?');" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash me-1"></i>Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-5 text-dim">
                        <i class="bi bi-shield-lock" style="font-size: 2rem;"></i>
                        <div class="mt-2">Belum ada administrator</div>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection