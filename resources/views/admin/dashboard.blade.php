@extends('admin.layout')

@section('title','Dashboard')
@section('page_title','Dashboard')

@section('content')
    @if(session('success'))
        <div class="alert alert-success py-2 small adm-card flat mb-4">{{ session('success') }}</div>
    @endif
    <div class="row g-4">
        <div class="col-md-4">
            <div class="adm-card h-100">
                <h5 class="mb-2">Statistik Pengguna</h5>
                <ul class="list-unstyled small mb-0 text-dim">
                    <li>Total: <strong class="text-light">{{ number_format($stats['total_users']) }}</strong></li>
                    <li>Hari ini: <strong class="text-light">{{ $stats['today_users'] }}</strong></li>
                    <li>Minggu ini: <strong class="text-light">{{ $stats['week_users'] }}</strong></li>
                    <li>Bulan ini: <strong class="text-light">{{ $stats['month_users'] }}</strong></li>
                </ul>
            </div>
        </div>
        <div class="col-md-4">
            <div class="adm-card h-100">
                <h5 class="mb-2">Perusahaan</h5>
                <ul class="list-unstyled small mb-0 text-dim">
                    <li>Total: <strong class="text-light">{{ number_format($stats['total_companies']) }}</strong></li>
                    <li>Rasio User/Perusahaan: <strong class="text-light">{{ $stats['total_companies'] ? round($stats['total_users']/$stats['total_companies'],2) : 0 }}</strong></li>
                </ul>
            </div>
        </div>
        <div class="col-md-4">
            <div class="adm-card h-100">
                <h5 class="mb-2">Catatan</h5>
                <p class="text-secondary small mb-0">Ringkasan aktivitas sistem.</p>
            </div>
        </div>
    </div>
    
@endsection