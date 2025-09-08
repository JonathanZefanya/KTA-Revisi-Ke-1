<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function(Blueprint $table){
            if(!Schema::hasColumn('users','phone')) $table->string('phone',30)->nullable()->after('email');
            // removed nik per new requirement
            if(!Schema::hasColumn('users','approved_at')) $table->timestamp('approved_at')->nullable()->after('email_verified_at');
        });
    }
    public function down(): void
    {
        Schema::table('users', function(Blueprint $table){
            if(Schema::hasColumn('users','phone')) $table->dropColumn('phone');
            // nik removed earlier; skip drop if not present
            if(Schema::hasColumn('users','approved_at')) $table->dropColumn('approved_at');
        });
    }
};
