<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** KYC: -1 = incomplete, 0 = inactive, 1 = active */
    public const KYC_INCOMPLETE = -1;

    public const KYC_INACTIVE = 0;

    public const KYC_ACTIVE = 1;

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function hasActiveKyc(): bool
    {
        return (int) $this->kyc_status === self::KYC_ACTIVE;
    }

    public function getKycStatusLabelAttribute(): string
    {
        return match ((int) $this->kyc_status) {
            self::KYC_INCOMPLETE => 'Incomplete',
            self::KYC_INACTIVE => 'Inactive',
            self::KYC_ACTIVE => 'Active',
            default => 'Unknown',
        };
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_code',
        'name',
        'email',
        'password',
        'phone',
        'pan',
        'aadhar',
        'pan_name',
        'is_admin',
        'daily_limit',
        'monthly_limit',
        'yearly_limit',
        'kyc_status',
        'status',
        'email_verified_at',
        'phone_verified_at',
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
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'daily_limit' => 'decimal:2',
            'monthly_limit' => 'decimal:2',
            'yearly_limit' => 'decimal:2',
            'kyc_status' => 'integer',
        ];
    }

    public function banks(): HasMany
    {
        return $this->hasMany(Bank::class);
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
