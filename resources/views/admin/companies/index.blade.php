@extends('admin.layout')
@section('title','Perusahaan')
@section('page_title','Daftar Perusahaan')
@section('content')
<style>
.companies-stats {
    background: linear-gradient(135deg, #1e3c72, #2a5298);
    border-radius: 8px;
    padding: 1.5rem;
    color: white;
    margin-bottom: 1.5rem;
}
.companies-stats .stat-item {
    text-align: center;
}
.companies-stats .stat-value {
    font-size: 1.75rem;
    font-weight: 700;
}
.companies-stats .stat-label {
    font-size: 0.8rem;
    opacity: 0.9;
}
.filter-card {
    background: var(--adm-card);
    border: 1px solid var(--adm-border);
    border-radius: 8px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
}
</style>

<!-- Stats Section -->
<div class="companies-stats">
    <div class="row g-3">
        <div class="col-md-3 col-6">
            <div class="stat-item">
                <div class="stat-value">{{ number_format($companies->total()) }}</div>
                <div class="stat-label"><i class="bi bi-building me-1"></i>Total Perusahaan</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-item">
                <div class="stat-value">{{ number_format(\App\Models\Company::where('jenis', 'BUJKN')->count()) }}</div>
                <div class="stat-label"><i class="bi bi-diagram-3 me-1"></i>BUJKN</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-item">
                <div class="stat-value">{{ number_format(\App\Models\Company::where('jenis', 'BUJKA')->count()) }}</div>
                <div class="stat-label"><i class="bi bi-bricks me-1"></i>BUJKA</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-item">
                <div class="stat-value">{{ number_format(\App\Models\Company::where('jenis', 'BUJKPMA')->count()) }}</div>
                <div class="stat-label"><i class="bi bi-globe-americas me-1"></i>BUJKPMA</div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="filter-card">
    <form class="row g-3 align-items-end" method="get">
        <div class="col-lg-3 col-md-6">
            <label class="form-label small text-dim mb-1"><i class="bi bi-search me-1"></i>Pencarian</label>
            <input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm bg-dark border-secondary text-light" placeholder="Nama / NPWP / PJBU / Email">
        </div>
        <div class="col-lg-2 col-md-6">
            <label class="form-label small text-dim mb-1"><i class="bi bi-filter me-1"></i>Jenis</label>
            <select name="jenis" class="form-select form-select-sm bg-dark border-secondary text-light">
                <option value="">Semua</option>
                @foreach(['BUJKN','BUJKA','BUJKPMA'] as $j)
                    <option value="{{ $j }}" @selected($jenis===$j)>{{ $j }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3 col-md-6">
            <label class="form-label small text-dim mb-1"><i class="bi bi-trophy me-1"></i>Kualifikasi</label>
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
        <div class="col-lg-4 col-md-6">
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-sm btn-primary"><i class="bi bi-funnel-fill me-1"></i>Filter</button>
                <a href="{{ route('admin.companies.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-clockwise me-1"></i>Reset</a>
                <a href="{{ route('admin.companies.export', request()->only(['q', 'jenis', 'kualifikasi'])) }}" class="btn btn-sm btn-success">
                    <i class="bi bi-file-earmark-excel me-1"></i>Export
                </a>
                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="bi bi-upload me-1"></i>Import
                </button>
                <a href="{{ route('admin.companies.create') }}" class="btn btn-sm btn-outline-primary ms-auto">
                    <i class="bi bi-plus-circle me-1"></i>Tambah
                </a>
            </div>
        </div>
    </form>
</div>
<!-- Table Section -->
<div class="adm-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-0"><i class="bi bi-building me-2"></i>Data Perusahaan</h6>
        <div class="text-dim small">
            Menampilkan {{ $companies->firstItem() ?? 0 }} - {{ $companies->lastItem() ?? 0 }} dari {{ $companies->total() }}
        </div>
    </div>

    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
            <tr>
                <th width="50">#</th>
                <th>Nama Perusahaan</th>
                <th width="100">Jenis</th>
                <th width="180">Kualifikasi</th>
                <th>Pimpinan</th>
                <th width="150">NPWP</th>
                <th width="100" class="text-center">Dokumen</th>
                <th width="180">Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse($companies as $i=>$c)
                <tr>
                    <td class="text-dim">{{ $companies->firstItem()+$i }}</td>
                    <td>
                        <div class="fw-semibold text-light">{{ $c->name }}</div>
                        @if($c->users->count() > 0)
                            <div class="small text-info"><i class="bi bi-person me-1"></i>{{ $c->users->count() }} member</div>
                        @endif
                    </td>
                    <td><span class="badge bg-primary">{{ $c->jenis ?? '-' }}</span></td>
                    <td class="small">{{ $c->kualifikasi ?? '-' }}</td>
                    <td class="small">{{ $c->penanggung_jawab ?? '-' }}</td>
                    <td class="small font-monospace">{{ $c->npwp ?? '-' }}</td>
                    <td class="text-center">
                        @php($docs = collect(['photo_pjbu_path','npwp_bu_path','nib_file_path','akte_bu_path','ktp_pjbu_path','npwp_pjbu_path'])->filter(fn($d)=>$c->$d))
                        <span class="badge bg-secondary">
                            <i class="bi bi-file-earmark me-1"></i>{{ $docs->count() }}
                        </span>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="{{ route('admin.companies.show',$c) }}" class="btn btn-outline-secondary" title="Detail"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('admin.companies.edit',$c) }}" class="btn btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center py-5 text-dim">
                    <i class="bi bi-building" style="font-size: 2rem;"></i>
                    <div class="mt-2">Tidak ada data perusahaan</div>
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-3">
        {{ $companies->links() }}
    </div>
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
                            <li><strong>Email WAJIB:</strong> Setiap baris harus ada email valid (untuk mencegah data orphan)</li>
                            <li><strong>Kode pos:</strong> Otomatis dipisahkan dari alamat (format: alamat - kodepos)</li>
                            <li><strong>KTA:</strong> Otomatis di-generate berdasarkan kolom "Tanggal Registrasi Terakhir"</li>
                            <li><strong>Tanggal:</strong> Gunakan kolom "Tanggal Registrasi Terakhir" untuk tanggal terbit KTA dan "Masa Berlaku" untuk tanggal expired</li>
                            <li><strong>Format tanggal:</strong> Bisa menggunakan format Excel date atau text (contoh: "29 Oktober 2025")</li>
                            <li><strong>User:</strong> Otomatis di-approve dan bisa langsung login dengan password: <code>password123</code></li>
                        </ul>
                    </div>
                    <div class="alert alert-info small mb-0">
                        <strong>Catatan:</strong> Jika nama badan usaha sudah ada, data akan di-update. Jika belum ada, data baru akan dibuat. Baris tanpa email akan dilewati.
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
