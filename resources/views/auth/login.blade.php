@php($appName = config('app.name'))
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk | {{ $appName }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body{font-family:'Inter',sans-serif;background:linear-gradient(135deg,#f8fafc,#eef2f7);} 
        .auth-wrapper{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem;}
        .card{border:none;border-radius:28px;box-shadow:0 8px 24px -8px rgba(0,0,0,.08),0 12px 40px -12px rgba(0,0,0,.06);} 
        .brand-badge{display:inline-flex;align-items:center;gap:.6rem;font-weight:600;font-size:1.05rem;color:#0d6efd;text-decoration:none;}
        .form-control{border-radius:14px;padding:.8rem 1rem;border:1px solid #dbe0e6;} 
        .form-control:focus{box-shadow:0 0 0 .25rem rgba(13,110,253,.15);border-color:#0d6efd;} 
        .btn-brand{background:#0d6efd;border:none;border-radius:14px;padding:.85rem 1rem;font-weight:600;letter-spacing:.3px;}
        .btn-brand:hover{background:#0b5ed7;} 
        .link-hover{text-decoration:none;position:relative;} 
        .link-hover:after{content:'';position:absolute;left:0;bottom:-2px;height:2px;width:0;background:currentColor;transition:.35s;} 
        .link-hover:hover:after{width:100%;}
        .divider{display:flex;align-items:center;gap:.75rem;font-size:.8rem;text-transform:uppercase;letter-spacing:.1em;color:#6c757d;font-weight:500;margin:1.5rem 0 1.25rem;} 
        .divider:before,.divider:after{content:'';flex:1;height:1px;background:linear-gradient(90deg,#dee2e6,#f8f9fa);} 
        .floating-shape{position:absolute;inset:0;pointer-events:none;overflow:hidden;border-radius:28px;} 
        .floating-shape:before{content:'';position:absolute;width:480px;height:480px;background:radial-gradient(circle at 30% 30%,rgba(13,110,253,.18),transparent 70%);top:-120px;left:-120px;filter:blur(10px);} 
        .floating-shape:after{content:'';position:absolute;width:380px;height:380px;background:radial-gradient(circle at 70% 70%,rgba(32,201,151,.18),transparent 70%);bottom:-120px;right:-100px;filter:blur(12px);} 
        @media (max-width:575.98px){.card{border-radius:22px;} .auth-side{display:none;}}
    </style>
</head>
<body>
<div class="auth-wrapper">
    <div class="container">
        <div class="row g-4 align-items-stretch justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card position-relative">
                    <div class="floating-shape"></div>
                    <div class="card-body p-4 p-md-5">
                        <a href="{{ route('home') }}" class="brand-badge mb-3">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 14 4-4"/><path d="M14 12V8"/><path d="M2 12h4"/><path d="M6 8V4"/><rect x="8" y="4" width="8" height="4" rx="1"/><rect x="4" y="12" width="8" height="4" rx="1"/><path d="M6 16v2"/><rect x="12" y="12" width="8" height="4" rx="1"/><path d="M18 16v2"/><rect x="8" y="20" width="8" height="4" rx="1" transform="rotate(-90 8 20)"/></svg>
                            <span>{{ $appName }}</span>
                        </a>
                        <h1 class="h4 fw-semibold mb-1">Selamat Datang Kembali</h1>
                        <p class="text-secondary mb-4">Masuk untuk melanjutkan ke dashboard Anda.</p>
                        @if(session('success'))
                            <div class="alert alert-success py-2 small mb-3">{{ session('success') }}</div>
                        @endif
                        @if($errors->any())
                            <div class="alert alert-danger py-2 small mb-3">
                                {{ $errors->first() }}
                            </div>
                        @endif
                        <form method="POST" action="{{ route('login.attempt') }}" novalidate class="needs-validation">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label small fw-medium">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus>
                                <div class="invalid-feedback">Masukkan email yang valid.</div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="password" class="form-label small fw-medium mb-0">Password</label>
                                    <a href="#" class="small link-hover text-decoration-none">Lupa?</a>
                                </div>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword" tabindex="-1" style="border-radius:0 14px 14px 0">👁</button>
                                    <div class="invalid-feedback">Password wajib diisi.</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                                    <label class="form-check-label small" for="remember">Ingat saya</label>
                                </div>
                                <small class="text-secondary">Belum punya akun? <a href="{{ route('register') }}" class="link-hover">Daftar</a></small>
                            </div>
                            <button type="submit" class="btn btn-brand w-100">Masuk</button>
                        </form>
                        <p class="mt-4 small text-secondary mb-0">Dengan masuk Anda menyetujui <a href="#" class="link-hover">Ketentuan</a> & <a href="#" class="link-hover">Privasi</a>.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 col-md-7 auth-side d-flex align-items-center">
                <div class="w-100 text-center px-lg-4">
                    <div class="ratio ratio-16x9 rounded-4 overflow-hidden shadow-sm mt-4">
                        <img src="https://images.unsplash.com/photo-1556761175-5973dc0f32e7?q=80&w=800&auto=format&fit=crop" alt="Illustration" class="w-100 h-100 object-fit-cover">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Client side validation boost
(() => {
  const forms = document.querySelectorAll('.needs-validation');
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  });
})();
// Toggle password
document.getElementById('togglePassword').addEventListener('click', () => {
  const pwd = document.getElementById('password');
  pwd.type = pwd.type === 'password' ? 'text':'password';
});
</script>
</body>
</html>