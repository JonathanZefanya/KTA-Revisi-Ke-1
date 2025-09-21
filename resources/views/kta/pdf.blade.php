@php
    $company = $user->companies()->first();
    $bgPath = public_path('img/kta_template.png');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>KTA {{ $user->membership_card_number }}</title>
<style>
    @page { margin: 0; }
    html, body { margin:0; padding:0; }
    body{font-family:DejaVu Sans,Arial,sans-serif;background:#fff;color:#000;}
    .page{position:relative;width:1000px;height:620px;margin:0 auto;}
    .bg{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;}
    .layer{position:absolute;inset:0;}

    /* Nomor Anggota (kotak putih di panel biru kiri) */
    .member-box{
        position:absolute;left:115px;top:60px;
        background:#fff;border:1px solid #000;border-radius:3px;
        padding:8px 14px;font-weight:700;font-size:18px;letter-spacing:1px;
        min-width:220px;text-align:center;
    }

    /* Judul tengah */
    .title{
        position:absolute;top:165px;left:240px;right:160px;
        text-align:center;font-weight:800;font-size:16px;text-decoration:underline;
    }

    /* Data perusahaan */
    .meta{position:absolute;left:470px;top:210px;width:470px;font-size:13px;}
    .row{display:flex;margin:3px 0;}
    .label{width:210px;font-weight:700}
    .val{flex:1}

    /* Bar masa berlaku */
    .expiry{position:absolute;left:470px;top:430px;width:420px;height:34px;
        border:1px solid #000;display:flex;align-items:center;justify-content:center;
        font-weight:700;font-size:12px;}

    /* Pas Foto (kiri bawah) */
    .photo{position:absolute;left:345px;top:455px;width:120px;height:160px;
        border:2px solid #000;overflow:hidden;background:#eee;display:flex;align-items:center;justify-content:center;}
    .photo img{width:100%;height:100%;object-fit:cover;}

    /* Tanggal terbit/valid (kecil di bawah) */
    .dates{position:absolute;left:470px;top:480px;font-size:12px;display:flex;gap:40px;font-weight:600}

    /* QR kanan bawah */
    .qr{position:absolute;right:145px;bottom:150px;width:130px;height:130px;border:1px solid #000;padding:6px;display:flex;align-items:center;justify-content:center;background:#fff;}
    .qr img{width:100%;height:100%;object-fit:contain;}
</style>
</head>
<body>
<div class="page">
    @if(file_exists($bgPath))
        <img class="bg" src="{{ $bgPath }}" alt="bg">
    @endif
    <div class="layer">
        <!-- Nomor Anggota -->
        @if($user->membership_card_number)
        <div class="member-box">{{ $user->membership_card_number }}</div>
        @endif

        <!-- Judul tengah -->
        <div class="title">KARTU TANDA ANGGOTA</div>

        <!-- Data Perusahaan -->
        @if($company)
        <div class="meta">
            <div class="row"><div class="label">NAMA PERUSAHAAN</div><div class="val">: {{ $company->name }}</div></div>
            <div class="row"><div class="label">NAMA PIMPINAN</div><div class="val">: {{ $user->name }}</div></div>
            <div class="row"><div class="label">NO. NPWP</div><div class="val">: {{ $company->npwp ?? '-' }}</div></div>
            <div class="row"><div class="label">KUALIFIKASI</div><div class="val">: {{ $company->kualifikasi ?? '-' }}</div></div>
            <div class="row"><div class="label">ALAMAT PERUSAHAAN</div><div class="val">: {{ $company->address ?? '-' }}</div></div>
        </div>
        @endif

        <!-- Bar Masa Berlaku -->
        <div class="expiry">BERLAKU SAMPAI DENGAN TANGGAL {{ optional($user->membership_card_expires_at)->format('d M Y') }}</div>

        <!-- Pas Foto -->
        @php($photo = $user->membership_photo_path ?? ($company->photo_pjbu_path ?? null))
        @if($photo)
            <div class="photo">
                <img src="{{ public_path('storage/'.$photo) }}" alt="Foto">
            </div>
        @endif

        <!-- Tanggal Terbit & Valid -->
        <div class="dates">
            <div>TERBIT: {{ optional($user->membership_card_issued_at)->format('d M Y') }}</div>
            <div>VALID S/D: {{ optional($user->membership_card_expires_at)->format('d M Y') }}</div>
        </div>

        <!-- QR Code -->
        <div class="qr">
            @if(!empty($qrPng))
                <img src="data:image/png;base64,{{ $qrPng }}" alt="QR">
            @else
                {!! $qrSvg !!}
            @endif
        </div>
    </div>
</div>
</body>
</html>
