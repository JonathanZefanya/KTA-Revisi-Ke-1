<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>419 | Sesi Kedaluwarsa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>body{background:#f5f7fa;font-family:system-ui,-apple-system,sans-serif} .box{max-width:600px;margin:8vh auto;padding:3rem;border-radius:28px;background:#fff;box-shadow:0 8px 32px -12px rgba(0,0,0,.12)} code{background:#eef1f5;padding:.15rem .45rem;border-radius:6px;font-size:.85rem}</style>
  </head>
  <body>
    <div class="box">
      <h1 class="h3 mb-2">419 • Sesi Kedaluwarsa / CSRF</h1>
      <p class="text-secondary mb-4">Permintaan tidak dapat diproses karena token keamanan hilang atau sesi sudah tidak valid.</p>
      <h6 class="text-uppercase small fw-bold">Kemungkinan Penyebab</h6>
      <ul class="small mb-4">
        <li>Form di-submit setelah halaman terlalu lama terbuka.</li>
  <li>Ukuran unggahan melebihi <code>upload_max_filesize</code> / <code>post_max_size</code> sehingga semua field (termasuk _token) terbuang.</li>
  <li>Cookie / sesi diblokir browser.</li>
  <li>Perbedaan domain / port (misal akses via 127.0.0.1 lalu redirect ke localhost).</li>
        <li>APP_KEY atau konfigurasi cache berubah tanpa refresh browser.</li>
      </ul>
      <h6 class="text-uppercase small fw-bold">Langkah Perbaikan Cepat</h6>
      <ol class="small mb-4">
        <li>Reload halaman registrasi lalu coba lagi.</li>
        <li>Cek batas PHP: pastikan <code>upload_max_filesize</code> &ge; 12M dan <code>post_max_size</code> &ge; 12M untuk dokumen 10MB.</li>
        <li>Pastikan folder <code>storage/framework/sessions</code> dapat ditulis.</li>
        <li>Jangan buka form terlalu lama sebelum submit.</li>
      </ol>
      <a href="{{ url()->previous() }}" class="btn btn-primary">Kembali</a>
      <a href="{{ route('register') }}" class="btn btn-outline-secondary ms-2">Muat Ulang Form</a>
    </div>
  </body>
  </html>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>419 | Sesi Kedaluwarsa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>body{background:#f5f7fa;font-family:system-ui,-apple-system,sans-serif} .box{max-width:600px;margin:8vh auto;padding:3rem;border-radius:28px;background:#fff;box-shadow:0 8px 32px -12px rgba(0,0,0,.12)} code{background:#eef1f5;padding:.15rem .45rem;border-radius:6px;font-size:.85rem}</style>
</head>
<body>
  <div class="box">
    <h1 class="h3 mb-2">419 • Sesi Kedaluwarsa / CSRF</h1>
    <p class="text-secondary mb-4">Permintaan tidak dapat diproses karena token keamanan hilang atau sesi sudah tidak valid.</p>
    <h6 class="text-uppercase small fw-bold">Kemungkinan Penyebab</h6>
    <ul class="small mb-4">
      <li>Form di-submit setelah halaman terlalu lama terbuka.</li>
      <li>Ukuran unggahan melebihi <code>upload_max_filesize</code> / <code>post_max_size</code> sehingga semua field (termasuk _token) terbuang.</li>
      <li>Cookie / sesi diblokir browser.</li>
      <li>Perbedaan domain / port (misal akses via 127.0.0.1 lalu redirect ke localhost).</li>
      <li>APP_KEY atau konfigurasi cache berubah tanpa refresh browser.</li>
    </ul>
    <h6 class="text-uppercase small fw-bold">Langkah Perbaikan Cepat</h6>
    <ol class="small mb-4">
      <li>Reload halaman registrasi lalu coba lagi.</li>
      <li>Cek batas PHP: pastikan <code>upload_max_filesize</code> &ge; 12M dan <code>post_max_size</code> &ge; 12M untuk dokumen 10MB.</li>
      <li>Pastikan folder <code>storage/framework/sessions</code> dapat ditulis.</li>
      <li>Jangan buka form terlalu lama sebelum submit.</li>
    </ol>
    <a href="{{ url()->previous() }}" class="btn btn-primary">Kembali</a>
    <a href="{{ route('register') }}" class="btn btn-outline-secondary ms-2">Muat Ulang Form</a>
  </div>
</body>
</html>