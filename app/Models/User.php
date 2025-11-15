<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
    'password',
    'phone',
    'approved_at',
    'membership_card_number','membership_card_issued_at','membership_card_expires_at'
    ,'membership_photo_path'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'approved_at' => 'datetime',
            'membership_card_issued_at' => 'date',
            'membership_card_expires_at' => 'date',
        ];
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class)->withTimestamps();
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function ktaRenewals()
    {
        return $this->hasMany(KtaRenewal::class);
    }

    public function hasActiveMembershipCard(): bool
    {
        return $this->membership_card_number && $this->membership_card_expires_at && now()->lte($this->membership_card_expires_at);
    }

    public function isEligibleForRenewal(): bool
    {
        if (!$this->hasActiveMembershipCard()) {
            return false;
        }
        
        // Renewal eligible 7 weeks (49 days) before expiry
        $expiryDate = \Carbon\Carbon::parse($this->membership_card_expires_at);
        $renewalEligibleDate = $expiryDate->copy()->subWeeks(7);
        return now()->gte($renewalEligibleDate);
    }

    public function getRenewalEligibilityDate(): ?\Carbon\Carbon
    {
        if (!$this->membership_card_expires_at) {
            return null;
        }
        
        $expiryDate = \Carbon\Carbon::parse($this->membership_card_expires_at);
        return $expiryDate->copy()->subWeeks(1);
    }

    public function issueMembershipCardIfNeeded(): void
    {
        if($this->hasActiveMembershipCard()) return; // still valid
        $number = $this->generateMembershipNumber();
        $issued = now();
        $expires = $issued->copy()->addYear()->subDay(); // valid 1 year (inclusive) or adjust logic
        $attrs = [
            'membership_card_number' => $number,
            'membership_card_issued_at' => $issued,
            'membership_card_expires_at' => $expires,
        ];
        if(!$this->membership_photo_path){
            $company = $this->companies()->first();
            if($company && $company->photo_pjbu_path){
                $attrs['membership_photo_path'] = $company->photo_pjbu_path; // reuse existing stored path
            }
        }
        $this->forceFill($attrs)->save();
    }

    protected function generateMembershipNumber(): string
    {
        // Format: NN/MMYY/AB (urut/bulan+tahun/suffix)
        // Contoh: 01/0125/AB = urut 01, bulan 01, tahun 25 (2025)
        // Sesuai format Excel: 16/012/AB, 13/014/AB, dst
        
        $now = now();
        $month = $now->format('m'); // 01-12
        $yearShort = $now->format('y'); // 25 untuk 2025
        $kodeWaktu = $month . $yearShort; // Contoh: 0125 untuk Januari 2025
        
        // Cari nomor urut terakhir dengan format yang sama
        // Pattern: __/0125/AB (__ = 2 digit urut)
        $lastKta = static::where('membership_card_number', 'like', "%/{$kodeWaktu}/AB")
            ->orderByRaw('CAST(SUBSTRING_INDEX(membership_card_number, "/", 1) AS UNSIGNED) DESC')
            ->value('membership_card_number');
        
        if ($lastKta) {
            // Ambil nomor urut dari format: 16/012/AB -> 16
            $parts = explode('/', $lastKta);
            $nextNumber = intval($parts[0]) + 1;
        } else {
            // Mulai dari 01
            $nextNumber = 1;
        }
        
        // Format: 01/0125/AB, 02/0125/AB, dst
        return sprintf('%02d/%s/AB', $nextNumber, $kodeWaktu);
    }

    /**
     * Accessor: first related company phone (primary business phone).
     * Falls back to null if no company or phone set.
     */
    public function getCompanyPhoneAttribute(): ?string
    {
        // Use loaded relation to avoid N+1; otherwise query the first company.
        $company = $this->relationLoaded('companies') ? $this->companies->first() : $this->companies()->first();
        return $company?->phone;
    }

}
