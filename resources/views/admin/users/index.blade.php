@extends('layouts.admin')

@section('title', 'Users & Business Management')
@section('page-title', 'Users & Business Accounts')

@section('content')
<div class="space-y-6">
    <!-- Top Header & Filter Tabs -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Registered Accounts</h1>
            <p class="text-sm text-slate-500 mt-1">Filter, manage, and view profiles for Earners (Users) and Businesses.</p>
        </div>

        <!-- Role Filter Tabs -->
        <div class="inline-flex p-1.5 bg-slate-100/90 rounded-2xl border border-slate-200/80 shadow-xs flex-wrap gap-1">
            <a href="{{ route('admin.users', array_merge(request()->except(['role', 'page']))) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ !request('role') ? 'bg-white text-indigo-700 shadow-sm border border-slate-200/80' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' }}">
                <span>🌐 All</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ !request('role') ? 'bg-indigo-50 text-indigo-700 font-extrabold' : 'bg-slate-200/70 text-slate-600' }}">{{ $totalUsers }}</span>
            </a>

            <a href="{{ route('admin.users', array_merge(request()->except('page'), ['role' => 'user'])) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ request('role') === 'user' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:text-emerald-700 hover:bg-white/60' }}">
                <span>👤 Earners / Users</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ request('role') === 'user' ? 'bg-white/20 text-white font-extrabold' : 'bg-slate-200/70 text-slate-600' }}">{{ $userCount }}</span>
            </a>

            <a href="{{ route('admin.users', array_merge(request()->except('page'), ['role' => 'business'])) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ request('role') === 'business' ? 'bg-amber-600 text-white shadow-sm' : 'text-slate-600 hover:text-amber-700 hover:bg-white/60' }}">
                <span>🏢 Businesses</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ request('role') === 'business' ? 'bg-white/20 text-white font-extrabold' : 'bg-slate-200/70 text-slate-600' }}">{{ $businessCount }}</span>
            </a>

            <a href="{{ route('admin.users', array_merge(request()->except('page'), ['role' => 'admin'])) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ request('role') === 'admin' ? 'bg-purple-600 text-white shadow-sm' : 'text-slate-600 hover:text-purple-700 hover:bg-white/60' }}">
                <span>🛡️ Admins</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ request('role') === 'admin' ? 'bg-white/20 text-white font-extrabold' : 'bg-slate-200/70 text-slate-600' }}">{{ $adminCount }}</span>
            </a>
        </div>
    </div>

    <!-- Active Filter Notification Bar -->
    @if(request('role') || request('search'))
        <div class="flex items-center justify-between bg-indigo-50/70 border border-indigo-200/70 px-4 py-2.5 rounded-xl text-xs text-indigo-900">
            <div class="flex items-center gap-2">
                <span class="font-bold">Active Filter:</span>
                @if(request('role'))
                    <span class="px-2.5 py-0.5 rounded-lg bg-indigo-100 text-indigo-800 font-semibold uppercase tracking-wider text-[11px]">
                        Role: {{ request('role') === 'user' ? 'Earners / Users' : (request('role') === 'business' ? 'Business Accounts' : ucfirst(request('role'))) }}
                    </span>
                @endif
                @if(request('search'))
                    <span class="px-2.5 py-0.5 rounded-lg bg-indigo-100 text-indigo-800 font-semibold text-[11px]">
                        Search: "{{ request('search') }}"
                    </span>
                @endif
            </div>
            <a href="{{ route('admin.users') }}" class="font-bold text-indigo-600 hover:text-indigo-800 underline flex items-center gap-1">
                <span>Reset All Filters</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </a>
        </div>
    @endif

    <!-- Users Table Card -->
    <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-xs">
        <!-- Table Search Bar Header -->
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.users') }}" class="relative w-full sm:max-w-md">
                @if(request('role'))
                    <input type="hidden" name="role" value="{{ request('role') }}">
                @endif
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by user name or email..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 text-xs focus:outline-none focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 transition-all">
            </form>

            <div class="text-xs text-slate-500 font-medium">
                Showing <span class="font-bold text-slate-800">{{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }}</span> of <span class="font-bold text-slate-800">{{ $users->total() }}</span> accounts
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Account Profile</th>
                        <th class="px-6 py-4">Type / Role</th>
                        <th class="px-6 py-4">Wallet Balance</th>
                        <th class="px-6 py-4">Activity / Performance</th>
                        <th class="px-6 py-4">Joined Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $user)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 font-mono text-xs text-slate-400">#{{ $user->id }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="flex items-center gap-3 group">
                                    <div class="w-10 h-10 rounded-full {{ $user->role === 'business' ? 'bg-amber-100 text-amber-800 border-amber-200' : ($user->role === 'admin' ? 'bg-purple-100 text-purple-700 border-purple-200' : 'bg-indigo-100 text-indigo-700 border-indigo-200') }} border flex items-center justify-center font-bold text-sm group-hover:scale-105 transition-transform">
                                        @if($user->role === 'business')
                                            🏢
                                        @elseif($user->role === 'admin')
                                            🛡️
                                        @else
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 text-sm group-hover:text-indigo-600 transition-colors flex items-center gap-1.5">
                                            <span>{{ $user->name }}</span>
                                            @if($user->role === 'business')
                                                <span class="text-[10px] px-1.5 py-0.2 bg-amber-100 text-amber-800 font-bold rounded">Biz</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-slate-500">{{ $user->email }}</div>
                                    </div>
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->role === 'business')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-300 shadow-2xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        🏢 Business
                                    </span>
                                @elseif($user->role === 'admin')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200 shadow-2xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                                        🛡️ Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-2xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        👤 Earner
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-900 text-sm">₹{{ number_format($user->wallet_balance ?? 0, 2) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->role === 'business')
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200 shadow-xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                            {{ $user->created_campaigns_count ?? 0 }} Campaigns Posted
                                        </span>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            {{ $user->completed_participations_count ?? 0 }} Completed
                                        </span>
                                        @if(($user->pending_participations_count ?? 0) > 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200" title="Pending Verification">
                                                {{ $user->pending_participations_count }} Pending
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500 font-medium">
                                {{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.users.show', $user->id) }}" 
                                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold bg-indigo-50 text-indigo-700 hover:bg-indigo-600 hover:text-white border border-indigo-200 transition-all shadow-xs">
                                    <span>View Details</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm">
                                No {{ request('role') ? (request('role') === 'user' ? 'earner' : request('role')) : 'user' }} accounts match your search filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($users->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
