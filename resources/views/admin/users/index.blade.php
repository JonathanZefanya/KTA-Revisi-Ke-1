@extends('admin.layout')

@section('title','Pengguna')
@section('page_title','Daftar Pengguna')

@section('content')
<style>
.stats-card {
    background: linear-gradient(135deg, var(--adm-card), var(--adm-accent));
    border: 1px solid var(--adm-border);
    border-radius: 8px;
    padding: 1rem;
    text-align: center;
}
.stats-card .stats-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--adm-text);
}
.stats-card .stats-label {
    font-size: 0.8rem;
    color: var(--adm-text-dim);
    margin-top: 0.25rem;
}
.filter-section {
    background: var(--adm-card);
    border: 1px solid var(--adm-border);
    border-radius: 8px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
}
.action-toolbar {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}
@media (max-width: 768px) {
    .action-toolbar {
        flex-direction: column;
    }
    .action-toolbar .btn {
        width: 100%;
    }
}
</style>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stats-card">
            <div class="stats-value">{{ number_format($users->total()) }}</div>
            <div class="stats-label"><i class="bi bi-people-fill me-1"></i>Total Pengguna</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card">
            <div class="stats-value text-warning">{{ number_format(\App\Models\User::whereNull('approved_at')->count()) }}</div>
            <div class="stats-label"><i class="bi bi-clock-history me-1"></i>Pending Approval</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card">
            <div class="stats-value text-success">{{ number_format(\App\Models\User::whereNotNull('approved_at')->count()) }}</div>
            <div class="stats-label"><i class="bi bi-check-circle-fill me-1"></i>Approved</div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="filter-section">
    <form method="get" class="row g-3 align-items-end">
        <div class="col-lg-3 col-md-6">
            <label class="form-label small text-dim mb-1"><i class="bi bi-search me-1"></i>Pencarian</label>
            <input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm bg-dark border-secondary text-light" placeholder="Nama / Email / Telp">
        </div>
        <div class="col-lg-2 col-md-6">
            <label class="form-label small text-dim mb-1"><i class="bi bi-funnel me-1"></i>Status</label>
            <select name="status" class="form-select form-select-sm bg-dark border-secondary text-light">
                <option value="">Semua</option>
                <option value="pending" @selected($status==='pending')>Pending</option>
                <option value="approved" @selected($status==='approved')>Approved</option>
            </select>
        </div>
        <div class="col-lg-2 col-md-6">
            <label class="form-label small text-dim mb-1">Bulan Berakhir</label>
            <input type="month" name="bulan_berakhir" 
                value="{{ $bulanBerakhir }}" 
                class="form-control form-control-sm bg-dark border-secondary text-light">
        </div>

        <div class="col-lg-2 col-md-6">
            <label class="form-label small text-dim mb-1">Status KTA</label>
            <select name="kta_status" class="form-select form-select-sm bg-dark border-secondary text-light">
                <option value="">Semua</option>
                <option value="1" @selected($ktaStatus==='1')>Aktif</option>
                <option value="0" @selected($ktaStatus==='0')>Tidak Aktif</option>
            </select>
        </div>

        <div class="col-lg-7 col-md-12">
            <div class="action-toolbar">
                <button class="btn btn-sm btn-primary"><i class="bi bi-funnel-fill me-1"></i>Filter</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-clockwise me-1"></i>Reset</a>
                <a href="{{ route('admin.users.export', request()->only(['q', 'status', 'bulan_berakhir', 'kta_status'])) }}" class="btn btn-sm btn-success">
                    <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
                </a>
                <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-outline-primary ms-auto"><i class="bi bi-plus-circle me-1"></i>Tambah User</a>
            </div>
        </div>
    </form>
</div>
<!-- Table Card -->
<div class="adm-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-0"><i class="bi bi-table me-2"></i>Data Pengguna</h6>
        <div class="text-dim small">
            Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }}
        </div>
    </div>

    <form id="bulk-approve-form" method="post" action="{{ route('admin.users.bulkApprove') }}" onsubmit="return confirm('Setujui user terpilih?')">
        @csrf
        
        <!-- Bulk Actions Toolbar -->
        <div class="mb-3 p-2 bg-dark rounded d-flex gap-2 flex-wrap align-items-center">
            <input type="checkbox" onclick="toggleAll(this)" class="form-check-input" id="selectAll">
            <label for="selectAll" class="form-check-label text-dim small me-auto">Pilih Semua</label>
            <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-check-circle me-1"></i>Bulk Approve</button>
            <button type="button" class="btn btn-sm btn-danger" onclick="bulkDeleteUsers()"><i class="bi bi-trash me-1"></i>Bulk Delete</button>
        </div>

        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                <tr>
                    <th width="40"></th>
                    <th width="50">#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Telp</th>
                    <th>Tgl Daftar</th>
                    <th width="100">Status</th>
                    <th width="80">Aktif</th>
                    <th width="320">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($users as $i => $u)
                    <tr>
                        <td><input type="checkbox" name="ids[]" value="{{ $u->id }}" class="row-check form-check-input"></td>
                        <td class="text-dim">{{ $users->firstItem() + $i }}</td>
                        <td>
                            <div class="fw-semibold text-light">{{ $u->name }}</div>
                            @if($u->companies->first())
                                <div class="small text-info">{{ $u->companies->first()->name }}</div>
                            @endif
                        </td>
                        <td class="small">{{ $u->email }}</td>
                        <td class="small">{{ $u->company_phone ?? '-' }}</td>
                        <td class="small text-dim">{{ $u->created_at?->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($u->approved_at)
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Approved</span>
                            @else
                                <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Pending</span>
                            @endif
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input toggle-active" type="checkbox" data-user-id="{{ $u->id }}" {{ $u->is_active ? 'checked' : '' }}>
                            </div>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin.users.show',$u) }}" class="btn btn-outline-secondary" title="Detail"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('admin.users.edit',$u) }}" class="btn btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <button type="button" class="btn btn-outline-danger del-user-btn" data-user-id="{{ $u->id }}" title="Hapus"><i class="bi bi-trash"></i></button>
                            </div>
                            @if(!$u->approved_at)
                                <button
                                    class="btn btn-sm btn-success ms-1"
                                    formaction="{{ route('admin.users.approve',$u) }}"
                                    formmethod="POST"
                                    onclick="return confirm('Setujui user ini?')"
                                    name="_token"
                                    value="{{ csrf_token() }}"
                                    title="Approve"
                                ><i class="bi bi-check-lg"></i> Approve</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-5 text-dim">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                        <div class="mt-2">Tidak ada data pengguna</div>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </form>

    <!-- Pagination -->
    <div class="mt-3">
        {{ $users->links() }}
    </div>
</div>
    
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

        // Toggle Active Status
        document.querySelectorAll('.toggle-active').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const userId = this.getAttribute('data-user-id');
                const isActive = this.checked;
                
                fetch(`/admin/users/${userId}/toggle-active`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ is_active: isActive })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Optional: show success notification
                        console.log('Status updated successfully');
                    } else {
                        // Revert toggle if failed
                        this.checked = !isActive;
                        alert('Gagal mengubah status');
                    }
                })
                .catch(error => {
                    // Revert toggle if error
                    this.checked = !isActive;
                    alert('Terjadi kesalahan');
                    console.error('Error:', error);
                });
            });
        });
    </script>
@endsection
