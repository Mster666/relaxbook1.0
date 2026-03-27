<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class UserLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'ip',
        'user_agent',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(?User $user, string $action, array $meta = []): void
    {
        if (! Schema::hasTable('user_logs')) {
            return;
        }

        $request = request();

        self::create([
            'user_id' => $user?->id,
            'action' => $action,
            'ip' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'meta' => $meta ?: null,
        ]);
    }
}

