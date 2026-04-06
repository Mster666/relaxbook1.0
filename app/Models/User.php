<?php

namespace App\Models;

use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmailContract
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, MustVerifyEmailTrait, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'gender',
        'age',
        'birth_date',
        'bio',
        'street_address',
        'city',
        'state_province',
        'zip_code',
        'country',
        'email_notifications',
        'sms_notifications',
        'language',
        'timezone',
        'profile_picture',
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
            'birth_date' => 'date',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
        ];
    }

    public function bookings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function companyRatings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CompanyRating::class);
    }
}
