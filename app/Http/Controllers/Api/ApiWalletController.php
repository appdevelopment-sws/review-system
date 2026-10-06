<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CampaignParticipation;
use App\Models\UserPayoutDetail;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiWalletController extends Controller
{
    /**
     * Get wallet summary (Available, Pending, Earned, Used Points), saved payout details, and transactions.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Wallet Balance Currency
        $walletBalance = (float) ($user->wallet_balance ?? 0.00);

        // Available Points (spendable in wallet)
        $availablePoints = (int) ($user->points_balance ?? round($walletBalance * 10));

        // Pending Submissions & Unscratched Cards Points
        $pendingSubmissionsPoints = (int) CampaignParticipation::where('user_id', $user->id)
            ->where('status', 'pending')
            ->sum('reward_points');
        if ($pendingSubmissionsPoints === 0) {
            $pendingSubmissionsPoints = (int) round(CampaignParticipation::where('user_id', $user->id)->where('status', 'pending')->sum('reward_amount') * 10);
        }

        $unscratchedPoints = (int) CampaignParticipation::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('is_scratched', false)
            ->sum('reward_points');
        if ($unscratchedPoints === 0) {
            $unscratchedPoints = (int) round(CampaignParticipation::where('user_id', $user->id)->where('status', 'approved')->where('is_scratched', false)->sum('reward_amount') * 10);
        }

        $totalPendingPoints = $pendingSubmissionsPoints + $unscratchedPoints;

        // Earned Points (lifetime completed credits)
        $earnedPoints = (int) WalletTransaction::where('user_id', $user->id)
            ->where('type', 'credit')
            ->where('status', 'completed')
            ->sum('points');
        $totalEarnedAmount = (float) WalletTransaction::where('user_id', $user->id)
            ->where('type', 'credit')
            ->where('status', 'completed')
            ->sum('amount');
        if ($earnedPoints === 0 && $totalEarnedAmount > 0) {
            $earnedPoints = (int) round($totalEarnedAmount * 10);
        }

        // Used / Withdrawn Points
        $usedPoints = (int) WithdrawalRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->sum('redeemed_points');
        $totalWithdrawnAmount = (float) WithdrawalRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->sum('amount');
        if ($usedPoints === 0 && $totalWithdrawnAmount > 0) {
            $usedPoints = (int) round($totalWithdrawnAmount * 10);
        }

        $completedCount = CampaignParticipation::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();

        $unscratchedCardsCount = CampaignParticipation::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('is_scratched', false)
            ->count();

        // Saved Payout Details
        $payoutDetail = UserPayoutDetail::where('user_id', $user->id)
            ->latest()
            ->first();

        // Paginated Transaction History
        $perPage = min(50, max(1, (int) $request->input('per_page', 10)));
        $transactionsPaginator = WalletTransaction::with('withdrawalRequest')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate($perPage);

        $transactions = collect($transactionsPaginator->items())
            ->map(function ($tx) {
                $proofImage = null;
                $utrNumber = null;

                if ($tx->reference_type === 'withdrawal_request' && $tx->withdrawalRequest) {
                    $proofImage = $tx->withdrawalRequest->proof_image;
                    $utrNumber = $tx->withdrawalRequest->utr_number;
                }

                $points = $tx->display_points;

                return [
                    'id' => $tx->id,
                    'type' => $tx->type,
                    'amount' => (float) $tx->amount,
                    'points' => $points,
                    'title' => $tx->title,
                    'description' => $tx->description,
                    'status' => $tx->status,
                    'proof_image' => $proofImage,
                    'utr_number' => $utrNumber,
                    'reference_type' => $tx->reference_type,
                    'reference_id' => $tx->reference_id,
                    'created_at' => $tx->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'status' => true,
            'data' => [
                'wallet' => [
                    'balance' => $walletBalance,
                    'available_points' => $availablePoints,
                    'pending_points' => $totalPendingPoints,
                    'earned_points' => $earnedPoints,
                    'used_points' => $usedPoints,
                    'withdrawn_points' => $usedPoints,
                    'total_earned' => $totalEarnedAmount,
                    'pending_rewards' => (float) ($totalPendingPoints / 10.0),
                    'completed_tasks_count' => $completedCount,
                    'unscratched_cards_count' => $unscratchedCardsCount,
                    'conversion_rate' => 10,
                    'conversion_note' => 'Points conversion & withdrawal values are configurable / pending finalization by Bounty Bits policy. Cashback/reward is processed only for verified and policy-compliant activity.',
                ],
                'payout_details' => $payoutDetail ? [
                    'id' => $payoutDetail->id,
                    'payout_type' => $payoutDetail->payout_type,
                    'account_holder_name' => $payoutDetail->account_holder_name,
                    'upi_id' => $payoutDetail->upi_id,
                    'bank_name' => $payoutDetail->bank_name,
                    'account_number' => $payoutDetail->account_number,
                    'ifsc_code' => $payoutDetail->ifsc_code,
                ] : null,
                'transactions' => $transactions,
                'pagination' => [
                    'current_page' => $transactionsPaginator->currentPage(),
                    'last_page' => $transactionsPaginator->lastPage(),
                    'per_page' => $transactionsPaginator->perPage(),
                    'total' => $transactionsPaginator->total(),
                    'has_more' => $transactionsPaginator->hasMorePages(),
                ],
            ],
        ]);
    }

    /**
     * Get user's scratch cards (unscratched & history).
     */
    public function scratchCards(Request $request): JsonResponse
    {
        $user = $request->user();

        $participations = CampaignParticipation::with('campaign')
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->latest('updated_at')
            ->get();

        $unscratched = [];
        $scratched = [];

        foreach ($participations as $p) {
            $item = [
                'id' => $p->id,
                'campaign_id' => $p->campaign_id,
                'campaign_title' => $p->campaign ? $p->campaign->title : 'Approved Task',
                'points' => (int) $p->points,
                'reward_amount' => (float) $p->reward_amount,
                'is_scratched' => (bool) $p->is_scratched,
                'scratched_at' => $p->scratched_at ? $p->scratched_at->toIso8601String() : null,
                'submitted_at' => $p->submitted_at ? $p->submitted_at->toIso8601String() : null,
            ];

            if ($p->is_scratched) {
                $scratched[] = $item;
            } else {
                $unscratched[] = $item;
            }
        }

        return response()->json([
            'status' => true,
            'data' => [
                'unscratched_cards' => $unscratched,
                'unscratched_count' => count($unscratched),
                'scratched_cards' => $scratched,
                'scratched_count' => count($scratched),
            ],
        ]);
    }

    /**
     * Get user's saved bank & UPI payout details.
     */
    public function getPayoutDetails(Request $request): JsonResponse
    {
        $user = $request->user();

        $detail = UserPayoutDetail::where('user_id', $user->id)
            ->latest()
            ->first();

        return response()->json([
            'status' => true,
            'data' => [
                'payout_details' => $detail ? [
                    'id' => $detail->id,
                    'payout_type' => $detail->payout_type,
                    'account_holder_name' => $detail->account_holder_name,
                    'upi_id' => $detail->upi_id,
                    'bank_name' => $detail->bank_name,
                    'account_number' => $detail->account_number,
                    'ifsc_code' => $detail->ifsc_code,
                ] : null,
            ],
        ]);
    }

    /**
     * Save or update user's bank & UPI payout details.
     */
    public function savePayoutDetails(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'payout_type' => 'required|in:upi,bank',
            'account_holder_name' => 'nullable|string|max:100',
            'upi_id' => 'nullable|required_if:payout_type,upi|string|max:100',
            'bank_name' => 'nullable|required_if:payout_type,bank|string|max:100',
            'account_number' => 'nullable|required_if:payout_type,bank|string|max:50',
            'ifsc_code' => 'nullable|required_if:payout_type,bank|string|max:20',
        ]);

        $detail = UserPayoutDetail::updateOrCreate(
            ['user_id' => $user->id],
            [
                'payout_type' => $validated['payout_type'],
                'account_holder_name' => $validated['account_holder_name'] ?? $user->name,
                'upi_id' => $validated['payout_type'] === 'upi' ? ($validated['upi_id'] ?? null) : null,
                'bank_name' => $validated['payout_type'] === 'bank' ? ($validated['bank_name'] ?? null) : null,
                'account_number' => $validated['payout_type'] === 'bank' ? ($validated['account_number'] ?? null) : null,
                'ifsc_code' => $validated['payout_type'] === 'bank' ? ($validated['ifsc_code'] ?? null) : null,
                'is_default' => true,
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Payout details saved successfully!',
            'data' => [
                'payout_details' => [
                    'id' => $detail->id,
                    'payout_type' => $detail->payout_type,
                    'account_holder_name' => $detail->account_holder_name,
                    'upi_id' => $detail->upi_id,
                    'bank_name' => $detail->bank_name,
                    'account_number' => $detail->account_number,
                    'ifsc_code' => $detail->ifsc_code,
                ],
            ],
        ]);
    }

    /**
     * Submit a Points Redemption / Withdrawal request.
     * Supported: UPI, Bank Transfer, Gift Cards, Other supported redemption options.
     */
    public function withdraw(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'amount' => 'nullable|numeric|min:0.1',
            'points' => 'nullable|integer|min:1',
            'payout_type' => 'required|in:upi,bank,gift_card,other',
            'account_holder_name' => 'nullable|string|max:100',
            'upi_id' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:50',
            'ifsc_code' => 'nullable|string|max:20',
            'gift_card_brand' => 'nullable|string|max:100',
            'gift_card_email' => 'nullable|string|max:150',
            'other_details' => 'nullable|string|max:500',
            'save_details' => 'nullable|boolean',
        ]);

        $availablePoints = (int) ($user->points_balance ?? round($user->wallet_balance * 10));
        $walletBalance = (float) $user->wallet_balance;

        // Resolve requested points & amount
        if (!empty($validated['points'])) {
            $pointsToRedeem = (int) $validated['points'];
            $amount = round($pointsToRedeem / 10.0, 2);
        } elseif (!empty($validated['amount'])) {
            $amount = (float) $validated['amount'];
            $pointsToRedeem = (int) round($amount * 10);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Please specify points or amount to redeem.',
            ], 422);
        }

        // Validate available balance & points
        if ($availablePoints < $pointsToRedeem || $walletBalance < $amount) {
            return response()->json([
                'status' => false,
                'message' => 'Insufficient points balance. You have ' . number_format($availablePoints) . ' Points (₹' . number_format($walletBalance, 2) . ') available.',
            ], 400);
        }

        $payoutType = $validated['payout_type'];
        $accountHolderName = $validated['account_holder_name'] ?? $user->name;
        $upiId = $validated['upi_id'] ?? null;
        $bankName = $validated['bank_name'] ?? null;
        $accountNumber = $validated['account_number'] ?? null;
        $ifscCode = $validated['ifsc_code'] ?? null;
        $giftCardBrand = $validated['gift_card_brand'] ?? 'Amazon Pay';
        $giftCardEmail = $validated['gift_card_email'] ?? $user->email;
        $otherDetails = $validated['other_details'] ?? null;

        // Method-specific validations
        if ($payoutType === 'upi') {
            if (empty($upiId)) {
                $saved = UserPayoutDetail::where('user_id', $user->id)->latest()->first();
                $upiId = $saved ? $saved->upi_id : null;
            }
            if (empty($upiId)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please enter your UPI ID (e.g. name@upi) to redeem.',
                ], 422);
            }
        } elseif ($payoutType === 'bank') {
            if (empty($accountNumber) || empty($ifscCode)) {
                $saved = UserPayoutDetail::where('user_id', $user->id)->latest()->first();
                $accountNumber = $saved ? $saved->account_number : null;
                $ifscCode = $saved ? $saved->ifsc_code : null;
                $bankName = $saved ? $saved->bank_name : $bankName;
            }
            if (empty($accountNumber) || empty($ifscCode)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please enter your Bank Account Number and IFSC code.',
                ], 422);
            }
        } elseif ($payoutType === 'gift_card') {
            if (empty($giftCardEmail)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please enter an Email or Phone number to deliver your Gift Card voucher.',
                ], 422);
            }
        } elseif ($payoutType === 'other') {
            if (empty($otherDetails)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please enter your account / contact details for other redemption option.',
                ], 422);
            }
        }

        // Save payout details if requested for UPI / Bank
        if (!empty($validated['save_details']) && in_array($payoutType, ['upi', 'bank'])) {
            UserPayoutDetail::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'payout_type' => $payoutType,
                    'account_holder_name' => $accountHolderName ?: $user->name,
                    'upi_id' => $payoutType === 'upi' ? $upiId : null,
                    'bank_name' => $payoutType === 'bank' ? $bankName : null,
                    'account_number' => $payoutType === 'bank' ? $accountNumber : null,
                    'ifsc_code' => $payoutType === 'bank' ? $ifscCode : null,
                    'is_default' => true,
                ]
            );
        }

        // Atomic redemption transaction
        $result = DB::transaction(function () use (
            $user,
            $amount,
            $pointsToRedeem,
            $payoutType,
            $accountHolderName,
            $upiId,
            $bankName,
            $accountNumber,
            $ifscCode,
            $giftCardBrand,
            $giftCardEmail,
            $otherDetails
        ) {
            // Deduct from available points and wallet balance
            $user->decrement('points_balance', min($user->points_balance, $pointsToRedeem));
            $user->decrement('wallet_balance', min($user->wallet_balance, $amount));

            $withdrawal = WithdrawalRequest::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'redeemed_points' => $pointsToRedeem,
                'payout_type' => $payoutType,
                'gift_card_brand' => $giftCardBrand,
                'gift_card_email' => $giftCardEmail,
                'other_details' => $otherDetails,
                'account_holder_name' => $accountHolderName ?: $user->name,
                'upi_id' => $payoutType === 'upi' ? $upiId : null,
                'bank_name' => $payoutType === 'bank' ? $bankName : null,
                'account_number' => $payoutType === 'bank' ? $accountNumber : null,
                'ifsc_code' => $payoutType === 'bank' ? $ifscCode : null,
                'status' => 'pending',
            ]);

            $dest = '';
            if ($payoutType === 'upi') {
                $dest = 'UPI: ' . $upiId;
            } elseif ($payoutType === 'bank') {
                $dest = 'Bank A/C: ' . $accountNumber;
            } elseif ($payoutType === 'gift_card') {
                $dest = $giftCardBrand . ' Gift Card (' . $giftCardEmail . ')';
            } else {
                $dest = 'Option: ' . $otherDetails;
            }

            $transaction = WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'debit',
                'amount' => $amount,
                'points' => $pointsToRedeem,
                'title' => 'Redemption: ' . $pointsToRedeem . ' Points (' . strtoupper($payoutType) . ')',
                'description' => 'Redeemed ' . $pointsToRedeem . ' Bounty Points via ' . $dest . ' (Pending Admin Processing)',
                'reference_id' => $withdrawal->id,
                'reference_type' => 'withdrawal_request',
                'status' => 'pending',
            ]);

            return [
                'withdrawal' => $withdrawal,
                'transaction' => $transaction,
            ];
        });

        $freshUser = $user->fresh();

        return response()->json([
            'status' => true,
            'message' => "Redemption request of {$pointsToRedeem} Points (₹" . number_format($amount, 2) . ") submitted successfully! Payout will be processed for verified activity.",
            'data' => [
                'new_points' => (int) $freshUser->points_balance,
                'new_balance' => (float) $freshUser->wallet_balance,
                'withdrawal_request' => [
                    'id' => $result['withdrawal']->id,
                    'amount' => (float) $result['withdrawal']->amount,
                    'redeemed_points' => (int) $result['withdrawal']->redeemed_points,
                    'payout_type' => $result['withdrawal']->payout_type,
                    'payout_summary' => $result['withdrawal']->payout_summary,
                    'status' => $result['withdrawal']->status,
                    'created_at' => $result['withdrawal']->created_at->toIso8601String(),
                ],
                'transaction' => [
                    'id' => $result['transaction']->id,
                    'type' => $result['transaction']->type,
                    'points' => (int) $result['transaction']->points,
                    'amount' => (float) $result['transaction']->amount,
                    'title' => $result['transaction']->title,
                    'status' => $result['transaction']->status,
                    'created_at' => $result['transaction']->created_at->toIso8601String(),
                ],
            ],
        ]);
    }

    /**
     * Get user's withdrawal request history.
     */
    public function withdrawals(Request $request): JsonResponse
    {
        $user = $request->user();

        $perPage = min(50, max(1, (int) $request->input('per_page', 10)));
        $paginator = WithdrawalRequest::where('user_id', $user->id)
            ->latest()
            ->paginate($perPage);

        $items = collect($paginator->items())->map(function ($item) {
            return [
                'id' => $item->id,
                'amount' => (float) $item->amount,
                'redeemed_points' => (int) ($item->redeemed_points ?: round($item->amount * 10)),
                'payout_type' => $item->payout_type,
                'payout_summary' => $item->payout_summary,
                'gift_card_brand' => $item->gift_card_brand,
                'gift_card_email' => $item->gift_card_email,
                'other_details' => $item->other_details,
                'account_holder_name' => $item->account_holder_name,
                'upi_id' => $item->upi_id,
                'bank_name' => $item->bank_name,
                'account_number' => $item->account_number,
                'ifsc_code' => $item->ifsc_code,
                'status' => $item->status,
                'proof_image' => $item->proof_image,
                'utr_number' => $item->utr_number,
                'admin_notes' => $item->admin_notes,
                'processed_at' => $item->processed_at ? $item->processed_at->toIso8601String() : null,
                'created_at' => $item->created_at->toIso8601String(),
            ];
        });

        return response()->json([
            'status' => true,
            'data' => [
                'withdrawals' => $items,
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'has_more' => $paginator->hasMorePages(),
                ],
            ],
        ]);
    }
}
