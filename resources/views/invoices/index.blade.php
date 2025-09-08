@php($user = auth()->user())
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tagihan | {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body{background:#f5f7fb;font-family:system-ui,-apple-system,Segoe UI,Inter,Roboto,Ubuntu,sans-serif;} .card-invoice{border:1px solid #e5e7eb;border-radius:14px;padding:1rem;background:#fff;transition:.25s;} .card-invoice:hover{box-shadow:0 6px 20px -8px rgba(0,0,0,.12);transform:translateY(-2px);} .status-badge{font-size:.65rem;letter-spacing:.5px;padding:.25rem .5rem;border-radius:.5rem;font-weight:600;text-transform:uppercase;} .status-unpaid{background:#fff3cd;color:#856404;} .status-paid{background:#d1fae5;color:#065f46;} .status-cancelled{background:#f8d7da;color:#842029;} a{text-decoration:none}</style>
</head>
<body class="p-4">
    <h1 class="h5 mb-4">Tagihan</h1>
    <div class="row g-3">
        @forelse($invoices as $inv)
            <div class="col-md-4">
                <a href="{{ route('invoices.show',$inv) }}" class="card-invoice d-block h-100">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="fw-semibold">{{ $inv->number }}</div>
                        <span class="status-badge status-{{ $inv->status }}">{{ strtoupper($inv->status) }}</span>
                    </div>
                    <div class="small text-secondary mb-2">{{ ucfirst($inv->type) }} • Jatuh tempo {{ $inv->due_date->format('d M Y') }}</div>
                    <div class="fw-semibold">Rp {{ number_format($inv->amount,0,',','.') }}</div>
                </a>
            </div>
        @empty
            <div class="col-12 text-secondary small">Belum ada tagihan.</div>
        @endforelse
    </div>
    <div class="mt-4">{{ $invoices->links() }}</div>
</body>
</html>
