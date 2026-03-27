<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'name',
        'code',
        'description',
        'capacity_max',
        'room_type',
        'price_per_hour',
        'amenities',
        'status',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'amenities' => 'array',
        'price_per_hour' => 'decimal:2',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function admin(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
}
