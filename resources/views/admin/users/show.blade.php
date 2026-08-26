@extends('layouts.admin')

@section('title', $user->name . ' - User & Completed Campaigns')
@section('page-title', 'User Details & Campaign History')

@section('content')
<div class="space-y-6">
    <!-- Top Breadcrumb & User Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-slate-900 shadow-xs transition-colors" title="Back to Users">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $user->name }}</h1>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold capitalize border
                        {{ $user->role === 'admin' ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-slate-100 text-slate-700 border-slate-200' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $user->role === 'admin' ? 'bg-purple-500' : 'bg-slate-400' }}"></span>
                        {{ $user->role }}
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 mt-1">
                    <span class="font-medium">{{ $user->email }}</span>
                    <span>&bull;</span>
                    <span>User ID: <strong class="font-mono text-slate-700">#{{ $user->id }}</strong></span>
                    <span>&bull;</span>
                    <span>Registered on {{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.conversions', ['search' => $user->email]) }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold bg-white border border-slate-200 text-slate-700 hover:text-indigo-600 hover:border-indigo-200 shadow-xs transition-all">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Conversions Hub</span>
            </a>
            <a href="{{ route('admin.withdrawals', ['search' => $user->email]) }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold bg-white border border-slate-200 text-slate-700 hover:text-indigo-600 hover:border-indigo-200 shadow-xs transition-all">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <span>Withdrawals Hub</span>
            </a>
        </div>
    </div>

    <!-- Key Performance Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Metric 1: Completed Campaigns -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Completed Campaigns</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl font-extrabold text-emerald-600">{{ $completedCampaignsCount }}</span>
                <span class="text-xs text-slate-500 font-medium">of {{ $totalSubmissions }} Total</span>
            </div>
        </div>

        <!-- Metric 2: Total Rewards Earned -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Earned</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-1">
                <span class="text-2xl font-extrabold text-slate-900">₹{{ number_format($totalEarned, 2) }}</span>
            </div>
        </div>

        <!-- Metric 3: Current Wallet Balance -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Wallet Balance</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-1">
                <span class="text-2xl font-extrabold text-indigo-600">₹{{ number_format($user->wallet_balance ?? 0, 2) }}</span>
            </div>
        </div>

        <!-- Metric 4: Total Withdrawn -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Paid Out</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-1">
                <span class="text-2xl font-extrabold text-slate-900">₹{{ number_format($totalWithdrawn, 2) }}</span>
            </div>
        </div>

        <!-- Metric 5: Pending Submissions -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pending Review</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl font-extrabold text-amber-600">{{ $pendingSubmissionsCount }}</span>
                <span class="text-xs text-slate-500 font-medium">Submissions</span>
            </div>
        </div>
    </div>

    <!-- MAIN GRID: Left (Completed Campaigns Table) & Right (Payout & Account Info) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- LEFT 2 COLUMNS: Completed & Participated Campaigns Table -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Table Card Header & Filters -->
            <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-xs">
                <div class="p-5 border-b border-slate-100">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-extrabold text-slate-900">Campaigns & Task History</h2>
                            <p class="text-xs text-slate-500 mt-0.5">All campaigns participated in, completed review proofs, and reward approvals for {{ $user->name }}.</p>
                        </div>
                    </div>

                    <!-- Filter Tabs & Search -->
                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-slate-100">
                        <div class="flex items-center gap-1.5 bg-slate-50 p-1 rounded-xl border border-slate-200">
                            <a href="{{ route('admin.users.show', ['id' => $user->id]) }}" 
                               class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ !request('status') ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                                All ({{ $totalSubmissions }})
                            </a>
                            <a href="{{ route('admin.users.show', ['id' => $user->id, 'status' => 'approved']) }}" 
                               class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ request('status') === 'approved' ? 'bg-emerald-600 text-white shadow-xs' : 'text-emerald-700 hover:bg-emerald-50' }}">
                                ✓ Completed ({{ $completedCampaignsCount }})
                            </a>
                            <a href="{{ route('admin.users.show', ['id' => $user->id, 'status' => 'pending']) }}" 
                               class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ request('status') === 'pending' ? 'bg-amber-500 text-white shadow-xs' : 'text-amber-700 hover:bg-amber-50' }}">
                                ⏳ Pending ({{ $pendingSubmissionsCount }})
                            </a>
                            <a href="{{ route('admin.users.show', ['id' => $user->id, 'status' => 'rejected']) }}" 
                               class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ request('status') === 'rejected' ? 'bg-red-600 text-white shadow-xs' : 'text-red-700 hover:bg-red-50' }}">
                                ✕ Rejected ({{ $rejectedSubmissionsCount }})
                            </a>
                        </div>

                        <form method="GET" action="{{ route('admin.users.show', $user->id) }}" class="relative w-full sm:w-56">
                            @if(request('status'))
                                <input type="hidden" name="status" value="{{ request('status') }}">
                            @endif
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search campaign..." 
                                   class="w-full pl-8 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 text-xs focus:outline-none focus:bg-white focus:border-indigo-600 transition-all">
                            <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                        </form>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-700">
                        <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider font-bold border-b border-slate-200">
                            <tr>
                                <th class="px-5 py-3.5">Campaign</th>
                                <th class="px-5 py-3.5">Reward</th>
                                <th class="px-5 py-3.5">Status</th>
                                <th class="px-5 py-3.5">Proof Screenshot</th>
                                <th class="px-5 py-3.5">Submitted Date</th>
                                <th class="px-5 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($participations as $item)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <!-- Campaign Column -->
                                    <td class="px-5 py-4 max-w-xs">
                                        <div class="flex items-center gap-3">
                                            @if ($item->campaign && $item->campaign->media_url && $item->campaign->media_type === 'image')
                                                <img src="{{ $item->campaign->media_url }}" alt="Campaign thumbnail" class="w-10 h-10 rounded-xl object-cover border border-slate-200 shrink-0">
                                            @else
                                                <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs shrink-0">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                                    </svg>
                                                </div>
                                            @endif
                                            <div class="min-w-0">
                                                <div class="font-bold text-slate-900 text-sm truncate" title="{{ $item->campaign->title ?? 'Deleted Campaign' }}">
                                                    {{ $item->campaign->title ?? 'Deleted Campaign' }}
                                                </div>
                                                @if($item->campaign)
                                                    <a href="{{ route('admin.campaigns.report', $item->campaign->id) }}" class="text-[11px] font-semibold text-indigo-600 hover:underline">
                                                        View Campaign Report ↗
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Reward Amount -->
                                    <td class="px-5 py-4">
                                        <span class="font-extrabold text-emerald-600 text-sm">₹{{ number_format($item->reward_amount, 2) }}</span>
                                    </td>

                                    <!-- Status Badge -->
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold capitalize border
                                            {{ $item->status === 'approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : '' }}
                                            {{ $item->status === 'pending' ? 'bg-amber-50 text-amber-700 border-amber-200' : '' }}
                                            {{ $item->status === 'rejected' ? 'bg-red-50 text-red-700 border-red-200' : '' }}">
                                            <span class="w-1.5 h-1.5 rounded-full 
                                                {{ $item->status === 'approved' ? 'bg-emerald-500' : '' }}
                                                {{ $item->status === 'pending' ? 'bg-amber-500 animate-pulse' : '' }}
                                                {{ $item->status === 'rejected' ? 'bg-red-500' : '' }}"></span>
                                            {{ $item->status === 'approved' ? 'Completed' : $item->status }}
                                        </span>
                                    </td>

                                    <!-- Proof Screenshot Thumbnail -->
                                    <td class="px-5 py-4">
                                        @if ($item->proof_image)
                                            <button type="button" onclick='openProofModal(@json($item))' 
                                                    class="group relative rounded-xl overflow-hidden border border-slate-200 block shadow-xs hover:border-indigo-500 transition-all cursor-pointer">
                                                <img src="{{ $item->proof_image }}" alt="Proof Screenshot" class="w-14 h-10 object-cover group-hover:scale-105 transition-transform duration-300">
                                                <span class="absolute inset-0 bg-slate-900/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-white text-[9px] font-bold">
                                                    View SS
                                                </span>
                                            </button>
                                        @else
                                            <span class="text-xs text-slate-400 italic">No SS</span>
                                        @endif
                                    </td>

                                    <!-- Date Column -->
                                    <td class="px-5 py-4 text-xs text-slate-500 font-medium">
                                        {{ $item->submitted_at ? $item->submitted_at->format('M d, Y h:i A') : $item->created_at->format('M d, Y') }}
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-5 py-4 text-right space-x-1">
                                        <button type="button" onclick='openProofModal(@json($item))' 
                                                class="px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors">
                                            Details
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">
                                        No campaigns found for this user under the selected filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($participations->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $participations->links() }}
                    </div>
                @endif
            </div>

            <!-- User Wallet Transactions Ledger History -->
            <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-xs">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Wallet Transactions History</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Recent earnings, payouts, and wallet ledger entries for this account.</p>
                    </div>
                    <span class="text-xs font-bold text-slate-500">{{ $recentTransactions->count() }} Recent Entries</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-700">
                        <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider font-bold border-b border-slate-200">
                            <tr>
                                <th class="px-5 py-3">Type</th>
                                <th class="px-5 py-3">Description</th>
                                <th class="px-5 py-3">Amount</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($recentTransactions as $tx)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold uppercase
                                            {{ $tx->type === 'credit' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                                            {{ $tx->type === 'credit' ? '+ Credit' : '- Debit' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="font-bold text-slate-900 text-xs">{{ $tx->title }}</div>
                                        @if($tx->description)
                                            <div class="text-[11px] text-slate-500">{{ $tx->description }}</div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 font-bold text-xs {{ $tx->type === 'credit' ? 'text-emerald-600' : 'text-slate-900' }}">
                                        {{ $tx->type === 'credit' ? '+' : '-' }}₹{{ number_format($tx->amount, 2) }}
                                    </td>
                                    <td class="px-5 py-3">
                                        <span class="text-xs font-semibold capitalize text-slate-600">{{ $tx->status }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-xs text-slate-500">
                                        {{ $tx->created_at->format('M d, Y h:i A') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-8 text-center text-slate-400 text-xs">
                                        No wallet ledger transactions recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- RIGHT 1 COLUMN: Bank / UPI Details & Withdrawal Requests -->
        <div class="space-y-6">
            
            <!-- User Payout Details (UPI & Bank Account) -->
            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Payout & Banking Details</h3>
                        <p class="text-[11px] text-slate-500">Registered payout destination accounts</p>
                    </div>
                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-indigo-50 text-indigo-700">Payment Methods</span>
                </div>

                @php
                    $payoutDetails = $user->payoutDetails;

                    // Match saved payout records (handles 'bank', 'bank_account', and 'upi')
                    $upiDetail = $payoutDetails->first(fn($d) => $d->payout_type === 'upi' || !empty($d->upi_id));
                    $bankDetail = $payoutDetails->first(fn($d) => in_array($d->payout_type, ['bank', 'bank_account']) || !empty($d->account_number));

                    // Fallback to recent withdrawal requests if not present in saved table
                    $latestUpiWith = $recentWithdrawals->where('payout_type', 'upi')->first(fn($w) => !empty($w->upi_id));
                    $latestBankWith = $recentWithdrawals->whereIn('payout_type', ['bank', 'bank_account'])->first(fn($w) => !empty($w->account_number));

                    $upiId = $upiDetail?->upi_id ?? $latestUpiWith?->upi_id;
                    $upiHolder = $upiDetail?->account_holder_name ?? $latestUpiWith?->account_holder_name ?? $user->name;

                    $accNumber = $bankDetail?->account_number ?? $latestBankWith?->account_number;
                    $ifscCode = $bankDetail?->ifsc_code ?? $latestBankWith?->ifsc_code;
                    $bankName = $bankDetail?->bank_name ?? $latestBankWith?->bank_name;
                    $bankHolder = $bankDetail?->account_holder_name ?? $latestBankWith?->account_holder_name ?? $user->name;
                @endphp

                <!-- UPI Details Box -->
                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                            UPI VPA / ID
                        </span>
                        @if($upiId)
                            <button type="button" onclick="copyText('{{ $upiId }}')" class="text-[10px] font-bold text-indigo-600 hover:underline cursor-pointer">
                                Copy UPI
                            </button>
                        @endif
                    </div>
                    @if($upiId)
                        <div class="font-mono text-xs font-bold text-slate-900 bg-white p-2 rounded-lg border border-slate-200 break-all">
                            {{ $upiId }}
                        </div>
                        @if($upiHolder)
                            <div class="text-[11px] text-slate-500">Holder: <strong>{{ $upiHolder }}</strong></div>
                        @endif
                    @else
                        <div class="text-xs text-slate-400 italic">No UPI ID registered yet.</div>
                    @endif
                </div>

                <!-- Bank Account Details Box -->
                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            Bank Account
                        </span>
                        @if($accNumber)
                            <button type="button" onclick="copyText('{{ $accNumber }}')" class="text-[10px] font-bold text-indigo-600 hover:underline cursor-pointer">
                                Copy A/C
                            </button>
                        @endif
                    </div>
                    @if($accNumber)
                        <div class="space-y-1.5 text-xs">
                            <div class="bg-white p-2.5 rounded-lg border border-slate-200 space-y-1">
                                <div>A/C Number: <strong class="font-mono text-slate-900">{{ $accNumber }}</strong></div>
                                <div>IFSC Code: <strong class="font-mono text-slate-900">{{ $ifscCode ?? 'N/A' }}</strong></div>
                                @if($bankName)
                                    <div>Bank: <strong class="text-slate-900">{{ $bankName }}</strong></div>
                                @endif
                                @if($bankHolder)
                                    <div>Holder: <strong class="text-slate-900">{{ $bankHolder }}</strong></div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="text-xs text-slate-400 italic">No bank account registered yet.</div>
                    @endif
                </div>
            </div>

            <!-- Recent Withdrawal Requests for this user -->
            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Withdrawal Requests</h3>
                        <p class="text-[11px] text-slate-500">Recent payout requests for this user</p>
                    </div>
                    <a href="{{ route('admin.withdrawals', ['search' => $user->email]) }}" class="text-[11px] font-bold text-indigo-600 hover:underline">
                        View All →
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse ($recentWithdrawals as $with)
                        <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-extrabold text-slate-900">₹{{ number_format($with->amount, 2) }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold capitalize border
                                    {{ $with->status === 'approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : '' }}
                                    {{ $with->status === 'pending' ? 'bg-amber-50 text-amber-700 border-amber-200' : '' }}
                                    {{ $with->status === 'rejected' ? 'bg-red-50 text-red-700 border-red-200' : '' }}">
                                    {{ $with->status === 'approved' ? 'Paid / Approved' : ucfirst($with->status) }}
                                </span>
                            </div>
                            <div class="text-[11px] text-slate-500 flex items-center justify-between">
                                <span class="uppercase font-bold text-indigo-600">{{ $with->payout_type }}</span>
                                <span>{{ $with->created_at ? $with->created_at->format('M d, Y') : 'N/A' }}</span>
                            </div>
                            @if($with->payout_type === 'upi' && $with->upi_id)
                                <div class="text-xs text-slate-700 font-mono bg-white px-2 py-1 rounded border border-slate-200 truncate">
                                    {{ $with->upi_id }}
                                </div>
                            @elseif($with->account_number)
                                <div class="text-xs text-slate-700 font-mono bg-white px-2 py-1 rounded border border-slate-200 truncate">
                                    A/C: {{ $with->account_number }} @if($with->ifsc_code)({{ $with->ifsc_code }})@endif
                                </div>
                            @endif
                            @if($with->utr_number)
                                <div class="text-[11px] text-slate-600 font-mono">UTR: <strong>{{ $with->utr_number }}</strong></div>
                            @endif
                            @if($with->admin_notes)
                                <div class="text-[11px] text-slate-500 italic">{{ $with->admin_notes }}</div>
                            @endif
                            @if($with->proof_image)
                                <div>
                                    <a href="{{ $with->proof_image }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-indigo-600 hover:underline">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Payment Proof Screenshot
                                    </a>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-xs text-slate-400 text-center py-4">No withdrawal requests yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- REVIEW SCREENSHOT PROOF LIGHTBOX MODAL -->
<div id="proofModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm hidden" onclick="if(event.target === this) closeProofModal()">
    <div class="min-h-full flex items-center justify-center p-4 sm:p-6 lg:p-8">
        <div class="bg-white border border-slate-200 rounded-3xl w-full max-w-3xl shadow-2xl p-6 sm:p-8 my-8 relative" onclick="event.stopPropagation()">
            <button type="button" onclick="closeProofModal()" class="absolute top-6 right-6 p-2 rounded-xl text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition-colors cursor-pointer">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <h2 class="text-xl font-extrabold text-slate-900 mb-1">Review Screenshot Proof & Submission Details</h2>
        <p class="text-xs text-slate-500 mb-6">Inspect user submitted review screenshot proof and manage task reward status.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Proof Screenshot Image -->
            <div>
                <span class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Review Screenshot Proof</span>
                <div class="rounded-2xl border border-slate-200 bg-slate-900 overflow-hidden shadow-md">
                    <img id="modalProofImage" src="" alt="Proof Screenshot" class="w-full max-h-96 object-contain bg-slate-950">
                </div>
            </div>

            <!-- Details Panel -->
            <div class="space-y-4 flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                        <span class="text-[10px] font-bold uppercase text-slate-400 block">User Name & Email</span>
                        <div id="modalUserName" class="text-sm font-bold text-slate-900 mt-0.5"></div>
                        <div id="modalUserEmail" class="text-xs text-slate-500"></div>
                    </div>

                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                        <span class="text-[10px] font-bold uppercase text-slate-400 block">Campaign Title</span>
                        <div id="modalCampaignTitle" class="text-sm font-bold text-slate-900 mt-0.5"></div>
                        <div id="modalRedirectLink" class="mt-1"></div>
                    </div>

                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                        <span class="text-[10px] font-bold uppercase text-slate-400 block">User Notes / Review Feedback</span>
                        <div id="modalReviewText" class="text-xs text-slate-700 leading-relaxed mt-1 italic"></div>
                    </div>
                </div>

                <!-- Approval / Rejection Action Form -->
                <form id="modalStatusForm" method="POST" action="" class="space-y-3 pt-3 border-t border-slate-100">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Admin Notes (Optional Reason)</label>
                        <input type="text" name="admin_notes" id="modalAdminNotes" placeholder="e.g. Verified 5-star Play Store review screenshot" 
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-xs focus:outline-none focus:border-indigo-600">
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" name="status" value="approved" class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors">
                            ✓ Approve & Reward
                        </button>
                        <button type="submit" name="status" value="rejected" class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors">
                            ✕ Reject Proof
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@section('scripts')
<script>
    function copyText(text) {
        if (!text) return;
        navigator.clipboard.writeText(text).then(() => {
            alert('Copied to clipboard: ' + text);
        }).catch(err => {
            prompt('Copy manually:', text);
        });
    }

    function openProofModal(item) {
        document.getElementById('modalProofImage').src = item.proof_image || '';
        document.getElementById('modalUserName').innerText = item.user ? item.user.name : '{{ $user->name }}';
        document.getElementById('modalUserEmail').innerText = item.user ? item.user.email : '{{ $user->email }}';
        document.getElementById('modalCampaignTitle').innerText = item.campaign ? item.campaign.title : 'N/A';
        document.getElementById('modalReviewText').innerText = item.review_text ? '"' + item.review_text + '"' : 'No review notes provided.';
        document.getElementById('modalAdminNotes').value = item.admin_notes || '';

        const redirectContainer = document.getElementById('modalRedirectLink');
        if (item.campaign && item.campaign.redirect_url) {
            redirectContainer.innerHTML = `<a href="${item.campaign.redirect_url}" target="_blank" class="text-xs font-bold text-indigo-600 hover:underline">Target Link: ${item.campaign.redirect_url} ↗</a>`;
        } else {
            redirectContainer.innerHTML = '';
        }

        const form = document.getElementById('modalStatusForm');
        form.action = "/admin/conversions/" + item.id + "/status";

        document.getElementById('proofModal').classList.remove('hidden');
    }

    function closeProofModal() {
        document.getElementById('proofModal').classList.add('hidden');
    }
</script>
@endsection
