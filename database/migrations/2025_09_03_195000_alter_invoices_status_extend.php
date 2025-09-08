<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; use Illuminate\Support\Facades\DB;
return new class extends Migration { public function up(): void { Schema::table('invoices', function(Blueprint $t){ DB::statement("ALTER TABLE invoices MODIFY status VARCHAR(40) NOT NULL DEFAULT 'unpaid'"); }); }
public function down(): void { Schema::table('invoices', function(Blueprint $t){ DB::statement("ALTER TABLE invoices MODIFY status ENUM('unpaid','paid','cancelled') NOT NULL DEFAULT 'unpaid'"); }); } };
