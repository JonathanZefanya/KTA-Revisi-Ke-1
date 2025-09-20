<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if column doesn't exist before adding
        if (!Schema::hasColumn('users', 'membership_photo_path')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('membership_photo_path')->nullable()->after('membership_card_expires_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'membership_photo_path')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('membership_photo_path');
            });
        }
    }
};