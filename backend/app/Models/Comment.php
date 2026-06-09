<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    protected $fillable = ['tea_id', 'user_id', 'content'];

    public function tea(): BelongsTo
    {
        return $this->belongsTo(Tea::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

