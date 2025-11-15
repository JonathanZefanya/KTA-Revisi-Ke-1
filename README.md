# 🏗️ Sistem Manajemen KTA (Kartu Tanda Anggota)

Sistem manajemen KTA untuk **Asosiasi Anggota Aspal dan Beton Indonesia (AABI)** yang dibangun dengan Laravel 12.

---

## 📋 Daftar Isi

- [Tentang Sistem](#-tentang-sistem)
- [Fitur Utama](#-fitur-utama)
- [Teknologi](#-teknologi)
- [Instalasi](#-instalasi)
- [Konfigurasi](#-konfigurasi)
- [Penggunaan](#-penggunaan)
- [Struktur Database](#-struktur-database)
- [Command Artisan](#-command-artisan)
- [API Endpoints](#-api-endpoints)
- [Troubleshooting](#-troubleshooting)
- [Lisensi](#-lisensi)

---

## 🎯 Tentang Sistem

Sistem KTA adalah aplikasi web untuk mengelola keanggotaan perusahaan konstruksi (Badan Usaha Jasa Konstruksi). Sistem ini memfasilitasi:

- Registrasi dan approval anggota baru
- Penerbitan KTA digital dengan QR Code
- Manajemen pembayaran dan invoice
- Verifikasi KTA secara online
- Import/Export data anggota

**Target Pengguna:**
- **User (Anggota):** Perusahaan konstruksi yang mendaftar sebagai anggota
- **Admin:** Pengelola asosiasi yang mengatur sistem

---

## ✨ Fitur Utama

### 👥 Portal User (Anggota)
- ✅ **Registrasi Online** - Pendaftaran dengan upload dokumen
- ✅ **Dashboard Anggota** - Informasi KTA dan status keanggotaan
- ✅ **Manajemen Profile** - Update data perusahaan dan dokumen
- ✅ **Invoice Management** - Lihat dan bayar invoice
- ✅ **Download KTA** - Download KTA dalam format PDF
- ✅ **Activity Log** - Riwayat aktivitas login

### 🔐 Portal Admin
- ✅ **User Management** - Approval, edit, dan hapus user
- ✅ **Company Management** - Kelola data perusahaan
- ✅ **Invoice Management** - Generate dan verifikasi invoice
- ✅ **KTA Management** - Kelola penerbitan KTA
- ✅ **Settings** - Konfigurasi sistem, tarif, dan bank
- ✅ **Bulk Operations** - Bulk approve, bulk delete
- ✅ **Import/Export Excel** - Migrasi data massal

### 📊 Fitur Khusus
- ✅ **QR Code Verification** - Verifikasi KTA dengan scan QR
- ✅ **Auto KTA Generation** - KTA otomatis di-generate
- ✅ **Email Notifications** - Notifikasi otomatis untuk invoice
- ✅ **Postal Code Parsing** - Auto-parse kode pos dari alamat
- ✅ **Certificate-Style KTA** - Desain KTA seperti sertifikat
- ✅ **Data Sync Command** - Cleanup orphan data

---

## 🛠️ Teknologi

### Backend
- **Laravel 12** - PHP Framework
- **PHP 8.2+** - Programming Language
- **MySQL 8.0+** - Database

### Frontend
- **Bootstrap 5.3.3** - CSS Framework
- **Blade Templates** - Templating Engine
- **JavaScript/jQuery** - Frontend Logic

### Packages & Libraries
```json
{
    "maatwebsite/excel": "^3.1",           // Excel Import/Export
    "simplesoftwareio/simple-qrcode": "*", // QR Code Generator
    "dompdf/dompdf": "^3.0",              // PDF Generator
    "barryvdh/laravel-dompdf": "^3.0"     // Laravel PDF Wrapper
}
```

---

## 📦 Instalasi

### Requirements
- PHP >= 8.2
- Composer
- MySQL 8.0+
- Node.js & NPM (untuk asset compilation)
- XAMPP / Laragon / Laravel Valet

### Langkah Instalasi

1. **Clone Repository**
```bash
git clone https://github.com/JonathanZefanya/KTA-Revisi-Ke-1.git
cd KTA-Revisi-Ke-1
```

2. **Install Dependencies**
```bash
composer install
npm install
npm run build
```

3. **Setup Environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Konfigurasi Database**

Edit file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kta_database
DB_USERNAME=root
DB_PASSWORD=
```

5. **Migrasi Database**
```bash
php artisan migrate --seed
```

6. **Setup Storage Link**
```bash
php artisan storage:link
```

7. **Run Development Server**
```bash
php artisan serve
```

Akses aplikasi di: `http://127.0.0.1:8000`

---

## ⚙️ Konfigurasi

### Default Admin Account
```
Email: admin@example.com
Password: Admin!1234
```

### Email Configuration (Optional)

Edit `.env` untuk email notifications:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Settings Aplikasi

Setelah login sebagai admin, konfigurasikan:
1. **Site Settings** - Nama website dan logo
2. **Payment Rates** - Tarif per jenis dan kualifikasi
3. **Bank Accounts** - Rekening untuk pembayaran
4. **Signature** - Tanda tangan untuk KTA PDF

---

## 🚀 Penggunaan

### Registrasi User Baru

1. Akses halaman registrasi: `/register`
2. Isi formulir dengan data:
   - Informasi Pribadi
   - Data Perusahaan (Nama, NPWP, Jenis, Kualifikasi)
   - Alamat (format: "Jl. Nama - KodePos" untuk auto-parse)
   - Upload Dokumen (Foto PJBU, NPWP, Akte, NIB, KTP, dll)
3. Submit dan tunggu approval admin

### Approval User (Admin)

1. Login admin → Menu **Pengguna**
2. Lihat list user dengan status **Pending**
3. Klik **Detail** → Verifikasi data dan dokumen
4. Klik **Approve** → User aktif dan dapat login
5. Invoice registrasi otomatis terbuat

### Generate KTA

Setelah invoice dibayar dan diverifikasi:
1. Admin → Menu **Pengguna** → Detail User
2. Klik **Generate KTA**
3. Isi tanggal terbit dan expired
4. KTA otomatis ter-generate dengan:
   - Nomor KTA (format: KTA-YYYY-NNNN)
   - QR Code untuk verifikasi
   - Certificate-style design

### Import Data Excel

1. Admin → Menu **Perusahaan**
2. Klik **Import** → Download template Excel
3. Isi data sesuai template:
   - **Email WAJIB** (untuk mencegah orphan data)
   - Format tanggal: "DD Month YYYY" atau Excel date
   - Alamat: "Alamat Lengkap - KodePos"
4. Upload file → Data otomatis ter-import dengan:
   - User auto-created (password: `password123`)
   - KTA auto-generated
   - Postal code auto-parsed

### Export Data Excel

1. Admin → Menu **Pengguna** / **Perusahaan**
2. Gunakan filter (optional)
3. Klik **Export Excel**
4. Download file dengan:
   - Styled headers (blue background)
   - Status KTA (Berlaku/Expired)
   - Empty fields auto-fill dengan "N/A"

### Verifikasi KTA Online

1. Akses: `/kta/check?q={nomor_kta}`
2. Atau scan QR Code di KTA
3. Sistem menampilkan:
   - Status KTA (Valid/Expired)
   - Data pemegang KTA
   - Certificate-style design

---

## 🗄️ Struktur Database

### Tabel Utama

#### `users`
```sql
- id (PK)
- name, email, password
- phone, nik
- membership_card_number (KTA)
- membership_card_issued_at
- membership_card_expires_at
- approved_at, email_verified_at
- timestamps
```

#### `companies`
```sql
- id (PK)
- name, bentuk, jenis, kualifikasi
- penanggung_jawab, npwp
- email, phone, address
- asphalt_mixing_plant_address
- concrete_batching_plant_address
- province_name, city_name, postal_code
- photo_pjbu_path, npwp_bu_path, akte_bu_path
- nib_file_path, ktp_pjbu_path, npwp_pjbu_path
- timestamps
```

#### `invoices`
```sql
- id (PK)
- user_id (FK → users)
- invoice_number
- type (registration/renewal)
- amount, status (pending/paid/cancelled)
- paid_at, payment_proof_path
- verified_at, verified_by
- timestamps
```

#### `company_user` (Pivot)
```sql
- company_id (FK → companies)
- user_id (FK → users)
- timestamps
```

### Tabel Pendukung

- `admins` - Admin accounts
- `settings` - System settings
- `payment_rates` - Tarif per jenis & kualifikasi
- `renewal_payment_rates` - Tarif renewal
- `bank_accounts` - Rekening bank
- `login_activities` - Log aktivitas login

---

## 🔧 Command Artisan

### Sync Users & Companies
```bash
# Check data consistency
php artisan sync:users-companies

# Delete orphan companies (companies without users)
php artisan sync:users-companies --delete-orphan
```

**Output:**
```
🔍 Checking data consistency...
+-------------------------+-------+
| Metric                  | Count |
+-------------------------+-------+
| Total Companies         | 366   |
| Total Users             | 367   |
| Companies without Users | 0     |
| Users without Companies | 0     |
+-------------------------+-------+
✅ All data is synced correctly!
```

### Clear Cache
```bash
# Clear all caches
php artisan optimize:clear

# Clear specific cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### Database Commands
```bash
# Fresh migration with seed
php artisan migrate:fresh --seed

# Run seeders
php artisan db:seed

# Specific seeder
php artisan db:seed --class=AdminSeeder
```

---

## 🌐 API Endpoints

### Public Routes
```
GET  /                          # Landing page
GET  /register                  # Registration form
POST /register                  # Submit registration
GET  /login                     # Login form
POST /login                     # Process login
GET  /kta/check                 # KTA verification (public)
```

### User Routes (Auth Required)
```
GET  /dashboard                 # User dashboard
GET  /profile                   # Profile page
POST /profile                   # Update profile
GET  /invoices                  # List invoices
GET  /invoices/{id}             # Invoice detail
POST /invoices/{id}/upload-proof # Upload payment proof
GET  /kta/download              # Download KTA PDF
```

### Admin Routes (Auth:Admin Required)
```
# Dashboard
GET  /admin/dashboard

# Users Management
GET  /admin/users
GET  /admin/users/{id}
POST /admin/users/{id}/approve
POST /admin/users/bulk-approve
POST /admin/users/bulk-delete
GET  /admin/users/export

# Companies Management
GET  /admin/companies
GET  /admin/companies/export
POST /admin/companies/import
GET  /admin/companies/download-template

# Invoices Management
GET  /admin/invoices
POST /admin/invoices/{id}/verify
POST /admin/invoices/{id}/reject
GET  /admin/invoices/export

# Settings
GET  /admin/settings
POST /admin/settings/site
POST /admin/settings/rates
POST /admin/settings/banks
```

---

## ❗ Troubleshooting

### Issue: Maximum execution time exceeded

**Problem:** Timeout saat import Excel dengan data banyak

**Solution:**
```php
// Sudah di-handle di AdminCompanyController
set_time_limit(300);
ini_set('max_execution_time', 300);
ini_set('memory_limit', '512M');
```

### Issue: Orphan Companies

**Problem:** Companies tanpa user (data tidak sinkron)

**Solution:**
```bash
php artisan sync:users-companies --delete-orphan
```

### Issue: Storage Link Not Working

**Problem:** Gambar tidak muncul

**Solution:**
```bash
php artisan storage:link
```

### Issue: Email Not Sending

**Problem:** Notifikasi email tidak terkirim

**Solution:**
1. Check `.env` mail configuration
2. Verify SMTP credentials
3. Use Gmail App Password (bukan password biasa)
4. Test dengan:
```bash
php artisan tinker
Mail::raw('Test', function($msg) { 
    $msg->to('test@example.com')->subject('Test'); 
});
```

### Issue: Pagination Not Styled

**Problem:** Button pagination tidak sesuai dark theme

**Solution:**
```bash
# Sudah di-handle dengan custom pagination view
php artisan view:clear
```

### Issue: PDF Not Generating

**Problem:** Error saat generate KTA PDF

**Solution:**
```bash
composer require dompdf/dompdf
composer require barryvdh/laravel-dompdf
```

---

## 📝 Best Practices

### Import Excel
- ✅ **Selalu isi email** - Wajib untuk mencegah orphan data
- ✅ **Format alamat:** "Alamat Lengkap - KodePos"
- ✅ **Format tanggal:** "DD Month YYYY" atau Excel date number
- ✅ **Check template** - Download template terbaru sebelum import
- ✅ **Test dengan data kecil** dulu (10-20 rows)

### Security
- 🔒 Change default admin password
- 🔒 Use strong passwords (min 8 karakter, kombinasi huruf/angka/simbol)
- 🔒 Enable 2FA (jika available)
- 🔒 Regular backup database
- 🔒 Keep Laravel dan packages up-to-date

### Performance
- ⚡ Enable caching di production
- ⚡ Optimize images sebelum upload
- ⚡ Use queue untuk email notifications
- ⚡ Regular cleanup orphan data

---

## 🤝 Contributing

Contributions, issues, and feature requests are welcome!

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

---

## 👨‍💻 Developer

**Jonathan Zefanya**
- GitHub: [@JonathanZefanya](https://github.com/JonathanZefanya)
- Repository: [KTA-Revisi-Ke-1](https://github.com/JonathanZefanya/KTA-Revisi-Ke-1)

---

## 📄 Lisensi

Sistem ini menggunakan [Laravel](https://laravel.com) yang dilisensikan di bawah [MIT License](https://opensource.org/licenses/MIT).

**Copyright © 2025 Asosiasi Anggota Aspal dan Beton Indonesia (AABI)**

---

## 📞 Support

Jika ada pertanyaan atau butuh bantuan:
- 📧 Email: support@aabi.or.id
- 📱 WhatsApp: +62 XXX XXXX XXXX
- 🌐 Website: https://aabi.or.id

---

<div align="center">
Made with ❤️ for AABI
</div>
