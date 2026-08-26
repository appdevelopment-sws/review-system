@extends('layouts.admin')

@section('title', 'Campaigns Management')
@section('page-title', 'Campaigns Management')

@section('content')
<div class="space-y-6">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Campaigns & Rewards</h1>
            <p class="text-sm text-slate-500 mt-1">Create participation campaigns, set reward payouts per user, upload media, set destination redirect URLs, and format descriptions with CKEditor.</p>
        </div>

        <button type="button" onclick="openCreateModal()" 
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white text-sm font-semibold rounded-xl shadow-md shadow-indigo-600/20 transition-all cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Create Campaign</span>
        </button>
    </div>

    <!-- Filter Pills -->
    <div class="flex flex-wrap items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.campaigns') }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold border transition-all {{ !request('status') && !request('category_id') ? 'bg-indigo-50 text-indigo-600 border-indigo-200' : 'text-slate-600 border-transparent hover:text-slate-900' }}">
                All Campaigns ({{ $totalCampaigns }})
            </a>
            <a href="{{ route('admin.campaigns', array_merge(request()->query(), ['status' => 'active'])) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold border transition-all {{ request('status') === 'active' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'text-slate-600 border-transparent hover:text-slate-900' }}">
                Active ({{ $activeCount }})
            </a>
            <a href="{{ route('admin.campaigns', array_merge(request()->query(), ['status' => 'paused'])) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold border transition-all {{ request('status') === 'paused' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'text-slate-600 border-transparent hover:text-slate-900' }}">
                Paused ({{ $pausedCount }})
            </a>
            <a href="{{ route('admin.campaigns', array_merge(request()->query(), ['status' => 'completed'])) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold border transition-all {{ request('status') === 'completed' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'text-slate-600 border-transparent hover:text-slate-900' }}">
                Completed ({{ $completedCount }})
            </a>
        </div>

        <div class="flex items-center gap-3 w-full sm:w-auto">
            @if(count($categories) > 0)
                <form method="GET" action="{{ route('admin.campaigns') }}" class="inline-block">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    <select name="category_id" onchange="this.form.submit()" 
                            class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs font-semibold focus:outline-none focus:bg-white focus:border-indigo-600 cursor-pointer">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                📁 {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif

            <form method="GET" action="{{ route('admin.campaigns') }}" class="relative w-full sm:w-64">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                @if(request('category_id'))
                    <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                @endif
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search campaigns..." 
                       class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 text-xs focus:outline-none focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 transition-all">
            </form>
        </div>
    </div>

    <!-- Campaigns Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($campaigns as $campaign)
            <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden flex flex-col hover:border-slate-300 hover:shadow-md transition-all shadow-xs group">
                
                <!-- Media Thumbnail Header -->
                <div class="h-44 bg-slate-100 relative overflow-hidden flex items-center justify-center border-b border-slate-100">
                    @if ($campaign->media_url && $campaign->media_type === 'image')
                        <img src="{{ $campaign->media_url }}" alt="{{ $campaign->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @elseif ($campaign->media_url && $campaign->media_type === 'video')
                        <video src="{{ $campaign->media_url }}" class="w-full h-full object-cover" muted loop autoPlay></video>
                        <span class="absolute top-3 right-3 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-600 text-white shadow-sm">Video</span>
                    @else
                        <div class="w-16 h-16 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                        </div>
                    @endif

                    <!-- Status & Category Badges -->
                    <div class="absolute top-3 left-3 flex flex-wrap items-center gap-1.5 z-10">
                        <span class="px-3 py-1 rounded-full text-xs font-bold capitalize border shadow-xs backdrop-blur-md
                            {{ $campaign->status === 'active' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : '' }}
                            {{ $campaign->status === 'paused' ? 'bg-amber-50 text-amber-700 border-amber-200' : '' }}
                            {{ $campaign->status === 'completed' ? 'bg-blue-50 text-blue-700 border-blue-200' : '' }}
                            {{ $campaign->status === 'draft' ? 'bg-slate-100 text-slate-700 border-slate-200' : '' }}">
                            {{ $campaign->status }}
                        </span>

                        @if ($campaign->category)
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-white/95 text-indigo-700 shadow-xs border border-indigo-200/80 backdrop-blur-md flex items-center gap-1">
                                <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                <span>{{ $campaign->category->name }}</span>
                            </span>
                        @endif
                    </div>

                    <!-- Quick Action Buttons (Edit / Report / Delete) -->
                    <div class="absolute top-3 right-3 flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="{{ route('admin.campaigns.report', $campaign->id) }}" 
                           class="p-2 rounded-xl bg-white/90 hover:bg-purple-600 text-slate-700 hover:text-white backdrop-blur-md transition-colors shadow-md border border-slate-200" title="Analytics & Report">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </a>
                        <button type="button" onclick='openEditModal(@json($campaign))' 
                                class="p-2 rounded-xl bg-white/90 hover:bg-indigo-600 text-slate-700 hover:text-white backdrop-blur-md transition-colors shadow-md border border-slate-200" title="Edit Campaign">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <form method="POST" action="{{ route('admin.campaigns.destroy', $campaign->id) }}" onsubmit="return confirm('Are you sure you want to delete this campaign?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 rounded-xl bg-white/90 hover:bg-red-600 text-slate-700 hover:text-white backdrop-blur-md transition-colors shadow-md border border-slate-200" title="Delete Campaign">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Card Content -->
                <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900 line-clamp-1 group-hover:text-indigo-600 transition-colors">{{ $campaign->title }}</h2>
                        
                        <!-- Rich Description Snippet -->
                        <div class="text-xs text-slate-500 mt-2 line-clamp-2 leading-relaxed">
                            {!! strip_tags($campaign->description) !!}
                        </div>

                        <!-- Redirect Destination Link & Report Button -->
                        <div class="mt-3 pt-2 flex items-center justify-between gap-2">
                            @if ($campaign->redirect_url)
                                <a href="{{ route('campaign.redirect', $campaign->id) }}" target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    <span>Target Link</span>
                                </a>
                            @else
                                <span class="text-[11px] text-slate-400">No redirect URL</span>
                            @endif

                            <a href="{{ route('admin.campaigns.report', $campaign->id) }}" 
                               class="inline-flex items-center gap-1 text-xs font-bold text-purple-600 hover:text-purple-700">
                                <span>Report & Stats 📊</span>
                            </a>
                        </div>
                    </div>

                    <div class="space-y-3 pt-3 border-t border-slate-100">
                        <!-- Stats Row: Clicks & Reward Amount -->
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500 font-medium">Clicks: <strong class="text-slate-900 font-bold">{{ $campaign->clicks_count }}</strong></span>
                            <span class="text-slate-500 font-medium">Conv Rate: <strong class="text-purple-600 font-bold">{{ $campaign->conversionRate() }}%</strong></span>
                        </div>

                        <!-- Reward Amount & Limit -->
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Reward / User</span>
                                <span class="text-lg font-extrabold text-emerald-600">₹{{ number_format($campaign->reward_amount, 2) }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Avail Limit</span>
                                <span class="text-sm font-bold text-slate-900">{{ $campaign->participants_count }} / {{ $campaign->participant_limit }}</span>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200/60">
                                <div class="h-2 rounded-full transition-all duration-500 {{ $campaign->isFull() ? 'bg-amber-500' : 'bg-gradient-to-r from-indigo-500 to-purple-600' }}" 
                                     style="width: {{ $campaign->progressPercentage() }}%"></div>
                            </div>
                            <div class="flex items-center justify-between text-[10px] text-slate-500 mt-1">
                                <span>{{ $campaign->progressPercentage() }}% claimed</span>
                                @if($campaign->isFull())
                                    <span class="text-amber-600 font-bold">LIMIT REACHED</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3 text-slate-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                    </svg>
                </div>
                <h3 class="text-base font-extrabold text-slate-900">No campaigns found</h3>
                <p class="text-xs text-slate-500 mt-1">Click "Create Campaign" to add your first reward campaign.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($campaigns->hasPages())
        <div class="p-4 border border-slate-200/80 rounded-2xl bg-white shadow-xs">
            {{ $campaigns->links() }}
        </div>
    @endif
</div>

<!-- CREATE / EDIT CAMPAIGN MODAL WITH DIRECT FILE UPLOAD & REDIRECT URL -->
<div id="campaignModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm hidden" onclick="if(event.target === this) closeCampaignModal()">
    <div class="min-h-full flex items-center justify-center p-4 sm:p-6 lg:p-8">
        <div class="bg-white border border-slate-200/90 rounded-3xl w-full max-w-5xl shadow-2xl p-6 sm:p-8 relative my-8" onclick="event.stopPropagation()">
            <!-- Close Button -->
            <button type="button" onclick="closeCampaignModal()" class="absolute top-6 right-6 p-2 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Modal Header -->
            <div class="pr-12 mb-6 border-b border-slate-100 pb-4">
                <h2 id="modalTitle" class="text-xl font-extrabold text-slate-900 tracking-tight mb-1">Create New Campaign</h2>
                <p id="modalSubtitle" class="text-xs text-slate-500">Upload media file, set destination redirect URL, reward limits, and format rich description with CKEditor.</p>
            </div>

            <form method="POST" action="{{ route('admin.campaigns.store') }}" id="campaignForm" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div id="methodContainer"></div>

                <!-- 2-Column Responsive Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    
                    <!-- Left Column: Core Campaign Settings, URLs, Limits, Upload & Dates -->
                    <div class="lg:col-span-6 space-y-4">
                        <!-- Campaign Title & Category Selector -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Campaign Title</label>
                                <input type="text" name="title" id="formTitle" required placeholder="e.g. Google Reviews Task" 
                                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Category</label>
                                <select name="category_id" id="formCategoryId" 
                                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-indigo-600">
                                    <option value="">-- No Category (General) --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Main Redirect URL (Destination Link) -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Campaign Target / Redirect URL (Main Link)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                    </svg>
                                </span>
                                <input type="url" name="redirect_url" id="formRedirectUrl" placeholder="https://example.com/campaign-landing-page" 
                                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                            </div>
                            <p class="text-[11px] text-slate-500 mt-1">Users clicking on this campaign will be redirected to this link.</p>
                        </div>

                        <!-- Reward Amount & Participant Limit -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Reward Amount (₹ / user)</label>
                                <input type="number" step="0.01" min="0" name="reward_amount" id="formRewardAmount" required placeholder="15.00" 
                                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Participant Limit (Max Users)</label>
                                <input type="number" min="1" name="participant_limit" id="formParticipantLimit" required placeholder="100" 
                                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                            </div>
                        </div>

                        <!-- Direct Media Upload Section -->
                        <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-2xl">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Media Type</label>
                                    <select name="media_type" id="formMediaType" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:border-indigo-600">
                                        <option value="image">Image</option>
                                        <option value="video">Video</option>
                                        <option value="none">None</option>
                                    </select>
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Upload Media File (Image / Video)</label>
                                    <input type="file" name="media_file" id="formMediaFile" accept="image/*,video/*" 
                                           class="w-full text-xs font-semibold text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 cursor-pointer">
                                </div>
                            </div>
                        </div>

                        <!-- Status & Dates -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Status</label>
                                <select name="status" id="formStatus" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-indigo-600">
                                    <option value="active">Active</option>
                                    <option value="paused">Paused</option>
                                    <option value="completed">Completed</option>
                                    <option value="draft">Draft</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Start Date</label>
                                <input type="date" name="start_date" id="formStartDate" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-indigo-600">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">End Date</label>
                                <input type="date" name="end_date" id="formEndDate" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-indigo-600">
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Rich Description (CKEditor) -->
                    <div class="lg:col-span-6 flex flex-col h-full">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Campaign Description (CKEditor Rich Text)</label>
                        <div class="flex-1 flex flex-col campaign-editor-wrapper">
                            <textarea name="description" id="campaign_description_editor" class="w-full hidden"></textarea>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-2">Detail instructions, eligibility rules, and steps participants need to follow.</p>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeCampaignModal()" class="px-4 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 text-sm font-semibold transition-colors cursor-pointer">Cancel</button>
                    <button type="submit" id="submitBtn" class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-semibold text-sm rounded-xl shadow-md shadow-indigo-600/20 transition-all cursor-pointer">
                        Publish Campaign
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .campaign-editor-wrapper .ck.ck-editor__main > .ck-editor__editable {
        min-height: 290px !important;
        max-height: 380px !important;
        overflow-y: auto !important;
    }
</style>
@endsection

@section('scripts')
<script>
    let campaignCkEditor = null;

    document.addEventListener('DOMContentLoaded', function() {
        ClassicEditor
            .create(document.querySelector('#campaign_description_editor'), {
                toolbar: {
                    items: [
                        'heading', '|',
                        'bold', 'italic', 'underline', 'strikethrough', 'link', '|',
                        'bulletedList', 'numberedList', 'blockQuote', '|',
                        'insertTable', 'undo', 'redo'
                    ]
                },
                placeholder: 'Enter rich campaign details, rules, instructions, and reward eligibility criteria...'
            })
            .then(editor => {
                campaignCkEditor = editor;
            })
            .catch(error => {
                console.error('CKEditor initialization error:', error);
            });

        // Form submit sync
        document.getElementById('campaignForm').addEventListener('submit', function() {
            if (campaignCkEditor) {
                document.querySelector('#campaign_description_editor').value = campaignCkEditor.getData();
            }
        });
    });

    function openCreateModal() {
        document.getElementById('modalTitle').innerText = 'Create New Campaign';
        document.getElementById('modalSubtitle').innerText = 'Upload media file, set destination redirect URL, reward limits, and format rich description with CKEditor.';
        document.getElementById('submitBtn').innerText = 'Publish Campaign';
        
        const form = document.getElementById('campaignForm');
        form.action = "{{ route('admin.campaigns.store') }}";
        document.getElementById('methodContainer').innerHTML = '';
        
        form.reset();
        document.getElementById('formCategoryId').value = '';
        if (campaignCkEditor) {
            campaignCkEditor.setData('<p>Welcome to our new campaign! Please follow the steps below to participate and claim your reward.</p>');
        }
        
        document.getElementById('campaignModal').classList.remove('hidden');
    }

    function openEditModal(campaign) {
        document.getElementById('modalTitle').innerText = 'Edit Campaign: ' + campaign.title;
        document.getElementById('modalSubtitle').innerText = 'Update campaign details, upload new media file, edit redirect URL, and edit rich formatted content.';
        document.getElementById('submitBtn').innerText = 'Save Changes';
        
        const form = document.getElementById('campaignForm');
        form.action = "/admin/campaigns/" + campaign.id;
        document.getElementById('methodContainer').innerHTML = '<input type="hidden" name="_method" value="PUT">';

        document.getElementById('formTitle').value = campaign.title || '';
        document.getElementById('formCategoryId').value = campaign.category_id || '';
        document.getElementById('formRedirectUrl').value = campaign.redirect_url || '';
        document.getElementById('formRewardAmount').value = campaign.reward_amount || '';
        document.getElementById('formParticipantLimit').value = campaign.participant_limit || '';
        document.getElementById('formMediaType').value = campaign.media_type || 'image';
        document.getElementById('formStatus').value = campaign.status || 'active';
        
        if (campaign.start_date) {
            document.getElementById('formStartDate').value = campaign.start_date.split('T')[0];
        } else {
            document.getElementById('formStartDate').value = '';
        }

        if (campaign.end_date) {
            document.getElementById('formEndDate').value = campaign.end_date.split('T')[0];
        } else {
            document.getElementById('formEndDate').value = '';
        }

        if (campaignCkEditor) {
            campaignCkEditor.setData(campaign.description || '');
        }

        document.getElementById('campaignModal').classList.remove('hidden');
    }

    function closeCampaignModal() {
        document.getElementById('campaignModal').classList.add('hidden');
    }
</script>
@endsection
