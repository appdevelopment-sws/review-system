<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignParticipation;
use App\Models\Category;
use App\Models\WalletTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ApiTaskController extends Controller
{
    /**
     * Get list of all categories with active campaigns count.
     */
    public function categories(Request $request): JsonResponse
    {
        $categories = Category::withCount(['campaigns' => function ($q) {
            $q->where('status', 'active');
        }])->get()->map(function ($cat) {
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'image' => $cat->image,
                'default_reward' => (float) ($cat->default_reward ?? 20.00),
                'campaigns_count' => $cat->campaigns_count,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => [
                'categories' => $categories,
            ],
        ]);
    }

    /**
     * Get paginated list of all available tasks/campaigns.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Campaign::with('category')->where('status', 'active');

        // Filter by Category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by Platform / Option
        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }

        // Search by Title
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'latest');
        if ($sortBy === 'reward_high') {
            $query->orderBy('reward_amount', 'desc');
        } elseif ($sortBy === 'reward_low') {
            $query->orderBy('reward_amount', 'asc');
        } else {
            $query->latest();
        }

        $perPage = min(50, max(1, (int) $request->input('per_page', 10)));
        $paginated = $query->paginate($perPage);

        // Get user's existing participations keyed by campaign_id
        $campaignIds = $paginated->pluck('id')->toArray();
        $userParticipations = CampaignParticipation::where('user_id', $user->id)
            ->whereIn('campaign_id', $campaignIds)
            ->get()
            ->keyBy('campaign_id');

        $tasks = collect($paginated->items())->map(function ($campaign) use ($userParticipations) {
            $participation = $userParticipations->get($campaign->id);
            $points = max(10, (int) round(((float) $campaign->reward_amount) * 10));

            return [
                'id' => $campaign->id,
                'title' => $campaign->title,
                'description' => $campaign->description,
                'platform' => $campaign->platform ?? 'General',
                'instructions' => $campaign->instructions,
                'suggested_points' => $campaign->suggested_points,
                'category_id' => $campaign->category_id,
                'category' => $campaign->category ? [
                    'id' => $campaign->category->id,
                    'name' => $campaign->category->name,
                    'image' => $campaign->category->image,
                ] : null,
                'media_type' => $campaign->media_type,
                'media_url' => $campaign->media_url,
                'redirect_url' => $campaign->redirect_url,
                'reward_amount' => (float) $campaign->reward_amount,
                'reward_points' => $points,
                'participant_limit' => $campaign->participant_limit,
                'participants_count' => $campaign->participants_count,
                'status' => $campaign->status,
                'user_submission' => $participation ? [
                    'id' => $participation->id,
                    'status' => $participation->status,
                    'proof_image' => $participation->proof_image,
                    'review_text' => $participation->review_text,
                    'review_link' => $participation->review_link,
                    'reward_amount' => (float) $participation->reward_amount,
                    'reward_points' => (int) $participation->points,
                    'is_scratched' => (bool) $participation->is_scratched,
                    'can_scratch' => (bool) $participation->can_scratch,
                    'scratched_at' => $participation->scratched_at ? $participation->scratched_at->toIso8601String() : null,
                    'admin_notes' => $participation->admin_notes,
                    'submitted_at' => $participation->submitted_at ? $participation->submitted_at->toIso8601String() : null,
                ] : null,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => [
                'tasks' => $tasks,
                'pagination' => [
                    'current_page' => $paginated->currentPage(),
                    'last_page' => $paginated->lastPage(),
                    'per_page' => $paginated->perPage(),
                    'total' => $paginated->total(),
                    'has_more' => $paginated->hasMorePages(),
                ],
            ],
        ]);
    }

    /**
     * Get specific task detail.
     */
    public function show(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $campaign = Campaign::with('category')->findOrFail($id);
        $participation = CampaignParticipation::where('campaign_id', $id)
            ->where('user_id', $user->id)
            ->first();

        $points = max(10, (int) round(((float) $campaign->reward_amount) * 10));

        return response()->json([
            'status' => true,
            'data' => [
                'task' => [
                    'id' => $campaign->id,
                    'title' => $campaign->title,
                    'description' => $campaign->description,
                    'platform' => $campaign->platform ?? 'General',
                    'instructions' => $campaign->instructions,
                    'suggested_points' => $campaign->suggested_points,
                    'category_id' => $campaign->category_id,
                    'category' => $campaign->category ? [
                        'id' => $campaign->category->id,
                        'name' => $campaign->category->name,
                        'image' => $campaign->category->image,
                    ] : null,
                    'media_type' => $campaign->media_type,
                    'media_url' => $campaign->media_url,
                    'redirect_url' => $campaign->redirect_url,
                    'reward_amount' => (float) $campaign->reward_amount,
                    'reward_points' => $points,
                    'participant_limit' => $campaign->participant_limit,
                    'participants_count' => $campaign->participants_count,
                    'status' => $campaign->status,
                    'user_submission' => $participation ? [
                        'id' => $participation->id,
                        'status' => $participation->status,
                        'proof_image' => $participation->proof_image,
                        'review_text' => $participation->review_text,
                        'review_link' => $participation->review_link,
                        'reward_amount' => (float) $participation->reward_amount,
                        'reward_points' => (int) $participation->points,
                        'is_scratched' => (bool) $participation->is_scratched,
                        'can_scratch' => (bool) $participation->can_scratch,
                        'scratched_at' => $participation->scratched_at ? $participation->scratched_at->toIso8601String() : null,
                        'admin_notes' => $participation->admin_notes,
                        'submitted_at' => $participation->submitted_at ? $participation->submitted_at->toIso8601String() : null,
                    ] : null,
                ],
            ],
        ]);
    }

    /**
     * Submit task proof (Screenshot / Review Notes).
     */
    public function submit(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $campaign = Campaign::findOrFail($id);

        // Check if user already submitted
        $existing = CampaignParticipation::where('campaign_id', $id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing && $existing->status === 'approved') {
            return response()->json([
                'status' => false,
                'message' => 'You have already completed this task and unlocked your reward scratch card.',
            ], 400);
        }

        $validated = $request->validate([
            'proof_file' => 'nullable|file|mimes:jpeg,png,jpg,webp,gif|max:10240',
            'proof_image' => 'nullable|string|max:2000',
            'review_text' => 'nullable|string|max:1000',
            'review_link' => 'nullable|string|max:2000',
        ]);

        $proofUrl = $validated['proof_image'] ?? null;

        if ($request->hasFile('proof_file')) {
            $path = $request->file('proof_file')->store('proofs', 'public');
            $proofUrl = asset('storage/' . $path);
        }

        if (empty($proofUrl)) {
            return response()->json([
                'status' => false,
                'message' => 'Please upload or provide a screenshot proof.',
            ], 422);
        }

        $points = max(10, (int) round(((float) $campaign->reward_amount) * 10));

        $participation = CampaignParticipation::updateOrCreate(
            [
                'campaign_id' => $campaign->id,
                'user_id' => $user->id,
            ],
            [
                'proof_image' => $proofUrl,
                'review_text' => $validated['review_text'] ?? null,
                'review_link' => $validated['review_link'] ?? null,
                'status' => 'pending',
                'reward_amount' => $campaign->reward_amount,
                'reward_points' => $points,
                'is_scratched' => false,
                'submitted_at' => now(),
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Task proof submitted successfully! Once verified & approved by admin, you will receive your Scratch Card to reveal ' . $points . ' Bounty Points.',
            'data' => [
                'submission' => [
                    'id' => $participation->id,
                    'campaign_id' => $participation->campaign_id,
                    'status' => $participation->status,
                    'proof_image' => $participation->proof_image,
                    'review_text' => $participation->review_text,
                    'review_link' => $participation->review_link,
                    'reward_amount' => (float) $participation->reward_amount,
                    'reward_points' => $points,
                    'is_scratched' => false,
                    'can_scratch' => false,
                    'submitted_at' => $participation->submitted_at->toIso8601String(),
                ],
            ],
        ]);
    }

    /**
     * Scratch Card Flow: Task Approved -> Scratch Card -> Scratch -> Points Revealed -> Wallet Credit.
     */
    public function scratchCard(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        // Find participation for user
        $participation = CampaignParticipation::with('campaign')
            ->where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$participation) {
            return response()->json([
                'status' => false,
                'message' => 'Reward submission not found.',
            ], 404);
        }

        if ($participation->status !== 'approved') {
            return response()->json([
                'status' => false,
                'message' => 'Reward Scratch Card is only available after task activity is verified & approved.',
            ], 400);
        }

        $points = (int) $participation->points;
        $rewardAmount = (float) $participation->reward_amount;

        // If already scratched, return revealed details without double-crediting
        if ($participation->is_scratched) {
            return response()->json([
                'status' => true,
                'already_scratched' => true,
                'message' => "Scratch card already revealed! {$points} Points were credited to your wallet.",
                'data' => [
                    'participation_id' => $participation->id,
                    'campaign_title' => $participation->campaign ? $participation->campaign->title : 'Task',
                    'points_revealed' => $points,
                    'reward_amount' => $rewardAmount,
                    'is_scratched' => true,
                    'scratched_at' => $participation->scratched_at ? $participation->scratched_at->toIso8601String() : null,
                    'current_points' => (int) $user->points_balance,
                    'current_balance' => (float) $user->wallet_balance,
                ],
            ]);
        }

        // Scratch Card Reveal & Wallet Credit Atomic Transaction
        $result = DB::transaction(function () use ($user, $participation, $points, $rewardAmount) {
            // Update participation state to scratched
            $participation->update([
                'is_scratched' => true,
                'scratched_at' => now(),
                'reward_points' => $points,
            ]);

            // Credit Points to User Wallet
            $user->increment('points_balance', $points);

            // Synchronize wallet balance currency
            if ($rewardAmount > 0) {
                $user->increment('wallet_balance', $rewardAmount);
            }

            // Create completed Wallet Transaction Record
            $taskTitle = $participation->campaign ? $participation->campaign->title : 'Approved Task';
            $transaction = WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'credit',
                'amount' => $rewardAmount,
                'points' => $points,
                'title' => 'Scratch Card Reward: ' . $points . ' Points',
                'description' => 'Scratched & credited ' . $points . ' Bounty Points for ' . $taskTitle . ' (Submission #' . $participation->id . ')',
                'reference_id' => $participation->id,
                'reference_type' => 'scratch_card',
                'status' => 'completed',
            ]);

            return [
                'participation' => $participation,
                'transaction' => $transaction,
            ];
        });

        $freshUser = $user->fresh();

        return response()->json([
            'status' => true,
            'message' => "🎉 Congratulations! {$points} Bounty Points revealed & credited to your wallet!",
            'data' => [
                'participation_id' => $participation->id,
                'campaign_title' => $participation->campaign ? $participation->campaign->title : 'Task',
                'points_revealed' => $points,
                'reward_amount' => $rewardAmount,
                'is_scratched' => true,
                'scratched_at' => $participation->scratched_at->toIso8601String(),
                'new_points' => (int) $freshUser->points_balance,
                'new_balance' => (float) $freshUser->wallet_balance,
                'transaction_id' => $result['transaction']->id,
            ],
        ]);
    }

    /**
     * Get paginated list of user's task submissions.
     */
    public function myTasks(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = CampaignParticipation::with('campaign')
            ->where('user_id', $user->id);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $perPage = min(50, max(1, (int) $request->input('per_page', 10)));
        $paginated = $query->latest('submitted_at')->paginate($perPage);

        $submissions = collect($paginated->items())->map(function ($item) {
            $points = (int) $item->points;

            return [
                'id' => $item->id,
                'campaign_id' => $item->campaign_id,
                'campaign_title' => $item->campaign ? $item->campaign->title : 'Task',
                'media_url' => $item->campaign ? $item->campaign->media_url : null,
                'status' => $item->status,
                'proof_image' => $item->proof_image,
                'review_text' => $item->review_text,
                'reward_amount' => (float) $item->reward_amount,
                'reward_points' => $points,
                'is_scratched' => (bool) $item->is_scratched,
                'can_scratch' => (bool) $item->can_scratch,
                'scratched_at' => $item->scratched_at ? $item->scratched_at->toIso8601String() : null,
                'admin_notes' => $item->admin_notes,
                'submitted_at' => $item->submitted_at ? $item->submitted_at->toIso8601String() : $item->created_at->toIso8601String(),
            ];
        });

        return response()->json([
            'status' => true,
            'data' => [
                'submissions' => $submissions,
                'pagination' => [
                    'current_page' => $paginated->currentPage(),
                    'last_page' => $paginated->lastPage(),
                    'per_page' => $paginated->perPage(),
                    'total' => $paginated->total(),
                    'has_more' => $paginated->hasMorePages(),
                ],
            ],
        ]);
    }
}
