@extends('admin.layout')
@section('title','Tambah Perusahaan')
@section('page_title','Tambah Perusahaan')
@section('content')
<div class="adm-card mb-4">
        @if(session('error'))<div class="alert alert-danger py-2 small">{{ session('error') }}</div>@endif
        <form method="post" action="{{ route('admin.companies.store') }}" enctype="multipart/form-data" class="row g-3">
        @csrf
                @include('admin.companies.partials.user-bind', ['users' => $users ?? collect()])
                @include('admin.companies.partials.form')
        <div class="col-12">
            <button class="btn btn-sm btn-primary">Simpan</button>
            <a href="{{ route('admin.companies.index') }}" class="btn btn-sm btn-outline-secondary">Kembali</a>
        </div>
    </form>
</div>
@endsection
@include('admin.companies.partials.region-script')
