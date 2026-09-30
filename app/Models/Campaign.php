<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category_id',
        'platform',
        'instructions',
        'suggested_points',
        'media_type',
        'media_url',
        'redirect_url',
        'reward_amount',
        'participant_limit',
        'participants_count',
        'clicks_count',
        'impressions_count',
        'status',
        'payment_info',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'reward_amount' => 'decimal:2',
        'participant_limit' => 'integer',
        'participants_count' => 'integer',
        'clicks_count' => 'integer',
        'impressions_count' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Get the business user who created this campaign.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the category this campaign belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all participations/conversions for this campaign.
     */
    public function participations(): HasMany
    {
        return $this->hasMany(CampaignParticipation::class);
    }

    /**
     * Check if campaign participation limit is reached.
     */
    public function isFull(): bool
    {
        return $this->participants_count >= $this->participant_limit;
    }

    /**
     * Calculate percentage of participants availed.
     */
    public function progressPercentage(): int
    {
        if ($this->participant_limit <= 0) return 0;
        return (int) min(100, round(($this->participants_count / $this->participant_limit) * 100));
    }

    /**
     * Calculate conversion rate percentage (Participations vs Clicks).
     */
    public function conversionRate(): float
    {
        if ($this->clicks_count <= 0) return 0.0;
        return round(($this->participants_count / $this->clicks_count) * 100, 1);
    }

    /**
     * Dynamic accessor for media URL to resolve Android emulator host alias when accessed via web.
     */
    public function getMediaUrlAttribute($value): ?string
    {
        if (!$value) return null;
        if (str_contains($value, '10.0.2.2:8000')) {
            $path = strstr($value, '/storage/');
            return $path ? asset(ltrim($path, '/')) : $value;
        }
        return $value;
    }
}
