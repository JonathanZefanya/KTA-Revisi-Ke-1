<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('renewal_payment_rates', function(Blueprint $t){
            $t->id();
            $t->string('jenis',30);
            $t->string('kualifikasi',60);
            $t->decimal('amount',15,2)->default(0);
            $t->timestamps();
            $t->unique(['jenis','kualifikasi']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('renewal_payment_rates');
    }
};
