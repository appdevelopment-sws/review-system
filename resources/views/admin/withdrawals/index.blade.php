@extends('layouts.admin')

@section('title', 'Withdrawal Requests & Payouts')
@section('page-title', 'Withdrawal Requests & Payouts')

@section('content')
<div class="space-y-6">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Withdrawal Requests & Payout Management</h1>
            <p class="text-sm text-slate-500 mt-1">Review user withdrawal requests, inspect bank/UPI details, upload payment proof screenshots, and process payouts.</p>
        </div>
    </div>

    <!-- Overview Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <!-- Stat 1: Pending Requests -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Pending Requests</span>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-2xl font-extrabold text-amber-600">{{ $pendingCount }}</span>
                    <span class="text-xs text-slate-500 font-semibold">(₹{{ number_format($totalPendingAmount, 2) }} total)</span>
                </div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Stat 2: Total Paid Out -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Paid Out</span>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-2xl font-extrabold text-emerald-600">₹{{ number_format($totalPaidAmount, 2) }}</span>
                    <span class="text-xs text-slate-500 font-semibold">({{ $approvedCount }} Paid)</span>
                </div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Stat 3: Total Requests -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Requests</span>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-2xl font-extrabold text-slate-900">{{ $totalWithdrawals }}</span>
                    <span class="text-xs text-slate-500 font-semibold">({{ $rejectedCount }} Rejected)</span>
                </div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Filter Pills & Search -->
    <div class="flex flex-wrap items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.withdrawals') }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold border transition-all {{ !request('status') ? 'bg-indigo-50 text-indigo-600 border-indigo-200' : 'text-slate-600 border-transparent hover:text-slate-900' }}">
                All Requests ({{ $totalWithdrawals }})
            </a>
            <a href="{{ route('admin.withdrawals', ['status' => 'pending']) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold border transition-all {{ request('status') === 'pending' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'text-slate-600 border-transparent hover:text-slate-900' }}">
                Pending ({{ $pendingCount }})
            </a>
            <a href="{{ route('admin.withdrawals', ['status' => 'approved']) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold border transition-all {{ request('status') === 'approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'text-slate-600 border-transparent hover:text-slate-900' }}">
                Paid / Approved ({{ $approvedCount }})
            </a>
            <a href="{{ route('admin.withdrawals', ['status' => 'rejected']) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold border transition-all {{ request('status') === 'rejected' ? 'bg-red-50 text-red-700 border-red-200' : 'text-slate-600 border-transparent hover:text-slate-900' }}">
                Rejected ({{ $rejectedCount }})
            </a>
        </div>

        <form method="GET" action="{{ route('admin.withdrawals') }}" class="relative w-full sm:w-72">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search user, UPI, account or UTR..." 
                   class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 text-xs focus:outline-none focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 transition-all">
        </form>
    </div>

    <!-- Withdrawals Table Card -->
    <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Requested Amount</th>
                        <th class="px-6 py-4">Method</th>
                        <th class="px-6 py-4">Bank / UPI Details</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Proof SS / Ref</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($withdrawals as $item)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- User Column -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-indigo-100 border border-indigo-200 flex items-center justify-center font-bold text-xs text-indigo-600">
                                        {{ strtoupper(substr($item->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 text-sm">{{ $item->user->name ?? 'Unknown User' }}</div>
                                        <div class="text-xs text-slate-500">{{ $item->user->email ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Amount Column -->
                            <td class="px-6 py-4">
                                <span class="font-extrabold text-slate-900 text-base">₹{{ number_format($item->amount, 2) }}</span>
                            </td>

                            <!-- Method Badge -->
                            <td class="px-6 py-4">
                                @if($item->payout_type === 'upi')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                        </svg>
                                        UPI
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                                        </svg>
                                        Bank Transfer
                                    </span>
                                @endif
                            </td>

                            <!-- Bank / UPI Details Snippet with 1-click Copy -->
                            <td class="px-6 py-4 max-w-xs">
                                @if($item->payout_type === 'upi')
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-xs font-semibold text-slate-800 bg-slate-100 px-2 py-0.5 rounded">{{ $item->upi_id ?? 'N/A' }}</span>
                                        @if($item->upi_id)
                                            <button type="button" onclick="copyText('{{ $item->upi_id }}')" title="Copy UPI ID" class="text-slate-400 hover:text-indigo-600 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                    @if($item->account_holder_name)
                                        <div class="text-[11px] text-slate-500 mt-0.5">Holder: {{ $item->account_holder_name }}</div>
                                    @endif
                                @else
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-xs font-semibold text-slate-800 bg-slate-100 px-2 py-0.5 rounded">A/C: {{ $item->account_number ?? 'N/A' }}</span>
                                        @if($item->account_number)
                                            <button type="button" onclick="copyText('{{ $item->account_number }}')" title="Copy Account Number" class="text-slate-400 hover:text-indigo-600 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        {{ $item->bank_name ?? 'Bank' }} &bull; IFSC: <span class="font-mono">{{ $item->ifsc_code ?? 'N/A' }}</span>
                                    </div>
                                @endif
                            </td>

                            <!-- Status Badge -->
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold capitalize border
                                    {{ $item->status === 'approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : '' }}
                                    {{ $item->status === 'pending' ? 'bg-amber-50 text-amber-700 border-amber-200' : '' }}
                                    {{ $item->status === 'rejected' ? 'bg-red-50 text-red-700 border-red-200' : '' }}">
                                    <span class="w-1.5 h-1.5 rounded-full 
                                        {{ $item->status === 'approved' ? 'bg-emerald-500' : '' }}
                                        {{ $item->status === 'pending' ? 'bg-amber-500 animate-pulse' : '' }}
                                        {{ $item->status === 'rejected' ? 'bg-red-500' : '' }}"></span>
                                    {{ $item->status === 'approved' ? 'Paid' : $item->status }}
                                </span>
                            </td>

                            <!-- Proof Screenshot Thumbnail / UTR -->
                            <td class="px-6 py-4">
                                @if ($item->proof_image)
                                    <button type="button" onclick='openWithdrawalModal(@json($item))' class="group relative rounded-xl overflow-hidden border border-slate-200 block shadow-xs hover:border-indigo-500 transition-all cursor-pointer">
                                        <img src="{{ $item->proof_image }}" alt="Payment Proof SS" class="w-16 h-10 object-cover group-hover:scale-105 transition-transform duration-300">
                                        <span class="absolute inset-0 bg-slate-900/30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-white text-[9px] font-bold">
                                            Proof SS
                                        </span>
                                    </button>
                                @elseif($item->utr_number)
                                    <span class="font-mono text-xs text-slate-700 bg-slate-100 px-2 py-0.5 rounded">UTR: {{ $item->utr_number }}</span>
                                @else
                                    <span class="text-xs text-slate-400 italic">No SS attached</span>
                                @endif
                            </td>

                            <!-- Date Column -->
                            <td class="px-6 py-4 text-xs text-slate-500 font-medium">
                                {{ $item->created_at->format('M d, Y h:i A') }}
                            </td>

                            <!-- Action Buttons -->
                            <td class="px-6 py-4 text-right space-x-2">
                                <button type="button" onclick='openWithdrawalModal(@json($item))' 
                                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold {{ $item->status === 'pending' ? 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }} transition-colors">
                                    {{ $item->status === 'pending' ? 'Inspect & Pay' : 'View Details' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400 text-sm">
                                No withdrawal requests found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($withdrawals->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $withdrawals->links() }}
            </div>
        @endif
    </div>
</div>

<!-- WITHDRAWAL DETAIL & PAYOUT ACTION MODAL -->
<div id="withdrawalModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm hidden p-4 overflow-y-auto">
    <div class="bg-white border border-slate-200 rounded-3xl w-full max-w-3xl shadow-2xl p-6 sm:p-8 my-8 relative">
        <button type="button" onclick="closeWithdrawalModal()" class="absolute top-6 right-6 text-slate-400 hover:text-slate-800">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <h2 class="text-xl font-extrabold text-slate-900 mb-1">Withdrawal Request & Payout Details</h2>
        <p class="text-xs text-slate-500 mb-6">Review user bank/UPI payout details, share payment proof screenshot, and approve or reject.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Left Side: User & Bank Details -->
            <div class="space-y-4">
                <!-- User Summary -->
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">User Information</span>
                    <div class="flex items-center gap-3 mt-1">
                        <div id="mUserAvatar" class="w-10 h-10 rounded-full bg-indigo-100 border border-indigo-200 flex items-center justify-center font-bold text-sm text-indigo-600">
                            U
                        </div>
                        <div>
                            <div id="mUserName" class="text-sm font-bold text-slate-900"></div>
                            <div id="mUserEmail" class="text-xs text-slate-500"></div>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-slate-200 flex items-center justify-between text-xs">
                        <span class="text-slate-500">Current Wallet Balance:</span>
                        <span id="mUserWallet" class="font-bold text-slate-900"></span>
                    </div>
                </div>

                <!-- Payout Details Box -->
                <div class="p-4 bg-indigo-50/50 border border-indigo-100 rounded-2xl space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600">Payment Destination</span>
                        <span id="mPayoutTypeBadge" class="text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-indigo-100 text-indigo-700">UPI</span>
                    </div>

                    <div id="mUpiSection" class="space-y-2">
                        <div class="flex items-center justify-between p-2.5 bg-white rounded-xl border border-indigo-100">
                            <div>
                                <span class="text-[10px] text-slate-400 block font-semibold">UPI ID / VPA</span>
                                <span id="mUpiId" class="font-mono text-xs font-bold text-slate-900"></span>
                            </div>
                            <button type="button" id="mCopyUpiBtn" class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-colors">
                                Copy UPI
                            </button>
                        </div>
                        <div class="text-xs text-slate-600 px-1">
                            Account Holder: <strong id="mUpiHolder"></strong>
                        </div>
                    </div>

                    <div id="mBankSection" class="space-y-2 hidden">
                        <div class="flex items-center justify-between p-2.5 bg-white rounded-xl border border-indigo-100">
                            <div>
                                <span class="text-[10px] text-slate-400 block font-semibold">Account Number</span>
                                <span id="mAccountNumber" class="font-mono text-xs font-bold text-slate-900"></span>
                            </div>
                            <button type="button" id="mCopyAccBtn" class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-colors">
                                Copy A/C
                            </button>
                        </div>

                        <div class="flex items-center justify-between p-2.5 bg-white rounded-xl border border-indigo-100">
                            <div>
                                <span class="text-[10px] text-slate-400 block font-semibold">IFSC Code</span>
                                <span id="mIfscCode" class="font-mono text-xs font-bold text-slate-900"></span>
                            </div>
                            <button type="button" id="mCopyIfscBtn" class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-colors">
                                Copy IFSC
                            </button>
                        </div>

                        <div class="text-xs text-slate-600 px-1 space-y-0.5">
                            <div>Bank Name: <strong id="mBankName"></strong></div>
                            <div>Account Holder: <strong id="mBankHolder"></strong></div>
                        </div>
                    </div>
                </div>

                <!-- Requested Amount Banner -->
                <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-between">
                    <span class="text-xs font-semibold text-emerald-800">Payout Amount to Transfer:</span>
                    <span id="mAmount" class="text-xl font-extrabold text-emerald-700"></span>
                </div>
            </div>

            <!-- Right Side: Admin Actions / Proof Screenshot -->
            <div class="flex flex-col justify-between space-y-4">
                <!-- If already has proof screenshot -->
                <div id="mExistingProofContainer" class="space-y-2 hidden">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-700 block">Payment Screenshot Proof (SS)</span>
                    <div class="rounded-2xl border border-slate-200 bg-slate-900 overflow-hidden shadow-sm max-h-56">
                        <img id="mProofImage" src="" alt="Payment Proof SS" class="w-full max-h-56 object-contain bg-slate-950">
                    </div>
                    <div id="mUtrDisplay" class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700"></div>
                </div>

                <!-- Admin Action Form (For Pending requests) -->
                <form id="mStatusForm" method="POST" action="" enctype="multipart/form-data" class="space-y-3 pt-2">
                    @csrf
                    <div id="mPendingFormFields" class="space-y-3">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Share Payment Screenshot Proof (SS Proof - Optional)
                            </label>
                            <input type="file" name="proof_image" accept="image/*" onchange="previewProofImage(event)"
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border border-slate-200 rounded-xl p-1 bg-slate-50">
                            <!-- Local Image Preview -->
                            <div id="mLocalPreviewWrapper" class="mt-2 hidden rounded-xl border border-slate-200 overflow-hidden max-h-40 bg-slate-900">
                                <img id="mLocalPreview" src="" class="w-full max-h-40 object-contain">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">UTR / Transaction Reference (Optional)</label>
                            <input type="text" name="utr_number" id="mUtrInput" placeholder="e.g. 423589104829 or UPI ref" 
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-xs focus:outline-none focus:border-indigo-600 focus:bg-white transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Admin Notes (Optional)</label>
                            <input type="text" name="admin_notes" id="mAdminNotes" placeholder="e.g. Transferred via PhonePe Business" 
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-xs focus:outline-none focus:border-indigo-600 focus:bg-white transition-all">
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div id="mPendingButtons" class="flex items-center gap-2 pt-2">
                        <button type="submit" name="status" value="approved" 
                                class="flex-1 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Mark Paid & Approve</span>
                        </button>

                        <button type="submit" name="status" value="rejected" onclick="return confirm('Are you sure you want to reject this withdrawal? The requested amount will be automatically refunded back to the user\'s wallet balance.');"
                                class="py-3 px-4 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 font-bold text-xs rounded-xl shadow-xs transition-colors">
                            Reject & Refund
                        </button>
                    </div>

                    <!-- Processed Notice (For Approved / Rejected) -->
                    <div id="mProcessedNotice" class="hidden p-3 rounded-xl text-xs font-medium space-y-1"></div>
                </form>
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

    function previewProofImage(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('mLocalPreview').src = e.target.result;
                document.getElementById('mLocalPreviewWrapper').classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            document.getElementById('mLocalPreviewWrapper').classList.add('hidden');
        }
    }

    function openWithdrawalModal(item) {
        // Reset preview
        document.getElementById('mLocalPreviewWrapper').classList.add('hidden');
        document.getElementById('mLocalPreview').src = '';

        // User info
        document.getElementById('mUserName').innerText = item.user ? item.user.name : 'Unknown User';
        document.getElementById('mUserEmail').innerText = item.user ? item.user.email : '';
        document.getElementById('mUserAvatar').innerText = item.user && item.user.name ? item.user.name.charAt(0).toUpperCase() : 'U';
        document.getElementById('mUserWallet').innerText = '₹' + (item.user ? parseFloat(item.user.wallet_balance || 0).toFixed(2) : '0.00');

        // Amount
        document.getElementById('mAmount').innerText = '₹' + parseFloat(item.amount).toFixed(2);

        // Payout Method details
        const isUpi = item.payout_type === 'upi';
        document.getElementById('mPayoutTypeBadge').innerText = isUpi ? 'UPI' : 'Bank Transfer';
        
        if (isUpi) {
            document.getElementById('mUpiSection').classList.remove('hidden');
            document.getElementById('mBankSection').classList.add('hidden');
            document.getElementById('mUpiId').innerText = item.upi_id || 'N/A';
            document.getElementById('mUpiHolder').innerText = item.account_holder_name || 'N/A';
            document.getElementById('mCopyUpiBtn').onclick = () => copyText(item.upi_id || '');
        } else {
            document.getElementById('mUpiSection').classList.add('hidden');
            document.getElementById('mBankSection').classList.remove('hidden');
            document.getElementById('mAccountNumber').innerText = item.account_number || 'N/A';
            document.getElementById('mIfscCode').innerText = item.ifsc_code || 'N/A';
            document.getElementById('mBankName').innerText = item.bank_name || 'N/A';
            document.getElementById('mBankHolder').innerText = item.account_holder_name || 'N/A';
            document.getElementById('mCopyAccBtn').onclick = () => copyText(item.account_number || '');
            document.getElementById('mCopyIfscBtn').onclick = () => copyText(item.ifsc_code || '');
        }

        // Existing Proof Image & UTR
        const proofContainer = document.getElementById('mExistingProofContainer');
        const proofImg = document.getElementById('mProofImage');
        const utrDisplay = document.getElementById('mUtrDisplay');

        if (item.proof_image || item.utr_number) {
            proofContainer.classList.remove('hidden');
            if (item.proof_image) {
                proofImg.src = item.proof_image;
                proofImg.parentElement.classList.remove('hidden');
            } else {
                proofImg.parentElement.classList.add('hidden');
            }
            let utrHtml = '';
            if (item.utr_number) utrHtml += '<strong>UTR Reference:</strong> ' + item.utr_number + '<br>';
            if (item.admin_notes) utrHtml += '<strong>Admin Note:</strong> ' + item.admin_notes;
            utrDisplay.innerHTML = utrHtml || 'No extra reference recorded.';
        } else {
            proofContainer.classList.add('hidden');
        }

        // Form Action & Fields
        const form = document.getElementById('mStatusForm');
        form.action = "/admin/withdrawals/" + item.id + "/status";

        const pendingFields = document.getElementById('mPendingFormFields');
        const pendingButtons = document.getElementById('mPendingButtons');
        const processedNotice = document.getElementById('mProcessedNotice');

        if (item.status === 'pending') {
            pendingFields.classList.remove('hidden');
            pendingButtons.classList.remove('hidden');
            processedNotice.classList.add('hidden');
            document.getElementById('mUtrInput').value = item.utr_number || '';
            document.getElementById('mAdminNotes').value = item.admin_notes || '';
        } else {
            pendingFields.classList.add('hidden');
            pendingButtons.classList.add('hidden');
            processedNotice.classList.remove('hidden');

            if (item.status === 'approved') {
                processedNotice.className = 'p-3 rounded-xl text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200';
                processedNotice.innerHTML = '✓ This withdrawal was marked as <strong>Paid & Approved</strong> on ' + (item.processed_at ? new Date(item.processed_at).toLocaleString() : 'N/A') + '.';
            } else {
                processedNotice.className = 'p-3 rounded-xl text-xs font-semibold bg-red-50 text-red-800 border border-red-200';
                processedNotice.innerHTML = '✕ This withdrawal was <strong>Rejected</strong> and refunded to user wallet balance.<br>Reason: ' + (item.admin_notes || 'No reason specified.');
            }
        }

        document.getElementById('withdrawalModal').classList.remove('hidden');
    }

    function closeWithdrawalModal() {
        document.getElementById('withdrawalModal').classList.add('hidden');
    }
</script>
@endsection
