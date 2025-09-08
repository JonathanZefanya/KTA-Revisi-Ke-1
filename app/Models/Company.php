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
    'penanggung_jawab',
    'npwp',
        'registration_number',
        'email',
        'phone',
        'website',
        'address',
    'postal_code',
    'province_code','province_name','city_code','city_name',
    'photo_pjbu_path','npwp_bu_path','nib_file_path','ktp_pjbu_path','npwp_pjbu_path'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
