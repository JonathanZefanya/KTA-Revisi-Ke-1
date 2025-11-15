@extends('admin.layout')
@section('title','Invoice')
@section('page_title','Invoice')
@section('content')
<style>
.invoice-stats {
    background: linear-gradient(135deg, #134e4a, #0f766e, #14b8a6);
    border-radius: 8px;
    padding: 1.5rem;
    color: white;
    margin-bottom: 1.5rem;
}
.invoice-stats .stat-item {
    text-align: center;
}
.invoice-stats .stat-value {
    font-size: 1.75rem;
    font-weight: 700;
}
.invoice-stats .stat-label {
    font-size: 0.8rem;
    opacity: 0.9;
}
.filter-toolbar {
    background: var(--adm-card);
    border: 1px solid var(--adm-border);
    border-radius: 8px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
}
</style>

<!-- Invoice Stats -->
@php
    $totalInvoices = \App\Models\Invoice::count();
    $unpaidInvoices = \App\Models\Invoice::where('status', 'unpaid')->count();
    $awaitingInvoices = \App\Models\Invoice::where('status', 'awaiting_verification')->count();
    $paidInvoices = \App\Models\Invoice::where('status', 'paid')->count();
@endphp

<div class="invoice-stats">
    <div class="row g-3">
        <div class="col-lg-3 col-sm-6">
            <div class="stat-item">
                <div class="stat-value">{{ number_format($totalInvoices) }}</div>
                <div class="stat-label"><i class="bi bi-receipt me-1"></i>Total Invoice</div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="stat-item">
                <div class="stat-value text-danger">{{ number_format($unpaidInvoices) }}</div>
                <div class="stat-label"><i class="bi bi-exclamation-circle me-1"></i>Unpaid</div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="stat-item">
                <div class="stat-value text-warning">{{ number_format($awaitingInvoices) }}</div>
                <div class="stat-label"><i class="bi bi-hourglass-split me-1"></i>Menunggu Verifikasi</div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="stat-item">
                <div class="stat-value text-success">{{ number_format($paidInvoices) }}</div>
                <div class="stat-label"><i class="bi bi-check-circle me-1"></i>Paid</div>
            </div>
        </div>
    </div>
</div>

<!-- Filter & Actions -->
<div class="filter-toolbar">
    <form class="row g-3 align-items-end" method="get">
        <div class="col-lg-3 col-md-6">
            <label class="form-label small mb-1 text-dim"><i class="bi bi-funnel me-1"></i>Status</label>
            <select name="status" class="form-select form-select-sm bg-dark border-secondary text-light" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                @foreach(['unpaid'=>'Unpaid','awaiting_verification'=>'Menunggu Verifikasi','paid'=>'Paid','rejected'=>'Ditolak'] as $k=>$v)
                    <option value="{{ $k }}" @selected(request('status')===$k)>{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-9 col-md-6">
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.invoices.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Tambah Invoice
                </a>
                <a href="{{ route('admin.invoices.export', request()->only(['status'])) }}" class="btn btn-sm btn-success">
                    <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
                </a>
            </div>
        </div>
    </form>
</div>
<!-- Table Section -->
<div class="adm-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-0"><i class="bi bi-receipt-cutoff me-2"></i>Data Invoice</h6>
        <div class="text-dim small">
            Menampilkan {{ $invoices->firstItem() ?? 0 }} - {{ $invoices->lastItem() ?? 0 }} dari {{ $invoices->total() }}
        </div>
    </div>

    <div class="table-responsive small">
        <table class="table table-sm table-dark table-hover align-middle">
            <thead>
                <tr>
                    <th width="120">No Invoice</th>
                    <th>User</th>
                    <th width="120">Type</th>
                    <th width="150" class="text-end">Amount</th>
                    <th width="180">Status</th>
                    <th width="100" class="text-center">Bukti</th>
                    <th width="100" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($invoices as $inv)
                <tr>
                    <td class="font-monospace small fw-semibold">{{ $inv->number }}</td>
                    <td>
                        <div class="fw-semibold text-light">{{ $inv->user->name }}</div>
                        <div class="text-info small">{{ $inv->user->email }}</div>
                    </td>
                    <td>
                        @if($inv->type === 'registration')
                            <span class="badge bg-primary"><i class="bi bi-person-plus me-1"></i>Registrasi</span>
                        @else
                            <span class="badge bg-info"><i class="bi bi-arrow-repeat me-1"></i>Renewal</span>
                        @endif
                    </td>
                    <td class="text-end fw-semibold text-warning">Rp {{ number_format($inv->amount,0,',','.') }}</td>
                    <td>
                        @switch($inv->status)
                            @case('unpaid')
                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Unpaid</span>
                                @break
                            @case('awaiting_verification')
                                <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Menunggu Verifikasi</span>
                                @break
                            @case('paid')
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Paid</span>
                                @break
                            @case('rejected')
                                <span class="badge bg-secondary"><i class="bi bi-slash-circle me-1"></i>Ditolak</span>
                                @break
                        @endswitch
                    </td>
                    <td class="text-center">
                        @if($inv->payment_proof_path)
                            <a href="{{ asset('storage/'.$inv->payment_proof_path) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-image"></i> Lihat
                            </a>
                        @else
                            <span class="text-dim">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.invoices.show',$inv) }}" class="btn btn-sm btn-outline-primary" title="Detail">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center py-5 text-dim">
                    <i class="bi bi-receipt" style="font-size: 2rem;"></i>
                    <div class="mt-2">Tidak ada data invoice</div>
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-3">
        {{ $invoices->links() }}
    </div>
</div>
@endsection
