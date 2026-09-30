<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApiBusinessCampaignController extends Controller
{
    /**
     * Get all campaigns created by the authenticated business.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Campaign::with('category')->where('user_id', $user->id);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        $campaigns = $query->latest()->get()->map(function ($c) {
            return [
                'id' => $c->id,
                'title' => $c->title,
                'description' => $c->description,
                'category_id' => $c->category_id,
                'category' => $c->category ? [
                    'id' => $c->category->id,
                    'name' => $c->category->name,
                ] : null,
                'platform' => $c->platform ?? 'General',
                'instructions' => $c->instructions,
                'media_type' => $c->media_type,
                'media_url' => $c->media_url,
                'redirect_url' => $c->redirect_url,
                'reward_amount' => (float) $c->reward_amount,
                'participant_limit' => (int) $c->participant_limit,
                'participants_count' => (int) $c->participants_count,
                'clicks_count' => (int) $c->clicks_count,
                'status' => $c->status,
                'payment_info' => $c->payment_info,
                'progress_percentage' => $c->progressPercentage(),
                'created_at' => $c->created_at ? $c->created_at->toIso8601String() : null,
            ];
        });

        $counts = [
            'total' => Campaign::where('user_id', $user->id)->count(),
            'pending' => Campaign::where('user_id', $user->id)->where('status', 'pending')->count(),
            'active' => Campaign::where('user_id', $user->id)->where('status', 'active')->count(),
            'completed' => Campaign::where('user_id', $user->id)->where('status', 'completed')->count(),
            'rejected' => Campaign::where('user_id', $user->id)->where('status', 'rejected')->count(),
            'total_participants' => (int) Campaign::where('user_id', $user->id)->sum('participants_count'),
        ];

        return response()->json([
            'status' => true,
            'data' => [
                'campaigns' => $campaigns,
                'counts' => $counts,
            ],
        ]);
    }

    /**
     * Store and submit a new campaign by business.
     * Status is always set to 'pending' for Bounty Bits manual review!
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'platform' => 'required|string|max:100',
            'description' => 'required|string',
            'instructions' => 'nullable|string',
            'suggested_points' => 'nullable|string|max:1000',
            'participant_limit' => 'required|integer|min:1',
            'reward_amount' => 'nullable|numeric|min:0.5',
            'redirect_url' => 'nullable|url|max:2000',
            'media_file' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif|max:20480',
            'media_url' => 'nullable|string|max:2000',
            'payment_info' => 'nullable|string|max:1000',
        ]);

        $mediaUrl = $validated['media_url'] ?? null;
        $mediaType = 'none';

        if ($request->hasFile('media_file')) {
            $path = $request->file('media_file')->store('campaigns', 'public');
            $mediaUrl = asset('storage/' . $path);
            $mediaType = 'image';
        } elseif (!empty($mediaUrl)) {
            $mediaType = 'image';
        }

        $campaign = Campaign::create([
            'user_id' => $user->id,
            'title' => $validated['title'],
            'category_id' => $validated['category_id'] ?? null,
            'platform' => $validated['platform'],
            'description' => $validated['description'],
            'instructions' => $validated['instructions'] ?? null,
            'suggested_points' => $validated['suggested_points'] ?? null,
            'media_type' => $mediaType,
            'media_url' => $mediaUrl,
            'redirect_url' => $validated['redirect_url'] ?? null,
            'reward_amount' => $validated['reward_amount'] ?? 20.0,
            'participant_limit' => $validated['participant_limit'],
            'participants_count' => 0,
            'clicks_count' => 0,
            'impressions_count' => 0,
            'status' => 'pending', // Awaiting Bounty Bits Admin review
            'payment_info' => $validated['payment_info'] ?? null,
            'start_date' => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Campaign submitted successfully! It is now pending approval by the Bounty Bits admin team before going live.',
            'data' => [
                'campaign' => [
                    'id' => $campaign->id,
                    'title' => $campaign->title,
                    'status' => $campaign->status,
                    'participant_limit' => $campaign->participant_limit,
                    'reward_amount' => (float) $campaign->reward_amount,
                    'created_at' => $campaign->created_at->toIso8601String(),
                ],
            ],
        ], 201);
    }

    /**
     * Show single business campaign details with live submissions.
     */
    public function show(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        $campaign = Campaign::with(['category', 'participations'])
            ->where('user_id', $user->id)
            ->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => [
                'campaign' => [
                    'id' => $campaign->id,
                    'title' => $campaign->title,
                    'description' => $campaign->description,
                    'category' => $campaign->category ? [
                        'id' => $campaign->category->id,
                        'name' => $campaign->category->name,
                    ] : null,
                    'platform' => $campaign->platform ?? 'General',
                    'instructions' => $campaign->instructions,
                    'media_type' => $campaign->media_type,
                    'media_url' => $campaign->media_url,
                    'redirect_url' => $campaign->redirect_url,
                    'reward_amount' => (float) $campaign->reward_amount,
                    'participant_limit' => (int) $campaign->participant_limit,
                    'participants_count' => (int) $campaign->participants_count,
                    'clicks_count' => (int) $campaign->clicks_count,
                    'status' => $campaign->status,
                    'payment_info' => $campaign->payment_info,
                    'progress_percentage' => $campaign->progressPercentage(),
                    'created_at' => $campaign->created_at ? $campaign->created_at->toIso8601String() : null,
                    'participations' => $campaign->participations->map(function ($p) {
                        return [
                            'id' => $p->id,
                            'participant_alias' => 'Reviewer #' . $p->id,
                            'user_name' => 'Reviewer #' . $p->id,
                            'status' => $p->status,
                            'proof_image' => $p->proof_image,
                            'review_text' => $p->review_text,
                            'review_link' => $p->review_link,
                            'reward_amount' => (float) $p->reward_amount,
                            'submitted_at' => $p->submitted_at ? $p->submitted_at->toIso8601String() : null,
                        ];
                    }),
                ],
            ],
        ]);
    }
}
