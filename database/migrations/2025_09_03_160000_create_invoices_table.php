<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('invoices', function(Blueprint $t){
            $t->id();
            $t->string('number',40)->unique();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $t->enum('type',['registration','renewal']);
            $t->decimal('amount',15,2);
            $t->string('currency',8)->default('IDR');
            $t->date('issued_date');
            $t->date('due_date');
            $t->enum('status',['unpaid','paid','cancelled'])->default('unpaid');
            $t->timestamp('paid_at')->nullable();
            $t->json('meta')->nullable();
            $t->timestamps();
            $t->index(['user_id','type']);
        });
    }
    public function down(): void { Schema::dropIfExists('invoices'); }
};