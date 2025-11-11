<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Use Bootstrap 5 pagination view
        Paginator::useBootstrapFive();
        
        // Set dynamic app name from settings if exists
        try {
            if(Schema::hasTable('settings')){
                $name = \App\Models\Setting::getValue('site_name');
                if($name){ config(['app.name'=>$name]); }
            }
        } catch (\Throwable $e) {}
        // Dynamic mail config
        try {
            $host=\App\Models\Setting::getValue('mail_host');
            if($host){
                config(['mail.mailers.smtp.host'=>$host]);
                $port=\App\Models\Setting::getValue('mail_port'); if($port){ config(['mail.mailers.smtp.port'=>(int)$port]); }
                $user=\App\Models\Setting::getValue('mail_username'); if($user){ config(['mail.mailers.smtp.username'=>$user]); }
                $pass=\App\Models\Setting::getValue('mail_password'); if($pass){ config(['mail.mailers.smtp.password'=>$pass]); }
                $enc=\App\Models\Setting::getValue('mail_encryption'); if($enc){ config(['mail.mailers.smtp.encryption'=>$enc]); }
            }
            $fromAddr=\App\Models\Setting::getValue('mail_from_address');
            $fromName=\App\Models\Setting::getValue('mail_from_name');
            if($fromAddr){ config(['mail.from.address'=>$fromAddr]); }
            if($fromName){ config(['mail.from.name'=>$fromName]); }
        } catch(\Throwable $e) {}
        // Share logo path globally
        try{ $logo=\App\Models\Setting::getValue('site_logo_path'); view()->share('siteLogoPath',$logo); }catch(\Throwable $e){}
    }
}
