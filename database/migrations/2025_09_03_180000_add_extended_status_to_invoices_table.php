<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::table('invoices', function(Blueprint $t){ if(!Schema::hasColumn('invoices','payment_proof_path')){ $t->string('payment_proof_path')->nullable(); $t->timestamp('proof_uploaded_at')->nullable(); $t->foreignId('verified_by')->nullable()->constrained('admins')->nullOnDelete(); $t->timestamp('verified_at')->nullable(); $t->string('verification_note')->nullable(); }
    if(!Schema::hasColumn('invoices','status')){ $t->string('status',30)->default('unpaid')->change(); }
}); }
public function down(): void { Schema::table('invoices', function(Blueprint $t){ /* no down for safety */ }); } };
