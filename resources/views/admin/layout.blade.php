@php($admin = auth('admin')->user())
<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') | {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --adm-bg:#0d1218; --adm-bg-alt:#111a24; --adm-surface:#16202b; --adm-border:#1f2b37;
            --adm-text:#e1e9f0; --adm-text-dim:#8da2b5; --adm-accent:#3b82f6; --adm-accent-rgb:59 130 246;
            --adm-danger:#ef4444; --adm-warning:#f59e0b; --adm-success:#10b981; --adm-radius:18px;
            --adm-font: system-ui,-apple-system,Segoe UI,Inter,"Helvetica Neue",Arial,sans-serif;
        }
        body {background:var(--adm-bg); color:var(--adm-text); font-family:var(--adm-font); font-size:15px;}
        .adm-sidebar {width:250px;background:var(--adm-bg-alt);position:fixed;inset:0 auto 0 0;padding:1.25rem 1rem;display:flex;flex-direction:column;gap:1rem;box-shadow:2px 0 8px -4px rgba(0,0,0,.5)}
        .adm-brand {font-weight:600;font-size:1.05rem;letter-spacing:.5px;display:flex;align-items:center;gap:.55rem;color:var(--adm-text);text-decoration:none;margin-bottom:.75rem}
        .adm-nav a {text-decoration:none;display:flex;align-items:center;gap:.6rem;padding:.6rem .85rem;border-radius:12px;font-weight:500;color:var(--adm-text-dim);transition:.18s;background:transparent;font-size:.85rem;}
        .adm-nav a:hover {color:var(--adm-text);background:var(--adm-surface)}
        .adm-nav a.active {background:linear-gradient(135deg,#1e3a8a,#1d4ed8);color:#fff;box-shadow:0 4px 14px -6px rgba(var(--adm-accent-rgb)/0.6)}
        main.adm-main {margin-left:250px;padding:1.9rem 2rem 3.5rem;min-height:100vh;}
        .adm-topbar {background:var(--adm-bg-alt);border:1px solid var(--adm-border);border-radius:16px;padding:.85rem 1rem;display:flex;align-items:center;justify-content:space-between;margin-bottom:1.6rem;backdrop-filter:blur(6px)}
        .adm-card {background:var(--adm-bg-alt);border:1px solid var(--adm-border);border-radius:var(--adm-radius);padding:1.4rem 1.35rem;position:relative;box-shadow:0 4px 18px -6px rgba(0,0,0,.55);}
        .adm-card.flat {box-shadow:none}
        .adm-card h5 {font-weight:600;font-size:.95rem;margin-bottom:.9rem;}
        .text-dim {color:var(--adm-text-dim)!important}
        .badge-soft {background:var(--adm-surface);color:var(--adm-accent);border:1px solid var(--adm-border);font-weight:500}
        .adm-table-wrap {background:var(--adm-bg-alt);border:1px solid var(--adm-border);border-radius:var(--adm-radius);overflow:hidden}
        table.adm-table {--row-stripe:rgba(255,255,255,.03);margin:0;width:100%;font-size:.8rem;color:var(--adm-text-dim);}
        table.adm-table thead {background:var(--adm-surface);color:var(--adm-text-dim);text-transform:uppercase;font-size:.65rem;letter-spacing:.08em}
        table.adm-table th,table.adm-table td {padding:.55rem .85rem;vertical-align:middle;border-bottom:1px solid var(--adm-border)}
        table.adm-table tbody tr {transition:.15s}
        table.adm-table tbody tr:nth-child(even){background:var(--row-stripe)}
        table.adm-table tbody tr:hover {background:rgba(var(--adm-accent-rgb)/0.12);color:var(--adm-text)}
        table.adm-table tbody tr:hover td {color:var(--adm-text)}
        .level-badge {border-radius:30px;padding:.25rem .6rem;font-size:.6rem;font-weight:600;letter-spacing:.5px}
        .level-ERROR,.level-CRITICAL,.level-ALERT {background:linear-gradient(135deg,#dc2626,#b91c1c);color:#fff}
        .level-WARNING {background:#f59e0b;color:#1f1300}
        .level-INFO {background:#1d4ed8;color:#fff}
        .level-DEBUG {background:#374151;color:#d1d5db}
        .table-actions {display:flex;gap:.4rem}
        .btn-ghost {background:var(--adm-surface);border:1px solid var(--adm-border);color:var(--adm-text-dim);font-size:.7rem;padding:.45rem .7rem;border-radius:10px;line-height:1.1;font-weight:500}
        .btn-ghost:hover {color:var(--adm-text);border-color:var(--adm-accent)}
        footer.adm-footer {margin-top:3rem;padding:1.2rem 0;font-size:.7rem;color:var(--adm-text-dim)}
        .truncate {white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:240px}
        /* Pagination Dark Theme */
        .pagination {gap:.25rem;}
        .pagination .page-link {
            background:var(--adm-surface);
            border:1px solid var(--adm-border);
            color:var(--adm-text-dim);
            padding:.35rem .65rem;
            font-size:.75rem;
            border-radius:8px;
            transition:.15s;
            font-weight:500;
        }
        .pagination .page-link:hover {
            background:var(--adm-bg-alt);
            color:var(--adm-text);
            border-color:var(--adm-accent);
        }
        .pagination .page-item.active .page-link {
            background:linear-gradient(135deg,#1e3a8a,#1d4ed8);
            border-color:#1d4ed8;
            color:#fff;
            box-shadow:0 2px 8px -2px rgba(var(--adm-accent-rgb)/0.5);
        }
        .pagination .page-item.disabled .page-link {
            background:var(--adm-surface);
            border-color:var(--adm-border);
            color:var(--adm-text-dim);
            opacity:.5;
        }
        @media (max-width: 991.98px){.adm-sidebar{position:static;width:100%;flex-direction:row;flex-wrap:wrap;gap:.75rem;padding:1rem;border-radius:0 0 22px 22px;}.adm-brand{margin-bottom:0;width:100%;} main.adm-main{margin-left:0;padding:1.25rem 1.25rem 3rem;} .adm-nav{display:flex;flex-direction:row;flex-wrap:wrap;gap:.4rem;} .adm-nav a{padding:.55rem .75rem;font-size:.7rem;} .hide-mobile{display:none!important}}
    </style>
    @stack('head')
</head>
<body>
    <aside class="adm-sidebar">
        <a href="{{ route('admin.dashboard') }}" class="adm-brand">
            <span style="background:linear-gradient(135deg,#2563eb,#1d4ed8);display:inline-flex;width:34px;height:34px;align-items:center;justify-content:center;border-radius:14px;font-size:.9rem;font-weight:600;letter-spacing:.5px;">AD</span>
            <span>Admin Panel</span>
        </a>
        <nav class="adm-nav">
            <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">Pengguna</a>
            <a class="{{ request()->routeIs('admin.companies.*') ? 'active' : '' }}" href="{{ route('admin.companies.index') }}">Perusahaan</a>
            <a class="{{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}" href="{{ route('admin.invoices.index') }}">Transaksi</a>
            <a class="{{ request()->routeIs('admin.kta.*') ? 'active' : '' }}" href="{{ route('admin.kta.index') }}">KTA</a>
            <a class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">Pengaturan</a>
            @if($admin->role === 'superadmin')
                <a class="{{ request()->routeIs('admin.admins.*') ? 'active' : '' }}" href="{{ route('admin.admins.index') }}">Manage Admin</a>
            @endif
        </nav>
        <div class="mt-auto small text-dim">
            <div class="mb-2">Masuk sebagai:<br><span class="text-light">{{ $admin->name }}</span></div>
            <form action="{{ route('admin.logout') }}" method="POST" class="d-grid gap-2">@csrf <button class="btn btn-sm btn-outline-danger">Logout</button></form>
            <div class="pt-3 border-top border-secondary-subtle mt-3">&copy; {{ date('Y') }} {{ config('app.name') }}</div>
        </div>
    </aside>
    <main class="adm-main">
        <div class="adm-topbar">
            <div class="small">
                <strong>@yield('page_title', 'Dashboard')</strong>
                <div class="text-dim">@yield('breadcrumbs')</div>
            </div>
            <div class="d-flex gap-2 align-items-center small text-dim">
                <span class="truncate" title="{{ $admin->email }}">{{ $admin->email }}</span>
            </div>
        </div>
        @yield('content')
        <footer class="adm-footer text-center text-dim">Dipersembahkan dengan ❤️ | Build Time: {{ now()->format('H:i') }}</footer>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>