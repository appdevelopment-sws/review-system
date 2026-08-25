<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignParticipation extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'user_id',
        'proof_image',
        'review_text',
        'status',
        'reward_amount',
        'admin_notes',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'reward_amount' => 'decimal:2',
            'submitted_at' => 'datetime',
        ];
    }

    /**
     * Get the campaign for this participation.
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Get the user who submitted this participation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
