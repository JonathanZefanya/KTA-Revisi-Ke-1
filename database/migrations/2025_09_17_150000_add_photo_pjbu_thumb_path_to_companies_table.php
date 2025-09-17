<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if(!Schema::hasColumn('companies','photo_pjbu_thumb_path')){
                $table->string('photo_pjbu_thumb_path')->nullable()->after('photo_pjbu_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if(Schema::hasColumn('companies','photo_pjbu_thumb_path')){
                $table->dropColumn('photo_pjbu_thumb_path');
            }
        });
    }
};
