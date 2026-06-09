<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tea extends Model
{
    protected $fillable = ['name', 'tea_type_id', 'description', 'brewing_tip', 'tasting_notes', 'image_url', 'origin'];

    public function teaType(): BelongsTo
    {
        return $this->belongsTo(TeaType::class);
    }
}
