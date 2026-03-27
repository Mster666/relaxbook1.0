<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminOperatingBreak extends Model
{
    protected $table = 'admin_operating_breaks';

    protected $fillable = [
        'operating_hour_id',
        'label',
        'starts_at',
        'ends_at',
    ];

    public function operatingHour(): BelongsTo
    {
        return $this->belongsTo(AdminOperatingHour::class, 'operating_hour_id');
    }
}
