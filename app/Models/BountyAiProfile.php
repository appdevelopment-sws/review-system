<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BountyAiProfile extends Model
{
    use HasFactory;

    protected $table = 'bounty_ai_profiles';

    protected $fillable = [
        'user_id',
        'business_name',
        'selected_goals',
        'phone_number',
        'email',
        'website',
        'address',
        'city',
        'state',
        'pincode',
        'business_type',
        'category',
        'category_id',
        'working_days',
        'opening_time',
        'closing_time',
        'is_24_hours',
        'latitude',
        'longitude',
        'landmark',
        'is_gps_detected',
        'is_google_connected',
        'google_account_email',
        'google_account_name',
        'google_avatar_url',
        'google_location_id',
        'google_location_title',
        'google_location_address',
        'google_rating',
        'google_reviews_count',
        'onboarding_status',
        'current_step',
    ];

    protected $casts = [
        'selected_goals' => 'array',
        'working_days' => 'array',
        'is_24_hours' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
        'is_gps_detected' => 'boolean',
        'is_google_connected' => 'boolean',
        'google_rating' => 'float',
        'google_reviews_count' => 'integer',
        'current_step' => 'integer',
    ];

    /**
     * Get the associated user account.
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Formatted string of operating hours.
     */
    public function getOperatingHoursFormattedAttribute(): string
    {
        if ($this->is_24_hours) {
            return '24 Hours Open';
        }
        if ($this->opening_time && $this->closing_time) {
            return $this->opening_time . ' - ' . $this->closing_time;
        }
        return 'Standard Hours';
    }

    /**
     * Formatted list of active operating days.
     */
    public function getWorkingDaysFormattedAttribute(): string
    {
        if (empty($this->working_days) || !is_array($this->working_days)) {
            return 'Mon - Sat';
        }
        return implode(', ', $this->working_days);
    }
}