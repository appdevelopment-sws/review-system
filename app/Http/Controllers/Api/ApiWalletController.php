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
     * Get wallet summary, saved payout details, and recent transactions.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Calculate statistics
        $walletBalance = (float) ($user->wallet_balance ?? 0.00);

        $totalEarned = (float) WalletTransaction::where('user_id', $user->id)
            ->where('type', 'credit')
            ->where('status', 'completed')
            ->sum('amount');

        $pendingRewards = (float) CampaignParticipation::where('user_id', $user->id)
            ->where('status', 'pending')
            ->sum('reward_amount');

        $completedCount = CampaignParticipation::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();

        // Saved Payout Details
        $payoutDetail = UserPayoutDetail::where('user_id', $user->id)
            ->latest()
            ->first();

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

                return [
                    'id' => $tx->id,
                    'type' => $tx->type,
                    'amount' => (float) $tx->amount,
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
                    'total_earned' => $totalEarned,
                    'pending_rewards' => $pendingRewards,
                    'completed_tasks_count' => $completedCount,
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
     * Submit a withdrawal request.
     */
    public function withdraw(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payout_type' => 'nullable|in:upi,bank',
            'account_holder_name' => 'nullable|string|max:100',
            'upi_id' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:50',
            'ifsc_code' => 'nullable|string|max:20',
            'save_details' => 'nullable|boolean',
        ]);

        $amount = (float) $validated['amount'];

        // Check wallet balance
        if ($user->wallet_balance < $amount) {
            return response()->json([
                'status' => false,
                'message' => 'Insufficient wallet balance. You have ₹' . number_format($user->wallet_balance, 2) . ' available.',
            ], 400);
        }

        // Determine payout destination (from request or saved profile)
        $savedDetail = UserPayoutDetail::where('user_id', $user->id)->latest()->first();

        $payoutType = $validated['payout_type'] ?? ($savedDetail ? $savedDetail->payout_type : 'upi');
        $accountHolderName = $validated['account_holder_name'] ?? ($savedDetail ? $savedDetail->account_holder_name : $user->name);
        $upiId = $validated['upi_id'] ?? ($savedDetail ? $savedDetail->upi_id : null);
        $bankName = $validated['bank_name'] ?? ($savedDetail ? $savedDetail->bank_name : null);
        $accountNumber = $validated['account_number'] ?? ($savedDetail ? $savedDetail->account_number : null);
        $ifscCode = $validated['ifsc_code'] ?? ($savedDetail ? $savedDetail->ifsc_code : null);

        // Validation: user must have bank or upi details specified
        if ($payoutType === 'upi' && empty($upiId)) {
            return response()->json([
                'status' => false,
                'message' => 'Please enter your UPI ID (e.g. yourname@upi) to request a withdrawal.',
            ], 422);
        }

        if ($payoutType === 'bank' && (empty($accountNumber) || empty($ifscCode))) {
            return response()->json([
                'status' => false,
                'message' => 'Please enter your Bank Account Number and IFSC code to request a withdrawal.',
            ], 422);
        }

        // Save payout details if requested or if none existed
        if (!empty($validated['save_details']) || !$savedDetail) {
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

        // Atomic withdrawal request creation and wallet deduction
        $result = DB::transaction(function () use ($user, $amount, $payoutType, $accountHolderName, $upiId, $bankName, $accountNumber, $ifscCode) {
            // Deduct wallet balance immediately to prevent double spending
            $user->decrement('wallet_balance', $amount);

            $withdrawal = WithdrawalRequest::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'payout_type' => $payoutType,
                'account_holder_name' => $accountHolderName ?: $user->name,
                'upi_id' => $payoutType === 'upi' ? $upiId : null,
                'bank_name' => $payoutType === 'bank' ? $bankName : null,
                'account_number' => $payoutType === 'bank' ? $accountNumber : null,
                'ifsc_code' => $payoutType === 'bank' ? $ifscCode : null,
                'status' => 'pending',
            ]);

            $dest = $payoutType === 'upi' ? 'UPI: ' . $upiId : 'A/C: ' . $accountNumber;
            $transaction = WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'debit',
                'amount' => $amount,
                'title' => 'Withdrawal to ' . strtoupper($payoutType),
                'description' => 'Transfer to ' . $dest . ' (Pending Admin Approval)',
                'reference_id' => $withdrawal->id,
                'reference_type' => 'withdrawal_request',
                'status' => 'pending',
            ]);

            return [
                'withdrawal' => $withdrawal,
                'transaction' => $transaction,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Withdrawal request of ₹' . number_format($amount, 2) . ' submitted successfully! Admin will process your payout.',
            'data' => [
                'new_balance' => (float) $user->fresh()->wallet_balance,
                'withdrawal_request' => [
                    'id' => $result['withdrawal']->id,
                    'amount' => (float) $result['withdrawal']->amount,
                    'payout_type' => $result['withdrawal']->payout_type,
                    'status' => $result['withdrawal']->status,
                    'created_at' => $result['withdrawal']->created_at->toIso8601String(),
                ],
                'transaction' => [
                    'id' => $result['transaction']->id,
                    'type' => $result['transaction']->type,
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
                'payout_type' => $item->payout_type,
                'payout_summary' => $item->payout_summary,
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
