@extends('admin.layout')
@section('title','Invoice')
@section('page_title','Invoice')
@section('content')
<div class="adm-card mb-3">
    <form class="row g-2 align-items-end small">
        <div class="col-auto">
            <label class="form-label small mb-1 text-dim">Status</label>
            <select name="status" class="form-select form-select-sm bg-dark border-secondary text-light" onchange="this.form.submit()">
                <option value="">Semua</option>
                @foreach(['unpaid'=>'Unpaid','awaiting_verification'=>'Menunggu Verifikasi','paid'=>'Paid','rejected'=>'Ditolak'] as $k=>$v)
                    <option value="{{ $k }}" @selected(request('status')===$k)>{{ $v }}</option>
                @endforeach
            </select>
        </div>
    </form>
    <div class="mt-2">
        <a href="{{ route('admin.invoices.create') }}" class="btn btn-sm btn-primary">Tambah Invoice</a>
        <a href="{{ route('admin.invoices.export', request()->only(['status'])) }}" class="btn btn-sm btn-success">
            <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="vertical-align:middle;margin-top:-2px;">
                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
            </svg> Export Excel
        </a>
    </div>
</div>
<div class="adm-card">
    <div class="table-responsive small">
        <table class="table table-sm table-dark align-middle">
            <thead><tr><th>No</th><th>User</th><th>Type</th><th>Amount</th><th>Status</th><th>Bukti</th><th></th></tr></thead>
            <tbody>
            @foreach($invoices as $inv)
                <tr>
                    <td class="font-monospace">{{ $inv->number }}</td>
                    <td>{{ $inv->user->name }}</td>
                    <td>{{ $inv->type }}</td>
                    <td class="text-end">Rp {{ number_format($inv->amount,0,',','.') }}</td>
                    <td>{{ $inv->status }}</td>
                    <td>@if($inv->payment_proof_path)<a href="{{ asset('storage/'.$inv->payment_proof_path) }}" target="_blank">Lihat</a>@else - @endif</td>
                    <td class="text-end"><a href="{{ route('admin.invoices.show',$inv) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-2">{{ $invoices->links() }}</div>
</div>
@endsection
