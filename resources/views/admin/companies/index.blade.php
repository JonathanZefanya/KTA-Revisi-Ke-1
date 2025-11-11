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
        <div class="col-md-5">
            <label class="form-label small text-dim mb-1 d-block">&nbsp;</label>
            <button class="btn btn-sm btn-primary">Filter</button>
            <a href="{{ route('admin.companies.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            <a href="{{ route('admin.companies.export', request()->only(['q', 'jenis', 'kualifikasi'])) }}" class="btn btn-sm btn-success">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="vertical-align:middle;margin-top:-2px;">
                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                    <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                </svg> Export
            </a>
            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#importModal">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="vertical-align:middle;margin-top:-2px;">
                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                </svg> Import
            </button>
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
                    @php($docs = collect(['photo_pjbu_path','npwp_bu_path','nib_file_path','akte_bu_path','ktp_pjbu_path','npwp_pjbu_path'])->filter(fn($d)=>$c->$d))
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

<!-- Modal Import -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header border-secondary">
                <h6 class="modal-title" id="importModalLabel">Import Data Companies dari Excel</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.companies.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label small mb-0">File Excel (.xlsx, .xls)</label>
                            <a href="{{ route('admin.companies.downloadTemplate') }}" class="btn btn-sm btn-outline-primary">
                                <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16" style="vertical-align:middle;margin-top:-2px;">
                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                                    <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                                </svg> Download Template
                            </a>
                        </div>
                        <input type="file" name="file" class="form-control form-control-sm bg-dark border-secondary text-light" required accept=".xlsx,.xls">
                        <div class="form-text text-dim small">
                            Max 5MB. <strong>Format alamat:</strong> "Jl. Nama Jalan No. XX - KodePos" (kode pos akan otomatis dipisahkan).
                        </div>
                    </div>
                    <div class="alert alert-warning small mb-2">
                        <strong>📋 Fitur Import:</strong>
                        <ul class="mb-0 ps-3 small">
                            <li><strong>Kode pos:</strong> Otomatis dipisahkan dari alamat (format: alamat - kodepos)</li>
                            <li><strong>KTA:</strong> Otomatis di-generate berdasarkan kolom "Tanggal Registrasi Terakhir"</li>
                            <li><strong>Tanggal:</strong> Gunakan kolom "Tanggal Registrasi Terakhir" untuk tanggal terbit KTA dan "Masa Berlaku" untuk tanggal expired</li>
                            <li><strong>Format tanggal:</strong> Bisa menggunakan format Excel date atau text (contoh: "29 Oktober 2025")</li>
                            <li><strong>User:</strong> Otomatis di-approve dan bisa langsung login dengan password: <code>password123</code></li>
                        </ul>
                    </div>
                    <div class="alert alert-info small mb-0">
                        <strong>Catatan:</strong> Jika nama badan usaha sudah ada, data akan di-update. Jika belum ada, data baru akan dibuat.
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary">Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
