@php
    $company = $user->companies()->first();
    $isPreview = isset($preview) && $preview;
    
    // Get template path from settings
    $templatePath = \App\Models\Setting::getValue('kta_template_path', 'img/kta_template.png');
    
    // Determine the full path
    if (str_starts_with($templatePath, 'uploads/')) {
        $bgPath = storage_path('app/public/' . $templatePath);
    } elseif (str_starts_with($templatePath, 'storage/')) {
        $bgPath = public_path($templatePath);
    } else {
        $bgPath = public_path($templatePath);
    }
    
    // Convert background image to base64 for iframe compatibility
    $bgBase64 = '';
    if(file_exists($bgPath)) {
        $imageData = file_get_contents($bgPath);
        $bgBase64 = 'data:image/png;base64,' . base64_encode($imageData);
    }
    
    // Get layout configuration
    $layoutConfig = json_decode(\App\Models\Setting::getValue('kta_layout_config', '{}'), true);
    $cfg = [
        'member_box' => $layoutConfig['member_box'] ?? ['left' => 50, 'top' => 53, 'fontSize' => 18],
        'title' => $layoutConfig['title'] ?? ['left' => 460, 'top' => 145, 'fontSize' => 18],
        'meta' => $layoutConfig['meta'] ?? ['left' => 260, 'top' => 190, 'width' => 460, 'fontSize' => 13, 'labelWidth' => 180],
        'expiry' => $layoutConfig['expiry'] ?? ['left' => 460, 'top' => 450, 'fontSize' => 12],
        'photo' => $layoutConfig['photo'] ?? ['left' => 262, 'top' => 438, 'width' => 95, 'height' => 125],
        'qr' => $layoutConfig['qr'] ?? ['right' => 50, 'bottom' => 20, 'width' => 50, 'height' => 50],
    ];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>KTA {{ $user->membership_card_number }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { 
        font-family: 'Arial', sans-serif; 
        @if($isPreview)
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        padding: 20px;
        @else
        background: #fff;
        @endif
    }
    .page{
        position:relative;
        width:1000px;
        height:620px;
        margin:0 auto;
        @if($isPreview)
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        border-radius: 8px;
        overflow: hidden;
        @endif
    }
    .bg{position:absolute;inset:0;width:100%;height:100%;object-fit:fill;}
    .layer{position:absolute;inset:0;}

    /* Nomor Anggota */
    .member-box{
        position:absolute;
        left:{{ $cfg['member_box']['left'] }}px;
        top:{{ $cfg['member_box']['top'] }}px;
        padding:6px 12px;font-weight:700;
        font-size:{{ $cfg['member_box']['fontSize'] }}px;
        letter-spacing:1px;min-width:100px;text-align:center;
    }

    /* Judul */
    .title{
        position:absolute;
        top:{{ $cfg['title']['top'] }}px;
        left:{{ $cfg['title']['left'] }}px;
        font-weight:800;
        font-size:{{ $cfg['title']['fontSize'] }}px;
        text-decoration:underline;
    }

    /* Data perusahaan */
    .meta{
        position:absolute;
        left:{{ $cfg['meta']['left'] }}px;
        top:{{ $cfg['meta']['top'] }}px;
        width:{{ $cfg['meta']['width'] }}px;
        font-size:{{ $cfg['meta']['fontSize'] }}px;
    }
    .meta table {
        border-collapse: collapse;
        width: 130%;
        border: none;
    }
    .meta table td {
        border: none;
        padding: 15px 10px;
        vertical-align: top;
    }
    .meta table td:first-child {
        font-weight: 700;
        width: auto;
        white-space: nowrap;
        padding-left: 40px;
        padding-right: 10px;
    }
    .meta table td:nth-child(2) {
        width: auto;
        padding-right: 10px;
    }
    .meta table td:last-child {
        word-break: break-word;
    }

    /* Bar masa berlaku - border mengikuti panjang teks */
    .expiry{
        position:absolute;
        left:{{ $cfg['expiry']['left'] }}px;
        top:{{ $cfg['expiry']['top'] }}px;
        display:inline-block;
        padding:6px 12px;
        border:1px solid #000;
        background:#fff;
        font-weight:700;
        font-size:{{ $cfg['expiry']['fontSize'] }}px;
        line-height:1.3;
        text-align:center;
        max-width:460px; /* batasi agar tidak melewati kolom kanan */
    }

    /* Pas Foto */
    .photo{
        position:absolute;
        left:{{ $cfg['photo']['left'] }}px;
        top:{{ $cfg['photo']['top'] }}px;
        width:{{ $cfg['photo']['width'] }}px;
        height:{{ $cfg['photo']['height'] }}px;
        border:2px solid #000;overflow:hidden;background:#eee;
    }
    .photo img{width:100%;height:100%;object-fit:cover;}

    /* QR Code */
    .qr{
        position:absolute;
        right:{{ $cfg['qr']['right'] }}px;
        bottom:{{ $cfg['qr']['bottom'] }}px;
        width:{{ $cfg['qr']['width'] }}px;
        height:{{ $cfg['qr']['height'] }}px;
        border:1px solid #000;padding:4px;background:#fff;
    }
    .qr img{width:100%;height:100%;object-fit:contain;}
</style>

</head>
<body>
<div class="page">
    @if($bgBase64)
        <img class="bg" src="{{ $bgBase64 }}" alt="bg">
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
            <table>
                <tr>
                    <td>NAMA PERUSAHAAN</td>
                    <td>:</td>
                    <td>{{ $company->name }}</td>
                </tr>
                <tr>
                    <td>NAMA PIMPINAN</td>
                    <td>:</td>
                    <td>{{ $user->name }}</td>
                </tr>
                <tr>
                    <td>NO. NPWP</td>
                    <td>:</td>
                    <td>{{ $company->npwp ?? '-' }}</td>
                </tr>
                <tr>
                    <td>KUALIFIKASI</td>
                    <td>:</td>
                    <td>{{ $company->kualifikasi ?? '-' }}</td>
                </tr>
                <tr>
                    <td>ALAMAT PERUSAHAAN</td>
                    <td>:</td>
                    <td>{{ $company->address ?? '-' }}</td>
                </tr>
            </table>
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
