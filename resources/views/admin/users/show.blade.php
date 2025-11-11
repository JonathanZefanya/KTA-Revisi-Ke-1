@extends('admin.layout')
@section('title','Detail User')
@section('page_title','Detail User')
@section('content')
<div class="adm-card mb-4">
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h5 class="mb-1">{{ $user->name }}</h5>
            <div class="small text-dim">Email: <span class="text-light">{{ $user->email }}</span></div>
            <div class="small text-dim">Telp: <span class="text-light">{{ $user->company_phone ?? '-' }}</span></div>
            
            <div class="small text-dim">Status: @if($user->approved_at)<span class="badge bg-success">Approved</span>@else<span class="badge bg-warning text-dark">Pending</span>@endif</div>
        </div>
        <div class="text-end small">
            <a href="{{ route('admin.users.edit',$user) }}" class="btn btn-sm btn-outline-primary">Edit</a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">Kembali</a>
        </div>
    </div>
    <hr class="border-secondary">
    <h6 class="mb-3">Perusahaan Terkait</h6>
    @php($hasRegInvoice = \App\Models\Invoice::where('user_id',$user->id)->where('type','registration')->exists())
    @if($user->approved_at && !$hasRegInvoice)
        <form method="POST" action="{{ route('admin.users.generateRegistrationInvoice',$user) }}" class="mb-3">@csrf
            <button class="btn btn-sm btn-primary" onclick="return confirm('Generate invoice registrasi sekarang?')">Generate Invoice Registrasi</button>
        </form>
    @endif
    @forelse($user->companies as $company)
        <div class="border rounded p-3 mb-3" style="border-color:var(--adm-border)!important;background:var(--adm-bg-alt)">
            <div class="d-flex justify-content-between">
                <strong class="text-light">{{ $company->name }}</strong>
                <span class="small text-dim">{{ $company->bentuk }} • {{ $company->jenis }} • {{ $company->kualifikasi }}</span>
            </div>
            <div class="small text-dim mt-2">
                PJBU: <span class="text-light">{{ $company->penanggung_jawab }}</span><br>
                NPWP: <span class="text-light">{{ $company->npwp }}</span><br>
                Alamat: <span class="text-light">{{ $company->address }}</span><br>
                @if($company->asphalt_mixing_plant_address)
                    Lokasi Asphalt Mixing Plant: <span class="text-light">{{ $company->asphalt_mixing_plant_address }}</span><br>
                @endif
                @if($company->concrete_batching_plant_address)
                    Lokasi Concrete Batching Plant: <span class="text-light">{{ $company->concrete_batching_plant_address }}</span><br>
                @endif
                Wilayah: <span class="text-light">{{ $company->city_name }}, {{ $company->province_name }} {{ $company->postal_code }}</span>
            </div>
            <div class="mt-3 small">
                <div class="fw-semibold mb-1 text-dim">Dokumen:</div>
                <ul class="small mb-0" style="columns:2; -webkit-columns:2; -moz-columns:2;">
                    <li>Foto PJBU: @if($company->photo_pjbu_path)<a target="_blank" href="{{ asset('storage/'.$company->photo_pjbu_path) }}">Lihat</a>@else<span class="text-dim">-</span>@endif</li>
                    <li>NPWP BU: @if($company->npwp_bu_path)<a target="_blank" href="{{ asset('storage/'.$company->npwp_bu_path) }}">Lihat</a>@else<span class="text-dim">-</span>@endif</li>
                    <li>NIB: @if($company->nib_file_path)<a target="_blank" href="{{ asset('storage/'.$company->nib_file_path) }}">Lihat</a>@else<span class="text-dim">-</span>@endif</li>
                    <li>KTP PJBU: @if($company->ktp_pjbu_path)<a target="_blank" href="{{ asset('storage/'.$company->ktp_pjbu_path) }}">Lihat</a>@else<span class="text-dim">-</span>@endif</li>
                    <li>NPWP PJBU: @if($company->npwp_pjbu_path)<a target="_blank" href="{{ asset('storage/'.$company->npwp_pjbu_path) }}">Lihat</a>@else<span class="text-dim">-</span>@endif</li>
                </ul>
            </div>
        </div>
    @empty
        <div class="text-dim small">Tidak ada perusahaan terkait.</div>
    @endforelse
</div>
@endsection
