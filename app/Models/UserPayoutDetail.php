<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPayoutDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payout_type',
        'account_holder_name',
        'upi_id',
        'bank_name',
        'account_number',
        'ifsc_code',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the payout detail.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
