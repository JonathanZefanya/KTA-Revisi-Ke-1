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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('asphalt_mixing_plant_address', 500)->nullable()->after('address');
            $table->string('concrete_batching_plant_address', 500)->nullable()->after('asphalt_mixing_plant_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['asphalt_mixing_plant_address', 'concrete_batching_plant_address']);
        });
    }
};
