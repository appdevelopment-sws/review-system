<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignParticipation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApiTaskController extends Controller
{
    /**
     * Get paginated list of all available tasks/campaigns.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Campaign::where('status', 'active');

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

            return [
                'id' => $campaign->id,
                'title' => $campaign->title,
                'description' => $campaign->description,
                'media_type' => $campaign->media_type,
                'media_url' => $campaign->media_url,
                'redirect_url' => $campaign->redirect_url,
                'reward_amount' => (float) $campaign->reward_amount,
                'participant_limit' => $campaign->participant_limit,
                'participants_count' => $campaign->participants_count,
                'status' => $campaign->status,
                'user_submission' => $participation ? [
                    'id' => $participation->id,
                    'status' => $participation->status,
                    'proof_image' => $participation->proof_image,
                    'review_text' => $participation->review_text,
                    'reward_amount' => (float) $participation->reward_amount,
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
        $campaign = Campaign::findOrFail($id);
        $participation = CampaignParticipation::where('campaign_id', $id)
            ->where('user_id', $user->id)
            ->first();

        return response()->json([
            'status' => true,
            'data' => [
                'task' => [
                    'id' => $campaign->id,
                    'title' => $campaign->title,
                    'description' => $campaign->description,
                    'media_type' => $campaign->media_type,
                    'media_url' => $campaign->media_url,
                    'redirect_url' => $campaign->redirect_url,
                    'reward_amount' => (float) $campaign->reward_amount,
                    'participant_limit' => $campaign->participant_limit,
                    'participants_count' => $campaign->participants_count,
                    'status' => $campaign->status,
                    'user_submission' => $participation ? [
                        'id' => $participation->id,
                        'status' => $participation->status,
                        'proof_image' => $participation->proof_image,
                        'review_text' => $participation->review_text,
                        'reward_amount' => (float) $participation->reward_amount,
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
                'message' => 'You have already completed this task and received your reward.',
            ], 400);
        }

        $validated = $request->validate([
            'proof_file' => 'nullable|file|mimes:jpeg,png,jpg,webp,gif|max:10240',
            'proof_image' => 'nullable|string|max:2000',
            'review_text' => 'nullable|string|max:1000',
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

        $participation = CampaignParticipation::updateOrCreate(
            [
                'campaign_id' => $campaign->id,
                'user_id' => $user->id,
            ],
            [
                'proof_image' => $proofUrl,
                'review_text' => $validated['review_text'] ?? null,
                'status' => 'pending',
                'reward_amount' => $campaign->reward_amount,
                'submitted_at' => now(),
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Task proof submitted successfully! Reward will be credited to your wallet once approved by admin.',
            'data' => [
                'submission' => [
                    'id' => $participation->id,
                    'campaign_id' => $participation->campaign_id,
                    'status' => $participation->status,
                    'proof_image' => $participation->proof_image,
                    'review_text' => $participation->review_text,
                    'reward_amount' => (float) $participation->reward_amount,
                    'submitted_at' => $participation->submitted_at->toIso8601String(),
                ],
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
            return [
                'id' => $item->id,
                'campaign_id' => $item->campaign_id,
                'campaign_title' => $item->campaign ? $item->campaign->title : 'Task',
                'media_url' => $item->campaign ? $item->campaign->media_url : null,
                'status' => $item->status,
                'proof_image' => $item->proof_image,
                'review_text' => $item->review_text,
                'reward_amount' => (float) $item->reward_amount,
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
