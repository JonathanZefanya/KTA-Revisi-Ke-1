# 📚 Dokumentasi Teknis - Sistem KTA

## Arsitektur Sistem

### MVC Pattern
```
├── app/
│   ├── Console/Commands/      # Artisan commands
│   ├── Http/
│   │   ├── Controllers/       # Business logic
│   │   └── Middleware/        # Request filters
│   ├── Models/                # Eloquent models
│   ├── Exports/               # Excel export classes
│   └── Imports/               # Excel import classes
├── database/
│   ├── migrations/            # Database schema
│   └── seeders/               # Sample data
├── resources/
│   ├── views/                 # Blade templates
│   └── css/                   # Stylesheets
└── routes/
    └── web.php                # Application routes
```

---

## Fitur Detail

### 1. Auto-Generate KTA

**File:** `app/Imports/CompaniesImport.php`

**Flow:**
1. Parse tanggal dari Excel (support Excel serial number & text date)
2. Generate nomor KTA dengan format: `KTA-{YEAR}-{NUMBER}`
3. Query last KTA number untuk tahun tersebut
4. Increment number (4 digit dengan leading zero)
5. Save ke database dengan relasi ke user

**Code:**
```php
protected function generateKtaForUser(User $user, $row)
{
    $year = $tanggalTerbitKta->format('Y');
    $lastNumber = User::where('membership_card_number', 'like', "KTA-{$year}-%")
        ->orderByRaw('CAST(SUBSTRING_INDEX(membership_card_number, "-", -1) AS UNSIGNED) DESC')
        ->value('membership_card_number');
    
    $nextNumber = $lastNumber ? intval(explode('-', $lastNumber)[2]) + 1 : 1;
    $ktaNumber = sprintf('KTA-%s-%04d', $year, $nextNumber);
    
    $user->update([
        'membership_card_number' => $ktaNumber,
        'membership_card_issued_at' => $tanggalTerbitKta,
        'membership_card_expires_at' => $tanggalExpiredKta,
    ]);
}
```

---

### 2. Auto-Parse Postal Code

**File:** `app/Imports/CompaniesImport.php`

**Pattern:** `"Alamat Lengkap - KodePos"`

**Logic:**
1. Explode alamat dengan delimiter ` - `
2. Ambil part terakhir sebagai kandidat kode pos
3. Validasi: harus numeric dan 5 digit
4. Jika valid: pisahkan ke kolom `postal_code`
5. Jika tidak: tetap di `address`

**Code:**
```php
if (!empty($alamat) && strpos($alamat, ' - ') !== false) {
    $parts = explode(' - ', $alamat);
    $lastPart = trim(array_pop($parts));
    
    if (is_numeric($lastPart) && strlen($lastPart) === 5) {
        $kodePos = $lastPart;
        $alamat = trim(implode(' - ', $parts));
    }
}
```

---

### 3. Batch Import with Optimization

**File:** `app/Imports/CompaniesImport.php`

**Optimizations:**
- ✅ Batch size: 100 rows
- ✅ Chunk reading: 100 rows
- ✅ Disable model events
- ✅ Use `updateOrCreate` (1 query instead of 2)
- ✅ Database transaction for consistency

**Performance:**
- Before: ~1 minute for 100 rows
- After: ~10 seconds for 100 rows (6x faster)

**Code:**
```php
class CompaniesImport implements 
    ToCollection, 
    WithHeadingRow, 
    WithBatchInserts,   // ← Enable batch
    WithChunkReading    // ← Enable chunk
{
    public function batchSize(): int { return 100; }
    public function chunkSize(): int { return 100; }
    
    public function collection(Collection $rows) {
        Company::withoutEvents(function () use ($rows) {
            User::withoutEvents(function () use ($rows) {
                // Process rows
            });
        });
    }
}
```

---

### 4. Certificate-Style KTA Design

**File:** `resources/views/kta/public.blade.php`

**Design Elements:**
- 🎨 Gradient background (blue shades)
- 🎨 Gold border with border-image
- 🎨 Ornamental corners (4 divs with custom borders)
- 🎨 Watermark "KTA" (rotated -45deg, opacity 0.05)
- 🎨 Georgia serif font for elegance
- 🎨 Responsive layout

**CSS:**
```css
background: linear-gradient(135deg, #1e3c72, #2a5298, #7e8ba3);
border: 15px solid;
border-image: linear-gradient(135deg, #c9b037, #f3e5ab, #c9b037) 1;
box-shadow: 0 20px 60px rgba(0,0,0,0.4);
```

---

### 5. Dark Theme Admin Panel

**File:** `resources/views/admin/layout.blade.php`

**Design System:**
```css
:root {
    --adm-bg: #0d1218;           /* Main background */
    --adm-bg-alt: #111a24;       /* Card background */
    --adm-surface: #16202b;      /* Surface elements */
    --adm-border: #1f2b37;       /* Border color */
    --adm-text: #e1e9f0;         /* Text color */
    --adm-text-dim: #8da2b5;     /* Secondary text */
    --adm-accent: #3b82f6;       /* Primary accent */
    --adm-radius: 18px;          /* Border radius */
}
```

**Components:**
- Sidebar with active state gradient
- Card with border and shadow
- Table with hover effects
- Pagination with custom styling
- Form inputs with dark theme

---

### 6. Bulk Operations

**File:** `app/Http/Controllers/AdminUserController.php`

**Bulk Approve:**
```php
public function bulkApprove(Request $request)
{
    $ids = $request->input('ids', []);
    User::whereIn('id', $ids)
        ->whereNull('approved_at')
        ->update([
            'approved_at' => now(),
            'email_verified_at' => now()
        ]);
    return back()->with('success', 'User diproses');
}
```

**Bulk Delete (with cascade):**
```php
public function bulkDelete(Request $request)
{
    DB::beginTransaction();
    
    foreach ($ids as $userId) {
        $user = User::find($userId);
        
        // 1. Delete invoices
        Invoice::where('user_id', $user->id)->delete();
        
        // 2. Get companies
        $companies = $user->companies;
        
        // 3. Detach from companies
        $user->companies()->detach();
        
        // 4. Delete user
        $user->delete();
        
        // 5. Delete orphan companies
        foreach ($companies as $company) {
            if ($company->users()->count() === 0) {
                // Delete files
                AdminCompanyController::deleteCompanyFiles($company);
                // Delete company
                $company->delete();
            }
        }
    }
    
    DB::commit();
}
```

---

### 7. QR Code Generation

**File:** `app/Http/Controllers/UserController.php`

**Library:** `simplesoftwareio/simple-qrcode`

**Code:**
```php
use SimpleSoftwareIO\QrCode\Facades\QrCode;

$qrUrl = route('kta.public', ['q' => $user->membership_card_number]);
$qrCode = QrCode::size(200)
    ->format('svg')
    ->generate($qrUrl);
```

**Output:** SVG QR code yang bisa di-embed di PDF

---

### 8. Email Notifications

**File:** `app/Mail/InvoiceCreated.php`

**Trigger:** Saat invoice dibuat (approval user)

**Content:**
- Subject: "Invoice Pendaftaran"
- Data: Invoice number, amount, due date
- Action: Link ke halaman invoice detail

**Code:**
```php
class InvoiceCreated extends Mailable
{
    public function __construct(
        public Invoice $invoice,
        public User $user
    ) {}
    
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice Pendaftaran - ' . config('app.name'),
        );
    }
    
    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice-created',
        );
    }
}
```

---

## Database Relations

### Many-to-Many: User ↔ Company

**Pivot Table:** `company_user`

**Use Case:** 1 company bisa punya beberapa user (contoh: tender staff & technical staff)

```php
// User Model
public function companies() {
    return $this->belongsToMany(Company::class);
}

// Company Model
public function users() {
    return $this->belongsToMany(User::class);
}
```

### One-to-Many: User → Invoice

```php
// User Model
public function invoices() {
    return $this->hasMany(Invoice::class);
}

// Invoice Model
public function user() {
    return $this->belongsTo(User::class);
}
```

---

## Security Features

### 1. Authentication Guards

**File:** `config/auth.php`

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'admin' => [
        'driver' => 'session',
        'provider' => 'admins',
    ],
],
```

### 2. Middleware

**Auth Middleware:**
- `auth:web` - User authentication
- `auth:admin` - Admin authentication

**Custom Middleware:**
- Check if user approved
- Check if email verified

### 3. CSRF Protection

**All POST/PUT/DELETE requests protected with CSRF token:**
```blade
<form method="POST">
    @csrf
    ...
</form>
```

### 4. File Upload Validation

```php
$request->validate([
    'photo_pjbu' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    'npwp_bu' => 'nullable|mimes:pdf,jpeg,png,jpg|max:2048',
    'file' => 'required|mimes:xlsx,xls|max:5120',
]);
```

---

## Performance Tips

### 1. Eager Loading

**Problem:** N+1 query problem

**Solution:**
```php
// Bad (N+1)
$users = User::all();
foreach ($users as $user) {
    echo $user->companies->first()->name; // 1 query per user!
}

// Good (2 queries total)
$users = User::with('companies')->get();
foreach ($users as $user) {
    echo $user->companies->first()->name;
}
```

### 2. Query Optimization

**Use `select()` untuk ambil kolom yang dibutuhkan:**
```php
User::select('id', 'name', 'email')
    ->where('approved_at', '!=', null)
    ->get();
```

**Use `exists()` instead of `count()`:**
```php
// Bad
if (User::where('email', $email)->count() > 0)

// Good
if (User::where('email', $email)->exists())
```

### 3. Caching

**Cache query results:**
```php
$stats = Cache::remember('dashboard-stats', 3600, function () {
    return [
        'total_users' => User::count(),
        'total_companies' => Company::count(),
        'pending_invoices' => Invoice::where('status', 'pending')->count(),
    ];
});
```

---

## Testing

### Manual Testing Checklist

**User Flow:**
- [ ] Register dengan data lengkap
- [ ] Upload semua dokumen
- [ ] Tunggu approval
- [ ] Login setelah approved
- [ ] Lihat invoice
- [ ] Upload bukti bayar
- [ ] Download KTA setelah verified

**Admin Flow:**
- [ ] Login admin
- [ ] Approve user
- [ ] Verify payment
- [ ] Generate KTA
- [ ] Export data
- [ ] Import data Excel

### Unit Testing (Planned)

```php
// tests/Feature/RegistrationTest.php
public function test_user_can_register()
{
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        // ... other fields
    ]);
    
    $response->assertRedirect('/dashboard');
    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com'
    ]);
}
```

---

## Deployment

### Production Checklist

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false`
- [ ] Change `APP_KEY`
- [ ] Update database credentials
- [ ] Configure mail settings
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Set proper file permissions
- [ ] Setup SSL certificate
- [ ] Configure backup schedule

### Server Requirements

**Minimum:**
- 2 CPU cores
- 4GB RAM
- 20GB SSD storage
- PHP 8.2+ with extensions:
  - BCMath, Ctype, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- MySQL 8.0+
- Composer
- Web server (Apache/Nginx)

**Recommended:**
- 4 CPU cores
- 8GB RAM
- 50GB SSD storage
- Redis for caching
- Queue worker for background jobs

---

## Maintenance

### Regular Tasks

**Daily:**
- [ ] Monitor error logs
- [ ] Check pending approvals
- [ ] Verify payment proofs

**Weekly:**
- [ ] Backup database
- [ ] Review system performance
- [ ] Update content/announcements

**Monthly:**
- [ ] Update dependencies (`composer update`)
- [ ] Review and cleanup old files
- [ ] Generate activity reports
- [ ] Check for security updates

### Backup Strategy

**Database Backup:**
```bash
# Manual backup
mysqldump -u root -p kta_database > backup_$(date +%Y%m%d).sql

# Automated (cron job)
0 2 * * * mysqldump -u root -p kta_database > /backups/db_$(date +\%Y\%m\%d).sql
```

**File Backup:**
```bash
tar -czf storage_$(date +%Y%m%d).tar.gz storage/app/public/
```

---

## Changelog

### Version 1.0 (November 2025)
- ✅ Initial release
- ✅ User registration & approval system
- ✅ KTA generation with QR code
- ✅ Invoice management
- ✅ Admin panel
- ✅ Import/Export Excel
- ✅ Auto-parse postal code
- ✅ Certificate-style KTA design
- ✅ Bulk operations
- ✅ Data sync command
- ✅ Dark theme admin panel

---

## Future Enhancements

### Planned Features
- 🔮 Dashboard analytics dengan charts
- 🔮 WhatsApp notifications
- 🔮 Mobile app (React Native)
- 🔮 API for third-party integration
- 🔮 Digital signature untuk KTA
- 🔮 Membership renewal automation
- 🔮 Payment gateway integration
- 🔮 Multi-language support
- 🔮 Activity audit trail
- 🔮 Advanced reporting

---

## Contact & Support

**Developer:** Jonathan Zefanya
**Project Repository:** https://github.com/JonathanZefanya/KTA-Revisi-Ke-1
**Documentation:** README.md

---

<div align="center">
📚 End of Technical Documentation
</div>
