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

    public function hasSkippedKyc(): bool
    {
        return $this->kyc_skipped_at !== null && ! $this->hasActiveKyc();
    }

    /** Explore dashboard, pay (when allowed), profile — without full KYC. */
    public function canUsePlatform(): bool
    {
        return $this->is_admin || $this->hasActiveKyc() || $this->hasSkippedKyc();
    }

    /** Bank payouts and settlements require verified KYC. */
    public function canReceivePayouts(): bool
    {
        return ! $this->is_admin && $this->hasActiveKyc();
    }

    public function isSeller(): bool
    {
        return $this->role === 'seller';
    }

    /** Seller has GSTIN on profile (15-char Indian GSTIN). */
    public function hasGstRegistered(): bool
    {
        $gstin = strtoupper(preg_replace('/\s+/', '', (string) $this->gstin) ?? '');

        return strlen($gstin) === 15;
    }

    public function getKycStatusLabelAttribute(): string
    {
        if ($this->hasSkippedKyc()) {
            return 'Skipped';
        }

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
        'role',
        'company_name',
        'gstin',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'pincode',
        'accept_only_kyc_customers',
        'daily_limit',
        'monthly_limit',
        'yearly_limit',
        'kyc_status',
        'status',
        'email_verified_at',
        'phone_verified_at',
        'kyc_skipped_at',
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
            'kyc_skipped_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'accept_only_kyc_customers' => 'boolean',
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

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function sellerSubservices(): HasMany
    {
        return $this->hasMany(SellerSubservice::class);
    }

    public function ordersAsCustomer(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function ordersAsFreelancer(): HasMany
    {
        return $this->hasMany(Order::class, 'freelancer_id');
    }

    public function acceptsCustomer(User $customer): bool
    {
        if (! $this->accept_only_kyc_customers) {
            return true;
        }

        return $customer->hasActiveKyc();
    }

    public function scopeMarketplaceSellers($query)
    {
        return $query
            ->where('is_admin', false)
            ->where('status', 'active')
            ->where('role', 'seller');
    }
}
