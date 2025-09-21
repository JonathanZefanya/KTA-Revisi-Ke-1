<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('company_user')) {
            Schema::table('company_user', function (Blueprint $table) {
                // Enforce one user -> one company
                $table->unique('user_id', 'company_user_user_id_unique');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('company_user')) {
            Schema::table('company_user', function (Blueprint $table) {
                $table->dropUnique('company_user_user_id_unique');
            });
        }
    }
};
