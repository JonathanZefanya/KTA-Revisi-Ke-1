<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Invoice extends Model {
    protected $fillable=['number','user_id','company_id','bank_account_id','type','amount','currency','issued_date','due_date','status','paid_at','meta','payment_proof_path','proof_uploaded_at','verified_by','verified_at','verification_note'];
    protected $casts=[ 'issued_date'=>'date','due_date'=>'date','paid_at'=>'datetime','meta'=>'array','proof_uploaded_at'=>'datetime','verified_at'=>'datetime'];
    public const STATUS_UNPAID='unpaid';
    public const STATUS_AWAITING='awaiting_verification';
    public const STATUS_PAID='paid';
    public const STATUS_REJECTED='rejected';
    public static function generateNumber(): string { return 'INV-'.date('Ymd').'-'.str_pad((string) (static::whereDate('created_at',today())->count()+1),4,'0',STR_PAD_LEFT); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function bankAccount(): BelongsTo { return $this->belongsTo(BankAccount::class); }
    public function scopeAwaiting($q){ return $q->where('status',self::STATUS_AWAITING); }
    public function scopeUnpaid($q){ return $q->where('status',self::STATUS_UNPAID); }
    public function scopePaid($q){ return $q->where('status',self::STATUS_PAID); }
    public function isPaid(): bool { return $this->status===self::STATUS_PAID; }
    public function hasProof(): bool { return !empty($this->payment_proof_path); }
}
