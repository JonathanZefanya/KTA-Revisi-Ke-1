<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert default KTA layout configuration
        $defaults = [
            'kta_template_path' => 'img/kta_template.png',
            'kta_layout_config' => json_encode([
                'member_box' => ['left' => 50, 'top' => 53, 'fontSize' => 18],
                'title' => ['left' => 460, 'top' => 145, 'fontSize' => 18],
                'meta' => ['left' => 260, 'top' => 190, 'width' => 460, 'fontSize' => 13, 'labelWidth' => 180],
                'expiry' => ['left' => 460, 'top' => 450, 'fontSize' => 12],
                'photo' => ['left' => 262, 'top' => 438, 'width' => 95, 'height' => 125],
                'qr' => ['right' => 50, 'bottom' => 20, 'width' => 50, 'height' => 50],
            ]),
        ];

        foreach ($defaults as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::whereIn('key', ['kta_template_path', 'kta_layout_config'])->delete();
    }
};
