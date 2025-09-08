<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::table('invoices', function(Blueprint $t){ if(!Schema::hasColumn('invoices','bank_account_id')){ $t->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete()->after('company_id'); } }); }
public function down(): void { Schema::table('invoices', function(Blueprint $t){ if(Schema::hasColumn('invoices','bank_account_id')){ $t->dropConstrainedForeignId('bank_account_id'); } }); } };
