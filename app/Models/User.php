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
        // Format: MM/NNN/AB (month + incremental + suffix) e.g. 09/048/AB
        $month = now()->format('m');
        $count = static::whereYear('membership_card_issued_at', now()->year)
            ->whereMonth('membership_card_issued_at', now()->month)
            ->count() + 1;
        return $month.'/'.str_pad($count,3,'0',STR_PAD_LEFT).'/AB';
    }

}
