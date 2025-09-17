<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KtaRenewal extends Model
{
    use HasFactory;

    protected $fillable = [ 'user_id','invoice_id','previous_expires_at','new_expires_at','amount','processed_at' ];

    protected $casts = [
        'previous_expires_at' => 'date',
        'new_expires_at' => 'date',
        'amount' => 'decimal:2',
        'processed_at' => 'datetime'
    ];

    public function user(){ return $this->belongsTo(User::class); }
    public function invoice(){ return $this->belongsTo(Invoice::class); }

    public function isProcessed(): bool { return !is_null($this->processed_at); }
}