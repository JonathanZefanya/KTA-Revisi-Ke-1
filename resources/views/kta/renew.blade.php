@extends('layouts.user')

@section('title','Perpanjang KTA')

@php($user = auth()->user())

@section('content')
<div class="surface p-4 p-md-5 mb-4 surface-appear" style="max-width:960px;">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
        <div>
            <h1 class="h6 fw-semibold mb-1">Perpanjang KTA</h1>
            <div class="text-secondary small">Perpanjang masa berlaku kartu anggota Anda selama 1 tahun.</div>
        </div>
        <div class="small text-secondary">{{ now()->format('d M Y H:i') }}</div>
    </div>

    @if(isset($pendingInvoice) && $pendingInvoice)
        <div class="alert alert-warning small d-flex flex-column gap-1 mb-4 surface-appear" style="border-radius:12px;">
            <div>Invoice perpanjangan sedang menunggu pembayaran / verifikasi.</div>
            <div>Nomor: <strong>{{ $pendingInvoice->number }}</strong> • Status:
                @php($s=$pendingInvoice->status)
                @if($s==='awaiting_verification')<x-status-badge type="warning">MENUNGGU</x-status-badge>
                @elseif($s==='unpaid')<x-status-badge type="neutral">UNPAID</x-status-badge>
                @elseif($s==='paid')<x-status-badge type="success">PAID</x-status-badge>
                @else <x-status-badge type="danger">{{ strtoupper($s) }}</x-status-badge>
                @endif
            </div>
            <div><a class="btn btn-sm btn-outline-primary" href="{{ route('pembayaran',['invoice'=>$pendingInvoice->id]) }}">Lihat Invoice</a></div>
        </div>
    @endif

    <div class="row g-4 mb-4">
        <div class="col-md-7">
            <div class="border rounded-4 p-3 p-md-4 h-100" style="background:var(--ui-surface-alt);border-color:var(--ui-border-soft);">
                <h6 class="fw-semibold mb-3">Rincian Perpanjangan</h6>
                <div class="table-responsive mb-2" style="max-width:620px;">
                    <table class="table table-sm table-borderless align-middle mb-0">
                        <tbody class="small">
                        <tr>
                            <th class="text-secondary" style="width:240px;">Nama</th>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th class="text-secondary">No KTA</th>
                            <td>{{ $user->membership_card_number ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-secondary">Masa Berlaku Saat Ini</th>
                            <td>{{ $currentExpiry? $currentExpiry->format('d M Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-secondary">Masa Berlaku Setelah Perpanjangan</th>
                            <td><span class="fw-semibold">{{ $proposed->format('d M Y') }}</span></td>
                        </tr>
                        <tr>
                            <th class="text-secondary">Biaya Perpanjangan</th>
                            <td><span class="fw-semibold">Rp {{ number_format($amount,0,',','.') }}</span></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                @php($confirmMsg = "Perpanjang masa berlaku KTA sampai ".$proposed->format('d M Y')."?")
                @if(empty($pendingInvoice))
                <form method="POST" action="{{ route('kta.renew.submit') }}" data-confirm="{{ $confirmMsg }}" onsubmit="return confirm(this.getAttribute('data-confirm'));" class="mt-2 d-flex gap-2">
                    @csrf
                    <button class="btn btn-primary btn-sm px-3">Perpanjang Sekarang</button>
                    <a href="{{ route('kta') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
                </form>
                @else
                    <a href="{{ route('kta') }}" class="btn btn-outline-secondary btn-sm mt-2">Kembali</a>
                @endif
            </div>
        </div>
        <div class="col-md-5">
            <div class="border rounded-4 p-3 p-md-4 h-100 d-flex flex-column justify-content-between" style="background:var(--ui-surface-alt);border-color:var(--ui-border-soft);">
                <div>
                    <h6 class="fw-semibold mb-2">Status Keanggotaan</h6>
                    <div class="small mb-3">Perpanjangan akan menambah 1 tahun dari masa berlaku terakhir.</div>
                    <ul class="small text-secondary ps-3 mb-3">
                        <li>Invoice harus <strong>dibayar & diverifikasi</strong>.</li>
                        <li>Jika ditolak, Anda dapat mengajukan ulang.</li>
                        <li>Durasi berlaku terbaru tampil setelah verifikasi.</li>
                    </ul>
                </div>
                <div class="small text-secondary">Terakhir diperbarui: {{ now()->format('d M Y H:i') }}</div>
            </div>
        </div>
    </div>

    <div class="divider"></div>

    <div class="d-flex align-items-center gap-2 mb-2">
        <h6 class="fw-semibold mb-0">Riwayat Perpanjangan</h6>
        <span class="badge rounded-pill text-bg-light text-secondary small fw-normal">{{ $renewals->count() }}</span>
    </div>
    <div class="table-responsive" style="max-height:360px;">
        <table class="table table-modern table-sm align-middle mb-0">
            <thead>
                <tr>
                    <th style="width:160px;">Tanggal</th>
                    <th style="width:170px;">Sebelumnya</th>
                    <th style="width:170px;">Baru</th>
                    <th style="width:130px;">Biaya (Rp)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody class="small">
                @forelse($renewals as $r)
                    @php($inv = $r->invoice)
                    <tr class="show-when-loaded">
                        <td>{{ $r->created_at->format('d M Y H:i') }}</td>
                        <td>{{ $r->previous_expires_at? $r->previous_expires_at->format('d M Y') : '-' }}</td>
                        <td>{{ $r->new_expires_at->format('d M Y') }}</td>
                        <td>{{ number_format($r->amount,0,',','.') }}</td>
                        <td>
                            @if($inv)
                                @switch($inv->status)
                                    @case('paid') <x-status-badge type="success">DITERIMA</x-status-badge> @break
                                    @case('rejected') <x-status-badge type="danger">DITOLAK</x-status-badge> @break
                                    @case('awaiting_verification') <x-status-badge type="warning">MENUNGGU</x-status-badge> @break
                                    @default <x-status-badge type="neutral">UNPAID</x-status-badge>
                                @endswitch
                            @else
                                <x-status-badge type="neutral">-</x-status-badge>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr class="show-when-loaded"><td colspan="5" class="text-center text-secondary py-4">Belum ada perpanjangan.</td></tr>
                @endforelse
                <!-- Skeleton rows (shown briefly while loading) -->
                @for($i=0;$i<3;$i++)
                <tr class="show-during-loading skeleton-table">
                    <td><div class="skeleton-block skeleton-line" style="width:120px;"></div></td>
                    <td><div class="skeleton-block skeleton-line" style="width:110px;"></div></td>
                    <td><div class="skeleton-block skeleton-line" style="width:110px;"></div></td>
                    <td><div class="skeleton-block skeleton-line" style="width:70px;"></div></td>
                    <td><div class="skeleton-block skeleton-line skeleton-rounded" style="width:70px;height:1rem;"></div></td>
                </tr>
                @endfor
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Simulasikan skeleton (perceived loading) sangat singkat agar shimmer memberikan kesan pemuatan
document.body.classList.add('loading');
window.addEventListener('load',()=>{
  setTimeout(()=>document.body.classList.remove('loading'),220); // 220ms subtle delay
});
</script>
@endpush
