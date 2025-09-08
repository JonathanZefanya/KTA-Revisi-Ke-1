@extends('admin.layout')
@section('title','KTA Detail')
@section('page_title','KTA Detail')

@section('content')
<div class="adm-card mb-3">
    <div class="d-flex justify-content-between flex-wrap gap-2 align-items-center">
        <div>
            <h5 style="margin:0;font-size:.9rem;font-weight:600">{{ $user->name }} <span class="text-dim">({{ $user->membership_card_number }})</span></h5>
            <div class="text-dim" style="font-size:.7rem">{{ $user->email }}</div>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-sm btn-outline-light" href="{{ route('admin.kta.show',[$user]) }}">Plain</a>
            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.kta.show',[$user,'full'=>1]) }}">Full</a>
            <a class="btn btn-sm btn-primary" href="{{ route('admin.kta.pdf',[$user,'full'=>$full?1:0]) }}">Unduh PDF</a>
            <a class="btn btn-sm btn-outline-secondary" target="_blank" href="{{ route('kta.public',[ 'user'=>$user->id, 'number'=>str_replace(['/', '\\'],'-',$user->membership_card_number) ]) }}">Validasi</a>
        </div>
    </div>
</div>
<div class="adm-card" style="background:#0a0f15;border:1px solid #1f2b37">
    @php(
    $html = view('kta.pdf',[ 'user'=>$user,'qrSvg'=>$qrSvg,'qrPng'=>$qrPng,'validationUrl'=>$validationUrl,'logo'=>$logo,'signature'=>$signature,'full'=>$full, 'preview'=>true ])->render()
    )
    @php($iframeHtml = str_replace('"','&quot;',$html))
    <iframe srcdoc="{!! $iframeHtml !!}" style="width:100%;height:760px;border:0;background:#111;border-radius:12px"></iframe>
</div>
@endsection
