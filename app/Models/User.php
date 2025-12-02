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
    'is_active',
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
            'is_active' => 'boolean',
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
        // Format Baru: AA/BBB/CCC
        // AA = Kode provinsi (2 digit dari Kemendagri)
        // BBB = Nomor urut anggota per provinsi (3 digit, mulai dari 001)
        // CCC = Status: AB (Anggota Biasa) atau ALB (Anggota Luar Biasa)
        // Contoh: 31/001/AB (Jakarta, urut 1, Anggota Biasa)
        
        // Get company with province info
        $company = $this->companies()->first();
        if (!$company || !$company->province_code) {
            // Fallback ke format lama jika tidak ada provinsi
            return '00/000/AB';
        }
        
        // Get province code (2 digit)
        $provinceCode = $company->province_code;
        $provinceCodes = config('province_codes', []);
        $ktaProvinceCode = $provinceCodes[$provinceCode] ?? substr($provinceCode, 0, 2);
        
        // Get membership type from company
        $membershipType = $company->membership_type ?? 'AB';
        
        // Count existing members in same province (exclude current user to avoid double count on regenerate)
        $count = static::whereHas('companies', function($q) use ($provinceCode) {
            $q->where('province_code', $provinceCode);
        })
        ->where('id', '!=', $this->id)
        ->where('membership_card_number', 'like', $ktaProvinceCode . '/%')
        ->count();
        
        // Next number (3 digit format)
        $nextNumber = $count + 1;
        
        // Format: 31/001/AB, 31/002/AB, 12/001/ALB, dst
        return sprintf('%s/%03d/%s', $ktaProvinceCode, $nextNumber, $membershipType);
    }

    /**
     * Regenerate nomor KTA untuk menyesuaikan status membership
     * Digunakan saat admin mengubah membership_type di company
     */
    public function regenerateKtaNumber(): void
    {
        if (!$this->membership_card_number) {
            return; // Belum punya KTA, tidak perlu regenerate
        }

        $newNumber = $this->generateMembershipNumber();
        $this->forceFill(['membership_card_number' => $newNumber])->save();
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
