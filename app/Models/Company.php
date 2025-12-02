<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    'bentuk',
    'jenis',
    'kualifikasi',
    'membership_type',
    'penanggung_jawab',
    'npwp',
        'registration_number',
        'email',
        'phone',
        'website',
        'address',
    'asphalt_mixing_plant_address',
    'concrete_batching_plant_address',
    'postal_code',
    'province_code','province_name','city_code','city_name',
    'photo_pjbu_path','photo_pjbu_thumb_path','npwp_bu_path','akte_bu_path','nib_file_path','ktp_pjbu_path','npwp_pjbu_path'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
