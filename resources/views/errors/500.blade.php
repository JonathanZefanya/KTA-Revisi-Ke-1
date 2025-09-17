@extends('errors.layout')
@section('title','500 • Kesalahan Server')
@section('code','500')
@section('label','Kesalahan Internal')
@section('headline')Ada Yang Salah.@endsection
@section('message')Terjadi kesalahan tak terduga di sisi server. Tim teknis akan meninjau ini. Coba lagi beberapa saat lagi.@endsection
@section('illustration')
<div class="illus">
  <svg class="decor float" viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <defs>
      <linearGradient id="grad500" x1="0" x2="1" y1="0" y2="1">
        <stop offset="0%" stop-color="#f472b6"/>
        <stop offset="55%" stop-color="#db2777"/>
        <stop offset="100%" stop-color="#9d174d"/>
      </linearGradient>
    </defs>
    <circle cx="200" cy="200" r="122" stroke="url(#grad500)" stroke-width="12" fill="rgba(219,39,119,.10)"/>
    <path d="M150 170l100 100" stroke="#fbcfe8" stroke-width="12" stroke-linecap="round"/>
    <path d="M250 170L150 270" stroke="#fbcfe8" stroke-width="12" stroke-linecap="round"/>
    <circle cx="200" cy="200" r="40" fill="#f472b6" opacity=".3" />
  </svg>
</div>
@endsection
@section('extra')
<div class="glass mb-3">
  <strong class="d-block mb-1" style="color:#e2e8f0;font-weight:600;">Langkah Sementara:</strong>
  <ul class="m-0 ps-3" style="list-style:disc;">
    <li>Refresh halaman setelah beberapa detik.</li>
    <li>Pastikan koneksi internet stabil.</li>
    <li>Jika terus berulang, kirim detail tindakan terakhir ke support.</li>
  </ul>
</div>
@endsection
