<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('bentuk', 30)->nullable()->after('name'); // PT, CV, Koperasi
            $table->string('jenis', 30)->nullable(); // BUJKN, BUJKA, BUJKPMA
            $table->string('kualifikasi', 30)->nullable(); // Kecil, Menengah, Besar, Spesialis
            $table->string('penanggung_jawab')->nullable();
            $table->string('npwp', 32)->nullable()->index();
            $table->string('postal_code', 10)->nullable();
            $table->string('province_code', 10)->nullable();
            $table->string('province_name')->nullable();
            $table->string('city_code', 10)->nullable();
            $table->string('city_name')->nullable();
            // Document paths
            $table->string('photo_pjbu_path')->nullable();
            $table->string('npwp_bu_path')->nullable();
            $table->string('nib_file_path')->nullable();
            $table->string('ktp_pjbu_path')->nullable();
            $table->string('npwp_pjbu_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'bentuk','jenis','kualifikasi','penanggung_jawab','npwp','postal_code','province_code','province_name','city_code','city_name',
                'photo_pjbu_path','npwp_bu_path','nib_file_path','ktp_pjbu_path','npwp_pjbu_path'
            ]);
        });
    }
};
