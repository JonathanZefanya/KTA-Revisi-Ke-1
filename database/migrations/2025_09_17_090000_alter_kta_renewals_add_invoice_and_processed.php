<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::table('kta_renewals', function(Blueprint $t){ $t->foreignId('invoice_id')->nullable()->after('user_id')->constrained('invoices')->nullOnDelete(); $t->dateTime('processed_at')->nullable()->after('new_expires_at'); }); }
 public function down(): void { Schema::table('kta_renewals', function(Blueprint $t){ $t->dropConstrainedForeignId('invoice_id'); $t->dropColumn('processed_at'); }); } };