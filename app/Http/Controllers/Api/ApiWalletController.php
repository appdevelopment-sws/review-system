<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CampaignParticipation;
use App\Models\WalletTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiWalletController extends Controller
{
    /**
     * Get wallet summary and recent transactions.
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

        $perPage = min(50, max(1, (int) $request->input('per_page', 10)));
        $transactionsPaginator = WalletTransaction::where('user_id', $user->id)
            ->latest()
            ->paginate($perPage);

        $transactions = collect($transactionsPaginator->items())
            ->map(function ($tx) {
                return [
                    'id' => $tx->id,
                    'type' => $tx->type,
                    'amount' => (float) $tx->amount,
                    'title' => $tx->title,
                    'description' => $tx->description,
                    'status' => $tx->status,
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
     * Submit a withdrawal request.
     */
    public function withdraw(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string|max:50', // e.g. UPI, Bank Transfer, Paytm
            'account_details' => 'required|string|max:255', // e.g. user@upi
        ]);

        $amount = (float) $validated['amount'];

        if ($user->wallet_balance < $amount) {
            return response()->json([
                'status' => false,
                'message' => 'Insufficient wallet balance. You have ₹' . number_format($user->wallet_balance, 2) . ' available.',
            ], 400);
        }

        $transaction = DB::transaction(function () use ($user, $amount, $validated) {
            $user->decrement('wallet_balance', $amount);

            return WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'debit',
                'amount' => $amount,
                'title' => 'Withdrawal to ' . $validated['payment_method'],
                'description' => 'Account: ' . $validated['account_details'],
                'reference_type' => 'withdrawal',
                'status' => 'completed',
            ]);
        });

        return response()->json([
            'status' => true,
            'message' => 'Withdrawal of ₹' . number_format($amount, 2) . ' processed successfully!',
            'data' => [
                'new_balance' => (float) $user->wallet_balance,
                'transaction' => [
                    'id' => $transaction->id,
                    'type' => $transaction->type,
                    'amount' => (float) $transaction->amount,
                    'title' => $transaction->title,
                    'status' => $transaction->status,
                    'created_at' => $transaction->created_at->toIso8601String(),
                ],
            ],
        ]);
    }
}
