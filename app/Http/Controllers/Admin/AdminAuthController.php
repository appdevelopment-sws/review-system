<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $recentUsers = User::latest()->take(5)->get();
        $recentCampaigns = Campaign::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'adminCount',
            'userCount',
            'totalCampaigns',
            'activeCampaigns',
            'totalRewardPool',
            'totalParticipants',
            'recentUsers',
            'recentCampaigns'
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
            'media_url' => 'nullable|url',
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
            'media_url' => 'nullable|url',
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
        } elseif (empty($validated['media_url'])) {
            $validated['media_url'] = $campaign->media_url;
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
