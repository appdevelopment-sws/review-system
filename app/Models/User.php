<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'role', 'wallet_balance', 'points_balance'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Check if user has admin role.
     */
    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleting(function (User $user) {
            \App\Models\BountyAiProfile::where('user_id', $user->id)
                ->orWhere(function ($q) use ($user) {
                    if (!empty($user->email)) {
                        $q->where('email', $user->email);
                    }
                })
                ->delete();
        });
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user has business role.
     */
    public function isBusiness(): bool
    {
        return $this->role === 'business';
    }

    /**
     * Get available points for user.
     */
    public function getAvailablePointsAttribute(): int
    {
        return (int) ($this->points_balance ?? round($this->wallet_balance * 10));
    }

    /**
     * Get campaigns created by this user / business.
     */
    public function campaigns(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Campaign::class, 'user_id');
    }

    public function businessCampaigns(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Campaign::class, 'user_id');
    }

    /**
     * Get user campaign participations.
     */
    public function participations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CampaignParticipation::class);
    }

    /**
     * Get user wallet transactions ledger.
     */
    public function walletTransactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Get user payout details (bank / upi).
     */
    public function payoutDetails(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserPayoutDetail::class);
    }

    /**
     * Get user's default/primary payout detail.
     */
    /**
     * Get Bounty AI business onboarding profile.
     */
    public function bountyAiProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(BountyAiProfile::class, 'user_id');
    }

    public function defaultPayoutDetail(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserPayoutDetail::class)->latestOfMany();
    }

    /**
     * Get user withdrawal requests.
     */
    public function withdrawalRequests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WithdrawalRequest::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'wallet_balance' => 'decimal:2',
            'points_balance' => 'integer',
        ];
    }
}
