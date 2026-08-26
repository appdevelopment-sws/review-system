<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignParticipation;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminAuthController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function showLoginForm()
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Handle admin login submission.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if ($user->isAdmin()) {
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Welcome back, Administrator!');
            }

            // Not an admin user
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Access denied. You do not have administrator privileges.',
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Display the main admin dashboard.
     */
    public function dashboard()
    {
        $totalUsers = User::count();
        $adminCount = User::where('role', 'admin')->count();
        $userCount = User::where('role', 'user')->count();
        
        $totalCampaigns = Campaign::count();
        $activeCampaigns = Campaign::where('status', 'active')->count();
        $totalRewardPool = Campaign::sum(\DB::raw('reward_amount * participant_limit'));
        $totalParticipants = Campaign::sum('participants_count');

        $recentUsers = User::latest()->take(10)->get();
        $recentCampaigns = Campaign::latest()->take(10)->get();
        $pendingConversionsCount = CampaignParticipation::where('status', 'pending')->count();
        $recentSubmissions = CampaignParticipation::with(['user', 'campaign'])->latest()->take(10)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'adminCount',
            'userCount',
            'totalCampaigns',
            'activeCampaigns',
            'totalRewardPool',
            'totalParticipants',
            'recentUsers',
            'recentCampaigns',
            'pendingConversionsCount',
            'recentSubmissions'
        ));
    }

    /**
     * Display the Users list page.
     */
    public function users(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString();
        $totalUsers = User::count();
        $adminCount = User::where('role', 'admin')->count();
        $userCount = User::where('role', 'user')->count();

        return view('admin.users.index', compact('users', 'totalUsers', 'adminCount', 'userCount'));
    }

    /**
     * Display the Campaigns management page.
     */
    public function campaigns(Request $request)
    {
        $query = Campaign::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        $campaigns = $query->latest()->paginate(9)->withQueryString();
        
        $totalCampaigns = Campaign::count();
        $activeCount = Campaign::where('status', 'active')->count();
        $pausedCount = Campaign::where('status', 'paused')->count();
        $completedCount = Campaign::where('status', 'completed')->count();

        return view('admin.campaigns.index', compact(
            'campaigns',
            'totalCampaigns',
            'activeCount',
            'pausedCount',
            'completedCount'
        ));
    }

    /**
     * Store a newly created Campaign.
     */
    public function storeCampaign(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'media_type' => 'required|in:image,video,none',
            'media_file' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm|max:51200',
            'redirect_url' => 'nullable|url|max:2000',
            'reward_amount' => 'required|numeric|min:0',
            'participant_limit' => 'required|integer|min:1',
            'status' => 'required|in:active,paused,completed,draft',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($request->hasFile('media_file')) {
            $path = $request->file('media_file')->store('campaigns', 'public');
            $validated['media_url'] = asset('storage/' . $path);
        }

        Campaign::create($validated);

        return redirect()->route('admin.campaigns')->with('success', 'Campaign created successfully!');
    }

    /**
     * Update an existing Campaign.
     */
    public function updateCampaign(Request $request, $id)
    {
        $campaign = Campaign::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'media_type' => 'required|in:image,video,none',
            'media_file' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm|max:51200',
            'redirect_url' => 'nullable|url|max:2000',
            'reward_amount' => 'required|numeric|min:0',
            'participant_limit' => 'required|integer|min:1',
            'status' => 'required|in:active,paused,completed,draft',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($request->hasFile('media_file')) {
            $path = $request->file('media_file')->store('campaigns', 'public');
            $validated['media_url'] = asset('storage/' . $path);
        }

        $campaign->update($validated);

        return redirect()->route('admin.campaigns')->with('success', 'Campaign updated successfully!');
    }

    /**
     * Delete a Campaign.
     */
    public function destroyCampaign($id)
    {
        $campaign = Campaign::findOrFail($id);
        $campaign->delete();

        return redirect()->route('admin.campaigns')->with('success', 'Campaign deleted successfully!');
    }

    /**
     * Display Recent Conversions & Submissions (Shared SS Proofs).
     */
    public function conversions(Request $request)
    {
        $query = CampaignParticipation::with(['campaign', 'user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('campaign', function($campQuery) use ($search) {
                    $campQuery->where('title', 'like', "%{$search}%");
                });
            });
        }

        $participations = $query->latest()->paginate(10)->withQueryString();

        $totalConversions = CampaignParticipation::count();
        $pendingCount = CampaignParticipation::where('status', 'pending')->count();
        $approvedCount = CampaignParticipation::where('status', 'approved')->count();
        $rejectedCount = CampaignParticipation::where('status', 'rejected')->count();

        return view('admin.conversions.index', compact(
            'participations',
            'totalConversions',
            'pendingCount',
            'approvedCount',
            'rejectedCount'
        ));
    }

    /**
     * Update Submission Status (Approve / Reject review screenshot proof).
     */
    public function updateConversionStatus(Request $request, $id)
    {
        $participation = CampaignParticipation::with(['campaign', 'user'])->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $oldStatus = $participation->status;
        $newStatus = $validated['status'];

        DB::transaction(function () use ($participation, $oldStatus, $newStatus, $validated) {
            $participation->update([
                'status' => $newStatus,
                'admin_notes' => $validated['admin_notes'] ?? null,
            ]);

            $campaign = $participation->campaign;
            $user = $participation->user;
            $rewardAmount = (float) $participation->reward_amount;

            // Adjust campaign participants count and user wallet when approved
            if ($oldStatus !== 'approved' && $newStatus === 'approved') {
                if ($campaign) {
                    $campaign->increment('participants_count');
                }
                if ($user && $rewardAmount > 0) {
                    $user->increment('wallet_balance', $rewardAmount);

                    // Create Wallet Transaction Record
                    WalletTransaction::create([
                        'user_id' => $user->id,
                        'type' => 'credit',
                        'amount' => $rewardAmount,
                        'title' => 'Task Reward: ' . ($campaign->title ?? 'Task Completed'),
                        'description' => 'Approved task reward for submission #' . $participation->id,
                        'reference_id' => $participation->id,
                        'reference_type' => 'campaign_participation',
                        'status' => 'completed',
                    ]);
                }
            } elseif ($oldStatus === 'approved' && $newStatus !== 'approved') {
                if ($campaign) {
                    $campaign->decrement('participants_count');
                }
                if ($user && $rewardAmount > 0) {
                    $user->decrement('wallet_balance', min($user->wallet_balance, $rewardAmount));

                    // Create Reversal Transaction Record
                    WalletTransaction::create([
                        'user_id' => $user->id,
                        'type' => 'debit',
                        'amount' => $rewardAmount,
                        'title' => 'Reversal: ' . ($campaign->title ?? 'Task Reverted'),
                        'description' => 'Submission #' . $participation->id . ' status changed to ' . $newStatus,
                        'reference_id' => $participation->id,
                        'reference_type' => 'campaign_participation',
                        'status' => 'completed',
                    ]);
                }
            }
        });

        return back()->with('success', 'Submission status updated to ' . ucfirst($newStatus) . ' successfully! Wallet balance updated.');
    }

    /**
     * Display detailed Report & Analytics for a specific Campaign.
     */
    public function campaignReport($id)
    {
        $campaign = Campaign::with(['participations.user'])->findOrFail($id);
        
        $totalClicks = $campaign->clicks_count;
        $totalSubmissions = $campaign->participations->count();
        $approvedSubmissions = $campaign->participations->where('status', 'approved')->count();
        $pendingSubmissions = $campaign->participations->where('status', 'pending')->count();
        $conversionRate = $campaign->conversionRate();
        $totalPaidOut = $approvedSubmissions * $campaign->reward_amount;

        return view('admin.campaigns.report', compact(
            'campaign',
            'totalClicks',
            'totalSubmissions',
            'approvedSubmissions',
            'pendingSubmissions',
            'conversionRate',
            'totalPaidOut'
        ));
    }

    /**
     * Track Click & Redirect User.
     */
    public function trackClick($id)
    {
        $campaign = Campaign::findOrFail($id);
        $campaign->increment('clicks_count');

        $redirectUrl = $campaign->redirect_url ?: route('admin.campaigns');
        return redirect()->away($redirectUrl);
    }

    /**
     * Handle admin logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'You have been logged out successfully.');
    }
}
