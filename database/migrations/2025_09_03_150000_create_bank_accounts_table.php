<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bank_accounts', function(Blueprint $t){
            $t->id();
            $t->string('bank_name',80);
            $t->string('account_number',40);
            $t->string('account_name',120);
            $t->boolean('is_active')->default(true);
            $t->unsignedInteger('sort')->default(0);
            $t->timestamps();
            $t->unique(['bank_name','account_number']);
        });
    }
    public function down(): void { Schema::dropIfExists('bank_accounts'); }
};
