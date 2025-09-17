@extends('errors.layout')
@section('title','404 • Halaman Tidak Ditemukan')
@section('code','404')
@section('label','Halaman Tidak Ditemukan')
@section('headline')Oops!<br/>Terjadi Kekosongan.@endsection
@section('message')Halaman yang Anda cari sudah dipindahkan, dihapus, atau mungkin tidak pernah ada. Jangan khawatir—Anda bisa kembali ke jalur yang benar.@endsection
@section('illustration')
<div class="illus">
    <svg class="decor float" viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <defs>
            <radialGradient id="gradPlanet404" cx="50%" cy="50%" r="50%">
                <stop offset="0%" stop-color="#60a5fa"/>
                <stop offset="70%" stop-color="#2563eb"/>
                <stop offset="100%" stop-color="#1e3a8a"/>
            </radialGradient>
            <linearGradient id="ring404" x1="0" x2="1" y1="0" y2="1">
                <stop offset="0%" stop-color="#93c5fd" stop-opacity="0.0"/>
                <stop offset="45%" stop-color="#60a5fa" stop-opacity=".55"/>
                <stop offset="100%" stop-color="#1d4ed8" stop-opacity="0"/>
            </linearGradient>
        </defs>
        <circle cx="200" cy="200" r="120" fill="url(#gradPlanet404)" />
        <ellipse cx="200" cy="210" rx="190" ry="54" fill="url(#ring404)" transform="rotate(-15 200 210)" />
        <circle cx="125" cy="140" r="12" fill="#93c5fd" opacity=".9" />
        <circle cx="265" cy="110" r="8" fill="#3b82f6" opacity=".8" />
        <circle cx="250" cy="275" r="10" fill="#60a5fa" opacity=".7" />
    </svg>
</div>
@endsection
@section('extra')
<div class="glass mb-3">
    <strong class="d-block mb-1" style="color:#e2e8f0;font-weight:600;">Kemungkinan Penyebab:</strong>
    <ul class="m-0 ps-3" style="list-style:disc;">
        <li>Tautan kedaluwarsa atau salah ketik URL.</li>
        <li>Resource sudah dihapus atau berpindah.</li>
        <li>Sesi lama / cache browser masih menyimpan rute lama.</li>
    </ul>
</div>
@endsection
