@extends('admin.layout')
@section('title','KTA')
@section('page_title','Daftar KTA')

@section('content')
<div class="adm-card mb-4">
    <form class="row g-2 align-items-center" method="get">
        <div class="col-auto">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / email / no KTA" class="form-control form-control-sm" />
        </div>
        {{-- <div class="col-auto">
            <button class="btn btn-sm btn-primary">Cari</button>
        </div> --}}
        @if(request('q'))
        <div class="col-auto"><a href="{{ route('admin.kta.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a></div>
        @endif
    </form>
</div>
<div class="adm-table-wrap">
<table class="adm-table">
    <thead>
        <tr>
            <th>NO</th>
            <th>Member</th>
            <th>No KTA</th>
            <th>Perusahaan</th>
            <th>Terbit</th>
            <th>Expire</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($users as $u)
        @php($company = $u->companies->first())
        <tr>
            <td>{{ $loop->iteration + ($users->currentPage()-1)*$users->perPage() }}</td>
            <td>
                <div style="font-weight:600;color:#fff">{{ $u->name }}</div>
                <div class="text-dim" style="font-size:.65rem">{{ $u->email }}</div>
            </td>
            <td>{{ $u->membership_card_number }}</td>
            <td>{{ $company?->name ?? '-' }}</td>
            <td>{{ optional($u->membership_card_issued_at)->format('d M Y') }}</td>
            <td>{{ optional($u->membership_card_expires_at)->format('d M Y') }}</td>
            <td>
                @php($active = $u->hasActiveMembershipCard())
                <span class="level-{{ $active ? 'INFO':'WARNING' }}">{{ $active ? 'AKTIF' : 'NONAKTIF' }}</span>
            </td>
            <td>
                <div class="table-actions">
                    <a class="btn-ghost" href="{{ route('admin.kta.show',$u) }}" title="Lihat">View</a>
                    <a class="btn-ghost" href="{{ route('admin.kta.show',[$u,'full'=>1]) }}" title="Full">Full</a>
                    <a class="btn-ghost" href="{{ route('admin.kta.pdf',[$u,'full'=>1]) }}" title="PDF Full">PDF</a>
                    <a class="btn-ghost" href="{{ route('kta.public',[ 'user'=>$u->id, 'number'=>str_replace(['/', '\\'],'-',$u->membership_card_number) ]) }}" target="_blank" title="Validasi">Validasi</a>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center py-4">Tidak ada data</td></tr>
        @endforelse
    </tbody>
</table>
</div>
<div class="mt-3">{{ $users->links() }}</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.querySelector('input[name="q"]');
    if (!input) return;
    
    input.addEventListener('input', function() {
        const q = this.value;
        fetch(`{{ route('admin.kta.index') }}?q=${encodeURIComponent(q)}`)
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTableBody = doc.querySelector('.adm-table tbody');
                if (newTableBody) {
                    document.querySelector('.adm-table tbody').innerHTML = newTableBody.innerHTML;
                }
            });
    });
});
</script>
@endpush