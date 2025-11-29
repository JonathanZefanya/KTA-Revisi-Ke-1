@extends('admin.layout')
@section('title','KTA Detail')
@section('page_title','Preview KTA')

@section('content')
<style>
.kta-preview-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
}
.kta-header-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1.5rem;
}
.kta-user-info h5 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 700;
    color: #fff;
    text-shadow: 0 2px 4px rgba(0,0,0,0.2);
}
.kta-user-info .badge-number {
    display: inline-block;
    background: rgba(255,255,255,0.2);
    backdrop-filter: blur(10px);
    padding: 0.35rem 0.85rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    color: #fff;
    margin-left: 0.5rem;
}
.kta-user-info .email {
    color: rgba(255,255,255,0.9);
    font-size: 0.85rem;
    margin-top: 0.25rem;
}
.kta-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}
.kta-actions .btn {
    border-radius: 10px;
    padding: 0.5rem 1.25rem;
    font-weight: 600;
    font-size: 0.85rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
}
.kta-actions .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.25);
}
.kta-preview-wrapper {
    background: #f8f9fa;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
}
.kta-canvas {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.12);
    overflow: hidden;
    max-width: 1000px;
    margin: 0 auto;
}
</style>

<div class="kta-preview-card">
    <div class="kta-header-info">
        <div class="kta-user-info">
            <h5>
                {{ $user->name }}
                <span class="badge-number">{{ $user->membership_card_number }}</span>
            </h5>
            <div class="email">
                <i class="bi bi-envelope me-1"></i>{{ $user->email }}
            </div>
        </div>
        <div class="kta-actions">
            <a class="btn btn-light" href="{{ route('admin.kta.index') }}" title="Kembali">
                <i class="bi bi-arrow-left"></i>
            </a>
            <a class="btn btn-danger" href="{{ route('admin.kta.pdf',[$user,'full'=>1]) }}" title="Download PDF">
                <i class="bi bi-file-pdf"></i>
            </a>
            <a class="btn btn-info" target="_blank" href="{{ route('kta.public',[ 'user'=>$user->id, 'number'=>str_replace(['/', '\\'],'-',$user->membership_card_number) ]) }}" title="Lihat Validasi Publik">
                <i class="bi bi-shield-check"></i>
            </a>
        </div>
    </div>
</div>

<div class="kta-preview-wrapper">
    <div class="kta-canvas">
        @php(
        $html = view('kta.pdf',[ 'user'=>$user,'qrSvg'=>$qrSvg,'qrPng'=>$qrPng,'validationUrl'=>$validationUrl,'logo'=>$logo,'signature'=>$signature,'full'=>true, 'preview'=>true ])->render()
        )
        @php($iframeHtml = str_replace('"','&quot;',$html))
        <iframe srcdoc="{!! $iframeHtml !!}" style="width:100%;height:680px;border:0;display:block;"></iframe>
    </div>
</div>
@endsection
