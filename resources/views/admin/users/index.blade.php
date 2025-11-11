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
                <a href="{{ route('admin.users.export', request()->only(['q', 'status'])) }}" class="btn btn-sm btn-success">
                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="vertical-align:middle;margin-top:-2px;">
                        <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                        <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                    </svg> Export Excel
                </a>
            </div>
            <div class="col-md-4 text-end small text-dim">
                <div>Total: <span class="text-light fw-semibold">{{ number_format($users->total()) }}</span></div>
                <div>Pending: <span class="text-warning fw-semibold">{{ number_format(\App\Models\User::whereNull('approved_at')->count()) }}</span></div>
            </div>
        </form>
    </div>
    <form id="bulk-approve-form" method="post" action="{{ route('admin.users.bulkApprove') }}" onsubmit="return confirm('Setujui user terpilih?')">
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
                            <button
                                class="btn btn-sm btn-success"
                                formaction="{{ route('admin.users.approve',$u) }}"
                                formmethod="POST"
                                onclick="return confirm('Setujui user ini?')"
                                name="_token"
                                value="{{ csrf_token() }}"
                            >Approve</button>
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
            <button type="submit" class="btn btn-sm btn-success">Bulk Approve</button>
            <button type="button" class="btn btn-sm btn-danger" onclick="bulkDeleteUsers()">Bulk Delete</button>
        </div>
        <div>{{ $users->links() }}</div>
    </div>
    </form>
    
    <form id="bulk-delete-form" method="POST" action="{{ route('admin.users.bulkDelete') }}" style="display:none;">
        @csrf
    </form>
    <form id="delete-user-form" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>
    <script>
        function toggleAll(cb){document.querySelectorAll('.row-check').forEach(c=>c.checked=cb.checked);}
        
        function bulkDeleteUsers() {
            const checkedBoxes = document.querySelectorAll('.row-check:checked');
            
            if (checkedBoxes.length === 0) {
                alert('Pilih minimal 1 user untuk dihapus');
                return;
            }
            
            const confirmMsg = `Apakah Anda yakin ingin menghapus ${checkedBoxes.length} user?\n\n⚠️ PERHATIAN:\n` +
                `• User yang dipilih akan dihapus\n` +
                `• Data KTA akan dihapus\n` +
                `• Semua transaksi/invoice akan dihapus\n` +
                `• Perusahaan yang hanya dimiliki user ini akan dihapus\n\n` +
                `Tindakan ini TIDAK DAPAT dibatalkan!`;
            
            if (confirm(confirmMsg)) {
                const form = document.getElementById('bulk-delete-form');
                
                // Copy all checked IDs to bulk delete form
                checkedBoxes.forEach(cb => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = cb.value;
                    form.appendChild(input);
                });
                
                form.submit();
            }
        }
        
        document.querySelectorAll('.del-user-btn').forEach(btn=>{
            btn.addEventListener('click', function(){
                const id = this.getAttribute('data-user-id');
                if(confirm('Hapus user ini?\n\n⚠️ Data KTA, transaksi, dan perusahaan terkait juga akan dihapus!')){
                    const f = document.getElementById('delete-user-form');
                    f.action = '/admin/users/' + id;
                    f.submit();
                }
            });
        });
    </script>
@endsection
