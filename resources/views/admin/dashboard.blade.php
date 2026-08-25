@extends('layouts.admin')

@section('title', 'Dashboard Overview')
@section('page-title', 'Dashboard Overview')

@section('content')
<div class="space-y-8">
    <!-- Top Overview Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">System Performance & Stats</h1>
            <p class="text-sm text-slate-500 mt-1">Real-time metrics for users, active campaigns, conversions, and review proof screenshots.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.conversions') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-700 hover:text-slate-900 text-sm font-bold rounded-xl shadow-xs transition-all">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>View Conversions</span>
                @if(isset($pendingConversionsCount) && $pendingConversionsCount > 0)
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-500 text-white">{{ $pendingConversionsCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.campaigns') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white text-sm font-semibold rounded-xl shadow-md shadow-indigo-600/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Create Campaign</span>
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Stat Card 1: Total Users -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 relative overflow-hidden group hover:border-slate-300 hover:shadow-md transition-all shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Users</span>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $totalUsers }}</span>
                <span class="text-xs text-slate-500 font-medium">({{ $adminCount }} Admins, {{ $userCount }} Users)</span>
            </div>
        </div>

        <!-- Stat Card 2: Active Campaigns -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 relative overflow-hidden group hover:border-slate-300 hover:shadow-md transition-all shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Active Campaigns</span>
                <div class="w-10 h-10 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $activeCampaigns }}</span>
                <span class="text-xs text-slate-500 font-medium">of {{ $totalCampaigns }} total</span>
            </div>
        </div>

        <!-- Stat Card 3: Pending Conversions (Shared SS Proofs) -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 relative overflow-hidden group hover:border-slate-300 hover:shadow-md transition-all shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Pending Review SS</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $pendingConversionsCount ?? 0 }}</span>
                <span class="text-xs text-amber-600 font-bold">Awaiting Approval</span>
            </div>
        </div>

        <!-- Stat Card 4: Total Reward Pool -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 relative overflow-hidden group hover:border-slate-300 hover:shadow-md transition-all shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Reward Pool</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">${{ number_format($totalRewardPool, 2) }}</span>
                <span class="text-xs text-emerald-600 font-bold">Allocated</span>
            </div>
        </div>
    </div>

    <!-- Recent Conversions & Shared Review Screenshots -->
    @if(isset($recentSubmissions) && $recentSubmissions->count() > 0)
        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-xs">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Recent Conversions & Review Proof Screenshots</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Latest user submissions with shared review screenshot proofs</p>
                </div>
                <a href="{{ route('admin.conversions') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700">View All Conversions →</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($recentSubmissions as $sub)
                    <div class="p-4 bg-slate-50 border border-slate-200/80 rounded-xl flex flex-col justify-between space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 border border-indigo-200 flex items-center justify-center font-bold text-xs text-indigo-600">
                                    {{ strtoupper(substr($sub->user->name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-900">{{ $sub->user->name ?? 'User' }}</div>
                                    <div class="text-[10px] text-slate-500">{{ $sub->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold capitalize border
                                {{ $sub->status === 'approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : '' }}
                                {{ $sub->status === 'pending' ? 'bg-amber-50 text-amber-700 border-amber-200' : '' }}
                                {{ $sub->status === 'rejected' ? 'bg-red-50 text-red-700 border-red-200' : '' }}">
                                {{ $sub->status }}
                            </span>
                        </div>

                        <div class="text-xs font-bold text-slate-900 truncate">
                            {{ $sub->campaign->title ?? 'Campaign' }}
                        </div>

                        @if ($sub->proof_image)
                            <a href="{{ route('admin.conversions') }}" class="block rounded-xl overflow-hidden border border-slate-200 shadow-xs h-28 bg-slate-900 relative group">
                                <img src="{{ $sub->proof_image }}" alt="Proof Screenshot" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <span class="absolute inset-0 bg-slate-900/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-white text-xs font-bold">
                                    Review Proof SS ↗
                                </span>
                            </a>
                        @endif

                        <div class="flex items-center justify-between pt-2 border-t border-slate-200/60">
                            <span class="text-xs text-slate-500 font-medium">Reward: <strong class="text-emerald-600">${{ number_format($sub->reward_amount, 2) }}</strong></span>
                            <a href="{{ route('admin.conversions') }}" class="text-xs font-bold text-indigo-600 hover:underline">Manage Submission →</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Grid Section: Recent Campaigns & Recent Users -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent Campaigns Panel (Span 2) -->
        <div class="lg:col-span-2 bg-white border border-slate-200/80 rounded-2xl p-6 shadow-xs">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Active & Featured Campaigns</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Campaigns currently running for users to participate</p>
                </div>
                <a href="{{ route('admin.campaigns') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700">View All →</a>
            </div>

            <div class="space-y-4">
                @forelse ($recentCampaigns as $campaign)
                    <div class="p-4 bg-slate-50 border border-slate-200/80 rounded-xl flex items-center justify-between gap-4 hover:border-slate-300 transition-colors">
                        <div class="flex items-center gap-4 min-w-0">
                            @if ($campaign->media_url && $campaign->media_type === 'image')
                                <img src="{{ $campaign->media_url }}" alt="{{ $campaign->title }}" class="w-14 h-14 rounded-xl object-cover border border-slate-200 shrink-0">
                            @else
                                <div class="w-14 h-14 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif

                            <div class="min-w-0">
                                <h3 class="text-sm font-bold text-slate-900 truncate">{{ $campaign->title }}</h3>
                                <div class="flex items-center gap-3 mt-1 text-xs text-slate-500">
                                    <span class="text-emerald-600 font-bold">${{ number_format($campaign->reward_amount, 2) }} / user</span>
                                    <span>•</span>
                                    <span>{{ $campaign->participants_count }} / {{ $campaign->participant_limit }} availed</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            <div class="w-24 bg-slate-200 rounded-full h-2 overflow-hidden">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $campaign->progressPercentage() }}%"></div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold capitalize border
                                {{ $campaign->status === 'active' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : '' }}
                                {{ $campaign->status === 'completed' ? 'bg-blue-50 text-blue-700 border-blue-200' : '' }}
                                {{ $campaign->status === 'paused' ? 'bg-amber-50 text-amber-700 border-amber-200' : '' }}">
                                {{ $campaign->status }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 text-center py-6">No campaigns found.</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Users Panel (Span 1) -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-xs">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Registered Users</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Latest platform accounts</p>
                </div>
                <a href="{{ route('admin.users') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700">Manage →</a>
            </div>

            <div class="space-y-3">
                @forelse ($recentUsers as $user)
                    <div class="p-3 bg-slate-50 border border-slate-200/80 rounded-xl flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 border border-indigo-200 flex items-center justify-center font-bold text-xs text-indigo-600">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-900">{{ $user->name }}</div>
                                <div class="text-[11px] text-slate-500">{{ $user->email }}</div>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border
                            {{ $user->role === 'admin' ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-slate-100 text-slate-600 border-slate-200' }}">
                            {{ $user->role }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 text-center py-6">No users found.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
