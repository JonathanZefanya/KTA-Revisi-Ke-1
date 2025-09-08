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
