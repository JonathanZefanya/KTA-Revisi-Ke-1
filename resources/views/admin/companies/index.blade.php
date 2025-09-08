@extends('admin.layout')
@section('title','Perusahaan')
@section('page_title','Daftar Perusahaan')
@section('content')
<div class="adm-card mb-4">
    <form class="row g-2 align-items-end" method="get">
        <div class="col-md-3">
            <label class="form-label small text-dim mb-1">Pencarian</label>
            <input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm bg-dark border-secondary text-light" placeholder="Nama / NPWP / PJBU / Email">
        </div>
        <div class="col-md-2">
            <label class="form-label small text-dim mb-1">Jenis</label>
            <select name="jenis" class="form-select form-select-sm bg-dark border-secondary text-light">
                <option value="">Semua</option>
                @foreach(['BUJKN','BUJKA','BUJKPMA'] as $j)
                    <option value="{{ $j }}" @selected($jenis===$j)>{{ $j }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small text-dim mb-1">Kualifikasi</label>
            <select name="kualifikasi" class="form-select form-select-sm bg-dark border-secondary text-light">
                <option value="">Semua</option>
                @foreach([
                    'Kecil / Spesialis 1',
                    'Menengah / Spesialis 2',
                    'Besar BUJKN / Spesialis 2',
                    'Besar PMA / Spesialis 2',
                    'BUJKA'
                ] as $k)
                    <option value="{{ $k }}" @selected($kualifikasi===$k)>{{ $k }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small text-dim mb-1 d-block">&nbsp;</label>
            <button class="btn btn-sm btn-primary">Filter</button>
            <a href="{{ route('admin.companies.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </div>
        <div class="col-md-2 text-end small text-dim">
            Total: <span class="text-light fw-semibold">{{ number_format($companies->total()) }}</span>
        </div>
    </form>
</div>
<div class="adm-table-wrap">
    <table class="adm-table">
        <thead>
        <tr>
            <th>#</th><th>Nama</th><th>Jenis</th><th>Kualifikasi</th><th>PJBU</th><th>NPWP</th><th>Dokumen</th><th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        @forelse($companies as $i=>$c)
            <tr>
                <td>{{ $companies->firstItem()+$i }}</td>
                <td class="text-light">{{ $c->name }}</td>
                <td>{{ $c->jenis ?? '-' }}</td>
                <td>{{ $c->kualifikasi ?? '-' }}</td>
                <td>{{ $c->penanggung_jawab ?? '-' }}</td>
                <td>{{ $c->npwp ?? '-' }}</td>
                <td class="small">
                    @php($docs = collect(['photo_pjbu_path','npwp_bu_path','nib_file_path','ktp_pjbu_path','npwp_pjbu_path'])->filter(fn($d)=>$c->$d))
                    {{ $docs->count() }} file
                </td>
                <td class="text-nowrap small">
                    <a href="{{ route('admin.companies.show',$c) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                    <a href="{{ route('admin.companies.edit',$c) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="text-center py-4 text-dim">Tidak ada data</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-between mt-3 small">
    <a href="{{ route('admin.companies.create') }}" class="btn btn-sm btn-outline-primary">Tambah</a>
    <div>{{ $companies->links() }}</div>
</div>
@endsection
