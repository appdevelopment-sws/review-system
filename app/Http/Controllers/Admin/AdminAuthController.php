<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignParticipation;
use App\Models\Category;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
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

                // Refund the amount back to user's wallet
                $user = $withdrawal->user;
                if ($user) {
                    $user->increment('wallet_balance', (float) $withdrawal->amount);
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
}
