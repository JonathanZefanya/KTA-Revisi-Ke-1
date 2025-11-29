@php
    $company = $user->companies()->first();
    $bgPath = public_path('img/kta_template.png');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>KTA - {{ $user->membership_card_number }}</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{
    background:#f5f7fb;
    font-family:system-ui,-apple-system,Segoe UI,Inter,sans-serif;
    padding:34px;
}
.preview-container{
    max-width:1000px;
    margin:0 auto;
}
.card-wrapper{
    position:relative;
    width:100%;
    height:0;
    padding-bottom:62%; /* Aspect ratio 1000:620 */
    border:2px solid #ccc;
    border-radius:8px;
    overflow:hidden;
    background:#fff;
    box-shadow:0 8px 28px -10px rgba(0,40,90,.25);
}
.card-inner{
    position:absolute;
    inset:0;
}
.bg{
    position:absolute;
    inset:0;
    width:100%;
    height:100%;
    object-fit:fill;
}
.layer{
    position:absolute;
    inset:0;
}

/* Nomor Anggota */
.member-box{
    position:absolute;
    left:5%;
    top:8.5%;
    padding:6px 12px;
    font-weight:700;
    font-size:clamp(12px, 1.8vw, 18px);
    letter-spacing:1px;
    min-width:100px;
    text-align:center;
    background:rgba(255,255,255,0.9);
    border-radius:4px;
}

/* Judul */
.title{
    position:absolute;
    top:23.4%;
    left:46%;
    font-weight:800;
    font-size:clamp(14px, 1.8vw, 18px);
    text-decoration:underline;
}

/* Data perusahaan */
.meta{
    position:absolute;
    left:26%;
    top:30.6%;
    width:46%;
    font-size:clamp(10px, 1.3vw, 13px);
    line-height:1.6;
}
.row{
    display:flex;
    margin:3px 0;
}
.label{
    flex:0 0 180px;
    max-width:180px;
    font-weight:700;
    white-space:nowrap;
}
.val{
    flex:1;
    min-width:0;
    word-break:break-word;
}

/* Bar masa berlaku */
.expiry{
    position:absolute;
    left:46%;
    top:72.5%;
    display:inline-block;
    padding:6px 12px;
    border:1px solid #000;
    background:#fff;
    font-weight:700;
    font-size:clamp(9px, 1.2vw, 12px);
    line-height:1.3;
    text-align:center;
    max-width:46%;
    border-radius:4px;
}

/* Pas Foto */
.photo{
    position:absolute;
    left:26.2%;
    top:70.6%;
    width:9.5%;
    height:20.2%;
    border:2px solid #000;
    overflow:hidden;
    background:#eee;
    border-radius:4px;
}
.photo img{
    width:100%;
    height:100%;
    object-fit:cover;
}

/* QR Code placeholder */
.qr{
    position:absolute;
    right:5%;
    bottom:3.2%;
    width:5%;
    height:8.1%;
    border:1px solid #000;
    padding:4px;
    background:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:10px;
    color:#666;
}

.download-actions{
    margin:18px auto 0;
    display:flex;
    gap:10px;
}

@media (max-width: 768px) {
    body{padding:15px;}
    .label{flex:0 0 120px;max-width:120px;font-size:10px;}
    .val{font-size:10px;}
}
</style>
</head>
<body>
<div class="preview-container">
    <div class="card-wrapper">
        <div class="card-inner">
            <!-- Background Image -->
            <img class="bg" src="{{ $bgPath }}" alt="KTA Background" onerror="this.style.display='none'">
            
            <div class="layer">
                <!-- Nomor Anggota -->
                @if($user->membership_card_number)
                <div class="member-box">{{ $user->membership_card_number }}</div>
                @endif

                <!-- Judul -->
                <div class="title">KARTU TANDA ANGGOTA</div>

                <!-- Data Perusahaan -->
                @if($company)
                <div class="meta">
                    <div class="row">
                        <div class="label">NAMA PERUSAHAAN</div>
                        <div class="val">: {{ $company->name }}</div>
                    </div>
                    <div class="row">
                        <div class="label">NAMA PIMPINAN</div>
                        <div class="val">: {{ $user->name }}</div>
                    </div>
                    <div class="row">
                        <div class="label">NO. NPWP</div>
                        <div class="val">: {{ $company->npwp ?? '-' }}</div>
                    </div>
                    <div class="row">
                        <div class="label">KUALIFIKASI</div>
                        <div class="val">: {{ $company->kualifikasi ?? '-' }}</div>
                    </div>
                    <div class="row">
                        <div class="label">ALAMAT PERUSAHAAN</div>
                        <div class="val">: {{ $company->address ?? '-' }}</div>
                    </div>
                </div>
                @endif

                <!-- Masa Berlaku -->
                <div class="expiry">
                    BERLAKU SAMPAI DENGAN TANGGAL<br>{{ optional($user->membership_card_expires_at)->format('d F Y') }}
                </div>

                <!-- Pas Foto -->
                @php($photo = $user->membership_photo_path ?? ($company->photo_pjbu_path ?? null))
                @if($photo)
                <div class="photo">
                    <img src="{{ asset('storage/'.$photo) }}" alt="Foto Anggota">
                </div>
                @else
                <div class="photo" style="display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:600;color:#666;">
                    FOTO
                </div>
                @endif

                <!-- QR Code -->
                <div class="qr">QR</div>
            </div>
        </div>
    </div>

    <div class="download-actions">
        <a href="{{ route('kta.pdf') }}" class="btn btn-sm btn-primary">Download PDF</a>
        <a href="{{ route('kta') }}" class="btn btn-sm btn-outline-secondary">Kembali</a>
    </div>
</div>
</body>
</html>
