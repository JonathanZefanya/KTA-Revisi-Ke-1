@extends('errors.layout')
@section('title','403 • Akses Dilarang')
@section('code','403')
@section('label','Akses Dilarang')
@section('headline')Tidak Diizinkan.@endsection
@section('message')Anda tidak memiliki hak untuk mengakses halaman atau aksi ini. Jika menurut Anda ini seharusnya bisa, hubungi administrator.@endsection
@section('illustration')
<div class="illus">
  <svg class="decor float" viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <defs>
      <linearGradient id="grad403" x1="0" x2="1" y1="0" y2="1">
        <stop offset="0%" stop-color="#f87171"/>
        <stop offset="60%" stop-color="#ef4444"/>
        <stop offset="100%" stop-color="#991b1b"/>
      </linearGradient>
    </defs>
    <circle cx="200" cy="200" r="118" stroke="url(#grad403)" stroke-width="12" fill="rgba(239,68,68,.08)"/>
    <rect x="150" y="145" width="100" height="110" rx="18" stroke="#ef4444" stroke-width="6" fill="rgba(239,68,68,.12)"/>
    <path d="M180 200h40" stroke="#fca5a5" stroke-width="10" stroke-linecap="round"/>
    <path d="M200 165v70" stroke="#fca5a5" stroke-width="10" stroke-linecap="round"/>
  </svg>
</div>
@endsection
@section('extra')
<div class="glass mb-3">
  <strong class="d-block mb-1" style="color:#e2e8f0;font-weight:600;">Kemungkinan Solusi:</strong>
  <ul class="m-0 ps-3" style="list-style:disc;">
    <li>Login dengan akun yang memiliki hak akses.</li>
    <li>Periksa apakah peran/role Anda sudah benar.</li>
    <li>Hubungi admin untuk meminta permission tambahan.</li>
  </ul>
</div>
@endsection
