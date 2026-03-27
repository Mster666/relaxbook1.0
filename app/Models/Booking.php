<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'user_id',
        'therapist_id',
        'service_id',
        'room_id',
        'booking_date',
        'booking_time',
        'status',
        'notes',
    ];

    protected $casts = [
        'booking_date' => 'date',
        // 'booking_time' => 'datetime', 
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function therapist(): BelongsTo
    {
        return $this->belongsTo(Therapist::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function services(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'booking_service')->withTimestamps();
    }

    public function bookingServiceTherapists(): HasMany
    {
        return $this->hasMany(BookingServiceTherapist::class);
    }
}
