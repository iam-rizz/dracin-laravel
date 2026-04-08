<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Watchlist extends Model
{
    protected $fillable = [
        'user_id',
        'provider',
        'drama_id',
        'drama_title',
        'drama_thumbnail',
        'total_episodes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
