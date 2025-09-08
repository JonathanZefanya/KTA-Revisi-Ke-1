@php($user = auth()->user())
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->number }} | {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{background:#f1f5f9;font-family:system-ui,-apple-system,Segoe UI,Inter,Roboto,Ubuntu,sans-serif;color:#1e293b;}
        .invoice-wrap{max-width:920px;margin:40px auto;background:#fff;border:1px solid #e2e8f0;border-radius:22px;overflow:hidden;box-shadow:0 10px 30px -12px rgba(15,23,42,.18);} 
        .invoice-header{display:flex;justify-content:space-between;padding:2rem 2rem 1rem 2rem;border-bottom:1px solid #e2e8f0;background:linear-gradient(135deg,#2563eb,#1d4ed8);} 
        .invoice-header .brand{color:#fff;font-weight:600;font-size:1.15rem;letter-spacing:.5px;} 
        .invoice-header .meta{color:#e0ecff;font-size:.75rem;text-transform:uppercase;letter-spacing:1px;font-weight:500;} 
        .invoice-body{padding:2rem;} 
        .badge-status{display:inline-block;font-size:.65rem;padding:.4rem .65rem;border-radius:1rem;font-weight:600;letter-spacing:.5px;} 
        .badge-unpaid{background:#fef3c7;color:#92400e;} .badge-paid{background:#d1fae5;color:#065f46;} .badge-cancelled{background:#fee2e2;color:#991b1b;} 
        table{width:100%;border-collapse:collapse;} th,td{padding:.75rem .75rem;} th{text-align:left;font-size:.75rem;letter-spacing:.5px;color:#475569;text-transform:uppercase;border-bottom:1px solid #e2e8f0;} 
        tr.item-row td{border-bottom:1px dashed #e2e8f0;} 
        .total-row td{font-weight:600;font-size:1.05rem;border-top:2px solid #2563eb;} 
    .watermark{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%) rotate(-20deg);font-size:6rem;font-weight:700;color:rgba(30,58,138,0.04);pointer-events:none;user-select:none;} 
    .watermark-paid{position:absolute;top:52%;left:50%;transform:translate(-50%,-50%) rotate(-18deg);font-size:7rem;font-weight:800;letter-spacing:4px;color:rgba(16,185,129,.18);mix-blend-mode:multiply;pointer-events:none;user-select:none;text-shadow:0 0 4px rgba(16,185,129,.3);} 
        .footer{padding:1rem 2rem 2rem 2rem;font-size:.7rem;color:#64748b;}
        .bank-box{border:1px solid #e2e8f0;border-radius:14px;padding:1rem;margin-top:1rem;}
        .pay-instruction{background:#f8fafc;border:1px solid #e2e8f0;padding:1rem;border-radius:12px;font-size:.8rem;line-height:1.4;}
    </style>
</head>
<body>
<div class="invoice-wrap position-relative">
    <div class="watermark">INVOICE</div>
    @if($invoice->status==='paid')
        <div class="watermark-paid">PAID</div>
    @endif
    <div class="invoice-header">
        <div>
            <div class="brand">{{ config('app.name') }}</div>
            <div class="meta mt-2">INVOICE RESMI</div>
        </div>
        <div class="text-end text-white small" style="min-width:220px;">
            <div><strong>No:</strong> {{ $invoice->number }}</div>
            <div><strong>Tanggal:</strong> {{ $invoice->issued_date->format('d M Y') }}</div>
            <div><strong>Jatuh Tempo:</strong> {{ $invoice->due_date->format('d M Y') }}</div>
            <div><strong>Status:</strong>
                <span class="badge-status badge-{{ $invoice->status }}">{{ strtoupper($invoice->status) }}</span>
            </div>
        </div>
    </div>
    <div class="invoice-body">
        <div class="row mb-4">
            <div class="col-md-6 small">
                <div class="fw-semibold mb-1 text-uppercase text-primary">DITERBITKAN OLEH</div>
                <div>{{ config('app.name') }}</div>
                <div>Email: {{ $user->email }}</div>
            </div>
            <div class="col-md-6 small">
                <div class="fw-semibold mb-1 text-uppercase text-primary">DITAGIHKAN KEPADA</div>
                <div>{{ $user->name }}</div>
                <div>{{ $user->email }}</div>
                @if($invoice->meta && ($invoice->meta['company_name'] ?? false))
                    <div class="mt-1">Perusahaan: {{ $invoice->meta['company_name'] }}</div>
                @endif
            </div>
        </div>
        <table class="mb-4">
            <thead><tr><th style="width:55%">Deskripsi</th><th style="width:15%">Tipe</th><th style="width:15%" class="text-end">Jumlah</th><th style="width:15%" class="text-end">Subtotal</th></tr></thead>
            <tbody>
            <tr class="item-row">
                <td>Biaya {{ $invoice->type==='registration'?'Registrasi Awal':'Perpanjangan' }} Badan Usaha</td>
                <td>{{ ucfirst($invoice->type) }}</td>
                <td class="text-end">1</td>
                <td class="text-end">Rp {{ number_format($invoice->amount,0,',','.') }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="3" class="text-end">Total</td>
                <td class="text-end">Rp {{ number_format($invoice->amount,0,',','.') }}</td>
            </tr>
            </tbody>
        </table>
        <div class="pay-instruction mb-3">
            <strong>Instruksi Pembayaran:</strong><br>
            Silakan lakukan transfer sesuai total tagihan ke salah satu rekening resmi di bawah ini. Setelah transfer, unggah bukti pembayaran melalui formulir di bawah.
        </div>
        @php($banks = \App\Models\BankAccount::orderBy('sort')->orderBy('bank_name')->get())
        <div class="row">
            @forelse($banks as $b)
                <div class="col-md-6">
                    <div class="bank-box small @if($invoice->bank_account_id==$b->id) border-primary @endif" style="position:relative;">
                        @if($invoice->bank_account_id==$b->id)<span class="badge bg-primary" style="position:absolute;top:8px;right:8px;">Dipilih</span>@endif
                        <div class="fw-semibold">{{ $b->bank_name }}</div>
                        <div class="text-secondary">No: <span class="font-monospace">{{ $b->account_number }}</span></div>
                        <div class="text-secondary">a.n {{ $b->account_name }}</div>
                    </div>
                </div>
            @empty
                <div class="col-12 small text-secondary">Belum ada rekening bank ditambahkan.</div>
            @endforelse
        </div>
        <div class="mt-4">
            @if(session('success'))<div class="alert alert-success py-2 small mb-3">{{ session('success') }}</div>@endif
            @if($invoice->status==='unpaid' || $invoice->status==='awaiting_verification')
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body p-3">
                        <h6 class="mb-2">Upload Bukti Pembayaran</h6>
                        @if($invoice->payment_proof_path)
                            <p class="small mb-2">Bukti sudah diunggah pada {{ $invoice->proof_uploaded_at?->format('d M Y H:i') }}. Anda dapat menggantinya dengan mengunggah ulang.</p>
                            <p class="small mb-2">Lihat bukti: <a href="{{ asset('storage/'.$invoice->payment_proof_path) }}" target="_blank">Buka</a></p>
                        @endif
                        <form method="POST" action="{{ route('invoices.uploadProof',$invoice) }}" enctype="multipart/form-data" class="small d-flex flex-column gap-2">
                            @csrf
                            <input type="file" name="payment_proof" accept="application/pdf,image/png,image/jpeg" class="form-control form-control-sm" required>
                            <div class="text-muted small">Format: pdf/jpg/png, maks 10MB.</div>
                            <div><button class="btn btn-sm btn-primary">Unggah</button>
                                @if($invoice->payment_proof_path && $invoice->status==='awaiting_verification')
                                    <span class="badge bg-warning text-dark ms-2">Menunggu Verifikasi</span>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            @endif
            <a href="{{ route('invoices.pdf',$invoice) }}" class="btn btn-outline-secondary btn-sm">Download PDF</a>
        </div>
        <div class="small mt-3 text-secondary">* Invoice ini dibuat secara otomatis dan sah tanpa tanda tangan basah.</div>
    </div>
    <div class="footer d-flex justify-content-between align-items-center">
        <div>&copy; {{ date('Y') }} {{ config('app.name') }}</div>
        <div class="text-end">Halaman ini dapat dicetak sebagai bukti tagihan.</div>
    </div>
</div>
</body>
</html>
