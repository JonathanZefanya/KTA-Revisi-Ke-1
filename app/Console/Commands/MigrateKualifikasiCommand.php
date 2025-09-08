<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateKualifikasiCommand extends Command
{
    protected $signature = 'kualifikasi:migrate {--dry-run : Hanya tampilkan perubahan tanpa update}';
    protected $description = 'Migrasi nilai kualifikasi lama ke format baru';

    private array $map = [
        'Kecil' => 'Kecil / Spesialis 1',
        'Menengah' => 'Menengah / Spesialis 2',
        'Besar' => 'Besar BUJKN / Spesialis 2',
        'Spesialis' => 'BUJKA', // sesuai instruksi mapping
    ];

    public function handle(): int
    {
        $dry = $this->option('dry-run');
        $this->info(($dry ? '[DRY-RUN] ' : '').'Memulai migrasi kualifikasi...');

        DB::beginTransaction();
        try {
            $totalCompanies = 0; $totalRates = 0; $changes = [];
            // Companies
            $rows = DB::table('companies')->select('id','kualifikasi')->whereIn('kualifikasi', array_keys($this->map))->get();
            foreach ($rows as $r) {
                $new = $this->map[$r->kualifikasi] ?? $r->kualifikasi;
                if ($new !== $r->kualifikasi) {
                    $changes[] = "Company #{$r->id}: {$r->kualifikasi} => $new";
                    $totalCompanies++;
                    if (!$dry) {
                        DB::table('companies')->where('id',$r->id)->update(['kualifikasi'=>$new]);
                    }
                }
            }
            // Payment rates
            $rates = DB::table('payment_rates')->select('id','kualifikasi')->whereIn('kualifikasi', array_keys($this->map))->get();
            foreach ($rates as $r) {
                $new = $this->map[$r->kualifikasi] ?? $r->kualifikasi;
                if ($new !== $r->kualifikasi) {
                    $changes[] = "Rate #{$r->id}: {$r->kualifikasi} => $new";
                    $totalRates++;
                    if (!$dry) {
                        DB::table('payment_rates')->where('id',$r->id)->update(['kualifikasi'=>$new]);
                    }
                }
            }

            if ($dry) {
                DB::rollBack();
                $this->line('Perubahan yang akan terjadi:');
                foreach ($changes as $c) { $this->line(' - '.$c); }
                $this->comment("Total companies: $totalCompanies, payment_rates: $totalRates (tidak disimpan)");
            } else {
                DB::commit();
                $this->info("Migrasi selesai. Updated companies: $totalCompanies, payment_rates: $totalRates");
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Gagal: '.$e->getMessage());
            return 1;
        }
        return 0;
    }
}
