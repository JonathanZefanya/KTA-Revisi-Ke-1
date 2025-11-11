<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SyncUsersCompanies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:users-companies {--delete-orphan : Delete companies without users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync users and companies data, optionally delete orphan companies';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking data consistency...');
        
        // Get statistics
        $totalCompanies = Company::count();
        $totalUsers = User::count();
        $companiesWithoutUsers = Company::whereDoesntHave('users')->count();
        $usersWithoutCompanies = User::whereDoesntHave('companies')->count();
        
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Companies', $totalCompanies],
                ['Total Users', $totalUsers],
                ['Companies without Users', $companiesWithoutUsers],
                ['Users without Companies', $usersWithoutCompanies],
            ]
        );
        
        if ($companiesWithoutUsers > 0) {
            $this->warn("⚠️  Found {$companiesWithoutUsers} companies without any users!");
            
            if ($this->option('delete-orphan')) {
                if ($this->confirm('Delete all companies without users?', false)) {
                    $this->info('🗑️  Deleting orphan companies...');
                    
                    $orphanCompanies = Company::whereDoesntHave('users')->get();
                    $deleted = 0;
                    
                    foreach ($orphanCompanies as $company) {
                        // Delete company files
                        foreach(['photo_pjbu_path','npwp_bu_path','akte_bu_path','nib_file_path','ktp_pjbu_path','npwp_pjbu_path'] as $col) {
                            if ($company->$col && Storage::disk('public')->exists($company->$col)) {
                                Storage::disk('public')->delete($company->$col);
                            }
                        }
                        
                        $company->delete();
                        $deleted++;
                    }
                    
                    $this->info("✅ Deleted {$deleted} orphan companies");
                }
            } else {
                $this->info('💡 Tip: Use --delete-orphan flag to delete companies without users');
                $this->newLine();
                $this->info('Example companies without users:');
                
                $examples = Company::whereDoesntHave('users')->limit(5)->get(['id', 'name', 'email']);
                foreach ($examples as $company) {
                    $this->line("  - [{$company->id}] {$company->name} ({$company->email})");
                }
            }
        }
        
        if ($usersWithoutCompanies > 0) {
            $this->warn("⚠️  Found {$usersWithoutCompanies} users without companies!");
            $this->info('These users should be manually reviewed.');
        }
        
        if ($companiesWithoutUsers === 0 && $usersWithoutCompanies === 0) {
            $this->info('✅ All data is synced correctly!');
        }
        
        $this->newLine();
        $this->info('Done!');
        
        return 0;
    }
}
