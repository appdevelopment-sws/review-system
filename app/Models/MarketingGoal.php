<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingGoal extends Model
{
    use HasFactory;

    protected $table = 'marketing_goals';

    protected $fillable = [
        'slug',
        'title',
        'description',
        'icon',
        'icon_bg_color',
        'icon_color',
        'badge_text',
        'is_instagram',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_instagram' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope query to only active goals.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope query ordered by sort order then id.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
    }
}