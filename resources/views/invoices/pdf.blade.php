@php($user=$invoice->user)
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Invoice {{ $invoice->number }}</title>
<style>body{font-family:DejaVu Sans,Arial,sans-serif;font-size:12px;color:#111;margin:0;padding:28px;} h1{font-size:20px;margin:0 0 4px;} table{width:100%;border-collapse:collapse;margin-top:14px;} th,td{padding:6px 8px;font-size:12px;} th{background:#f2f6fc;text-align:left;} .tot{font-weight:700;border-top:2px solid #333;} .meta td{padding:2px 4px;font-size:11px;} .badge{display:inline-block;padding:2px 8px;font-size:10px;border-radius:12px;background:#eee;} .footer{margin-top:40px;font-size:10px;color:#555;} .logo{width:70px;height:70px;object-fit:cover;border:1px solid #ccc;border-radius:8px;} .signature-box{margin-top:50px;text-align:right;font-size:11px;} .signature-box img{max-width:140px;max-height:70px;display:block;margin-left:auto;margin-right:0;}
.water{position:fixed;top:40%;left:50%;transform:translate(-50%,-50%) rotate(-30deg);font-size:70px;color:rgba(0,0,0,0.05);font-weight:700;}
.water-paid{position:fixed;top:42%;left:50%;transform:translate(-50%,-50%) rotate(-25deg);font-size:80px;font-weight:800;color:rgba(16,185,129,0.18);text-shadow:0 0 2px rgba(16,185,129,0.2);} 
</style></head><body>
<div class="water">INVOICE</div>
@if($invoice->status==='paid')
<div class="water-paid">PAID</div>
@endif
<table class="meta"><tr>
<td style="width:60%">
    <h1>Invoice</h1>
    <div>No: <strong>{{ $invoice->number }}</strong></div>
    <div>Tanggal: {{ $invoice->issued_date?->format('d M Y') }}</div>
    <div>Jatuh Tempo: {{ $invoice->due_date?->format('d M Y') }}</div>
    <div>Status: {{ strtoupper($invoice->status) }}</div>
</td>
<td style="text-align:right;vertical-align:top">
    @if($logo && file_exists(public_path('storage/'.$logo)))
        <img class="logo" src="{{ public_path('storage/'.$logo) }}" alt="Logo">
    @else
        <div style="font-size:16px;font-weight:600">{{ config('app.name') }}</div>
    @endif
</td>
</tr></table>
<table style="margin-top:10px"><tr><td style="width:50%;vertical-align:top">
    <strong>Diterbitkan Oleh</strong><br>{{ config('app.name') }}<br>
</td><td style="width:50%;vertical-align:top">
    <strong>Ditagihkan Kepada</strong><br>{{ $user->name }}<br>{{ $user->email }}<br>
    @if(($invoice->meta['company_name']??false))Perusahaan: {{ $invoice->meta['company_name'] }}@endif
</td></tr></table>
<table style="margin-top:18px"><thead><tr><th>Deskripsi</th><th style="width:80px">Qty</th><th style="width:120px">Subtotal (Rp)</th></tr></thead><tbody>
<tr><td>Biaya {{ $invoice->type==='registration'?'Registrasi':'Perpanjangan' }} Badan Usaha</td><td>1</td><td style="text-align:right">{{ number_format($invoice->amount,0,',','.') }}</td></tr>
<tr class="tot"><td colspan="2" style="text-align:right">Total</td><td style="text-align:right">{{ number_format($invoice->amount,0,',','.') }}</td></tr>
</tbody></table>
<div style="margin-top:20px;font-size:11px;">Silakan lakukan transfer sesuai total ke rekening resmi yang terdaftar. Simpan dokumen ini sebagai arsip pembayaran Anda.</div>
@if($invoice->bank_account_id && $invoice->bankAccount)
<div style="margin-top:10px;font-size:11px;padding:8px 10px;border:1px solid #ccc;border-radius:6px;">
    <strong>Rekening Dipilih:</strong><br>
    {{ $invoice->bankAccount->bank_name }} - {{ $invoice->bankAccount->account_number }} a.n {{ $invoice->bankAccount->account_name }}
</div>
@endif
<div class="signature-box">
    <div style="margin-bottom:50px;">&nbsp;</div>
    <div><strong>{{ config('app.name') }}</strong></div>
    @if($signature && file_exists(public_path('storage/'.$signature)))
        <img src="{{ public_path('storage/'.$signature) }}" alt="Signature">
    @endif
</div>
<div class="footer">Dokumen ini dibuat otomatis dan sah tanpa tanda tangan basah. &copy; {{ date('Y') }} {{ config('app.name') }}</div>
</body></html>
