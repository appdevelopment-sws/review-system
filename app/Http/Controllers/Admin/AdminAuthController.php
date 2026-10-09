<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignParticipation;
use App\Models\Category;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use App\Models\MarketingGoal;
use App\Models\BountyAiProfile;
use App\Models\BountyAiCategory;
use Illuminate\Support\Str;
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

        $pendingWithdrawalsCount = WithdrawalRequest::where('status', 'pending')->count();
        $totalWithdrawalPaid = WithdrawalRequest::where('status', 'approved')->sum('amount');
        $recentWithdrawals = WithdrawalRequest::with('user')->latest()->take(8)->get();

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
            'recentSubmissions',
            'pendingWithdrawalsCount',
            'totalWithdrawalPaid',
            'recentWithdrawals'
        ));
    }

    /**
     * Display the Users list page.
     */
    public function users(Request $request)
    {
        $query = User::withCount([
            'participations as total_participations_count',
            'participations as completed_participations_count' => function ($q) {
                $q->where('status', 'approved');
            },
            'participations as pending_participations_count' => function ($q) {
                $q->where('status', 'pending');
            },
            'campaigns as created_campaigns_count',
        ]);

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
        $userCount = User::where('role', 'user')->count();
        $businessCount = User::where('role', 'business')->count();
        $adminCount = User::where('role', 'admin')->count();

        return view('admin.users.index', compact('users', 'totalUsers', 'userCount', 'businessCount', 'adminCount'));
    }

    /**
     * Display detailed profile and completed campaigns/tasks for a specific User.
     */
    public function showUser(Request $request, $id)
    {
        $user = User::with(['payoutDetails', 'defaultPayoutDetail'])->findOrFail($id);

        // Participations / Task Submissions Query
        $participationsQuery = CampaignParticipation::with('campaign')
            ->where('user_id', $user->id);

        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $participationsQuery->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $participationsQuery->whereHas('campaign', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        $participations = $participationsQuery->latest()->paginate(10)->withQueryString();

        // Metrics for this user
        $totalSubmissions = CampaignParticipation::where('user_id', $user->id)->count();
        $completedCampaignsCount = CampaignParticipation::where('user_id', $user->id)->where('status', 'approved')->count();
        $pendingSubmissionsCount = CampaignParticipation::where('user_id', $user->id)->where('status', 'pending')->count();
        $rejectedSubmissionsCount = CampaignParticipation::where('user_id', $user->id)->where('status', 'rejected')->count();

        $totalEarned = (float) CampaignParticipation::where('user_id', $user->id)
            ->where('status', 'approved')
            ->sum('reward_amount');

        $totalWithdrawn = (float) WithdrawalRequest::where('user_id', $user->id)
            ->where('status', 'approved')
            ->sum('amount');

        $pendingWithdrawalAmount = (float) WithdrawalRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->sum('amount');

        // Recent Wallet Transactions & Withdrawal Requests
        $recentTransactions = WalletTransaction::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        $recentWithdrawals = WithdrawalRequest::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        return view('admin.users.show', compact(
            'user',
            'participations',
            'totalSubmissions',
            'completedCampaignsCount',
            'pendingSubmissionsCount',
            'rejectedSubmissionsCount',
            'totalEarned',
            'totalWithdrawn',
            'pendingWithdrawalAmount',
            'recentTransactions',
            'recentWithdrawals'
        ));
    }

    /**
     * Delete a user or business account permanently.
     */
    public function destroyUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Security check: Admin cannot delete their own account
        if (Auth::id() == $user->id) {
            return redirect()->back()->with('error', 'You cannot delete your own logged-in admin account.');
        }

        // Security check: Prevent deleting the only remaining admin
        if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return redirect()->back()->with('error', 'Cannot delete the only remaining administrator account.');
        }

        $userName = $user->name;
        $userEmail = $user->email;
        $roleLabel = match ($user->role) {
            'business' => 'Business account',
            'admin' => 'Admin account',
            default => 'Earner user account',
        };

        DB::transaction(function () use ($user) {
            // If the account has created campaigns (e.g. Business), delete campaigns & their participations
            if ($user->campaigns()->exists()) {
                foreach ($user->campaigns as $campaign) {
                    $campaign->delete();
                }
            }

            // Remove participations/submissions made by this user
            $user->participations()->delete();

            // Remove wallet transactions
            $user->walletTransactions()->delete();

            // Remove withdrawal requests
            $user->withdrawalRequests()->delete();

            // Remove payout bank/upi details
            $user->payoutDetails()->delete();

            // Revoke Sanctum API tokens
            if (method_exists($user, 'tokens')) {
                $user->tokens()->delete();
            }

            // Invalidate user web sessions
            try {
                DB::table('sessions')->where('user_id', $user->id)->delete();
            } catch (\Throwable $e) {}

            // Delete password reset tokens
            try {
                DB::table('password_reset_tokens')->where('email', $user->email)->delete();
            } catch (\Throwable $e) {}

            // Delete associated Bounty AI business profile(s) for this user/business
            \App\Models\BountyAiProfile::where('user_id', $user->id)
                ->orWhere(function ($q) use ($user) {
                    if (!empty($user->email)) {
                        $q->where('email', $user->email);
                    }
                })
                ->delete();

            // Delete the user record
            $user->delete();
        });

        return redirect()->route('admin.users')->with('success', "{$roleLabel} \"{$userName}\" ({$userEmail}) has been deleted successfully.");
    }

    /**
     * Display the Categories management page.
     */
    public function categories(Request $request)
    {
        $query = Category::withCount('campaigns');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $categories = $query->latest()->paginate(12)->withQueryString();
        $totalCategories = Category::count();
        $totalCampaignsCategorized = Campaign::whereNotNull('category_id')->count();

        return view('admin.categories.index', compact(
            'categories',
            'totalCategories',
            'totalCampaignsCategorized'
        ));
    }

    /**
     * Store a newly created Category.
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'default_reward' => 'required|numeric|min:0.5',
            'image_file' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,svg|max:10240',
            'image_url' => 'nullable|string|max:2000',
        ]);

        $imageUrl = $validated['image_url'] ?? null;

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('categories', 'public');
            $imageUrl = asset('storage/' . $path);
        }

        Category::create([
            'name' => $validated['name'],
            'image' => $imageUrl,
        ]);

        return redirect()->route('admin.categories')->with('success', 'Category created successfully!');
    }

    /**
     * Update an existing Category.
     */
    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'image_file' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,svg|max:10240',
            'image_url' => 'nullable|string|max:2000',
        ]);

        $updateData = [
            'name' => $validated['name'],
        ];

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('categories', 'public');
            $updateData['image'] = asset('storage/' . $path);
        } elseif ($request->filled('image_url')) {
            $updateData['image'] = $validated['image_url'];
        }

        $category->update($updateData);

        return redirect()->route('admin.categories')->with('success', 'Category updated successfully!');
    }

    /**
     * Delete a Category.
     */
    public function destroyCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.categories')->with('success', 'Category deleted successfully!');
    }

    /**
     * Display the Campaigns management page.
     */
    public function campaigns(Request $request)
    {
        $query = Campaign::with('category');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        $campaigns = $query->latest()->paginate(9)->withQueryString();
        
        $totalCampaigns = Campaign::count();
        $activeCount = Campaign::where('status', 'active')->count();
        $pendingCount = Campaign::where('status', 'pending')->count();
        $pausedCount = Campaign::where('status', 'paused')->count();
        $completedCount = Campaign::where('status', 'completed')->count();
        $categories = Category::orderBy('name')->get();

        return view('admin.campaigns.index', compact(
            'campaigns',
            'totalCampaigns',
            'activeCount',
            'pendingCount',
            'pausedCount',
            'completedCount',
            'categories'
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
            'category_id' => 'nullable|exists:categories,id',
            'media_type' => 'required|in:image,video,none',
            'media_file' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm|max:51200',
            'redirect_url' => 'nullable|url|max:2000',
            'reward_amount' => 'required|numeric|min:0',
            'participant_limit' => 'required|integer|min:1',
            'status' => 'required|in:active,paused,completed,draft,pending,rejected',
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
            'category_id' => 'nullable|exists:categories,id',
            'media_type' => 'required|in:image,video,none',
            'media_file' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm|max:51200',
            'redirect_url' => 'nullable|url|max:2000',
            'reward_amount' => 'required|numeric|min:0',
            'participant_limit' => 'required|integer|min:1',
            'status' => 'required|in:active,paused,completed,draft,pending,rejected',
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
     * Approve a submitted business campaign and make it live for users.
     */
    public function approveCampaign($id)
    {
        $campaign = Campaign::findOrFail($id);
        $campaign->update([
            'status' => 'active',
            'start_date' => $campaign->start_date ?? now(),
        ]);

        return redirect()->back()->with('success', "Campaign \"{$campaign->title}\" approved and published LIVE for all users!");
    }

    /**
     * Reject a submitted business campaign.
     */
    public function rejectCampaign(Request $request, $id)
    {
        $campaign = Campaign::findOrFail($id);
        $campaign->update([
            'status' => 'rejected',
        ]);

        return redirect()->back()->with('success', "Campaign \"{$campaign->title}\" marked as rejected.");
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

            // Adjust campaign participants count and scratch card unlocking when approved
            if ($oldStatus !== 'approved' && $newStatus === 'approved') {
                if ($campaign) {
                    $campaign->increment('participants_count');
                }
                $points = $participation->reward_points > 0 ? (int) $participation->reward_points : max(10, (int) round($rewardAmount * 10));
                $participation->update([
                    'reward_points' => $points,
                    'is_scratched' => false, // Task Approved -> Scratch Card -> Scratch -> Points Revealed -> Wallet Credit
                ]);
            } elseif ($oldStatus === 'approved' && $newStatus !== 'approved') {
                if ($campaign) {
                    $campaign->decrement('participants_count');
                }
                if ($participation->is_scratched) {
                    $points = (int) $participation->points;
                    if ($user && $rewardAmount > 0) {
                        $user->decrement('wallet_balance', min($user->wallet_balance, $rewardAmount));
                        $user->decrement('points_balance', min($user->points_balance, $points));

                        // Create Reversal Transaction Record
                        WalletTransaction::create([
                            'user_id' => $user->id,
                            'type' => 'debit',
                            'amount' => $rewardAmount,
                            'points' => $points,
                            'title' => 'Reversal: ' . ($campaign->title ?? 'Task Reverted'),
                            'description' => 'Submission #' . $participation->id . ' status changed to ' . $newStatus,
                            'reference_id' => $participation->id,
                            'reference_type' => 'campaign_participation',
                            'status' => 'completed',
                        ]);
                    }
                    $participation->update(['is_scratched' => false]);
                }
            }
        });

        return back()->with('success', 'Submission status updated to ' . ucfirst($newStatus) . ' successfully! ' . ($newStatus === 'approved' ? 'Scratch card unlocked for user to scratch & claim points.' : ''));
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
     * Display Withdrawal Requests management page.
     */
    public function withdrawals(Request $request)
    {
        $query = WithdrawalRequest::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payout_type')) {
            $query->where('payout_type', $request->payout_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('upi_id', 'like', "%{$search}%")
                  ->orWhere('account_number', 'like', "%{$search}%")
                  ->orWhere('account_holder_name', 'like', "%{$search}%")
                  ->orWhere('utr_number', 'like', "%{$search}%");
            });
        }

        $withdrawals = $query->latest()->paginate(10)->withQueryString();

        $totalWithdrawals = WithdrawalRequest::count();
        $pendingCount = WithdrawalRequest::where('status', 'pending')->count();
        $approvedCount = WithdrawalRequest::where('status', 'approved')->count();
        $rejectedCount = WithdrawalRequest::where('status', 'rejected')->count();

        $totalPendingAmount = (float) WithdrawalRequest::where('status', 'pending')->sum('amount');
        $totalPaidAmount = (float) WithdrawalRequest::where('status', 'approved')->sum('amount');

        return view('admin.withdrawals.index', compact(
            'withdrawals',
            'totalWithdrawals',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'totalPendingAmount',
            'totalPaidAmount'
        ));
    }

    /**
     * Update Withdrawal Request Status (Approve/Pay with screenshot proof, or Reject & Refund).
     */
    public function updateWithdrawalStatus(Request $request, $id)
    {
        $withdrawal = WithdrawalRequest::with(['user', 'walletTransaction'])->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'proof_image' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:10240',
            'utr_number' => 'nullable|string|max:100',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $oldStatus = $withdrawal->status;
        $newStatus = $validated['status'];

        if ($oldStatus !== 'pending') {
            return back()->with('error', 'This withdrawal request has already been processed as ' . ucfirst($oldStatus) . '.');
        }

        DB::transaction(function () use ($withdrawal, $newStatus, $validated, $request) {
            $updateData = [
                'status' => $newStatus,
                'admin_notes' => $validated['admin_notes'] ?? null,
                'processed_at' => now(),
            ];

            if ($newStatus === 'approved') {
                if ($request->hasFile('proof_image')) {
                    $path = $request->file('proof_image')->store('withdrawals', 'public');
                    $updateData['proof_image'] = asset('storage/' . $path);
                }
                if (!empty($validated['utr_number'])) {
                    $updateData['utr_number'] = $validated['utr_number'];
                }

                $withdrawal->update($updateData);

                // Update corresponding WalletTransaction to completed
                $transaction = WalletTransaction::where('reference_type', 'withdrawal_request')
                    ->where('reference_id', $withdrawal->id)
                    ->first();

                if ($transaction) {
                    $desc = 'Processed to ' . ($withdrawal->upi_id ?: $withdrawal->account_number);
                    if (!empty($validated['utr_number'])) {
                        $desc .= ' [UTR: ' . $validated['utr_number'] . ']';
                    }
                    $transaction->update([
                        'status' => 'completed',
                        'description' => $desc,
                    ]);
                }
            } elseif ($newStatus === 'rejected') {
                $withdrawal->update($updateData);

                // Refund the amount & points back to user's wallet
                $user = $withdrawal->user;
                $points = (int) ($withdrawal->redeemed_points ?: round($withdrawal->amount * 10));
                if ($user) {
                    $user->increment('wallet_balance', (float) $withdrawal->amount);
                    $user->increment('points_balance', $points);
                }

                // Update corresponding WalletTransaction to rejected
                $transaction = WalletTransaction::where('reference_type', 'withdrawal_request')
                    ->where('reference_id', $withdrawal->id)
                    ->first();

                if ($transaction) {
                    $transaction->update([
                        'status' => 'rejected',
                        'description' => 'Rejected: ' . ($validated['admin_notes'] ?? 'Refunded to wallet balance'),
                    ]);
                }

                // Log a refund transaction entry in wallet transactions
                if ($user) {
                    WalletTransaction::create([
                        'user_id' => $user->id,
                        'type' => 'credit',
                        'amount' => $withdrawal->amount,
                        'title' => 'Refund: Withdrawal #' . $withdrawal->id,
                        'description' => 'Refunded due to rejection: ' . ($validated['admin_notes'] ?? 'Request rejected by admin'),
                        'reference_id' => $withdrawal->id,
                        'reference_type' => 'withdrawal_refund',
                        'status' => 'completed',
                    ]);
                }
            }
        });

        $msg = $newStatus === 'approved' 
            ? 'Withdrawal of ₹' . number_format($withdrawal->amount, 2) . ' marked as Paid & Approved successfully!' 
            : 'Withdrawal request rejected and ₹' . number_format($withdrawal->amount, 2) . ' refunded to user wallet.';

        return back()->with('success', $msg);
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

    /**
     * Display Marketing / Growth Goals management list.
     */
    public function marketingGoals(Request $request)
    {
        $query = MarketingGoal::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $goals = $query->ordered()->paginate(15)->withQueryString();
        $totalGoals = MarketingGoal::count();
        $activeGoals = MarketingGoal::where('is_active', true)->count();
        $inactiveGoals = MarketingGoal::where('is_active', false)->count();

        return view('admin.marketing_goals.index', compact(
            'goals',
            'totalGoals',
            'activeGoals',
            'inactiveGoals'
        ));
    }

    /**
     * Store a newly created Marketing Goal.
     */
    public function storeMarketingGoal(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:100|unique:marketing_goals,slug',
            'description' => 'nullable|string|max:1000',
            'icon' => 'required|string|max:50',
            'icon_bg_color' => 'nullable|string|max:20',
            'icon_color' => 'nullable|string|max:20',
            'badge_text' => 'nullable|string|max:50',
            'is_instagram' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $slug = !empty($validated['slug']) 
            ? Str::slug($validated['slug']) 
            : Str::slug($validated['title']);

        // Check if slug exists, append number if needed
        $originalSlug = $slug;
        $counter = 1;
        while (MarketingGoal::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        MarketingGoal::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'],
            'icon_bg_color' => $validated['icon_bg_color'] ?? '#EEF2FF',
            'icon_color' => $validated['icon_color'] ?? '#3B82F6',
            'badge_text' => $validated['badge_text'] ?? null,
            'is_instagram' => $request->boolean('is_instagram'),
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
        ]);

        return redirect()->route('admin.marketing-goals')->with('success', 'Marketing goal added successfully!');
    }

    /**
     * Update an existing Marketing Goal.
     */
    public function updateMarketingGoal(Request $request, $id)
    {
        $goal = MarketingGoal::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:100|unique:marketing_goals,slug,' . $goal->id,
            'description' => 'nullable|string|max:1000',
            'icon' => 'required|string|max:50',
            'icon_bg_color' => 'nullable|string|max:20',
            'icon_color' => 'nullable|string|max:20',
            'badge_text' => 'nullable|string|max:50',
            'is_instagram' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $goal->update([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['slug']),
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'],
            'icon_bg_color' => $validated['icon_bg_color'] ?? '#EEF2FF',
            'icon_color' => $validated['icon_color'] ?? '#3B82F6',
            'badge_text' => $validated['badge_text'] ?? null,
            'is_instagram' => $request->boolean('is_instagram'),
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : false,
        ]);

        return redirect()->route('admin.marketing-goals')->with('success', 'Marketing goal updated successfully!');
    }

    /**
     * Toggle active/inactive status of a Marketing Goal.
     */
    public function toggleMarketingGoalStatus($id)
    {
        $goal = MarketingGoal::findOrFail($id);
        $goal->is_active = !$goal->is_active;
        $goal->save();

        $statusStr = $goal->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Goal '{$goal->title}' {$statusStr} successfully!");
    }

    /**
     * Delete a Marketing Goal.
     */
    public function destroyMarketingGoal($id)
    {
        $goal = MarketingGoal::findOrFail($id);
        $goalTitle = $goal->title;
        $goal->delete();

        return redirect()->route('admin.marketing-goals')->with('success', "Goal '{$goalTitle}' deleted successfully!");
    }

    /**
     * Display Bounty AI business onboarding users/profiles.
     */
    public function bountyAiUsers(Request $request)
    {
        $query = BountyAiProfile::with('user')->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('business_type', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('onboarding_status', $request->status);
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('business_type', $request->type);
        }

        $profiles = $query->paginate(15)->withQueryString();

        $totalProfiles = BountyAiProfile::count();
        $completedProfiles = BountyAiProfile::where('onboarding_status', 'completed')->count();
        $inProgressProfiles = BountyAiProfile::where('onboarding_status', 'in_progress')->count();
        $uniqueCities = BountyAiProfile::whereNotNull('city')->distinct()->count('city');

        if ($request->filled('category') && $request->category !== 'all') {
            $selectedCategory = $request->category;
            $query->where(function ($q) use ($selectedCategory) {
                $q->where('category', $selectedCategory)
                  ->orWhere('category_id', $selectedCategory);
            });
        }

        $marketingGoalsMap = MarketingGoal::pluck('title', 'slug')->toArray();
        $businessTypes = BountyAiProfile::whereNotNull('business_type')->distinct()->pluck('business_type')->toArray();
        $categoriesMap = BountyAiCategory::pluck('name', 'category_key')->toArray();
        $availableCategories = BountyAiCategory::orderBy('sort_order', 'asc')->get();

        return view('admin.bounty_ai.index', compact(
            'profiles',
            'totalProfiles',
            'completedProfiles',
            'inProgressProfiles',
            'uniqueCities',
            'marketingGoalsMap',
            'businessTypes',
            'categoriesMap',
            'availableCategories'
        ));
    }

    /**
     * Fetch single Bounty AI business profile details as JSON.
     */
    public function showBountyAiUser($id)
    {
        $profile = BountyAiProfile::with('user')->findOrFail($id);
        return response()->json([
            'status' => true,
            'data' => $profile,
        ]);
    }

    /**
     * Delete a Bounty AI business profile.
     */
    public function destroyBountyAiUser($id)
    {
        $profile = BountyAiProfile::findOrFail($id);
        $name = $profile->business_name;
        $profile->delete();

        return redirect()->route('admin.bounty-ai-users')->with('success', "Bounty AI profile for '{$name}' deleted successfully!");
    }

    /**
     * Display Bounty AI Business Categories management.
     */
    public function bountyAiCategories(Request $request)
    {
        $query = BountyAiCategory::query()->orderBy('sort_order', 'asc')->orderBy('id', 'asc');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('category_key', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        $categories = $query->paginate(15)->withQueryString();

        $totalCategories = BountyAiCategory::count();
        $activeCategories = BountyAiCategory::where('is_active', true)->count();
        $inactiveCategories = BountyAiCategory::where('is_active', false)->count();
        $totalAssignedUsers = BountyAiProfile::whereNotNull('category')->count();

        return view('admin.bounty_ai.categories', compact(
            'categories',
            'totalCategories',
            'activeCategories',
            'inactiveCategories',
            'totalAssignedUsers'
        ));
    }

    /**
     * Store a new Bounty AI Business Category.
     */
    public function storeBountyAiCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'category_key' => 'nullable|string|max:100|unique:bounty_ai_categories,category_key',
            'description' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:50',
            'icon_bg_color' => 'nullable|string|max:30',
            'icon_color' => 'nullable|string|max:30',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $categoryKey = !empty($validated['category_key']) 
            ? Str::slug($validated['category_key'], '_') 
            : Str::slug($validated['name'], '_');

        // Ensure key uniqueness if auto-generated
        $originalKey = $categoryKey;
        $counter = 1;
        while (BountyAiCategory::where('category_key', $categoryKey)->exists()) {
            $categoryKey = $originalKey . '_' . $counter++;
        }

        BountyAiCategory::create([
            'category_key' => $categoryKey,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'icon' => !empty($validated['icon']) ? $validated['icon'] : 'shopping_bag',
            'icon_bg_color' => !empty($validated['icon_bg_color']) ? $validated['icon_bg_color'] : '#FFFBEB',
            'icon_color' => !empty($validated['icon_color']) ? $validated['icon_color'] : '#D97706',
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
        ]);

        return redirect()->route('admin.bounty-ai-categories')->with('success', "Business category '{$validated['name']}' created successfully!");
    }

    /**
     * Update an existing Bounty AI Business Category.
     */
    public function updateBountyAiCategory(Request $request, $id)
    {
        $category = BountyAiCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'category_key' => 'required|string|max:100|unique:bounty_ai_categories,category_key,' . $category->id,
            'description' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:50',
            'icon_bg_color' => 'nullable|string|max:30',
            'icon_color' => 'nullable|string|max:30',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $category->update([
            'name' => $validated['name'],
            'category_key' => Str::slug($validated['category_key'], '_'),
            'description' => $validated['description'] ?? null,
            'icon' => !empty($validated['icon']) ? $validated['icon'] : $category->icon,
            'icon_bg_color' => !empty($validated['icon_bg_color']) ? $validated['icon_bg_color'] : $category->icon_bg_color,
            'icon_color' => !empty($validated['icon_color']) ? $validated['icon_color'] : $category->icon_color,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : false,
        ]);

        return redirect()->route('admin.bounty-ai-categories')->with('success', "Category '{$category->name}' updated successfully!");
    }

    /**
     * Quick toggle active status of a Business Category.
     */
    public function toggleBountyAiCategoryStatus($id)
    {
        $category = BountyAiCategory::findOrFail($id);
        $category->is_active = !$category->is_active;
        $category->save();

        $statusStr = $category->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Category '{$category->name}' {$statusStr} successfully!");
    }

    /**
     * Delete a Business Category.
     */
    public function destroyBountyAiCategory($id)
    {
        $category = BountyAiCategory::findOrFail($id);
        $name = $category->name;
        $category->delete();

        return redirect()->route('admin.bounty-ai-categories')->with('success', "Category '{$name}' deleted successfully!");
    }
}
