@extends('admin.layout')
@section('title','KTA')
@section('page_title','Daftar KTA')

@section('content')
<style>
.kta-stats {
    background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
    border-radius: 8px;
    padding: 1.5rem;
    color: white;
    margin-bottom: 1.5rem;
}
.kta-stats .stat-item {
    text-align: center;
}
.kta-stats .stat-value {
    font-size: 1.75rem;
    font-weight: 700;
}
.kta-stats .stat-label {
    font-size: 0.8rem;
    opacity: 0.9;
}
.search-card {
    background: var(--adm-card);
    border: 1px solid var(--adm-border);
    border-radius: 8px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
}
</style>

<!-- KTA Stats -->
@php
    $totalKTA = \App\Models\User::whereNotNull('membership_card_number')->count();
    $activeKTA = \App\Models\User::whereNotNull('membership_card_number')
        ->where('membership_card_expires_at', '>=', now())->count();
    $expiredKTA = $totalKTA - $activeKTA;
@endphp

<div class="kta-stats">
    <div class="row g-3">
        <div class="col-md-4 col-sm-6">
            <div class="stat-item">
                <div class="stat-value">{{ number_format($totalKTA) }}</div>
                <div class="stat-label"><i class="bi bi-card-heading me-1"></i>Total KTA</div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="stat-item">
                <div class="stat-value text-success">{{ number_format($activeKTA) }}</div>
                <div class="stat-label"><i class="bi bi-check-circle me-1"></i>KTA Aktif</div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="stat-item">
                <div class="stat-value text-warning">{{ number_format($expiredKTA) }}</div>
                <div class="stat-label"><i class="bi bi-exclamation-triangle me-1"></i>KTA Expired</div>
            </div>
        </div>
    </div>
</div>

<!-- Search Section -->
<div class="search-card">
    <form class="row g-3 align-items-center" method="get">
        <div class="col-lg-6 col-md-8">
            <label class="form-label small text-dim mb-1"><i class="bi bi-search me-1"></i>Pencarian</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / email / no KTA" class="form-control form-control-sm bg-dark border-secondary text-light" />
        </div>
        <div class="col-lg-6 col-md-4">
            <label class="form-label small text-dim mb-1 d-block">&nbsp;</label>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-primary"><i class="bi bi-search me-1"></i>Cari</button>
                @if(request('q'))
                    <a href="{{ route('admin.kta.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-circle me-1"></i>Reset</a>
                @endif
            </div>
        </div>
    </form>
</div>
<!-- Table Section -->
<div class="adm-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-0"><i class="bi bi-card-heading me-2"></i>Data Kartu Tanda Anggota</h6>
        <div class="text-dim small">
            Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }}
        </div>
    </div>

    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th width="50">NO</th>
                    <th>Member</th>
                    <th width="150">No KTA</th>
                    <th>Perusahaan</th>
                    <th width="100">Terbit</th>
                    <th width="100">Expire</th>
                    <th width="100">Status</th>
                    <th width="300">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                @php($company = $u->companies->first())
                <tr>
                    <td class="text-dim">{{ $loop->iteration + ($users->currentPage()-1)*$users->perPage() }}</td>
                    <td>
                        <div class="fw-semibold text-light">{{ $u->name }}</div>
                        <div class="text-info small">{{ $u->email }}</div>
                    </td>
                    <td class="font-monospace small">{{ $u->membership_card_number }}</td>
                    <td class="small">{{ $company?->name ?? '-' }}</td>
                    <td class="small">{{ optional($u->membership_card_issued_at)->format('d M Y') }}</td>
                    <td class="small">{{ optional($u->membership_card_expires_at)->format('d M Y') }}</td>
                    <td>
                        @php($active = $u->hasActiveMembershipCard())
                        @if($active)
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>AKTIF</span>
                        @else
                            <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle me-1"></i>EXPIRED</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <a class="btn btn-outline-primary" href="{{ route('admin.kta.show',$u) }}" title="Lihat Preview KTA">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a class="btn btn-outline-danger" href="{{ route('admin.kta.pdf',[$u,'full'=>1]) }}" title="Download PDF">
                                <i class="bi bi-file-pdf"></i>
                            </a>
                            <a class="btn btn-outline-info" href="{{ route('kta.public',[ 'user'=>$u->id, 'number'=>str_replace(['/', '\\'],'-',$u->membership_card_number) ]) }}" target="_blank" title="Validasi Publik">
                                <i class="bi bi-shield-check"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-5 text-dim">
                    <i class="bi bi-card-heading" style="font-size: 2rem;"></i>
                    <div class="mt-2">Tidak ada data KTA</div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-3">
        {{ $users->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.querySelector('input[name="q"]');
    if (!input) return;
    
    input.addEventListener('input', function() {
        const q = this.value;
        fetch(`{{ route('admin.kta.index') }}?q=${encodeURIComponent(q)}`)
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTableBody = doc.querySelector('.adm-table tbody');
                if (newTableBody) {
                    document.querySelector('.adm-table tbody').innerHTML = newTableBody.innerHTML;
                }
            });
    });
});
</script>
@endpush