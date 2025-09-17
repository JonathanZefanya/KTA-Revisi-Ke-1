<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('kta_renewals', function(Blueprint $table){
            $table->decimal('amount',12,2)->after('new_expires_at')->default(0);
        });
    }
    public function down(): void
    {
        Schema::table('kta_renewals', function(Blueprint $table){
            $table->dropColumn('amount');
        });
    }
};