<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class RenewalPaymentRate extends Model { protected $fillable=['jenis','kualifikasi','amount']; public static function upsertRate($jenis,$kualifikasi,$amount){ static::updateOrCreate(['jenis'=>$jenis,'kualifikasi'=>$kualifikasi],['amount'=>$amount]); }}
