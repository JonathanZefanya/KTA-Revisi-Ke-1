<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminCompanyController;
use App\Http\Controllers\AdminSettingController;
use App\Http\Controllers\MembershipCardController;
use App\Http\Controllers\PublicMemberListController;
use Illuminate\Support\Facades\Auth;

Route::get('/', action: function () {
    return view('auth.login');
})->name('home');

// Public KTA validation page (static-like dynamic page)
Route::get('/kta/verify/{user}/{number}', [MembershipCardController::class,'publicPage'])->where([ 'user'=>'[0-9]+', 'number'=>'[A-Za-z0-9\-]+' ])->name('kta.public');

// Public member list page
Route::get('/list_anggota', [PublicMemberListController::class, 'index'])->name('public.members');

// Wilayah API (public, cached server-side)
Route::prefix('api/wilayah')->group(function(){
    Route::get('provinces', [WilayahController::class, 'provinces']);
    Route::get('regencies/{province}', [WilayahController::class, 'regencies']);
});

// User Auth Routes (guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.attempt');
    // Admin Login
    Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.attempt');
});

// User Protected routes
Route::middleware(['web','auth','user.active'])->group(function () {
    Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');
    Route::get('/pembayaran', [\App\Http\Controllers\PaymentPageController::class,'index'])->middleware(\App\Http\Middleware\EnsureUserApproved::class)->name('pembayaran');
    //profile
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class,'show'])->name('profile');
    Route::post('/profile/update-password', [\App\Http\Controllers\ProfileController::class,'updatePassword'])->name('profile.updatePassword');
    
    Route::get('/kta', function(){
        $user = request()->user();
        if($user && !$user->hasActiveMembershipCard()){
            $hasPaid = $user->invoices()->where('status', \App\Models\Invoice::STATUS_PAID)->exists();
            if($hasPaid){
                $user->issueMembershipCardIfNeeded();
                // reload instance to get fresh dates
                $user->refresh();
            }
        }
        return view('kta');
    })->middleware(\App\Http\Middleware\EnsureUserApproved::class)->name('kta');
    Route::get('/kta/card', [\App\Http\Controllers\MembershipCardController::class,'show'])->middleware(\App\Http\Middleware\EnsureUserApproved::class)->name('kta.card');
    Route::get('/kta/pdf', [\App\Http\Controllers\MembershipCardController::class,'pdf'])->middleware(\App\Http\Middleware\EnsureUserApproved::class)->name('kta.pdf');
    Route::get('/kta/validate', [\App\Http\Controllers\MembershipCardController::class,'validateCard'])->name('kta.validate');
    // KTA renewal
    Route::get('/kta/renew', [\App\Http\Controllers\KtaRenewalController::class,'form'])
        ->middleware([\App\Http\Middleware\EnsureUserApproved::class, \App\Http\Middleware\EnsureUserHasKta::class])
        ->name('kta.renew.form');
    Route::post('/kta/renew', [\App\Http\Controllers\KtaRenewalController::class,'submit'])
        ->middleware([\App\Http\Middleware\EnsureUserApproved::class, \App\Http\Middleware\EnsureUserHasKta::class])
        ->name('kta.renew.submit');
    Route::post('/kta/generate-renewal-invoice', [\App\Http\Controllers\MembershipCardController::class,'generateRenewalInvoice'])
        ->middleware(\App\Http\Middleware\EnsureUserApproved::class)
        ->name('kta.generateRenewalInvoice');
    Route::get('/invoices', [\App\Http\Controllers\InvoiceController::class,'index'])->name('invoices.index');
    Route::get('/invoices/{invoice}', [\App\Http\Controllers\InvoiceController::class,'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/pdf', [\App\Http\Controllers\InvoiceController::class,'downloadPdf'])->name('invoices.pdf');
    Route::post('/invoices/{invoice}/upload-proof', [\App\Http\Controllers\InvoiceController::class,'uploadProof'])->name('invoices.uploadProof');
    Route::post('/invoices/{invoice}/select-bank', [\App\Http\Controllers\InvoiceController::class,'selectBank'])->name('invoices.selectBank');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Admin Protected routes (separate guard)
Route::prefix('admin')->middleware('auth:admin')->group(function () {
    Route::get('/', fn() => redirect()->route('admin.dashboard'));
    Route::get('/dashboard', function () {
        $stats = [
            'total_users' => \App\Models\User::count(),
            'total_companies' => \App\Models\Company::count(),
            'today_users' => \App\Models\User::whereDate('created_at', today())->count(),
            'week_users' => \App\Models\User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'month_users' => \App\Models\User::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'latest_users' => \App\Models\User::latest()->take(10)->get(),
        ];
        return view('admin.dashboard', compact('stats'));
    })->name('admin.dashboard');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    // User management CRUD + approval
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/search', [AdminUserController::class, 'search'])->name('admin.users.search');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
    Route::get('/users/export', [AdminUserController::class, 'export'])->name('admin.users.export');
    Route::post('/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::post('/users/bulk-approve', [AdminUserController::class, 'bulkApprove'])->name('admin.users.bulkApprove');
    Route::post('/users/bulk-delete', [AdminUserController::class, 'bulkDelete'])->name('admin.users.bulkDelete');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('admin.users.show');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    Route::post('/users/{user}/approve', [AdminUserController::class, 'approve'])->name('admin.users.approve');
    Route::post('/users/{user}/toggle-active', [AdminUserController::class, 'toggleActive'])->name('admin.users.toggleActive');
    Route::post('/users/{user}/generate-registration-invoice', [AdminUserController::class,'generateRegistrationInvoice'])->name('admin.users.generateRegistrationInvoice');

    // Company management
    Route::get('/companies', [AdminCompanyController::class, 'index'])->name('admin.companies.index');
    Route::get('/companies/create', [AdminCompanyController::class, 'create'])->name('admin.companies.create');
    Route::get('/companies/export', [AdminCompanyController::class, 'export'])->name('admin.companies.export');
    Route::post('/companies/import', [AdminCompanyController::class, 'import'])->name('admin.companies.import');
    Route::get('/companies/download-template', [AdminCompanyController::class, 'downloadTemplate'])->name('admin.companies.downloadTemplate');
    Route::post('/companies', [AdminCompanyController::class, 'store'])->name('admin.companies.store');
    Route::get('/companies/{company}', [AdminCompanyController::class, 'show'])->name('admin.companies.show');
    Route::get('/companies/{company}/edit', [AdminCompanyController::class, 'edit'])->name('admin.companies.edit');
    Route::put('/companies/{company}', [AdminCompanyController::class, 'update'])->name('admin.companies.update');
    Route::get('/companies/{company}/download-all', [AdminCompanyController::class, 'downloadAll'])->name('admin.companies.downloadAll');
    
    // Settings
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/settings/site', [AdminSettingController::class, 'updateSite'])->name('admin.settings.updateSite');
    Route::post('/settings/signature', [AdminSettingController::class, 'storeSignature'])->name('admin.settings.storeSignature');
    Route::post('/settings/rates', [AdminSettingController::class, 'saveRates'])->name('admin.settings.saveRates');
    Route::post('/settings/renewal-rates', [AdminSettingController::class, 'saveRenewalRates'])->name('admin.settings.saveRenewalRates');
    Route::post('/settings/banks', [AdminSettingController::class, 'storeBank'])->name('admin.settings.banks.store');
    Route::delete('/settings/banks/{bank}', [AdminSettingController::class, 'deleteBank'])->name('admin.settings.banks.delete');
    Route::post('/settings/kta-template', [AdminSettingController::class, 'uploadKtaTemplate'])->name('admin.settings.ktaTemplate');
    Route::post('/settings/kta-layout', [AdminSettingController::class, 'saveKtaLayout'])->name('admin.settings.ktaLayout');
    // Admin invoice verification
    Route::get('/invoices', [\App\Http\Controllers\AdminInvoiceController::class,'index'])->name('admin.invoices.index');
    Route::get('/invoices/create', [\App\Http\Controllers\AdminInvoiceController::class,'create'])->name('admin.invoices.create');
    Route::post('/invoices', [\App\Http\Controllers\AdminInvoiceController::class,'store'])->name('admin.invoices.store');
    Route::get('/invoices/{invoice}', [\App\Http\Controllers\AdminInvoiceController::class,'show'])->name('admin.invoices.show');
    Route::post('/invoices/{invoice}/verify', [\App\Http\Controllers\AdminInvoiceController::class,'verify'])->name('admin.invoices.verify');
    Route::get('/invoices/export', [\App\Http\Controllers\AdminInvoiceController::class,'export'])->name('admin.invoices.export');

    // KTA Admin
    Route::get('/kta', [\App\Http\Controllers\AdminKtaController::class,'index'])->name('admin.kta.index');
    Route::get('/kta/{user}', [\App\Http\Controllers\AdminKtaController::class,'show'])->name('admin.kta.show');
    Route::get('/kta/{user}/pdf', [\App\Http\Controllers\AdminKtaController::class,'pdf'])->name('admin.kta.pdf');

    // Admin management (manage fellow admins)
    Route::get('/admins', [\App\Http\Controllers\AdminAdminController::class,'index'])->name('admin.admins.index');
    Route::post('/admins', [\App\Http\Controllers\AdminAdminController::class,'store'])->name('admin.admins.store');
    Route::get('/admins/create', [\App\Http\Controllers\AdminAdminController::class,'create'])->name('admin.admins.create');
    Route::get('/admins/{admin}/edit', [\App\Http\Controllers\AdminAdminController::class,'edit'])->name('admin.admins.edit');
    Route::put('/admins/{admin}', [\App\Http\Controllers\AdminAdminController::class,'update'])->name('admin.admins.update');
    Route::delete('/admins/{admin}', [\App\Http\Controllers\AdminAdminController::class,'destroy'])->name('admin.admins.destroy');
});

// Route::get('/throttle-test', fn() => 'ok')->middleware('throttle:3,1');