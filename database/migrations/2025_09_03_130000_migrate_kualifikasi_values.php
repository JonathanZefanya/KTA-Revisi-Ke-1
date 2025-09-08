<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    private array $map = [
        'Kecil' => 'Kecil / Spesialis 1',
        'Menengah' => 'Menengah / Spesialis 2',
        'Besar' => 'Besar BUJKN / Spesialis 2',
        'Spesialis' => 'BUJKA',
    ];
    private array $reverse = [
        'Kecil / Spesialis 1' => 'Kecil',
        'Menengah / Spesialis 2' => 'Menengah',
        'Besar BUJKN / Spesialis 2' => 'Besar',
        'BUJKA' => 'Spesialis',
    ];
    public function up(): void {
        foreach ($this->map as $old=>$new) {
            DB::table('companies')->where('kualifikasi',$old)->update(['kualifikasi'=>$new]);
            DB::table('payment_rates')->where('kualifikasi',$old)->update(['kualifikasi'=>$new]);
        }
    }
    public function down(): void {
        foreach ($this->reverse as $new=>$old) {
            DB::table('companies')->where('kualifikasi',$new)->update(['kualifikasi'=>$old]);
            DB::table('payment_rates')->where('kualifikasi',$new)->update(['kualifikasi'=>$old]);
        }
    }
};
