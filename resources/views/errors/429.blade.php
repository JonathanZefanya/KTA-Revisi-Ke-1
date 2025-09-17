@extends('errors.layout')
@section('title','429 • Terlalu Banyak Permintaan')
@section('code','429')
@section('label','Rate Limit')
@section('headline')Terlalu Cepat.@endsection
@section('message')Anda mengirim terlalu banyak permintaan dalam waktu singkat. Sistem menahan sementara agar tetap stabil.@endsection
@section('illustration')
<div class="illus">
  <svg class="decor float" viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <defs>
      <linearGradient id="grad429" x1="0" x2="1" y1="0" y2="1">
        <stop offset="0%" stop-color="#a78bfa"/>
        <stop offset="60%" stop-color="#7c3aed"/>
        <stop offset="100%" stop-color="#5b21b6"/>
      </linearGradient>
    </defs>
    <circle cx="200" cy="200" r="118" fill="rgba(167,139,250,.10)" stroke="url(#grad429)" stroke-width="10"/>
    <path d="M130 225c40-55 100-55 140 0" stroke="#c4b5fd" stroke-width="10" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M170 190c15-18 45-18 60 0" stroke="#7c3aed" stroke-width="10" stroke-linecap="round" stroke-linejoin="round"/>
    <circle cx="165" cy="160" r="12" fill="#a78bfa"/>
    <circle cx="235" cy="150" r="10" fill="#7c3aed"/>
  </svg>
</div>
@endsection
@section('extra')
<div class="glass mb-3">
  <strong class="d-block mb-1" style="color:#e2e8f0;font-weight:600;">Tips:</strong>
  <ul class="m-0 ps-3" style="list-style:disc;">
    <li>Tunggu beberapa detik sebelum mencoba lagi.</li>
    <li>Hindari auto-refresh terlalu sering.</li>
    <li>Gunakan backoff eksponensial di script otomatis.</li>
  </ul>
</div>
@endsection
