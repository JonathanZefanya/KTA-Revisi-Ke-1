@php($company = $user->companies()->first())
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>KTA {{ $user->membership_card_number }}</title>
<style>
    body{font-family:DejaVu Sans,Arial,sans-serif;margin:0;background:#fff;color:#000;font-size:11.5px;}
    .kta-card{width:1000px;height:620px;border:2px solid #000;margin:0 auto;position:relative;box-sizing:border-box;padding:20px 30px;}
    
    /* Nomor Anggota box */
    .member-number{position:absolute;top:20px;left:20px;background:#0d479a;color:#fff;
        font-weight:700;font-size:15px;padding:10px 20px;border-radius:4px;}
    
    /* Header organisasi */
    .org-header{text-align:center;margin-top:40px;}
    .org-header h1{font-size:28px;margin:0;color:#000;font-weight:900;letter-spacing:1px;}
    .org-header .id{font-size:15px;font-weight:600;margin-top:4px;}
    .org-header .en{font-size:12px;font-weight:600;margin-top:2px;}
    
    .title{margin:20px 0 14px;text-align:center;font-size:15px;font-weight:700;text-decoration:underline;}
    
    table.meta{width:100%;font-size:12px;border-collapse:collapse;margin-bottom:14px;}
    table.meta td{padding:3px 4px;vertical-align:top;}
    table.meta td.label{width:190px;font-weight:600;}
    
    /* Foto */
    .photo{position:absolute;left:50px;bottom:120px;width:140px;height:190px;
        border:2px solid #000;overflow:hidden;background:#d00;display:flex;
        align-items:center;justify-content:center;color:#fff;font-size:12px;}
    .photo img{width:100%;height:100%;object-fit:cover;}
    
    /* Expiry bar */
    .expiry{
        border:1px solid #000;
        padding:5px 14px;
        display:inline-block;
        font-weight:600;
        font-size:11px;
        text-align:center;
        margin:0 auto 10px;
        width:auto; /* supaya sesuai teks */
    }
    
    /* Signature area */
    .signatures{display:flex;justify-content:space-around;position:absolute;bottom:90px;left:0;right:0;}
    .sign-box{text-align:center;font-size:10px;}
    .sign-box img{max-width:180px;max-height:70px;margin-bottom:4px;}
    .sign-box .line{height:1px;background:#000;width:180px;margin:6px auto;}
    .sign-box .label{font-weight:600;}
    
    /* QR Code */
    .qr{position:absolute;right:60px;bottom:120px;width:120px;height:120px;
        border:2px solid #000;padding:6px;display:flex;align-items:center;justify-content:center;}
    .qr img{width:100%;height:100%;object-fit:contain;}

    /* Logo */
    .logo img{
        max-height:60px;   /* tinggi maksimal logo */
        max-width:80px;    /* lebar maksimal logo */
        height:auto;
        width:auto;
    }

    /* Footer bar */
    .footer{position:absolute;left:0;right:0;bottom:0;background:#0d479a;color:#fff;
        display:flex;justify-content:space-between;padding:8px 24px;font-size:11px;font-weight:600;}
</style>
</head>
<body>
<div class="kta-card">
    
    <!-- Nomor Anggota -->
    <div class="member-number">{{ $user->membership_card_number }}</div>
    
    <!-- Header Organisasi -->
    <div class="org-header">
        <h1>{{ config('app.name') }}</h1>
    </div>

    <!-- Tambahkan Logo -->
    <div class="logo" style="text-align:center;margin:10px 0;">
        @if($logo)
            <img src="{{ isset($preview) ? asset('storage/'.$logo) : public_path('storage/'.$logo) }}" alt="Logo">
        @endif
    </div>

    <div class="title">KARTU TANDA ANGGOTA</div>
    
    <!-- Data Perusahaan -->
    <table class="meta">
        @if($company)
        <tr><td class="label">NAMA PERUSAHAAN</td><td>: {{ $company->name }}</td></tr>
        <tr><td class="label">NAMA PIMPINAN</td><td>: {{ $user->name }}</td></tr>
        <tr><td class="label">NO. NPWP</td><td>: {{ $company->npwp ?? '-' }}</td></tr>
        <tr><td class="label">KUALIFIKASI</td><td>: {{ $company->kualifikasi ?? '-' }}</td></tr>
        <tr><td class="label">ALAMAT PERUSAHAAN</td><td>: {{ $company->address ?? '-' }}</td></tr>
        @endif
    </table>
    
    <!-- Masa Berlaku -->
    <div style="text-align:center;">
        <div class="expiry">
            BERLAKU SAMPAI DENGAN TANGGAL {{ optional($user->membership_card_expires_at)->format('d M Y') }}
        </div>
    </div>
    
    <!-- Foto -->
    <div class="photo">
        @php($photo = $user->membership_photo_path ?? ($company->photo_pjbu_path ?? null))
        @if($photo)
            <img src="{{ isset($preview) ? asset('storage/'.$photo) : public_path('storage/'.$photo) }}" alt="Foto">
        @else
            FOTO
        @endif  
    </div>
    
    <!-- Signatures -->
    <div class="signatures">
        <div class="sign-box">
            @if($signature)
                <img src="{{ isset($preview) ? asset('storage/'.$signature) : public_path('storage/'.$signature) }}" alt="TTD Ketua">
            @endif
            <div class="line"></div>
            <div class="label"><br>Ketua Umum</div>
        </div>
    </div>
    
    <!-- QR Code -->
    <div class="qr">
        @if(!empty($qrPng))
            <img src="data:image/png;base64,{{ $qrPng }}" alt="QR">
        @else
            {!! $qrSvg !!}
        @endif
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <div>TERBIT: {{ optional($user->membership_card_issued_at)->format('d M Y') }}</div>
        <div>VALID S/D: {{ optional($user->membership_card_expires_at)->format('d M Y') }}</div>
        <div>QR VERIFIKASI</div>
    </div>
</div>
</body>
</html>
