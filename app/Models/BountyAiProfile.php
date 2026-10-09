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