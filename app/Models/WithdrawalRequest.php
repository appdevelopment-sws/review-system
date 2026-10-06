<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WithdrawalRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'redeemed_points',
        'payout_type',
        'gift_card_brand',
        'gift_card_email',
        'other_details',
        'account_holder_name',
        'upi_id',
        'bank_name',
        'account_number',
        'ifsc_code',
        'status',
        'admin_notes',
        'proof_image',
        'utr_number',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'redeemed_points' => 'integer',
            'processed_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the user that requested the withdrawal.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the corresponding wallet transaction.
     */
    public function walletTransaction(): HasOne
    {
        return $this->hasOne(WalletTransaction::class, 'reference_id')
            ->where('reference_type', 'withdrawal_request');
    }

    /**
     * Helper to get formatted payout summary string.
     */
    public function getPayoutSummaryAttribute(): string
    {
        if ($this->payout_type === 'upi') {
            return 'UPI: ' . ($this->upi_id ?? 'N/A') . ($this->account_holder_name ? ' (' . $this->account_holder_name . ')' : '');
        }

        if ($this->payout_type === 'bank') {
            return 'Bank: ' . ($this->bank_name ? $this->bank_name . ' - ' : '') . 'A/C: ' . ($this->account_number ?? 'N/A') . ($this->ifsc_code ? ' [IFSC: ' . $this->ifsc_code . ']' : '');
        }

        if ($this->payout_type === 'gift_card') {
            return 'Gift Card (' . ($this->gift_card_brand ?? 'Amazon/Brand') . ') ➔ ' . ($this->gift_card_email ?? 'User Delivery');
        }

        if ($this->payout_type === 'other') {
            return 'Other Option: ' . ($this->other_details ?? 'Supported Payout / Recharge');
        }

        return strtoupper($this->payout_type);
    }
}
