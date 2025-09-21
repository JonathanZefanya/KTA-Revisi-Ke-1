@extends('admin.layout')
@section('title','Tambah Invoice')
@section('page_title','Tambah Invoice')
@section('content')
@if($errors->any())
    <div class="alert alert-danger small">
        <div class="fw-semibold mb-1">Terjadi kesalahan:</div>
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="adm-card small">
    <form method="POST" action="{{ route('admin.invoices.store') }}" class="row g-3">@csrf
        <div class="col-md-6">
            <label class="form-label small text-dim">Pengguna</label>
            <select name="user_id" id="user_id" class="form-select form-select-sm bg-dark border-secondary text-light" required>
                <option value="">Pilih pengguna...</option>
                @foreach($users as $u)
                    @php($uc = $userCompanyMap[$u->id] ?? null)
                    <option value="{{ $u->id }}" data-company-id="{{ $uc }}" @selected(old('user_id')==$u->id)>{{ $u->name }} ({{ $u->email }})</option>
                @endforeach
            </select>
            <div class="form-text text-dim">Saat pengguna dipilih, perusahaan akan dipilih otomatis jika ada.</div>
        </div>
        <div class="col-md-6">
            <label class="form-label small text-dim">Badan Usaha (opsional)</label>
            <select name="company_id" id="company_id" data-old="{{ old('company_id') }}" class="form-select form-select-sm bg-dark border-secondary text-light">
                <option value="">-- Tidak terkait perusahaan --</option>
                @foreach($companies as $c)
                    <option value="{{ $c->id }}" @selected(old('company_id')==$c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label small text-dim">Tipe</label>
            <select name="type" class="form-select form-select-sm bg-dark border-secondary text-light" required>
                @php($opt = ['registration'=>'Registrasi','renewal'=>'Perpanjangan','other'=>'Lainnya'])
                @foreach($opt as $k=>$v)
                    <option value="{{ $k }}" @selected(old('type')===$k)>{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label small text-dim">Jumlah</label>
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-dark text-dim border-secondary">Rp</span>
                <input type="number" step="1" min="0" name="amount" value="{{ old('amount') }}" class="form-control form-control-sm bg-dark border-secondary text-light" placeholder="0" required />
            </div>
        </div>
        <div class="col-md-4">
            <label class="form-label small text-dim">Mata Uang</label>
            <input type="text" name="currency" value="{{ old('currency','IDR') }}" class="form-control form-control-sm bg-dark border-secondary text-light" />
        </div>

        <div class="col-md-6">
            <label class="form-label small text-dim">Tanggal Terbit</label>
            <input type="date" name="issued_date" value="{{ old('issued_date', now()->toDateString()) }}" class="form-control form-control-sm bg-dark border-secondary text-light" />
        </div>
        <div class="col-md-6">
            <label class="form-label small text-dim">Jatuh Tempo</label>
            <input type="date" name="due_date" value="{{ old('due_date', now()->addDays(7)->toDateString()) }}" class="form-control form-control-sm bg-dark border-secondary text-light" />
        </div>

        <div class="col-md-6">
            <label class="form-label small text-dim">Rekening Tujuan (opsional)</label>
            <select name="bank_account_id" class="form-select form-select-sm bg-dark border-secondary text-light">
                <option value="">-- Pilih rekening --</option>
                @foreach($banks as $b)
                    <option value="{{ $b->id }}" @selected(old('bank_account_id')==$b->id)>{{ $b->bank_name }} - {{ $b->account_number }} a.n. {{ $b->account_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label small text-dim">Catatan (opsional)</label>
            <input type="text" name="note" value="{{ old('note') }}" class="form-control form-control-sm bg-dark border-secondary text-light" placeholder="Catatan internal" />
        </div>

        <div class="col-12">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="mark_paid" name="mark_paid" @checked(old('mark_paid'))>
                <label class="form-check-label" for="mark_paid">
                    Tandai sebagai sudah dibayar (langsung set status Paid)
                </label>
            </div>
            <div class="text-dim small">Jika dicentang, sistem akan menandai invoice sebagai Paid, mengisi Paid At, dan mencoba menerbitkan kartu anggota bila memenuhi syarat.</div>
        </div>

        <div class="col-12 d-flex gap-2">
            <a href="{{ route('admin.invoices.index') }}" class="btn btn-sm btn-outline-secondary">Kembali</a>
            <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
        </div>
    </form>
    <script>
        (function(){
            var userSel = document.getElementById('user_id');
            var companySel = document.getElementById('company_id');
            var oldCompanyId = (companySel && companySel.getAttribute('data-old')) || '';
            function applyUserCompany(){
                var opt = userSel && userSel.options[userSel.selectedIndex];
                if(!opt) return;
                var compId = opt.getAttribute('data-company-id');
                if(compId){
                    companySel.value = compId;
                } else {
                    // only clear if user changed and old() didn't explicitly set company
                    if(!oldCompanyId) companySel.value = '';
                }
            }
            if(userSel){ userSel.addEventListener('change', applyUserCompany); }
            // Initial load: if user is preselected and no explicit company chosen, auto-apply
            if(userSel && userSel.value && !oldCompanyId){
                applyUserCompany();
            }
        })();
    </script>
    <div class="text-dim mt-3 small">
        Tips: Pilih pengguna terlebih dahulu. Jika pengguna sudah memiliki perusahaan, Anda dapat mengaitkan invoice ke perusahaan tersebut (opsional).
    </div>
    </div>
@endsection
