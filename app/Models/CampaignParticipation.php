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
        'review_link',
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

    /**
     * Dynamic accessor for proof image URL to handle host resolution across web & mobile.
     */
    public function getProofImageAttribute($value): ?string
    {
        if (!$value) return null;
        if (str_contains($value, '10.0.2.2:8000')) {
            $path = strstr($value, '/storage/');
            return $path ? asset(ltrim($path, '/')) : $value;
        }
        return $value;
    }
}
