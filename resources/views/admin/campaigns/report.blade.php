@extends('layouts.admin')

@section('title', 'Campaign Report & Analytics')
@section('page-title', 'Campaign Analytics')

@section('content')
<div class="space-y-8">
    <!-- Top Header Navigation & Back Button -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.campaigns') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-slate-900 shadow-xs transition-colors" title="Back to Campaigns">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $campaign->title }}</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Comprehensive performance report, clicks, conversion rates, and submitted review screenshots.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <span class="px-3.5 py-1.5 rounded-full text-xs font-bold capitalize border shadow-xs
                {{ $campaign->status === 'active' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : '' }}
                {{ $campaign->status === 'paused' ? 'bg-amber-50 text-amber-700 border-amber-200' : '' }}
                {{ $campaign->status === 'completed' ? 'bg-blue-50 text-blue-700 border-blue-200' : '' }}">
                Status: {{ $campaign->status }}
            </span>

            @if ($campaign->redirect_url)
                <a href="{{ route('campaign.redirect', $campaign->id) }}" target="_blank" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md transition-colors">
                    <span>Visit Target Link ↗</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Analytics Key Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Metric 1: Total Clicks -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Clicks</span>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ number_format($totalClicks) }}</span>
                <span class="text-xs text-slate-500 font-medium">Redirect Visits</span>
            </div>
        </div>

        <!-- Metric 2: Total Submissions -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Submissions (SS)</span>
                <div class="w-10 h-10 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ number_format($totalSubmissions) }}</span>
                <span class="text-xs text-slate-500 font-medium">({{ $approvedSubmissions }} Approved, {{ $pendingSubmissions }} Pending)</span>
            </div>
        </div>

        <!-- Metric 3: Conversion Rate -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Conversion Rate</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $conversionRate }}%</span>
                <span class="text-xs text-emerald-600 font-bold">Submissions / Click</span>
            </div>
        </div>

        <!-- Metric 4: Total Paid Out -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Paid Out</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-emerald-600">₹{{ number_format($totalPaidOut, 2) }}</span>
                <span class="text-xs text-slate-500 font-medium">of ₹{{ number_format($campaign->reward_amount * $campaign->participant_limit, 2) }} Pool</span>
            </div>
        </div>
    </div>

    <!-- Campaign Description & Media Details Card -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-xs grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-3">
            <h2 class="text-base font-extrabold text-slate-900">Campaign Overview & Description</h2>
            <div class="text-sm text-slate-700 leading-relaxed prose max-w-none">
                {!! $campaign->description !!}
            </div>
        </div>

        <div class="space-y-4 border-l border-slate-100 pl-6">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Reward per User</span>
                <span class="text-xl font-extrabold text-emerald-600">₹{{ number_format($campaign->reward_amount, 2) }}</span>
            </div>

            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Participant Limit</span>
                <span class="text-sm font-bold text-slate-900">{{ $campaign->participants_count }} / {{ $campaign->participant_limit }} Availed</span>
            </div>

            @if ($campaign->redirect_url)
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Target Landing URL</span>
                    <a href="{{ $campaign->redirect_url }}" target="_blank" class="text-xs font-bold text-indigo-600 truncate block hover:underline">
                        {{ $campaign->redirect_url }} ↗
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Campaign Submissions & Review Screenshots List -->
    <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-xs">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-base font-extrabold text-slate-900">Submitted Review Screenshots for this Campaign</h2>
            <span class="text-xs font-bold text-slate-500">{{ $campaign->participations->count() }} Total Submissions</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Review Screenshot Proof</th>
                        <th class="px-6 py-4">User Feedback</th>
                        <th class="px-6 py-4">Reward</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Date Submitted</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($campaign->participations as $item)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $item->user->name ?? 'User' }}</div>
                                <div class="text-xs text-slate-500">{{ $item->user->email ?? 'N/A' }}</div>
                            </td>

                            <td class="px-6 py-4">
                                @if ($item->proof_image)
                                    <a href="{{ $item->proof_image }}" target="_blank" class="inline-block rounded-xl overflow-hidden border border-slate-200 shadow-xs hover:border-indigo-500 transition-all">
                                        <img src="{{ $item->proof_image }}" alt="Proof Screenshot" class="w-16 h-12 object-cover">
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400 italic">No SS attached</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 max-w-xs">
                                <div class="text-xs text-slate-600 line-clamp-2 italic">"{{ $item->review_text ?? 'No notes provided' }}"</div>
                            </td>

                            <td class="px-6 py-4 font-extrabold text-emerald-600">
                                ₹{{ number_format($item->reward_amount, 2) }}
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold capitalize border
                                    {{ $item->status === 'approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : '' }}
                                    {{ $item->status === 'pending' ? 'bg-amber-50 text-amber-700 border-amber-200' : '' }}
                                    {{ $item->status === 'rejected' ? 'bg-red-50 text-red-700 border-red-200' : '' }}">
                                    {{ $item->status }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-xs text-slate-500 font-medium">
                                {{ $item->submitted_at ? $item->submitted_at->format('M d, Y h:i A') : $item->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">
                                No user submissions recorded for this campaign yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
