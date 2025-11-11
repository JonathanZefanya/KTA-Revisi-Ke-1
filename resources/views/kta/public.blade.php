<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Validasi KTA</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
 *{margin:0;padding:0;box-sizing:border-box;}
 body{
   font-family:'Georgia','Times New Roman',serif;
   margin:0;
   min-height:100vh;
   background:linear-gradient(135deg,#1e3c72 0%,#2a5298 50%,#7e8ba3 100%);
   color:#1a2332;
   padding:20px;
   position:relative;
   overflow-x:hidden;
 }
 body::before{
   content:'';
   position:fixed;
   top:0;
   left:0;
   right:0;
   bottom:0;
   background-image:
     repeating-linear-gradient(45deg,transparent,transparent 35px,rgba(255,255,255,.03) 35px,rgba(255,255,255,.03) 70px),
     repeating-linear-gradient(-45deg,transparent,transparent 35px,rgba(255,255,255,.02) 35px,rgba(255,255,255,.02) 70px);
   pointer-events:none;
   z-index:0;
 }
 .certificate-container{
   max-width:1000px;
   margin:30px auto;
   position:relative;
   z-index:1;
 }
 .certificate-wrap{
   background:#ffffff;
   border-radius:8px;
   overflow:hidden;
   box-shadow:0 20px 60px rgba(0,0,0,.3),0 0 0 1px rgba(255,255,255,.1);
   border:12px solid;
   border-image:linear-gradient(135deg,#c9b037 0%,#f3e87a 25%,#c9b037 50%,#b8941d 75%,#c9b037 100%) 1;
   position:relative;
 }
 .certificate-wrap::before{
   content:'';
   position:absolute;
   top:20px;
   left:20px;
   right:20px;
   bottom:20px;
   border:2px solid #0d479a;
   pointer-events:none;
   z-index:1;
 }
 .certificate-wrap::after{
   content:'';
   position:absolute;
   top:0;
   left:0;
   right:0;
   bottom:0;
   background:
     radial-gradient(circle at 20% 80%,rgba(13,71,154,.03) 0%,transparent 50%),
     radial-gradient(circle at 80% 20%,rgba(13,71,154,.03) 0%,transparent 50%);
   pointer-events:none;
 }
 .ornament-corner{
   position:absolute;
   width:80px;
   height:80px;
   z-index:2;
 }
 .ornament-corner.tl{top:35px;left:35px;border-top:3px solid #c9b037;border-left:3px solid #c9b037;}
 .ornament-corner.tr{top:35px;right:35px;border-top:3px solid #c9b037;border-right:3px solid #c9b037;}
 .ornament-corner.bl{bottom:35px;left:35px;border-bottom:3px solid #c9b037;border-left:3px solid #c9b037;}
 .ornament-corner.br{bottom:35px;right:35px;border-bottom:3px solid #c9b037;border-right:3px solid #c9b037;}
 header{
   padding:50px 60px 30px;
   text-align:center;
   position:relative;
   z-index:2;
   background:linear-gradient(to bottom,rgba(13,71,154,.02),transparent);
 }
 .logo{
   width:80px;
   height:80px;
   margin:0 auto 20px;
   background:#0d479a;
   border-radius:50%;
   display:flex;
   align-items:center;
   justify-content:center;
   font-size:32px;
   font-weight:bold;
   color:#fff;
   box-shadow:0 4px 12px rgba(13,71,154,.3);
 }
 h1{
   font-size:36px;
   font-weight:700;
   color:#0d479a;
   letter-spacing:2px;
   margin-bottom:8px;
   text-transform:uppercase;
   text-shadow:1px 1px 2px rgba(0,0,0,.05);
 }
 .subtitle{
   font-size:16px;
   color:#5a6c7d;
   letter-spacing:3px;
   text-transform:uppercase;
   font-weight:400;
   margin-bottom:20px;
 }
 .status{
   display:inline-block;
   padding:10px 28px;
   border-radius:50px;
   font-size:13px;
   font-weight:700;
   letter-spacing:1.5px;
   margin-top:10px;
   text-transform:uppercase;
   box-shadow:0 4px 12px rgba(0,0,0,.15);
   font-family:system-ui,Arial,sans-serif;
 }
 .status.valid{
   background:linear-gradient(135deg,#0d7a2b,#10a03a);
   color:#fff;
 }
 .status.expired{
   background:linear-gradient(135deg,#b12b2b,#d63838);
   color:#fff;
 }
 main{
   display:flex;
   gap:60px;
   padding:40px 70px 50px;
   position:relative;
   z-index:2;
 }
 .col-left{flex:1;min-width:0;}
 .data-section{
   background:linear-gradient(to right,rgba(13,71,154,.02),transparent);
   padding:25px 30px;
   border-radius:8px;
   border-left:4px solid #c9b037;
   margin-bottom:20px;
 }
 table{
   width:100%;
   border-collapse:collapse;
   font-size:15px;
   line-height:1.8;
 }
 td{
   padding:8px 6px;
   vertical-align:top;
 }
 td.label{
   width:190px;
   font-weight:600;
   color:#0d479a;
   letter-spacing:.5px;
   font-size:14px;
   text-transform:uppercase;
   font-family:system-ui,Arial,sans-serif;
 }
 td:last-child{
   color:#2a3f5f;
   font-size:15px;
 }
 .col-right{
   display:flex;
   flex-direction:column;
   align-items:center;
 }
 .photo-frame{
   position:relative;
   padding:8px;
   background:linear-gradient(135deg,#c9b037,#f3e87a,#c9b037);
   border-radius:8px;
   box-shadow:0 8px 24px rgba(0,0,0,.2);
   margin-bottom:20px;
 }
 .photo{
   width:200px;
   height:260px;
   border:4px solid #fff;
   border-radius:4px;
   overflow:hidden;
   background:#f8f9fa;
   display:flex;
   align-items:center;
   justify-content:center;
   font-weight:600;
   color:#0d479a;
   font-size:18px;
   letter-spacing:2px;
 }
 .photo img{width:100%;height:100%;object-fit:cover;}
 .verification-note{
   text-align:center;
   font-size:11px;
   line-height:1.6;
   color:#5a6c7d;
   max-width:220px;
   font-style:italic;
 }
 footer{
   background:linear-gradient(135deg,#0d479a,#1e5bb8);
   color:#fff;
   padding:20px 60px;
   display:flex;
   justify-content:space-between;
   align-items:center;
   flex-wrap:wrap;
   gap:15px;
   position:relative;
   z-index:2;
   box-shadow:0 -2px 10px rgba(0,0,0,.1);
 }
 .footer-badge{
   font-weight:700;
   letter-spacing:1.5px;
   font-size:13px;
   text-transform:uppercase;
 }
 .footer-info{
   font-size:11px;
   opacity:.9;
 }
 .invalid-note{
   background:#fff5f5;
   border-left:4px solid #b12b2b;
   padding:15px 20px;
   color:#b12b2b;
   font-weight:600;
   margin-top:20px;
   border-radius:4px;
   font-family:system-ui,Arial,sans-serif;
   font-size:14px;
 }
 .watermark{
   position:absolute;
   top:50%;
   left:50%;
   transform:translate(-50%,-50%) rotate(-45deg);
   font-size:120px;
   font-weight:900;
   color:rgba(13,71,154,.02);
   letter-spacing:10px;
   pointer-events:none;
   z-index:0;
   text-transform:uppercase;
 }
 @media print{
   body{background:#fff;padding:0;}
   .certificate-container{margin:0;}
   .certificate-wrap{box-shadow:none;}
 }
 @media(max-width:768px){
   main{flex-direction:column;gap:30px;padding:30px 40px;}
   .col-right{order:-1;}
   h1{font-size:28px;}
   .data-section{padding:20px;}
 }
</style></head><body>
 <div class="certificate-container">
  <div class="certificate-wrap">
    <!-- Ornamental Corners -->
    <div class="ornament-corner tl"></div>
    <div class="ornament-corner tr"></div>
    <div class="ornament-corner bl"></div>
    <div class="ornament-corner br"></div>
    
    <!-- Watermark -->
    <div class="watermark">KTA</div>
    
    <header>
      <div class="logo">KTA</div>
      <h1>Sertifikat Validasi</h1>
      <div class="subtitle">Kartu Tanda Anggota</div>
      <div class="status {{ $isValid ? 'valid':'expired' }}">
        {{ $isValid ? '✓ Masih Berlaku' : '✗ Tidak Berlaku' }}
      </div>
    </header>
    
    <main>
      <div class="col-left">
        <div class="data-section">
          <table>
            <tr><td class="label">Nama Anggota</td><td>: {{ $user->name }}</td></tr>
            @if($company)
            <tr><td class="label">Perusahaan</td><td>: {{ $company->name }}</td></tr>
            <tr><td class="label">NPWP</td><td>: {{ $company->npwp ?? '-' }}</td></tr>
            <tr><td class="label">Kualifikasi</td><td>: {{ $company->kualifikasi ?? '-' }}</td></tr>
            <tr><td class="label">Alamat</td><td>: {{ $company->address ?? '-' }}</td></tr>
            @endif
            <tr><td class="label">Email</td><td>: {{ $user->email }}</td></tr>
            <tr><td class="label">No. KTA</td><td>: {{ $user->membership_card_number }}</td></tr>
            <tr><td class="label">Tanggal Terbit</td><td>: {{ optional($user->membership_card_issued_at)->format('d M Y') }}</td></tr>
            <tr><td class="label">Berlaku Sampai</td><td>: {{ optional($user->membership_card_expires_at)->format('d M Y') }}</td></tr>
          </table>
        </div>
        @unless($isValid)
          <div class="invalid-note">
            ⚠️ Perhatian: Kartu tanda anggota ini tidak aktif atau telah kedaluwarsa.
          </div>
        @endunless
      </div>
      
      <div class="col-right">
        <div class="photo-frame">
          <div class="photo">
            @php($photo = $user->membership_photo_path ?? ($company->photo_pjbu_path ?? null))
            @if($photo)
              <img src="{{ asset('storage/'.$photo) }}" alt="Foto Anggota">
            @else
              FOTO
            @endif
          </div>
        </div>
        <div class="verification-note">
          Dokumen ini dihasilkan secara otomatis dan menampilkan status keabsahan kartu anggota secara real-time.
        </div>
      </div>
    </main>
    
    <footer>
      <div class="footer-badge">{{ config('app.name') }}</div>
      <div class="footer-info">Dihasilkan pada: {{ now()->format('d M Y H:i') }} WIB</div>
    </footer>
  </div>
 </div>
</body></html>
