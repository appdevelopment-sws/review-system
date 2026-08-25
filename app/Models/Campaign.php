<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'media_type',
        'media_url',
        'redirect_url',
        'reward_amount',
        'participant_limit',
        'participants_count',
        'status',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'reward_amount' => 'decimal:2',
            'participant_limit' => 'integer',
            'participants_count' => 'integer',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
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
}
