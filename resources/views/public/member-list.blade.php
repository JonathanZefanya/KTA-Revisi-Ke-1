@php($appName = config('app.name'))
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Anggota Aktif | {{ $appName }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --ui-bg:#f5f7fb;--ui-surface:#ffffff;--ui-border:#e5e7eb;--ui-border-soft:#eef0f3;
            --ui-primary:#2563eb;--ui-primary-hover:#1d4ed8;
            --ui-radius:14px;--ui-radius-sm:8px;
            --ui-shadow:0 4px 16px -4px rgba(0,0,0,.04),0 2px 6px -1px rgba(0,0,0,.04);
            --ui-shadow-hover:0 6px 28px -6px rgba(0,0,0,.08),0 4px 12px -2px rgba(0,0,0,.06);
        }
        body{font-family:'Inter',sans-serif;background:var(--ui-bg);color:#111;font-size:15px;} 
        .container-main{max-width:1400px;margin:0 auto;padding:2rem 1.5rem;}
        .header{background:var(--ui-surface);border-bottom:1px solid var(--ui-border);padding:-0.5rem 0;margin-bottom:2rem;box-shadow:var(--ui-shadow);}
        .brand-badge{display:inline-flex;align-items:center;gap:.6rem;font-weight:600;font-size:1.1rem;color:var(--ui-primary);text-decoration:none;}
        .surface{background:var(--ui-surface);border:1px solid var(--ui-border-soft);border-radius:var(--ui-radius);box-shadow:var(--ui-shadow);padding:1.5rem;}
        .search-box{max-width:400px;position:relative;}
        .search-box input{border-radius:var(--ui-radius-sm);padding:.75rem 1rem;padding-left:2.75rem;border:1px solid var(--ui-border);width:100%;font-size:.9rem;}
        .search-box input:focus{outline:none;border-color:var(--ui-primary);box-shadow:0 0 0 3px rgba(37,99,235,.1);}
        .search-box svg{position:absolute;left:1rem;top:50%;transform:translateY(-50%);color:#6b7280;pointer-events:none;}
        .table-modern{width:100%;border-collapse:separate;border-spacing:0;}
        .table-modern thead th{font-size:.7rem;letter-spacing:.5px;font-weight:600;color:#475569;text-transform:uppercase;border-bottom:2px solid var(--ui-border);padding:.85rem 1rem;text-align:left;background:var(--ui-surface);}
        .table-modern tbody tr{transition:background .2s ease;}
        .table-modern tbody tr:hover{background:var(--ui-surface-alt,#f9fafc);}
        .table-modern tbody td{padding:1rem;border-bottom:1px solid var(--ui-border-soft);font-size:.85rem;vertical-align:top;}
        .table-modern tbody tr:last-child td{border-bottom:none;}
        .status-badge{display:inline-flex;align-items:center;gap:.35rem;font-size:.65rem;font-weight:600;padding:.35rem .65rem;border-radius:999px;letter-spacing:.5px;text-transform:uppercase;background:#ecfdf5;color:#065f46;}
        .btn-primary{background:var(--ui-primary);border:none;border-radius:var(--ui-radius-sm);padding:.6rem 1.25rem;font-weight:600;font-size:.85rem;transition:background .2s;}
        .btn-primary:hover{background:var(--ui-primary-hover);}
        .pagination{gap:.5rem;margin-top:1.5rem;}
        .page-link{border-radius:8px;border:1px solid var(--ui-border);color:#374151;padding:.5rem .85rem;font-size:.85rem;font-weight:500;}
        .page-link:hover{background:var(--ui-surface-alt,#f9fafc);color:#111;}
        .page-item.active .page-link{background:var(--ui-primary);color:#fff;border-color:var(--ui-primary);}
        .company-name{font-weight:600;color:#111;margin-bottom:.25rem;}
        .text-muted{color:#6b7280;font-size:.8rem;}
        @media (max-width: 768px){
            .table-responsive{overflow-x:auto;-webkit-overflow-scrolling:touch;}
            .container-main{padding:1rem;}
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container-main">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('home') }}" class="brand-badge">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l9 4-9 4-9-4 9-4z"/><path d="M3 10l9 4 9-4"/><path d="M3 18l9 4 9-4"/></svg>
                    <span>{{ $appName }}</span>
                </a>
                <div>
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary" style="border-radius:8px;font-size:.85rem;padding:.5rem 1rem;">Login</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-main">
        <div class="mb-4">
            <h1 class="h3 fw-bold mb-2">Daftar Anggota Aktif</h1>
            <p class="text-muted mb-0">Daftar anggota {{ $appName }} yang memiliki status keanggotaan aktif</p>
        </div>

        <div class="surface">
            <form method="GET" action="{{ route('public.members') }}" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="search-box" style="max-width:100%;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                            <input 
                                type="text" 
                                name="search" 
                                placeholder="Cari nama PT, PJBU, atau alamat..."
                                value="{{ request('search') }}"
                                class="form-control"
                            >
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="position:relative;">
                            <input 
                                type="text" 
                                name="province"
                                id="provinceSearch" 
                                placeholder="Ketik untuk cari provinsi..."
                                value="{{ request('province') }}"
                                class="form-control"
                                style="border-radius:var(--ui-radius-sm);padding:.75rem 1rem;border:1px solid var(--ui-border);font-size:.9rem;"
                                autocomplete="off"
                            >
                            <div id="provinceSuggestions" style="position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid var(--ui-border);border-radius:var(--ui-radius-sm);max-height:200px;overflow-y:auto;display:none;z-index:1000;box-shadow:var(--ui-shadow);margin-top:4px;"></div>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:.4rem;"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                        Cari
                    </button>
                    @if(request('search') || request('province'))
                        <a href="{{ route('public.members') }}" class="btn btn-outline-secondary" style="border-radius:8px;font-size:.85rem;">Reset</a>
                    @endif
                </div>
            </form>

            @if($members->total() > 0)
                <div class="mb-3">
                    <span class="text-muted" style="font-size:.85rem;">Menampilkan {{ $members->firstItem() }} - {{ $members->lastItem() }} dari {{ $members->total() }} anggota</span>
                </div>

                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th style="width:60px;">No</th>
                                <th>Nama PT</th>
                                <th>PJBU</th>
                                <th>Alamat</th>
                                <th style="width:120px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($members as $index => $member)
                                @php($company = $member->companies->first())
                                <tr>
                                    <td class="text-center">{{ $members->firstItem() + $index }}</td>
                                    <td>
                                        <div class="company-name">{{ $company ? $company->name : '-' }}</div>
                                    </td>
                                    <td>{{ $company ? $company->penanggung_jawab : '-' }}</td>
                                    <td>
                                        @if($company && $company->address)
                                            <span class="text-muted">{{ Str::limit($company->address, 80) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="status-badge">Aktif</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($members->hasPages())
                    <div class="d-flex justify-content-center">
                        {!! $members->links('pagination::bootstrap-5') !!}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color:#cbd5e1;margin-bottom:1rem;"><circle cx="12" cy="12" r="10"/><path d="M16 16s-1.5-2-4-2-4 2-4 2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
                    <h5 class="fw-semibold mb-2">Tidak Ada Data</h5>
                    <p class="text-muted mb-0">
                        @if(request('search'))
                            Tidak ada anggota yang sesuai dengan pencarian "{{ request('search') }}"
                        @else
                            Belum ada anggota aktif yang terdaftar
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load provinces from wilayah.id API
        const provinceSearch = document.getElementById('provinceSearch');
        const provinceSuggestions = document.getElementById('provinceSuggestions');
        let allProvinces = [];

        async function loadProvinces() {
            try {
                const response = await fetch('https://wilayah.id/api/provinces.json');
                
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                
                const data = await response.json();
                
                if (data && data.data && Array.isArray(data.data)) {
                    allProvinces = data.data.map(p => p.name);
                }
            } catch (error) {
                console.error('Error loading provinces:', error);
                // Fallback to hardcoded provinces if API fails (updated format from wilayah.id)
                allProvinces = [
                    'Aceh', 'Bali', 'Banten', 'Bengkulu', 'Daerah Istimewa Yogyakarta',
                    'DKI Jakarta', 'Gorontalo', 'Jambi', 'Jawa Barat', 'Jawa Tengah',
                    'Jawa Timur', 'Kalimantan Barat', 'Kalimantan Selatan', 'Kalimantan Tengah',
                    'Kalimantan Timur', 'Kalimantan Utara', 'Kepulauan Bangka Belitung',
                    'Kepulauan Riau', 'Lampung', 'Maluku', 'Maluku Utara', 'Nusa Tenggara Barat',
                    'Nusa Tenggara Timur', 'Papua', 'Papua Barat', 'Papua Barat Daya',
                    'Papua Pegunungan', 'Papua Selatan', 'Papua Tengah', 'Riau', 'Sulawesi Barat',
                    'Sulawesi Selatan', 'Sulawesi Tengah', 'Sulawesi Tenggara', 'Sulawesi Utara',
                    'Sumatera Barat', 'Sumatera Selatan', 'Sumatera Utara'
                ];
            }
        }

        // Show suggestions
        provinceSearch.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase().trim();
            
            if (searchTerm.length === 0) {
                provinceSuggestions.style.display = 'none';
                return;
            }
            
            const filtered = allProvinces.filter(prov => 
                prov.toLowerCase().includes(searchTerm)
            );
            
            if (filtered.length === 0) {
                provinceSuggestions.innerHTML = '<div style="padding:.75rem 1rem;color:#6b7280;font-size:.85rem;">Tidak ada hasil</div>';
                provinceSuggestions.style.display = 'block';
                return;
            }
            
            let html = '';
            filtered.forEach(prov => {
                html += `<div class="province-item" style="padding:.75rem 1rem;cursor:pointer;font-size:.9rem;border-bottom:1px solid var(--ui-border-soft);transition:background .15s;" onmouseover="this.style.background='var(--ui-surface-alt,#f9fafc)'" onmouseout="this.style.background='#fff'" onclick="selectProvince('${prov}')">${prov}</div>`;
            });
            
            provinceSuggestions.innerHTML = html;
            provinceSuggestions.style.display = 'block';
        });

        // Select province
        function selectProvince(province) {
            provinceSearch.value = province;
            provinceSuggestions.style.display = 'none';
        }

        // Hide suggestions when clicking outside
        document.addEventListener('click', (e) => {
            if (!provinceSearch.contains(e.target) && !provinceSuggestions.contains(e.target)) {
                provinceSuggestions.style.display = 'none';
            }
        });

        // Show suggestions on focus if there's value
        provinceSearch.addEventListener('focus', (e) => {
            if (e.target.value.length > 0) {
                e.target.dispatchEvent(new Event('input'));
            }
        });

        loadProvinces();
    </script>
</body>
</html>
