<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Therapist extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'name',
        'title',
        'email',
        'phone',
        'gender',
        'languages',
        'certifications',
        'bio',
        'photo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'languages' => 'array',
        'certifications' => 'array',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function admin(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function services(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'service_therapist')->withTimestamps();
    }
}
