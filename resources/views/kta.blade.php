@php($user = auth()->user())
@php($isExpired = $user->membership_card_expires_at && now()->gt($user->membership_card_expires_at))
@extends('layouts.user')
@section('title','KTA')
@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h5 fw-semibold mb-0">KTA</h1>
        @if($user->hasActiveMembershipCard())
            <x-status-badge type="success">AKTIF</x-status-badge>
        @elseif($isExpired)
            <x-status-badge type="danger">EXPIRED</x-status-badge>
        @else
            <x-status-badge type="warning">BELUM TERBIT</x-status-badge>
        @endif
    </div>
    @if($isExpired)
        <div class="surface p-3 small mb-3">
            <div class="fw-semibold mb-2 text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>KTA Anda Sudah Expired</div>
            <p class="text-secondary mb-2">Kartu keanggotaan Anda telah berakhir pada {{ $user->membership_card_expires_at->format('d M Y') }}. Silakan generate invoice perpanjangan untuk memperpanjang masa aktif KTA.</p>
            <form method="POST" action="{{ route('kta.generateRenewalInvoice') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-file-earmark-text me-1"></i>Generate Invoice Perpanjangan
                </button>
            </form>
        </div>
    @endif
    @if($user->hasActiveMembershipCard())
        <div class="surface p-3 small d-flex flex-column flex-md-row justify-content-between gap-3 align-items-start align-items-md-center mb-4">
            <div>
                <div class="fw-semibold mb-1">Kartu Anggota Aktif</div>
                <div class="text-secondary">Berlaku sampai {{ optional($user->membership_card_expires_at)->format('d M Y') }} (No: {{ $user->membership_card_number }})</div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('kta.card') }}" class="btn btn-sm btn-outline-primary">Preview</a>
                <a href="{{ route('kta.pdf') }}" class="btn btn-sm btn-primary">Download PDF</a>
            </div>
        </div>
        <div class="surface p-3 small">
            <div class="fw-semibold mb-2">Perpanjang Masa Berlaku</div>
            <p class="text-secondary mb-2">Gunakan menu Perpanjang KTA untuk memperpanjang masa aktif hingga 1 tahun setelah melakukan pembayaran yang terverifikasi.</p>
            <a href="{{ route('kta.renew.form') }}" class="btn btn-sm btn-outline-primary">Ajukan Perpanjangan</a>
        </div>
    @else
        <div class="surface p-3 small mb-3">
            <div class="fw-semibold mb-1">Belum Ada KTA Diterbitkan</div>
            <p class="text-secondary mb-2">Kartu akan terbit otomatis setelah pembayaran pertama Anda terverifikasi (status invoice menjadi PAID).</p>
            <a href="{{ route('pembayaran') }}" class="btn btn-sm btn-outline-primary">Lihat Status Pembayaran</a>
        </div>
    @endif
@endsection
