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
    .page{position:relative;width:1000px;height:620px;margin:0 auto;}
    .bg{position:absolute;inset:0;width:100%;height:100%;object-fit:fill;}
    .layer{position:absolute;inset:0;}

    /* Nomor Anggota */
    .member-box{
        position:absolute;left:50px;top:53px;
        padding:6px 12px;font-weight:700;font-size:18px;letter-spacing:1px;
        min-width:100px;text-align:center;
    }

    /* Judul */
    .title{
        position:absolute;top:145px;left:460px;
        font-weight:800;font-size:18px;text-decoration:underline;
    }

    /* Data perusahaan */
    .meta{position:absolute;left:260px;top:190px;width:460px;font-size:13px;line-height:1.6;}
    .row{display:flex;margin:3px 0;}
    .label {
        flex:0 0 180px; 
        max-width:180px;
        font-weight:700;
        white-space:nowrap;
    }
    .val {
        flex:1;
        min-width:0;
    }

    /* Bar masa berlaku - border mengikuti panjang teks */
    .expiry{
        position:absolute;left:460px;top:450px;
        display:inline-block;
        padding:6px 12px;
        border:1px solid #000;
        background:#fff;
        font-weight:700;font-size:12px;line-height:1.3;
        text-align:center;
        max-width:460px; /* batasi agar tidak melewati kolom kanan */
    }

    /* Pas Foto */
    .photo{position:absolute;left:262px;top:438px;width:95px;height:125px;
        border:2px solid #000;overflow:hidden;background:#eee;}
    .photo img{width:100%;height:100%;object-fit:cover;}

    /* QR Code */
    .qr{position:absolute;right:50px;bottom:20px;width:50px;height:50px;border:1px solid #000;padding:4px;background:#fff;}
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

        <!-- Judul -->
        <div class="title">KARTU TANDA ANGGOTA</div>

        <!-- Data Perusahaan -->
        @if($company)
        <div class="meta">
            <div class="row">
                <div class="label">
                    NAMA PERUSAHAAN
                </div>
                <div class="val">
                    : {{ $company->name }}
                </div>
            </div>
            <div class="row">
                <div class="label">
                    NAMA PIMPINAN
                </div>
                <div class="val">
                    : {{ $user->name }}
                </div>
            </div>
            <div class="row">
                <div class="label">
                    NO. NPWP
                </div>
                <div class="val">
                    : {{ $company->npwp ?? '-' }}
                </div>
            </div>
            <div class="row">
                <div class="label">
                    KUALIFIKASI
                </div>
                <div class="val">
                    : {{ $company->kualifikasi ?? '-' }}
                </div>
            </div>
            <div class="row">
                <div class="label">
                    ALAMAT PERUSAHAAN
                </div>
                <div class="val">
                    : {{ $company->address ?? '-' }}
                </div>
            </div>
        </div>
        @endif

        <!-- Masa Berlaku -->
        <div class="expiry">
            BERLAKU SAMPAI DENGAN TANGGAL {{ optional($user->membership_card_expires_at)->format('d F Y') }}
        </div>

        <!-- Pas Foto -->
        @php($photo = $user->membership_photo_path ?? ($company->photo_pjbu_path ?? null))
        @if($photo)
            <div class="photo">
                <img src="{{ public_path('storage/'.$photo) }}" alt="Foto">
            </div>
        @endif

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
