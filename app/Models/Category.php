<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'default_reward',
    ];

    protected $casts = [
        'default_reward' => 'float',
    ];

    /**
     * Campaigns belonging to this category.
     */
    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    /**
     * Dynamic accessor for image URL to resolve Android emulator host alias when accessed via web.
     */
    public function getImageAttribute($value): ?string
    {
        if (!$value) return null;
        if (str_contains($value, '10.0.2.2:8000')) {
            $path = strstr($value, '/storage/');
            return $path ? asset(ltrim($path, '/')) : $value;
        }
        return $value;
    }
}
