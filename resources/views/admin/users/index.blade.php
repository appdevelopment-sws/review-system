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
                                <div class="inline-flex items-center gap-2 justify-end">
                                    <a href="{{ route('admin.users.show', $user->id) }}" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-indigo-50 text-indigo-700 hover:bg-indigo-600 hover:text-white border border-indigo-200 transition-all shadow-xs"
                                       title="View Profile Details">
                                        <span>View</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>

                                    @if(Auth::id() !== $user->id)
                                        <button type="button" 
                                                onclick="openDeleteUserModal('{{ $user->id }}', @js($user->name), @js($user->email), '{{ $user->role }}', {{ $user->role === 'business' ? ($user->created_campaigns_count ?? 0) : ($user->total_participations_count ?? 0) }})"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-bold bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white border border-rose-200 transition-all shadow-xs cursor-pointer"
                                                title="Delete {{ $user->role === 'business' ? 'Business' : ($user->role === 'admin' ? 'Admin' : 'User') }} Account">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            <span>Delete</span>
                                        </button>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-[10px] font-bold text-slate-400 bg-slate-100 border border-slate-200" title="Your logged-in account cannot be deleted">
                                            You
                                        </span>
                                    @endif
                                </div>
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

<!-- DELETE ACCOUNT CONFIRMATION MODAL -->
<div id="deleteUserModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm hidden" onclick="if(event.target === this) closeDeleteUserModal()">
    <div class="min-h-full flex items-center justify-center p-4 sm:p-6">
        <div class="bg-white border border-slate-200/90 rounded-3xl w-full max-w-md shadow-2xl p-6 sm:p-8 relative my-8" onclick="event.stopPropagation()">
            <!-- Close Button -->
            <button type="button" onclick="closeDeleteUserModal()" class="absolute top-5 right-5 p-2 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Danger Icon Header -->
            <div class="flex items-center gap-3.5 mb-5">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-200 flex items-center justify-center text-rose-600 shrink-0 shadow-xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <div>
                    <h3 id="deleteModalTitle" class="text-lg font-extrabold text-slate-900 leading-tight">Delete Account</h3>
                    <p class="text-xs text-rose-600 font-semibold mt-0.5">Permanent & Irreversible Action</p>
                </div>
            </div>

            <!-- Target User Details Box -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 mb-5 space-y-2.5">
                <div class="flex items-center gap-3">
                    <div id="deleteModalAvatar" class="w-10 h-10 rounded-full bg-slate-200 border border-slate-300 flex items-center justify-center font-bold text-sm text-slate-700 shrink-0">
                        U
                    </div>
                    <div class="min-w-0 flex-1">
                        <div id="deleteModalUserName" class="text-sm font-extrabold text-slate-900 truncate">User Name</div>
                        <div id="deleteModalUserEmail" class="text-xs text-slate-500 truncate">email@example.com</div>
                    </div>
                    <span id="deleteModalRoleBadge" class="px-2 py-0.5 rounded-full text-[10px] font-bold border uppercase shrink-0">
                        User
                    </span>
                </div>
                <div id="deleteModalWarningNotice" class="text-xs text-slate-600 bg-white p-3 rounded-xl border border-slate-200/70 leading-relaxed">
                    <!-- Dynamic text inserted here -->
                </div>
            </div>

            <!-- Confirmation Form -->
            <form id="deleteUserForm" method="POST" action="" class="space-y-3">
                @csrf
                @method('DELETE')

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="closeDeleteUserModal()" class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 text-xs font-bold transition-colors cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" id="deleteSubmitBtn" class="w-full sm:w-auto px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-md shadow-rose-600/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Yes, Permanently Delete</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openDeleteUserModal(id, name, email, role, count) {
        const form = document.getElementById('deleteUserForm');
        form.action = "{{ url('/admin/users') }}/" + id;

        const roleText = role === 'business' ? 'Business Account' : (role === 'admin' ? 'Admin Account' : 'Earner / User Account');
        document.getElementById('deleteModalTitle').innerText = 'Delete ' + roleText;
        document.getElementById('deleteModalUserName').innerText = name;
        document.getElementById('deleteModalUserEmail').innerText = email;

        const avatar = document.getElementById('deleteModalAvatar');
        const roleBadge = document.getElementById('deleteModalRoleBadge');
        const warning = document.getElementById('deleteModalWarningNotice');

        if (role === 'business') {
            avatar.className = 'w-10 h-10 rounded-full bg-amber-100 border border-amber-200 text-amber-800 flex items-center justify-center font-bold text-sm shrink-0';
            avatar.innerText = '🏢';
            roleBadge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold border bg-amber-50 text-amber-800 border-amber-200 uppercase shrink-0';
            roleBadge.innerText = 'Business';
            warning.innerHTML = '⚠️ <strong>Warning:</strong> Deleting this business will permanently remove all <strong>' + count + ' posted campaign(s)</strong>, participations, and recorded proofs. This cannot be undone.';
        } else if (role === 'admin') {
            avatar.className = 'w-10 h-10 rounded-full bg-purple-100 border border-purple-200 text-purple-700 flex items-center justify-center font-bold text-sm shrink-0';
            avatar.innerText = '🛡️';
            roleBadge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold border bg-purple-50 text-purple-700 border-purple-200 uppercase shrink-0';
            roleBadge.innerText = 'Admin';
            warning.innerHTML = '⚠️ <strong>Warning:</strong> Deleting this admin account will immediately revoke all administrative access permissions.';
        } else {
            avatar.className = 'w-10 h-10 rounded-full bg-indigo-100 border border-indigo-200 text-indigo-700 flex items-center justify-center font-bold text-sm shrink-0';
            avatar.innerText = name ? name.charAt(0).toUpperCase() : 'U';
            roleBadge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold border bg-emerald-50 text-emerald-800 border-emerald-200 uppercase shrink-0';
            roleBadge.innerText = 'Earner';
            warning.innerHTML = '⚠️ <strong>Warning:</strong> Deleting this earner user will permanently remove their <strong>' + count + ' task participation(s)</strong>, wallet balance, withdrawal history, and payout accounts.';
        }

        document.getElementById('deleteUserModal').classList.remove('hidden');
    }

    function closeDeleteUserModal() {
        document.getElementById('deleteUserModal').classList.add('hidden');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteUserModal();
        }
    });
</script>
@endsection
