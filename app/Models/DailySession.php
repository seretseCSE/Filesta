<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailySession extends Model
{
    /** @use HasFactory<\Database\Factories\DailySessionFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'activated_at',
        'closed_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'activated_at' => 'datetime',
            'closed_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
