@php($user = auth()->user())
@php($companies = $user->companies()->latest()->get())
@extends('layouts.user')
@section('title','Dashboard')
@section('content')
@if(is_null($user->approved_at))
  <div class="surface p-4 p-md-5 mb-4 surface-appear">
      <div class="d-flex flex-wrap align-items-start gap-3 mb-3">
          <div class="flex-grow-1">
              <h1 class="h6 fw-semibold mb-2">Akun Belum Terverifikasi</h1>
              <p class="small text-secondary mb-3">Terima kasih telah mendaftar. Akun Anda sedang menunggu persetujuan admin sebelum bisa memakai semua fitur.</p>
              <ul class="small text-secondary mb-3 ps-3">
                  <li>Pastikan data & dokumen yang diunggah benar.</li>
                  <li>Hubungi admin jika butuh percepatan.</li>
                  <li>Notifikasi status akan muncul otomatis.</li>
              </ul>
              <x-status-badge type="warning">Pending Approval</x-status-badge>
          </div>
          <div class="border rounded-4 p-3 small" style="min-width:220px;background:var(--ui-surface-alt);border-color:var(--ui-border-soft);">
              <div class="fw-semibold mb-2">Apa Selanjutnya?</div>
              <ol class="ps-3 mb-0 text-secondary" style="font-size:.7rem;line-height:1.2rem;">
                  <li>Cek ulang dokumen.</li>
                  <li>Siapkan NPWP / NIB.</li>
                  <li>Catat kontak admin.</li>
              </ol>
          </div>
      </div>
      <div class="d-flex gap-2 flex-wrap">
          <a href="{{ route('logout') }}" onclick="event.preventDefault();this.nextElementSibling.submit();" class="btn btn-sm btn-outline-danger">Logout</a>
          <form method="POST" action="{{ route('logout') }}" class="d-none">@csrf</form>
      </div>
  </div>
@else
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
      <div>
          <h1 class="h6 fw-semibold mb-1">Selamat Datang Kembali</h1>
          <div class="text-secondary small">Ringkasan aktivitas & status keanggotaan Anda.</div>
      </div>
      <x-status-badge type="success">Terverifikasi</x-status-badge>
  </div>

  <!-- Metric Cards -->
  <div class="row g-4 mb-4">
      <div class="col-sm-6 col-xl-3">
          <div class="surface p-3 p-md-4 h-100 surface-appear">
              <div class="text-secondary text-uppercase fw-semibold mb-1" style="font-size:.6rem;letter-spacing:.5px;">Badan Usaha</div>
              <div class="d-flex align-items-end gap-2">
                  <div class="fs-3 fw-semibold">{{ $companies->count() }}</div>
              </div>
              <div class="small text-secondary mt-2">Total terdaftar</div>
          </div>
      </div>
      <div class="col-sm-6 col-xl-3">
          <div class="surface p-3 p-md-4 h-100 surface-appear">
              @php($paidInvoices = $user->invoices()->where('status','paid')->count())
              <div class="text-secondary text-uppercase fw-semibold mb-1" style="font-size:.6rem;letter-spacing:.5px;">Invoice Lunas</div>
              <div class="fs-3 fw-semibold">{{ $paidInvoices }}</div>
              <div class="small text-secondary mt-2">Sudah diverifikasi</div>
          </div>
      </div>
      <div class="col-sm-6 col-xl-3">
          <div class="surface p-3 p-md-4 h-100 surface-appear">
              @php($pendingInvoices = $user->invoices()->whereIn('status',['unpaid','awaiting_verification'])->count())
              <div class="text-secondary text-uppercase fw-semibold mb-1" style="font-size:.6rem;letter-spacing:.5px;">Invoice Pending</div>
              <div class="fs-3 fw-semibold">{{ $pendingInvoices }}</div>
              <div class="small text-secondary mt-2">Menunggu aksi</div>
          </div>
      </div>
      <div class="col-sm-6 col-xl-3">
          <div class="surface p-3 p-md-4 h-100 surface-appear">
              @php($nextExpiry = $user->membership_card_expires_at)
              <div class="text-secondary text-uppercase fw-semibold mb-1" style="font-size:.6rem;letter-spacing:.5px;">Masa Berlaku KTA</div>
              <div class="fs-6 fw-semibold mb-0">{{ $nextExpiry? $nextExpiry->format('d M Y') : '-' }}</div>
              <div class="small mt-1">
                  @if($user->membership_card_number && $user->isEligibleForRenewal())
                      <x-status-badge type="info">Dapat Diperpanjang</x-status-badge>
                  @elseif($nextExpiry && now()->diffInDays($nextExpiry,false)<=30)
                      <x-status-badge type="warning">Segera Perpanjang</x-status-badge>
                  @elseif($nextExpiry)
                      <x-status-badge type="success">Aktif</x-status-badge>
                  @else
                      <x-status-badge type="danger">Tidak Ada</x-status-badge>
                  @endif
              </div>
          </div>
      </div>
  </div>

  <div class="row g-4 mb-4">
      <!-- Membership Status Panel -->
      <div class="col-xl-4 order-xl-2">
          <div class="surface h-100 p-4 surface-appear d-flex flex-column">
              <h6 class="fw-semibold mb-3">Status Keanggotaan</h6>
              <div class="small mb-2">Nomor KTA:</div>
              <div class="fw-semibold mb-3">{{ $user->membership_card_number ?? '—' }}</div>
              <div class="small text-secondary mb-3">Terakhir login: {{ $user->updated_at?->format('d/m/Y H:i') }}</div>
              <div class="mt-auto d-flex flex-wrap gap-2">
                  <a href="{{ route('kta') }}" class="btn btn-sm btn-outline-primary">Lihat KTA</a>
                  <a href="{{ route('kta.renew.form') }}" class="btn btn-sm btn-primary">Perpanjang</a>
              </div>
          </div>
      </div>
      <!-- Companies Grid -->
      <div class="col-xl-8 order-xl-1">
          <div class="d-flex align-items-center justify-content-between mb-3">
              <h6 class="fw-semibold mb-0">Badan Usaha</h6>
              <span class="badge rounded-pill text-bg-light text-secondary small fw-normal">{{ $companies->count() }}</span>
          </div>
          @if($companies->isEmpty())
              <div class="surface p-4 text-center text-secondary small surface-appear">Belum ada data badan usaha.</div>
          @else
              <div class="row g-3">
                  @foreach($companies as $c)
                      <div class="col-md-6">
                          <div class="surface p-3 h-100 surface-appear">
                              <div class="d-flex align-items-start justify-content-between mb-2">
                                  <div class="fw-semibold" style="font-size:.85rem;">{{ $c->name }}</div>
                              </div>
                              <div class="small text-secondary mb-2">{{ $c->city_name }}, {{ $c->province_name }}</div>
                              <div class="d-flex flex-wrap gap-1 mb-2">
                                  @if($c->jenis)<span class="badge rounded-pill text-bg-light text-secondary">{{ $c->jenis }}</span>@endif
                                  @if($c->kualifikasi)<span class="badge rounded-pill text-bg-light text-secondary">{{ $c->kualifikasi }}</span>@endif
                              </div>
                              <div class="small mb-2">NPWP: <span class="text-secondary">{{ $c->npwp ?? '-' }}</span></div>
                              <div class="d-flex flex-wrap gap-1">
                                  @if($c->photo_pjbu_path)<a class="badge rounded-pill text-bg-secondary text-decoration-none" target="_blank" href="{{ asset('storage/'.$c->photo_pjbu_path) }}">Foto PJBU</a>@endif
                                  @if($c->npwp_bu_path)<a class="badge rounded-pill text-bg-secondary text-decoration-none" target="_blank" href="{{ asset('storage/'.$c->npwp_bu_path) }}">NPWP BU</a>@endif
                                  @if($c->akte_bu_path)<a class="badge rounded-pill text-bg-secondary text-decoration-none" target="_blank" href="{{ asset('storage/'.$c->akte_bu_path) }}">AKTE BU</a>@endif
                                  @if($c->nib_file_path)<a class="badge rounded-pill text-bg-secondary text-decoration-none" target="_blank" href="{{ asset('storage/'.$c->nib_file_path) }}">NIB</a>@endif
                                  @if($c->ktp_pjbu_path)<a class="badge rounded-pill text-bg-secondary text-decoration-none" target="_blank" href="{{ asset('storage/'.$c->ktp_pjbu_path) }}">KTP PJBU</a>@endif
                                  @if($c->npwp_pjbu_path)<a class="badge rounded-pill text-bg-secondary text-decoration-none" target="_blank" href="{{ asset('storage/'.$c->npwp_pjbu_path) }}">NPWP PJBU</a>@endif
                              </div>
                          </div>
                      </div>
                  @endforeach
              </div>
          @endif
      </div>
  </div>

  <!-- Recent Activity (Invoices & Renewals Combined) -->
  @php($recentInvoices = $user->invoices()->latest()->take(5)->get())
  @php($recentRenewals = $user->ktaRenewals()->latest()->take(5)->get())
  <div class="surface p-4 mb-4 surface-appear">
      <div class="d-flex align-items-center justify-content-between mb-3">
          <h6 class="fw-semibold mb-0">Aktivitas Terbaru</h6>
      </div>
      <div class="small">
          @if($recentInvoices->isEmpty() && $recentRenewals->isEmpty())
              <div class="text-secondary">Belum ada aktivitas.</div>
          @else
              <ul class="list-unstyled mb-0 d-flex flex-column gap-3">
                  @foreach($recentInvoices as $inv)
                      <li class="d-flex align-items-start justify-content-between gap-3">
                          <div>
                              <div class="fw-semibold" style="font-size:.75rem;">Invoice #{{ $inv->number }}</div>
                              <div class="text-secondary" style="font-size:.65rem;">{{ $inv->created_at->format('d M Y H:i') }}</div>
                          </div>
                          <div>
                              @switch($inv->status)
                                  @case('paid')<x-status-badge type="success">PAID</x-status-badge>@break
                                  @case('awaiting_verification')<x-status-badge type="warning">MENUNGGU</x-status-badge>@break
                                  @case('rejected')<x-status-badge type="danger">REJECTED</x-status-badge>@break
                                  @default<x-status-badge type="neutral">UNPAID</x-status-badge>
                              @endswitch
                          </div>
                      </li>
                  @endforeach
                  @foreach($recentRenewals as $r)
                      <li class="d-flex align-items-start justify-content-between gap-3">
                          <div>
                              <div class="fw-semibold" style="font-size:.75rem;">Perpanjangan KTA</div>
                              <div class="text-secondary" style="font-size:.65rem;">{{ $r->created_at->format('d M Y H:i') }} → {{ $r->new_expires_at->format('d M Y') }}</div>
                          </div>
                          <div>
                              @if($r->invoice)
                                  @switch($r->invoice->status)
                                      @case('paid')<x-status-badge type="success">DITERIMA</x-status-badge>@break
                                      @case('awaiting_verification')<x-status-badge type="warning">MENUNGGU</x-status-badge>@break
                                      @case('rejected')<x-status-badge type="danger">DITOLAK</x-status-badge>@break
                                      @default<x-status-badge type="neutral">UNPAID</x-status-badge>
                                  @endswitch
                              @else
                                  <x-status-badge type="neutral">-</x-status-badge>
                              @endif
                          </div>
                      </li>
                  @endforeach
              </ul>
          @endif
      </div>
  </div>

  <!-- Quick Actions -->
  <div class="surface p-4 surface-appear">
      <h6 class="fw-semibold mb-3">Aksi Cepat</h6>
      <div class="d-flex flex-wrap gap-2">
          <a href="{{ route('pembayaran') }}" class="btn btn-sm btn-outline-primary">Lihat Pembayaran</a>
          <a href="{{ route('kta') }}" class="btn btn-sm btn-outline-primary">Kartu Anggota</a>
          <a href="{{ route('kta.renew.form') }}" class="btn btn-sm btn-primary">Perpanjang KTA</a>
      </div>
  </div>
@endif
@endsection