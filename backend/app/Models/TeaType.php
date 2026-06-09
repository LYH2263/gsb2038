<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeaType extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'image_url'];

    public function teas(): HasMany
    {
        return $this->hasMany(Tea::class);
    }
}
