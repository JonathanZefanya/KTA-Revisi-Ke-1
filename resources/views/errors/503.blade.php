@extends('errors.layout')
@section('title','503 • Sedang Pemeliharaan')
@section('code','503')
@section('label','Pemeliharaan')
@section('headline')Sedang Dirawat.@endsection
@section('message')Situs sementara tidak tersedia karena pemeliharaan terjadwal atau peningkatan sistem. Kami akan segera kembali online.@endsection
@section('illustration')
<div class="illus">
  <svg class="decor float" viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <defs>
      <linearGradient id="grad503" x1="0" x2="1" y1="0" y2="1">
        <stop offset="0%" stop-color="#34d399"/>
        <stop offset="55%" stop-color="#10b981"/>
        <stop offset="100%" stop-color="#047857"/>
      </linearGradient>
    </defs>
    <circle cx="200" cy="200" r="118" stroke="url(#grad503)" stroke-width="10" fill="rgba(16,185,129,.10)"/>
    <path d="M175 160l50 50" stroke="#6ee7b7" stroke-width="12" stroke-linecap="round"/>
    <path d="M195 140l65 65" stroke="#34d399" stroke-width="10" stroke-linecap="round"/>
    <path d="M215 120l65 65" stroke="#10b981" stroke-width="8" stroke-linecap="round"/>
    <circle cx="165" cy="235" r="18" fill="#34d399" opacity=".5" />
  </svg>
</div>
@endsection
@section('extra')
<div class="glass mb-3">
  <strong class="d-block mb-1" style="color:#e2e8f0;font-weight:600;">Informasi:</strong>
  <ul class="m-0 ps-3" style="list-style:disc;">
    <li>Pemeliharaan biasanya beberapa menit.</li>
    <li>Refresh berkala atau cek kembali nanti.</li>
    <li>Tidak perlu mengulang submit form sebelumnya.</li>
  </ul>
</div>
@endsection
