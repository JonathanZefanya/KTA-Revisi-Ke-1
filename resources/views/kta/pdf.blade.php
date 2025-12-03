@php
    $company = $user->companies()->first();
    $isPreview = isset($preview) && $preview;

    // Get template path from settings
    $templatePath = \App\Models\Setting::getValue('kta_template_path', 'img/kta_template.png');

    // Determine the full path (support uploads stored on public disk)
    if (str_starts_with($templatePath, 'uploads/')) {
        $bgPath = storage_path('app/public/' . $templatePath);
    } else {
        $bgPath = public_path($templatePath);
    }

    // Convert background image to base64 for PDF embedding
    $bgBase64 = '';
    if ($bgPath && file_exists($bgPath)) {
        $imageData = file_get_contents($bgPath);
        $mime = mime_content_type($bgPath) ?: 'image/png';
        $bgBase64 = 'data:' . $mime . ';base64,' . base64_encode($imageData);
    }

    // Get layout configuration
    $layoutConfig = json_decode(\App\Models\Setting::getValue('kta_layout_config', '{}'), true);
    $cfg = [
        'member_box' => $layoutConfig['member_box'] ?? ['left' => 1.3, 'top' => 1.2, 'fontSize' => 14],
        'title' => $layoutConfig['title'] ?? ['left' => 11.5, 'top' => 4.2, 'fontSize' => 16],
        'meta' => $layoutConfig['meta'] ?? ['left' => 7.5, 'top' => 6.5, 'width' => 16, 'fontSize' => 12, 'labelWidth' => 6],
        'expiry' => $layoutConfig['expiry'] ?? ['left' => 10.5, 'top' => 15.8, 'fontSize' => 11],
        'photo' => $layoutConfig['photo'] ?? ['left' => 7.3, 'top' => 14.5, 'width' => 3.5, 'height' => 4.8],
        'qr' => $layoutConfig['qr'] ?? ['right' => 1, 'bottom' => 1.8, 'width' => 3.5, 'height' => 3.5],
    ];

    // Prepare member/company photo as base64 (embed from storage)
    $photo = $user->membership_photo_path ?? ($company->photo_pjbu_path ?? null);
    $photoBase64 = '';
    if ($photo) {
        $photoPath = storage_path('app/public/' . ltrim($photo, '/'));
        if (!file_exists($photoPath)) {
            // Try public storage path
            $photoPath = public_path('storage/' . ltrim($photo, '/'));
        }
        if ($photoPath && file_exists($photoPath)) {
            $pData = file_get_contents($photoPath);
            $pMime = mime_content_type($photoPath) ?: 'image/jpeg';
            $photoBase64 = 'data:' . $pMime . ';base64,' . base64_encode($pData);
        }
    }
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
    /* Make PDF exactly the required physical size (cm) */
    @page { size: 29.7cm 21.28cm; margin: 0; }

    .page{
        position:relative;
        width:29.7cm;
        height:21.28cm;
        margin:0 auto;
        @if($isPreview)
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        border-radius: 8px;
        overflow: hidden;
        @endif
    }
    .bg{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;}
    .layer{position:absolute;inset:0;}

    /* Nomor Anggota */
    .member-box{
        position:absolute;
        left:{{ $cfg['member_box']['left'] }}cm;
        top:{{ $cfg['member_box']['top'] }}cm;
        padding:0.15cm 0.3cm;
        font-weight:700;
        font-size:{{ $cfg['member_box']['fontSize'] }}px;
        letter-spacing:0.05cm;
        min-width:2.5cm;
        text-align:center;
    }

    /* Judul */
    .title{
        position:absolute;
        top:{{ $cfg['title']['top'] }}cm;
        left:{{ $cfg['title']['left'] }}cm;
        font-weight:800;
        font-size:{{ $cfg['title']['fontSize'] }}px;
        text-decoration:underline;
    }

    /* Data perusahaan */
    .meta{
        position:absolute;
        left:{{ $cfg['meta']['left'] }}cm;
        top:{{ $cfg['meta']['top'] }}cm;
        width:{{ $cfg['meta']['width'] }}cm;
        font-size:{{ $cfg['meta']['fontSize'] }}px;
    }
    .meta table {
        border-collapse: collapse;
        width: 130%;
        border: none;
    }
    .meta table td {
        border: none;
        padding: 0.2cm 0.15cm;
        vertical-align: top;
        line-height: 1.4;
    }
    .meta table td:first-child {
        font-weight: 700;
        width: auto;
        white-space: nowrap;
        padding-right: 0.2cm;
    }
    .meta table td:nth-child(2) {
        width: 0.3cm;
        text-align: center;
        padding: 0.2cm 0.1cm;
    }
    .meta table td:last-child {
        word-break: break-word;
    }

    /* Bar masa berlaku */
    .expiry{
        position:absolute;
        left:{{ $cfg['expiry']['left'] }}cm;
        top:{{ $cfg['expiry']['top'] }}cm;
        display:inline-block;
        padding:0.15cm 0.3cm;
        border:1px solid #000;
        background:#fff;
        font-weight:700;
        font-size:{{ $cfg['expiry']['fontSize'] }}px;
        line-height:1.3;
        text-align:center;
        max-width:12cm;
    }

    /* Pas Foto */
    .photo{
        position:absolute;
        left:{{ $cfg['photo']['left'] }}cm;
        top:{{ $cfg['photo']['top'] }}cm;
        width:{{ $cfg['photo']['width'] }}cm;
        height:{{ $cfg['photo']['height'] }}cm;
        border:0.08cm solid #000;
        overflow:hidden;
        background:#eee;
        display:flex;
        align-items:center;
        justify-content:center;
    }
    .photo img{
        width:100%;
        height:100%;
        object-fit:cover;
        object-position:center;
    }

    /* QR Code */
    .qr{
        position:absolute;
        right:{{ $cfg['qr']['right'] }}cm;
        bottom:{{ $cfg['qr']['bottom'] }}cm;
        width:{{ $cfg['qr']['width'] }}cm;
        height:{{ $cfg['qr']['height'] }}cm;
        border:0.05cm solid #000;
        padding:0.05cm;
        background:#fff;
        display:flex;
        align-items:center;
        justify-content:center;
        z-index:100;
    }
    .qr img, .qr svg{
        max-width:100%;
        max-height:100%;
        object-fit:contain;
    }
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
                    <td>NO. NIB</td>
                    <td>:</td>
                    <td>{{ $company->nib ?? '-' }}</td>
                </tr>
                <tr>
                    <td>KUALIFIKASI</td>
                    <td>:</td>
                    <td>{{ $company->kualifikasi ?? '-' }}</td>
                </tr>
                <tr>
                    <td>ALAMAT PERUSAHAAN</td>
                    <td>:</td>
                    <td>{{ $company->address ?? '-' }}<br>
                        {{ $company->city_name ?? 'Kab/Kota: -' }}, Provinsi: {{ $company->province_name ?? '-' }}<br>
                        <b>No. Telp:</b> {{ $company->phone ?? '-' }}, <b>Email:</b> {{ $company->email ?? '-' }}
                    </td>
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
        @if($photoBase64)
            <div class="photo">
                <img src="{{ $photoBase64 }}" alt="Foto">
            </div>
        @endif

        <!-- QR Code for Validation -->
        @if(isset($qrPng) || isset($qrSvg))
        <div class="qr">
            @if(isset($qrPng) && !empty($qrPng))
                <img src="data:image/png;base64,{{ $qrPng }}" alt="QR">
            @elseif(isset($qrSvg) && !empty($qrSvg))
                {!! $qrSvg !!}
            @endif
        </div>
        @endif
    </div>
</div>
</body>
</html>
