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
        'working_days',
        'opening_time',
        'closing_time',
        'is_24_hours',
        'onboarding_status',
        'current_step',
    ];

    protected $casts = [
        'selected_goals' => 'array',
        'working_days' => 'array',
        'is_24_hours' => 'boolean',
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
     * Format working days as comma-separated or summary.
     */
    public function getWorkingDaysFormattedAttribute(): string
    {
        if (empty($this->working_days)) {
            return 'All Days';
        }
        if (count($this->working_days) === 7) {
            return 'Mon - Sun (Daily)';
        }
        return implode(', ', $this->working_days);
    }

    /**
     * Friendly status display.
     */
    public function getStepStatusLabelAttribute(): string
    {
        if ($this->current_step >= 2 || $this->onboarding_status === 'completed') {
            return 'Fully Onboarded';
        }
        return 'In Progress (Step 1)';
    }
}