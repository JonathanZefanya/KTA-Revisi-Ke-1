@php($user = auth()->user())
@extends('layouts.user')
@section('title','Pembayaran')
@section('content')
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h1 class="h5 fw-semibold mb-0">Pembayaran</h1>
            <div class="small text-secondary">Tagihan & status pembayaran</div>
        </div>
        @php($invoices = $invoices ?? collect())
        @if($invoices->isEmpty())
            <div class="surface p-3 small mb-4">
                <div class="fw-semibold mb-1">Belum ada Tagihan</div>
                <div class="text-secondary">Belum ada tagihan pembayaran.<br></div>
                <ul class="mt-2 mb-0 ps-3 text-secondary">
                    <li>Akun mungkin belum disetujui admin.</li>
                    <li>Belum ada data perusahaan terhubung.</li>
                    <li>Invoice otomatis dibuat saat admin menyetujui user pertama kali.</li>
                </ul>
                <div class="mt-2">Jika Anda baru saja disetujui, <a href="" onclick="location.reload();return false;">refresh</a> halaman ini.</div>
            </div>
        @else
            <div class="row g-3 mb-4">
                @foreach($invoices as $inv)
                    <div class="col-md-6 col-lg-4">
                        <div class="surface h-100 d-flex flex-column p-3 @if(isset($selected) && $selected && $selected->id===$inv->id) border border-primary @endif">
                            <div class="d-flex justify-content-between mb-2 align-items-start">
                                <div class="fw-semibold" style="font-size:.8rem;">{{ $inv->number }}</div>
                                @php($statusClass = match($inv->status){ 'paid'=>'success','cancelled'=>'danger','rejected'=>'danger','awaiting_verification'=>'warning','unpaid'=>'warning', default=>'neutral'})
                                <x-status-badge :type="$statusClass">{{ strtoupper($inv->status) }}</x-status-badge>
                            </div>
                            @php($due = $inv->due_date instanceof \Carbon\Carbon ? $inv->due_date : \Carbon\Carbon::parse($inv->due_date))
                            <div class="small text-secondary mb-2">{{ ucfirst($inv->type) }} • Jatuh tempo {{ $due->format('d M Y') }}</div>
                            <div class="mt-auto fw-semibold" style="font-size:.9rem;">Rp {{ number_format($inv->amount,0,',','.') }}</div>
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                <a href="{{ route('pembayaran',['invoice'=>$inv->id]) }}" class="btn btn-sm btn-outline-primary">{{ (isset($selected) && $selected && $selected->id===$inv->id)?'Sedang Dibuka':'Detail' }}</a>
                                <a href="{{ route('invoices.pdf',$inv) }}" class="btn btn-sm btn-link p-0">PDF</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @if(isset($selected) && $selected)
                <div class="surface mb-4 p-3">
                        <div class="d-flex justify-content-between flex-wrap align-items-start mb-3">
                            <div>
                                <h5 class="mb-1" style="font-size:1rem;">Invoice {{ $selected->number }}</h5>
                                @php($statusClass = match($selected->status){ 'paid'=>'success','cancelled'=>'danger','rejected'=>'danger','awaiting_verification'=>'warning','unpaid'=>'warning', default=>'neutral'})
                                <div class="small text-secondary">Status: <x-status-badge :type="$statusClass">{{ strtoupper($selected->status) }}</x-status-badge></div>
                            </div>
                            <div class="text-end small">
                                <div>Tanggal: {{ $selected->issued_date?->format('d M Y') }}</div>
                                <div>Jatuh Tempo: {{ $selected->due_date?->format('d M Y') }}</div>
                                @if($selected->paid_at)<div>Dibayar: {{ $selected->paid_at->format('d M Y H:i') }}</div>@endif
                            </div>
                        </div>
                        <div class="table-responsive mb-3">
                            <table class="table table-sm table-modern mb-0">
                                <thead><tr><th>Deskripsi</th><th class="text-end" style="width:160px">Subtotal (Rp)</th></tr></thead>
                                <tbody>
                                    <tr><td>Biaya {{ $selected->type==='registration'?'Registrasi':'Perpanjangan' }} Badan Usaha</td><td class="text-end">{{ number_format($selected->amount,0,',','.') }}</td></tr>
                                    <tr class="fw-semibold"><td class="text-end" colspan="2">Total: {{ number_format($selected->amount,0,',','.') }}</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mb-3 small text-secondary">Unggah bukti pembayaran setelah transfer ke rekening resmi. Pastikan nominal sesuai.</div>
                        <div class="mb-3 small">
                            <strong>Pilih Rekening Transfer</strong>
                            @php($banks = \App\Models\BankAccount::orderBy('sort')->orderBy('bank_name')->get())
                            <form method="POST" action="{{ route('invoices.selectBank',$selected) }}" class="d-flex flex-wrap gap-2 align-items-end mt-2">@csrf
                                <select name="bank_account_id" class="form-select form-select-sm" style="max-width:320px" @disabled($selected->payment_proof_path) required>
                                    <option value="">-- pilih bank --</option>
                                    @foreach($banks as $b)
                                        <option value="{{ $b->id }}" @selected($selected->bank_account_id==$b->id)>{{ $b->bank_name }} - {{ $b->account_number }} ({{ $b->account_name }})</option>
                                    @endforeach
                                </select>
                                @if(!$selected->payment_proof_path)
                                    <button class="btn btn-sm btn-outline-primary">Simpan</button>
                                @endif
                            </form>
                            @if($selected->bank_account_id)
                                <div class="mt-2 small text-success">Rekening dipilih: {{ $selected->bankAccount?->bank_name }} / {{ $selected->bankAccount?->account_number }}</div>
                            @endif
                        </div>
                        @if(session('success'))<div class="alert alert-success py-2 small">{{ session('success') }}</div>@endif
                        @if($selected->status==='unpaid' || $selected->status==='awaiting_verification')
                            <form method="POST" action="{{ route('invoices.uploadProof',$selected) }}" enctype="multipart/form-data" class="small d-flex flex-column gap-2 mb-3">
                                @csrf
                                <input type="file" name="payment_proof" accept="application/pdf,image/png,image/jpeg" class="form-control form-control-sm" required>
                                @if($selected->payment_proof_path)
                                    <div class="small">Bukti saat ini: <a href="{{ asset('storage/'.$selected->payment_proof_path) }}" target="_blank">Lihat</a></div>
                                @endif
                                <div class="text-muted small">Format: pdf/jpg/png, maks 10MB.</div>
                                <div><button class="btn btn-sm btn-primary">Unggah Bukti</button></div>
                            </form>
                        @endif
                        <a href="{{ route('invoices.pdf',$selected) }}" class="btn btn-outline-secondary btn-sm">Download PDF</a>
                    </div>
                </div>
            @endif
        @endif
@endsection
