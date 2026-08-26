@extends('layouts.admin')

@section('title', 'Conversions & Review Proofs')
@section('page-title', 'Conversions & Review Proofs')

@section('content')
<div class="space-y-6">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Recent Conversions & Submissions</h1>
            <p class="text-sm text-slate-500 mt-1">Review user screenshot proofs, verify completed app reviews, and approve reward payouts.</p>
        </div>
    </div>

    <!-- Filter Pills & Search -->
    <div class="flex flex-wrap items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.conversions') }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold border transition-all {{ !request('status') ? 'bg-indigo-50 text-indigo-600 border-indigo-200' : 'text-slate-600 border-transparent hover:text-slate-900' }}">
                All Submissions ({{ $totalConversions }})
            </a>
            <a href="{{ route('admin.conversions', ['status' => 'pending']) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold border transition-all {{ request('status') === 'pending' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'text-slate-600 border-transparent hover:text-slate-900' }}">
                Pending ({{ $pendingCount }})
            </a>
            <a href="{{ route('admin.conversions', ['status' => 'approved']) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold border transition-all {{ request('status') === 'approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'text-slate-600 border-transparent hover:text-slate-900' }}">
                Approved ({{ $approvedCount }})
            </a>
            <a href="{{ route('admin.conversions', ['status' => 'rejected']) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold border transition-all {{ request('status') === 'rejected' ? 'bg-red-50 text-red-700 border-red-200' : 'text-slate-600 border-transparent hover:text-slate-900' }}">
                Rejected ({{ $rejectedCount }})
            </a>
        </div>

        <form method="GET" action="{{ route('admin.conversions') }}" class="relative w-full sm:w-72">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search user or campaign..." 
                   class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 text-xs focus:outline-none focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 transition-all">
        </form>
    </div>

    <!-- Conversions Table Card -->
    <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Campaign</th>
                        <th class="px-6 py-4">Review Proof SS</th>
                        <th class="px-6 py-4">Reward Amount</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Submitted Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($participations as $item)
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

                            <!-- Campaign Column -->
                            <td class="px-6 py-4 max-w-xs">
                                <div class="font-bold text-slate-900 text-sm truncate" title="{{ $item->campaign->title ?? '' }}">
                                    {{ $item->campaign->title ?? 'Deleted Campaign' }}
                                </div>
                                @if ($item->campaign && $item->campaign->redirect_url)
                                    <a href="{{ route('campaign.redirect', $item->campaign->id) }}" target="_blank" 
                                       class="inline-flex items-center gap-1 text-[11px] font-semibold text-indigo-600 hover:underline mt-0.5">
                                        <span>Target Redirect Link</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                @endif
                            </td>

                            <!-- Proof Screenshot Thumbnail -->
                            <td class="px-6 py-4">
                                @if ($item->proof_image)
                                    <button type="button" onclick='openProofModal(@json($item))' class="group relative rounded-xl overflow-hidden border border-slate-200 block shadow-xs hover:border-indigo-500 transition-all cursor-pointer">
                                        <img src="{{ $item->proof_image }}" alt="Proof Screenshot" class="w-16 h-12 object-cover group-hover:scale-105 transition-transform duration-300">
                                        <span class="absolute inset-0 bg-slate-900/30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-white text-[10px] font-bold">
                                            View SS
                                        </span>
                                    </button>
                                @else
                                    <span class="text-xs text-slate-400 italic">No SS attached</span>
                                @endif
                            </td>

                            <!-- Reward Amount -->
                            <td class="px-6 py-4">
                                <span class="font-extrabold text-emerald-600 text-base">₹{{ number_format($item->reward_amount, 2) }}</span>
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
                                    {{ $item->status }}
                                </span>
                            </td>

                            <!-- Submitted Date -->
                            <td class="px-6 py-4 text-xs text-slate-500 font-medium">
                                {{ $item->submitted_at ? $item->submitted_at->format('M d, Y h:i A') : $item->created_at->format('M d, Y') }}
                            </td>

                            <!-- Actions (Approve / Reject / View) -->
                            <td class="px-6 py-4 text-right space-x-1">
                                <button type="button" onclick='openProofModal(@json($item))' 
                                        class="px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors">
                                    View Details
                                </button>
                                
                                @if ($item->status === 'pending')
                                    <form method="POST" action="{{ route('admin.conversions.status', $item->id) }}" class="inline-block">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white transition-colors shadow-xs">
                                            Approve
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.conversions.status', $item->id) }}" class="inline-block">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-red-600 hover:bg-red-700 text-white transition-colors shadow-xs">
                                            Reject
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm">
                                No conversion submissions found.
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
</div>

<!-- REVIEW SCREENSHOT PROOF LIGHTBOX MODAL -->
<div id="proofModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm hidden p-4 overflow-y-auto">
    <div class="bg-white border border-slate-200 rounded-3xl w-full max-w-3xl shadow-2xl p-6 sm:p-8 my-8 relative">
        <button type="button" onclick="closeProofModal()" class="absolute top-6 right-6 text-slate-400 hover:text-slate-800">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <h2 class="text-xl font-extrabold text-slate-900 mb-1">Review Screenshot Proof & Submission Details</h2>
        <p class="text-xs text-slate-500 mb-6">Inspect user submitted review screenshot proof and verify reward payout qualification.</p>

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
                            ✓ Approve & Reward ${{ '' }}
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
@endsection

@section('scripts')
<script>
    function openProofModal(item) {
        document.getElementById('modalProofImage').src = item.proof_image || '';
        document.getElementById('modalUserName').innerText = item.user ? item.user.name : 'Unknown User';
        document.getElementById('modalUserEmail').innerText = item.user ? item.user.email : '';
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
