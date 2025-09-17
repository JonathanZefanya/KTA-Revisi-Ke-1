@extends('admin.layout')

@section('title','Pengguna')
@section('page_title','Daftar Pengguna')

@section('content')
    <div class="adm-card mb-4">
        <form method="get" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-dim mb-1">Pencarian</label>
                <input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm bg-dark border-secondary text-light" placeholder="Nama / Email / Telp">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-dim mb-1">Status</label>
                <select name="status" class="form-select form-select-sm bg-dark border-secondary text-light">
                    <option value="">Semua</option>
                    <option value="pending" @selected($status==='pending')>Pending</option>
                    <option value="approved" @selected($status==='approved')>Approved</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-dim mb-1 d-block">&nbsp;</label>
                <button class="btn btn-sm btn-primary">Filter</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
            <div class="col-md-4 text-end small text-dim">
                <div>Total: <span class="text-light fw-semibold">{{ number_format($users->total()) }}</span></div>
                <div>Pending: <span class="text-warning fw-semibold">{{ number_format(\App\Models\User::whereNull('approved_at')->count()) }}</span></div>
            </div>
        </form>
    </div>
    <form method="post" action="{{ route('admin.users.bulkApprove') }}" onsubmit="return confirm('Setujui user terpilih?')">
        @csrf
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
            <tr>
                <th><input type="checkbox" onclick="toggleAll(this)"></th>
                <th>#</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Telp</th>
                <th>Daftar</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse($users as $i => $u)
                <tr>
                    <td><input type="checkbox" name="ids[]" value="{{ $u->id }}" class="row-check"></td>
                    <td>{{ $users->firstItem() + $i }}</td>
                    <td class="text-light">{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td>{{ $u->company_phone ?? '-' }}</td>
                    <td>{{ $u->created_at?->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($u->approved_at)
                            <span class="badge bg-success">Approved</span>
                        @else
                            <span class="badge bg-warning text-dark">Pending</span>
                        @endif
                    </td>
                    <td class="text-nowrap">
                        <a href="{{ route('admin.users.show',$u) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                        <a href="{{ route('admin.users.edit',$u) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <button type="button" class="btn btn-sm btn-outline-danger del-user-btn" data-user-id="{{ $u->id }}">Hapus</button>
                        @if(!$u->approved_at)
                            <form action="{{ route('admin.users.approve',$u) }}" method="POST" class="d-inline" onsubmit="return confirm('Setujui user ini?')">@csrf <button class="btn btn-sm btn-success">Approve</button></form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center py-4 text-dim">Tidak ada data</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-between mt-3 small">
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-outline-primary">Tambah</a>
            <button class="btn btn-sm btn-success">Bulk Approve</button>
        </div>
        <div>{{ $users->links() }}</div>
    </div>
    </form>
    <form id="delete-user-form" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>
    <script>
        function toggleAll(cb){document.querySelectorAll('.row-check').forEach(c=>c.checked=cb.checked);}  
        document.querySelectorAll('.del-user-btn').forEach(btn=>{
            btn.addEventListener('click', function(){
                const id = this.getAttribute('data-user-id');
                if(confirm('Hapus user ini?')){
                    const f = document.getElementById('delete-user-form');
                    f.action = '/admin/users/' + id;
                    f.submit();
                }
            });
        });
    </script>
@endsection
