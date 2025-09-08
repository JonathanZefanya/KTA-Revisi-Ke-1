@php($admin = auth('admin')->user())
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Log Login Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body{background:#0e141b;color:#d9e3ec;font-family:system-ui,Inter,sans-serif;} .card{background:#111a24;border:none;border-radius:18px;box-shadow:0 4px 18px -4px rgba(0,0,0,.5);} a{color:#4dabf7}</style>
</head>
<body class="p-4">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h5 mb-0">Log Login Pengguna</h1>
        <div class="d-flex gap-2 align-items-center small">
            <span>{{ $admin->name }}</span>
            <form action="{{ route('logout') }}" method="POST" class="m-0">@csrf <button class="btn btn-sm btn-outline-danger">Logout</button></form>
        </div>
    </div>
    <div class="card p-0 overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-dark table-sm align-middle mb-0" style="--bs-table-bg:#111a24;--bs-table-striped-bg:#18212c;">
                <thead class="small text-uppercase" style="background:#18212c;letter-spacing:.5px;">
                    <tr>
                        <th>#</th>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>IP</th>
                        <th>User Agent</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($logs as $i=>$log)
                        <tr>
                            <td>{{ $logs->firstItem() + $i }}</td>
                            <td>{{ $log->logged_in_at?->format('Y-m-d H:i:s') }}</td>
                            <td>{{ $log->user?->name ?? '-' }}</td>
                            <td class="text-break" style="max-width:200px;">{{ $log->email }}</td>
                            <td>{{ $log->ip_address }}</td>
                            <td class="text-break" style="max-width:300px;">{{ $log->user_agent }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-secondary py-4">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top border-dark-subtle">{{ $logs->links() }}</div>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">&laquo; Kembali Dashboard</a>
  </div>
</body>
</html>