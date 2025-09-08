<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Validasi KTA</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
 body{font-family:system-ui,Segoe UI,Arial,sans-serif;margin:0;background:#0d479a;color:#0b2948;}
 .wrap{max-width:880px;margin:40px auto;background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 10px 34px -14px rgba(0,40,90,.35);}
 header{padding:28px 36px 16px;border-bottom:4px solid #0d479a1a;position:relative;}
 h1{margin:0 0 4px;font-size:26px;letter-spacing:.5px;color:#0d479a;}
 .status{display:inline-block;padding:6px 14px;border-radius:40px;font-size:12px;font-weight:600;letter-spacing:.5px;background:#eee;margin-top:8px;}
 .status.valid{background:#0d7a2b;color:#fff;}
 .status.expired{background:#b12b2b;color:#fff;}
 main{display:flex;gap:48px;padding:36px 40px 46px;}
 .col-left{flex:1;min-width:0;}
 table{width:100%;border-collapse:collapse;font-size:14px;line-height:1.35;}
 td{padding:3px 4px 4px;vertical-align:top;}
 td.label{width:170px;font-weight:600;color:#0d479a;letter-spacing:.3px;}
 .photo{width:190px;height:250px;border:3px solid #0d479a;border-radius:18px;overflow:hidden;background:#f2f6fb;display:flex;align-items:center;justify-content:center;font-weight:600;color:#0d479a;margin-bottom:18px;}
 .photo img{width:100%;height:100%;object-fit:cover;}
 footer{background:#0d479a;color:#fff;padding:16px 28px;font-size:12px;display:flex;justify-content:space-between;flex-wrap:wrap;gap:12px;}
 .badge{font-weight:600;letter-spacing:.6px;}
 .invalid-note{color:#b12b2b;font-weight:600;margin-top:14px;}
 a.back{position:absolute;top:18px;right:28px;font-size:12px;text-decoration:none;color:#0d479a;font-weight:600;background:#f0f4fa;padding:6px 14px;border-radius:30px;border:1px solid #cdd9e8;}
 a.back:hover{background:#e4ecf6;}
</style></head><body>
 <div class="wrap">
  <header>
    <h1>Validasi Kartu Tanda Anggota</h1>
    <div class="status {{ $isValid ? 'valid':'expired' }}">{{ $isValid ? 'MASIH BERLAKU' : 'TIDAK BERLAKU' }}</div>
  </header>
  <main>
    <div class="col-left">
      <table>
        <tr><td class="label">NAMA ANGGOTA</td><td>: {{ $user->name }}</td></tr>
        @if($company)
        <tr><td class="label">PERUSAHAAN</td><td>: {{ $company->name }}</td></tr>
        <tr><td class="label">NPWP</td><td>: {{ $company->npwp ?? '-' }}</td></tr>
        <tr><td class="label">KUALIFIKASI</td><td>: {{ $company->kualifikasi ?? '-' }}</td></tr>
        <tr><td class="label">ALAMAT</td><td>: {{ $company->address ?? '-' }}</td></tr>
        @endif
        <tr><td class="label">EMAIL</td><td>: {{ $user->email }}</td></tr>
        <tr><td class="label">NO KTA</td><td>: {{ $user->membership_card_number }}</td></tr>
        <tr><td class="label">TERBIT</td><td>: {{ optional($user->membership_card_issued_at)->format('d M Y') }}</td></tr>
        <tr><td class="label">BERLAKU S/D</td><td>: {{ optional($user->membership_card_expires_at)->format('d M Y') }}</td></tr>
      </table>
      @unless($isValid)
        <div class="invalid-note">Kartu tidak aktif / kedaluwarsa.</div>
      @endunless
    </div>
    <div class="col-right">
      <div class="photo">
        @php($photo = $user->membership_photo_path ?? ($company->photo_pjbu_path ?? null))
        @if($photo)
          {{-- Untuk tampilan web gunakan URL publik, bukan path filesystem --}}
          <img src="{{ asset('storage/'.$photo) }}" alt="Foto">
        @else
          FOTO
        @endif
      </div>
      <div style="font-size:12px;line-height:1.4;color:#334e6f;max-width:190px;">Halaman ini menampilkan status keabsahan kartu anggota secara real-time.</div>
    </div>
  </main>
  <footer>
    <div class="badge">{{ config('app.name') }}</div>
    <div>Generated: {{ now()->format('d M Y H:i') }} WIB</div>
  </footer>
 </div>
</body></html>
