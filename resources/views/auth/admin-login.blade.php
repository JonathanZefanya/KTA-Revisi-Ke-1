<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{min-height:100vh;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#1d2530,#0d1117);color:#fff;font-family:system-ui,Inter,sans-serif;}
        .card{border:none;border-radius:26px;background:#111a24;box-shadow:0 12px 32px -10px rgba(0,0,0,.6),0 2px 6px -1px rgba(0,0,0,.4);} 
        .form-control{background:#162231;border:1px solid #263242;color:#fff;border-radius:14px;padding:.85rem 1rem;} 
        .form-control:focus{box-shadow:0 0 0 .25rem rgba(13,110,253,.25);border-color:#3d8bfd;} 
        .btn-brand{background:#3d8bfd;border:none;font-weight:600;letter-spacing:.4px;border-radius:14px;padding:.85rem 1rem;} 
        .btn-brand:hover{background:#2f6ec7;} 
        a{color:#3d8bfd;text-decoration:none;} a:hover{text-decoration:underline;} 
        .logo-circle{width:54px;height:54px;background:linear-gradient(135deg,#3d8bfd,#6a5af9);display:flex;align-items:center;justify-content:center;border-radius:18px;font-weight:600;font-size:1.05rem;letter-spacing:.5px;}
    </style>
</head>
<body>
    <div class="container" style="max-width:460px;">
        <div class="card p-4 p-md-5">
            <div class="mb-4 text-center">
                <div class="logo-circle mx-auto mb-3">ADM</div>
                <h1 class="h4 fw-semibold mb-1">Panel Admin</h1>
                <p class="text-secondary mb-0" style="color:#7a899a!important">Masuk sebagai administrator.</p>
            </div>
            @if($errors->any())
                <div class="alert alert-danger py-2 small">{{ $errors->first() }}</div>
            @endif
            @if(session('success'))
                <div class="alert alert-success py-2 small">{{ session('success') }}</div>
            @endif
            <form method="POST" action="{{ route('admin.login.attempt') }}" novalidate class="needs-validation">
                @csrf
                <div class="mb-3">
                    <label class="form-label small">Email Admin</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus class="form-control @error('email') is-invalid @enderror">
                    <div class="invalid-feedback">Masukkan email.</div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="form-label small mb-0">Password</label>
                    </div>
                    <input type="password" name="password" id="password" required class="form-control">
                    <div class="invalid-feedback">Password wajib diisi.</div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check small">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1">
                        <label class="form-check-label" for="remember">Ingat</label>
                    </div>
                    <small><a href="{{ route('login') }}">Masuk user biasa</a></small>
                </div>
                <button class="btn btn-brand w-100" type="submit">Masuk Admin</button>
            </form>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(() => { const form=document.querySelector('.needs-validation'); form.addEventListener('submit',e=>{ if(!form.checkValidity()){e.preventDefault();e.stopPropagation();} form.classList.add('was-validated'); },false);})();
</script>
</body>
</html>