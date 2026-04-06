<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class Admin extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasFactory, Notifiable;

    protected static function booted(): void
    {
        static::saving(function (self $admin): void {
            if (! $admin->isDirty('subscription_expires_at')) {
                return;
            }

            if ($admin->isDirty('subscription_verified_at')) {
                return;
            }

            $new = $admin->subscription_expires_at;
            $old = $admin->getOriginal('subscription_expires_at');

            if ($new === null && $old === null) {
                return;
            }

            $admin->subscription_verified_at = now();
        });

        static::saved(function (self $admin): void {
            if (! $admin->wasChanged('subscription_expires_at')) {
                return;
            }

            if ($admin->subscription_expires_at === null) {
                return;
            }

            $startsAtCarbon = $admin->subscription_verified_at ?? now();
            $startsAt = $startsAtCarbon->toDateString();
            $endsAt = $admin->subscription_expires_at->toDateString();
            $isExpired = now()->startOfDay()->gt($admin->subscription_expires_at->copy()->startOfDay());
            $months = max(1, (int) $startsAtCarbon->copy()->startOfDay()->diffInMonths($admin->subscription_expires_at->copy()->startOfDay()));
            $label = $months >= 12 ? '1 year' : ($months . ' months');
            $paymentStatus = $isExpired ? 'EXPIRED' : 'PENDING';

            $admin->subscriptionLogs()->create([
                'business_name' => (string) ($admin->company_name ?: $admin->name),
                'subscription_plan' => "₱24,999/month ({$label})",
                'amount' => 24999 * $months,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'payment_status' => $paymentStatus,
                'paid_at' => null,
            ]);
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_name',
        'company_logo',
        'company_address',
        'company_latitude',
        'company_longitude',
        'phone_number',
        'gender',
        'age',
        'verification_code',
        'profile_picture',
        'is_super_admin',
        'is_active',
        'subscription_expires_at',
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
            'subscription_expires_at' => 'datetime',
            'subscription_verified_at' => 'datetime',
            'company_latitude' => 'float',
            'company_longitude' => 'float',
            'is_super_admin' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'super-admin') {
            return false;
        }

        if (! $this->is_active) {
            return false;
        }

        if ($this->subscription_expires_at && now()->greaterThan($this->subscription_expires_at)) {
            return false;
        }

        return true;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->profile_picture ? Storage::disk('public')->url($this->profile_picture) : null;
    }

    public function subscriptionLogs(): HasMany
    {
        return $this->hasMany(SubscriptionLog::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(CompanyRating::class);
    }
}
