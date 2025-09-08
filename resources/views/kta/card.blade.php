@php($company = $user->companies()->first())
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>KTA</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{background:#f5f7fb;font-family:system-ui,-apple-system,Segoe UI,Inter,sans-serif;padding:34px;}
.preview-wrap{max-width:940px;margin:0 auto;display:flex;border:2px solid #0d479a;border-radius:16px;overflow:hidden;background:#fff;min-height:440px;box-shadow:0 8px 28px -10px rgba(0,40,90,.25);} 
.p-band{width:170px;background:linear-gradient(180deg,#0d479a,#0a60c2);color:#fff;padding:20px 16px;display:flex;flex-direction:column;align-items:center;position:relative;}
.p-band .logo{width:80px;height:80px;object-fit:cover;border:2px solid rgba(255,255,255,.5);border-radius:12px;background:#fff;margin-bottom:16px;position:relative;}
/* Micro security overlay inside logo area */
.p-band .logo-secure{position:relative;width:80px;height:80px;margin-bottom:16px;border:2px solid rgba(255,255,255,.5);border-radius:12px;overflow:hidden;display:flex;align-items:center;justify-content:center;background:#fff;}
.p-band .logo-secure img{width:100%;height:100%;object-fit:cover;border-radius:10px;}
.p-band .logo-secure .micro{position:absolute;inset:0;display:flex;flex-wrap:wrap;align-content:center;justify-content:center;font-size:6px;line-height:1.05;font-weight:600;letter-spacing:1px;text-align:center;color:rgba(13,71,154,.15);padding:6px;pointer-events:none;}
.p-band h2{font-size:14px;text-align:center;line-height:1.25;margin:0 0 6px;letter-spacing:.5px;}
.p-band small{font-size:9px;text-align:center;opacity:.85;}
.p-content{flex:1;padding:30px 40px 86px 40px;position:relative;display:flex;flex-direction:column;}
.p-number{position:absolute;top:0;right:0;background:#0d479a;color:#fff;padding:8px 18px;font-weight:600;font-size:12px;border-radius:0 0 0 10px;letter-spacing:.5px;}
table.meta{width:100%;margin-top:10px;font-size:.72rem;line-height:1.35;}
table.meta td{padding:3px 4px 4px;vertical-align:top;}
table.meta td.label{width:165px;font-weight:600;color:#053463;letter-spacing:.3px;}
.title{text-align:center;margin-top:2px;}
.title h1{font-size:1.08rem;margin:0 0 2px;letter-spacing:.06rem;font-weight:700;color:#0b2948;}
.title .en{font-size:.55rem;letter-spacing:.05rem;font-weight:600;color:#0d479a;}
.valid-bar{position:absolute;left:0;right:0;bottom:0;background:#0d479a;color:#fff;padding:12px 20px;font-size:.58rem;display:flex;justify-content:space-between;align-items:center;letter-spacing:.4px;}
.download-actions{margin:18px auto 0;max-width:940px;display:flex;gap:10px;}
</style></head><body>
<div class="preview-wrap">
    <div class="p-band">
        @php($micro = strtoupper(substr(preg_replace('/[^A-Z0-9]/','', $user->membership_card_number ?? ''),0,6)))
        @if(isset($logo) && $logo)
            <div class="logo-secure">
                <img src="{{ asset('storage/'.$logo) }}" alt="Logo">
                <div class="micro">{{ $micro }} • {{ $micro }} • {{ $micro }} • {{ $micro }}</div>
            </div>
        @else
            <div class="logo-secure text-primary fw-semibold" style="font-size:.6rem;">LOGO<div class="micro">{{ $micro }} • {{ $micro }}</div></div>
        @endif
        <h2>{{ strtoupper(config('app.name')) }}</h2>
    </div>
    <div class="p-content">
        <div class="p-number">NO: {{ $user->membership_card_number }}</div>
        <div class="title">
            <h1>KARTU TANDA ANGGOTA</h1>
            <div class="en">MEMBERSHIP IDENTIFICATION CARD</div>
        </div>
        @php($photo = $user->membership_photo_path ?? ($company->photo_pjbu_path ?? null))
    <div style="position:absolute;top:84px;right:40px;width:120px;height:156px;border:2px solid #0d479a;border-radius:10px;overflow:hidden;background:#fff;display:flex;align-items:center;justify-content:center;font-size:.55rem;font-weight:600;color:#0d479a;box-shadow:0 4px 12px -6px rgba(0,0,0,.25);">
            @if($photo)
                <img src="{{ asset('storage/'.$photo) }}" alt="Foto" style="width:100%;height:100%;object-fit:cover;">
            @else
                FOTO ANGGOTA
            @endif
        </div>
    <table class="meta" style="margin-right:150px;margin-top:18px;">
            <tr><td class="label">NAMA ANGGOTA</td><td>: {{ $user->name }}</td></tr>
            @if($company)
            <tr><td class="label">NAMA PERUSAHAAN</td><td>: {{ $company->name }}</td></tr>
            <tr><td class="label">NPWP</td><td>: {{ $company->npwp ?? '-' }}</td></tr>
            <tr><td class="label">KUALIFIKASI</td><td>: {{ $company->kualifikasi ?? '-' }}</td></tr>
            <tr><td class="label">ALAMAT</td><td>: {{ $company->address ?? '-' }}</td></tr>
            @endif
            <tr><td class="label">EMAIL</td><td>: {{ $user->email }}</td></tr>
            <tr><td class="label">TERBIT</td><td>: {{ optional($user->membership_card_issued_at)->format('d M Y') }}</td></tr>
            <tr><td class="label">BERLAKU S/D</td><td>: {{ optional($user->membership_card_expires_at)->format('d M Y') }}</td></tr>
        </table>
    <div style="margin-top:auto"></div>
    <div class="valid-bar"><span>VALID</span><span>QR DI VERSI PDF</span></div>
    </div>
</div>
<div class="download-actions">
    <a href="{{ route('kta.pdf') }}" class="btn btn-sm btn-primary">Download PDF</a>
    <a href="{{ route('kta') }}" class="btn btn-sm btn-outline-secondary">Kembali</a>
</div>
</body></html>
