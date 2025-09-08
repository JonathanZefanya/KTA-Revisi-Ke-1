<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->string('membership_card_number')->nullable()->unique();
            $table->date('membership_card_issued_at')->nullable();
            $table->date('membership_card_expires_at')->nullable();
            $table->string('membership_photo_path')->nullable();
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['membership_card_number','membership_card_issued_at','membership_card_expires_at','membership_photo_path']);
        });
    }
};
