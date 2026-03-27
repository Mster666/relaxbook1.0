<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdminOperatingHour extends Model
{
    protected $table = 'admin_operating_hours';

    protected $fillable = [
        'admin_id',
        'day_of_week',
        'is_closed',
        'opens_at',
        'closes_at',
    ];

    protected $casts = [
        'is_closed' => 'boolean',
        'day_of_week' => 'integer',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function breaks(): HasMany
    {
        return $this->hasMany(AdminOperatingBreak::class, 'operating_hour_id');
    }
}
