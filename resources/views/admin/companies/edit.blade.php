@extends('admin.layout')
@section('title','Edit Perusahaan')
@section('page_title','Edit Perusahaan')
@section('content')
<div class="adm-card mb-4">
    @if(session('success'))<div class="alert alert-success py-2 small">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger py-2 small">{{ session('error') }}</div>@endif
    <form method="post" action="{{ route('admin.companies.update',$company) }}" enctype="multipart/form-data" class="row g-3">
        @csrf @method('PUT')
    @include('admin.companies.partials.user-bind', ['users' => $users ?? collect(), 'selectedUserId' => $selectedUserId ?? null, 'mode' => 'edit'])
        @include('admin.companies.partials.form',['company'=>$company])
        <div class="col-12">
            <button class="btn btn-sm btn-primary">Simpan</button>
            <a href="{{ route('admin.companies.show',$company) }}" class="btn btn-sm btn-outline-secondary">Batal</a>
        </div>
    </form>
    <hr class="border-secondary">
</div>
@endsection
@include('admin.companies.partials.region-script')
