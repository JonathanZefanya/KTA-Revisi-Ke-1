@extends('admin.layout')
@section('title','Detail Invoice')
@section('page_title','Detail Invoice')
@section('content')
@if(session('success'))<div class="alert alert-success small">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger small">{{ session('error') }}</div>@endif
<div class="adm-card mb-3 small">
    <div class="d-flex justify-content-between flex-wrap gap-3">
        <div>
            <div class="fw-semibold">{{ $invoice->number }}</div>
            <div class="text-dim">User: {{ $invoice->user->name }} (ID {{ $invoice->user_id }})</div>
            <div class="text-dim">Type: {{ $invoice->type }}</div>
            <div class="text-dim">Dibuat: {{ $invoice->issued_date?->format('d M Y') }}</div>
            <div class="text-dim">Jatuh Tempo: {{ $invoice->due_date?->format('d M Y') }}</div>
            <div class="mt-2">Status: <span class="badge bg-secondary">{{ $invoice->status }}</span></div>
        </div>
        <div class="text-end">
            <div class="fw-semibold">Total</div>
            <div class="fs-5">Rp {{ number_format($invoice->amount,0,',','.') }}</div>
            @if($invoice->paid_at)<div class="text-success small mt-1">Paid at {{ $invoice->paid_at->format('d M Y H:i') }}</div>@endif
        </div>
    </div>
</div>
<div class="row g-3">
    <div class="col-lg-6">
        <div class="adm-card small mb-3">
            <h6 class="mb-2">Bukti Pembayaran</h6>
            @if($invoice->payment_proof_path)
                <div class="mb-2"><a href="{{ asset('storage/'.$invoice->payment_proof_path) }}" target="_blank">Lihat Bukti</a></div>
                <div class="text-dim">Diupload: {{ $invoice->proof_uploaded_at?->format('d M Y H:i') }}</div>
            @else
                <div class="text-dim">Belum ada bukti.</div>
            @endif
        </div>
    </div>
    <div class="col-lg-6">
        @if($invoice->status==='awaiting_verification')
        <div class="adm-card small">
            <h6 class="mb-2">Verifikasi</h6>
            <form method="POST" action="{{ route('admin.invoices.verify',$invoice) }}" class="d-grid gap-2">@csrf
                <textarea name="note" class="form-control form-control-sm bg-dark border-secondary text-light" placeholder="Catatan (opsional)" rows="2"></textarea>
                <div class="d-flex gap-2">
                    <button name="action" value="approve" class="btn btn-sm btn-success flex-fill" onclick="return confirm('Setujui pembayaran?')">Setujui</button>
                    <button name="action" value="reject" class="btn btn-sm btn-danger flex-fill" onclick="return confirm('Tolak pembayaran?')">Tolak</button>
                </div>
            </form>
        </div>
        @else
            <div class="adm-card small"><div class="text-dim">Tidak ada tindakan verifikasi untuk status ini.</div></div>
        @endif
        @if($invoice->verification_note)
            <div class="adm-card small mt-3"><strong>Catatan Verifikasi:</strong><br>{{ $invoice->verification_note }}</div>
        @endif
    </div>
</div>
@endsection
