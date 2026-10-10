@extends('layouts.admin')

@section('title', 'Bounty AI Users & Businesses')

@section('content')
<div class="space-y-6">
    <!-- Top Header Banner -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                    <svg class="w-3.5 h-3.5 mr-1 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 011.342 1.342l-.8 1.599 1.582 3.954H20a1 1 0 110 2h-1.323l-1.582 3.954.8 1.599a1 1 0 01-1.342 1.342l-1.599-.8-3.954 1.582V20a1 1 0 11-2 0v-1.323l-3.954-1.582-1.599.8a1 1 0 01-1.342-1.342l.8-1.599L1.323 12H0a1 1 0 110-2h1.323l1.582-3.954-.8-1.599a1 1 0 011.342-1.342l1.599.8L8.677 4.323V3a1 1 0 011-1z" />
                    </svg>
                    Bounty AI Suite
                </span>
                <span class="text-xs text-slate-400 font-medium">Mobile App Onboarding Flow</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Bounty AI Users</h1>
            <p class="text-sm text-slate-500 mt-1">
                Track and manage business onboarding registrations, selected growth goals, and detailed business profiles submitted through the app.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.marketing-goals') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl border border-slate-200 transition-all shadow-2xs">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span>Manage Growth Goals</span>
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Businesses -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Registered</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1">{{ number_format($totalProfiles) }}</h3>
                <p class="text-xs text-indigo-600 font-semibold mt-1">Bounty AI Profiles</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
        </div>

        <!-- Card 2: Fully Onboarded -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Step 2 Completed</p>
                <h3 class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($completedProfiles) }}</h3>
                <p class="text-xs text-emerald-600 font-semibold mt-1">Full Business Details Added</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Card 3: In Progress (Step 1 Goals) -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Step 1 Only</p>
                <h3 class="text-2xl font-black text-amber-500 mt-1">{{ number_format($inProgressProfiles) }}</h3>
                <p class="text-xs text-amber-600 font-semibold mt-1">Growth Goals Selected</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Card 4: Unique Cities -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Cities Covered</p>
                <h3 class="text-2xl font-black text-purple-600 mt-1">{{ number_format($uniqueCities) }}</h3>
                <p class="text-xs text-purple-600 font-semibold mt-1">Regional Markets</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('admin.bounty-ai-users') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            <!-- Search field -->
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Search Business / User / City</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search by business name, user name, phone, city, or email..."
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-hidden focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Onboarding Status</label>
                <select name="status" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-hidden focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Statuses</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Step 2 Completed (Full Info)</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Step 1 In Progress (Goals Only)</option>
                </select>
            </div>

            <!-- Business Type Filter & Buttons -->
            <div class="flex items-center gap-2">
                <div class="flex-1">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Category</label>
                    <select name="type" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-hidden focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>All Categories</option>
                        @foreach($businessTypes as $bType)
                            <option value="{{ $bType }}" {{ request('type') == $bType ? 'selected' : '' }}>{{ $bType }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="pt-5 flex gap-1">
                    <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl transition-all shadow-xs">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'status', 'type']))
                        <a href="{{ route('admin.bounty-ai-users') }}" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-sm rounded-xl transition-all flex items-center justify-center" title="Clear Filters">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Main Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-extrabold text-base text-slate-900">Registered Businesses</h3>
            <span class="text-xs text-slate-400 font-semibold">Showing {{ $profiles->firstItem() ?? 0 }} to {{ $profiles->lastItem() ?? 0 }} of {{ $profiles->total() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        <th class="py-3.5 px-6">Business & Owner</th>
                        <th class="py-3.5 px-6">Selected Growth Goals (Step 1)</th>
                        <th class="py-3.5 px-6">Category & Location (Step 2)</th>
                        <th class="py-3.5 px-6">Contact Info</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6">Registered</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($profiles as $profile)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <!-- Business & Owner -->
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-400 to-amber-600 text-slate-950 font-black text-sm flex items-center justify-center shadow-xs">
                                        {{ strtoupper(substr($profile->business_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-extrabold text-slate-900 leading-tight">
                                            {{ $profile->business_name }}
                                        </div>
                                        <div class="text-xs text-slate-500 mt-0.5 flex items-center gap-1.5">
                                            @if($profile->user)
                                                <span class="flex items-center gap-1"><svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg> {{ $profile->user->name }}</span>
                                                <span class="text-slate-300">&bull;</span>
                                                <span class="text-[10px] px-1.5 py-0.2 rounded-md bg-indigo-50 text-indigo-700 font-bold">UID #{{ $profile->user->id }}</span>
                                            @else
                                                <span class="text-slate-400 italic">Guest / App User</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Goals Selected -->
                            <td class="py-4 px-6">
                                @if(!empty($profile->selected_goals) && is_array($profile->selected_goals))
                                    <div class="flex flex-wrap gap-1.5 max-w-xs">
                                        @foreach(array_slice($profile->selected_goals, 0, 3) as $goalSlug)
                                            @php
                                                $goalTitle = $marketingGoalsMap[$goalSlug] ?? ucwords(str_replace(['_', '-'], ' ', $goalSlug));
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.8 rounded-lg text-xs font-semibold bg-amber-50 text-amber-900 border border-amber-200/70 shadow-2xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                                {{ $goalTitle }}
                                            </span>
                                        @endforeach
                                        @if(count($profile->selected_goals) > 3)
                                            <span class="text-[11px] font-bold text-slate-400 self-center">
                                                +{{ count($profile->selected_goals) - 3 }} more
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">No goals selected</span>
                                @endif
                            </td>

                            <!-- Category (Step 3) & Location -->
                            <td class="py-4 px-6">
                                <div>
                                    @if($profile->category)
                                        <div class="mb-1">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-xs font-extrabold bg-amber-50 text-amber-900 border border-amber-200 shadow-2xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                {{ $categoriesMap[$profile->category] ?? ucwords(str_replace('_', ' ', $profile->category)) }}
                                            </span>
                                        </div>
                                    @endif

                                    @if($profile->business_type)
                                        <span class="inline-block px-2 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200 mb-1">
                                            {{ $profile->business_type }}
                                        </span>
                                    @elseif(!$profile->category)
                                        <span class="text-xs text-slate-400 italic">&mdash;</span>
                                    @endif

                                    @if($profile->is_google_connected)
                                        <div class="mb-1">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                <svg class="w-2.5 h-2.5 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                                Google Verified
                                            </span>
                                        </div>
                                    @endif
                                    <div class="text-xs text-slate-600 flex items-center gap-1">
                                        @if($profile->city)
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            </svg>
                                            <span>{{ $profile->city }}, {{ $profile->state }}</span>
                                        @elseif($profile->address)
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            </svg>
                                            <span class="truncate max-w-[140px]" title="{{ $profile->address }}">{{ $profile->address }}</span>
                                        @else
                                            <span class="text-slate-400 italic text-[11px]">Pending Step 2</span>
                                        @endif
                                    </div>
                                    @if($profile->latitude && $profile->longitude)
                                        <a href="https://www.google.com/maps?q={{ $profile->latitude }},{{ $profile->longitude }}" target="_blank" class="inline-flex items-center gap-1 mt-1 text-[11px] font-semibold text-emerald-700 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 px-2 py-0.5 rounded border border-emerald-200 transition-colors shadow-2xs">
                                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /></svg>
                                            <span>GPS: {{ number_format($profile->latitude, 3) }}, {{ number_format($profile->longitude, 3) }}</span>
                                            @if($profile->is_gps_detected)
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" title="Auto GPS Detected"></span>
                                            @endif
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <!-- Contact Info -->
                            <td class="py-4 px-6">
                                <div class="text-xs space-y-0.5">
                                    @if($profile->phone_number)
                                        <div class="font-semibold text-slate-800 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                            <span>{{ $profile->phone_number }}</span>
                                        </div>
                                    @endif
                                    @if($profile->email)
                                        <div class="text-slate-500 truncate max-w-[150px] flex items-center gap-1.5" title="{{ $profile->email }}">
                                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            <span>{{ $profile->email }}</span>
                                        </div>
                                    @endif
                                    @if($profile->website)
                                        <a href="{{ $profile->website }}" target="_blank" class="text-indigo-600 hover:underline flex items-center gap-1 text-[11px] font-medium">
                                            <svg class="w-3.5 h-3.5 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                            <span>{{ parse_url($profile->website, PHP_URL_HOST) ?? $profile->website }}</span>
                                        </a>
                                    @endif
                                    @if(!$profile->phone_number && !$profile->email && !$profile->website)
                                        <span class="text-slate-400 italic text-xs">&mdash;</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Onboarding Status -->
                            <td class="py-4 px-6">
                                @if($profile->onboarding_status === 'completed' || $profile->current_step >= 2)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Fully Onboarded
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Step 1 Done
                                    </span>
                                @endif
                            </td>

                            <!-- Registered Date -->
                            <td class="py-4 px-6 text-xs text-slate-500">
                                <div class="font-medium text-slate-700">{{ $profile->created_at->format('M d, Y') }}</div>
                                <div class="text-[11px] text-slate-400">{{ $profile->created_at->format('h:i A') }}</div>
                            </td>

                            <!-- Action Buttons -->
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" 
                                            onclick="openDetailModal({{ json_encode($profile) }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs rounded-lg transition-colors border border-indigo-200/80">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>Details</span>
                                    </button>

                                    <form method="POST" action="{{ route('admin.bounty-ai-users.destroy', $profile->id) }}" onsubmit="return confirm('Are you sure you want to delete this Bounty AI profile?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete Profile">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                    </div>
                                    <p class="font-bold text-slate-700">No Bounty AI Businesses Found</p>
                                    <p class="text-xs text-slate-500">
                                        When businesses register or go through the onboarding screens on the mobile app, their profiles will appear here dynamically.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($profiles->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $profiles->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Complete Profile Details Modal -->
<div id="detailModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-slate-200">
        <!-- Modal Header -->
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white/95 backdrop-blur-xs z-10">
            <div class="flex items-center gap-3">
                <div id="modalAvatar" class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-400 to-amber-600 text-slate-950 font-black text-base flex items-center justify-center shadow-xs">
                    B
                </div>
                <div>
                    <h3 id="modalBusinessName" class="text-lg font-black text-slate-900 leading-tight">Business Name</h3>
                    <p id="modalStatusBadge" class="text-xs text-slate-500 mt-0.5">Status</p>
                </div>
            </div>
            <button type="button" onclick="closeDetailModal()" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Modal Body Content -->
        <div class="p-6 space-y-6">
            <!-- Section 1: Step 1 Goals Selected -->
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-5 h-5 rounded-full bg-amber-500 text-slate-950 font-bold text-xs flex items-center justify-center">1</span>
                    <h4 class="font-extrabold text-sm text-slate-900 uppercase tracking-wider">Step 1: Growth Goals ("What are you looking to achieve?")</h4>
                </div>
                <div id="modalGoalsContainer" class="flex flex-wrap gap-2 p-4 bg-amber-50/50 rounded-xl border border-amber-200/60">
                    <!-- Dynamic badges populated via JS -->
                </div>
            </div>

            <!-- Section 2: Step 2 Business Information -->
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-5 h-5 rounded-full bg-indigo-600 text-white font-bold text-xs flex items-center justify-center">2</span>
                    <h4 class="font-extrabold text-sm text-slate-900 uppercase tracking-wider">Step 2: Business Information Details</h4>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200/80 text-xs">
                    <div>
                        <span class="block text-slate-400 font-bold uppercase tracking-wider text-[10px]">Business Type / Category</span>
                        <span id="modalBusinessType" class="font-bold text-slate-800 text-sm mt-0.5 block">&mdash;</span>
                    </div>

                    <div>
                        <span class="block text-slate-400 font-bold uppercase tracking-wider text-[10px]">Contact Phone</span>
                        <span id="modalPhone" class="font-bold text-slate-800 text-sm mt-0.5 block">&mdash;</span>
                    </div>

                    <div>
                        <span class="block text-slate-400 font-bold uppercase tracking-wider text-[10px]">Business Email</span>
                        <span id="modalEmail" class="font-semibold text-slate-700 text-sm mt-0.5 block">&mdash;</span>
                    </div>

                    <div>
                        <span class="block text-slate-400 font-bold uppercase tracking-wider text-[10px]">Website Link</span>
                        <span id="modalWebsite" class="font-semibold text-indigo-600 text-sm mt-0.5 block truncate">&mdash;</span>
                    </div>

                    <div class="md:col-span-2">
                        <span class="block text-slate-400 font-bold uppercase tracking-wider text-[10px]">Street Address</span>
                        <span id="modalAddress" class="font-medium text-slate-800 mt-0.5 block">&mdash;</span>
                    </div>

                    <div>
                        <span class="block text-slate-400 font-bold uppercase tracking-wider text-[10px]">City & State</span>
                        <span id="modalCityState" class="font-bold text-slate-800 mt-0.5 block">&mdash;</span>
                    </div>

                    <div>
                        <span class="block text-slate-400 font-bold uppercase tracking-wider text-[10px]">Pincode</span>
                        <span id="modalPincode" class="font-bold text-slate-800 mt-0.5 block">&mdash;</span>
                    </div>
                </div>
            </div>

            <!-- Section 3: Operating Hours -->
            <div>
                <h4 class="font-extrabold text-xs text-slate-600 uppercase tracking-wider mb-2">Operating Hours & Schedule</h4>
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200/80 flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs">
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px] block">Working Days</span>
                        <div id="modalWorkingDays" class="flex flex-wrap gap-1 mt-1">
                            <!-- Populated via JS -->
                        </div>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px] block">Hours</span>
                        <span id="modalHours" class="font-extrabold text-slate-800 text-sm mt-1 block">09:00 AM - 09:00 PM</span>
                    </div>
                </div>
            </div>

            <!-- Section 3.5: Step 4 Storefront Map & GPS Location -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-emerald-600 text-white font-bold text-xs flex items-center justify-center">4</span>
                        <h4 class="font-extrabold text-sm text-slate-900 uppercase tracking-wider">Step 4: Storefront Map & GPS Coordinates</h4>
                    </div>
                    <div id="modalGpsDetectedBadge"></div>
                </div>

                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200/80 space-y-3 text-xs">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="md:col-span-2">
                            <span class="block text-slate-400 font-bold uppercase tracking-wider text-[10px]">Confirmed Storefront Address</span>
                            <span id="modalLocationAddress" class="font-bold text-slate-800 text-sm mt-0.5 block">&mdash;</span>
                        </div>
                        <div>
                            <span class="block text-slate-400 font-bold uppercase tracking-wider text-[10px]">Landmark (Optional)</span>
                            <span id="modalLandmark" class="font-semibold text-slate-700 text-sm mt-0.5 block">&mdash;</span>
                        </div>
                        <div>
                            <span class="block text-slate-400 font-bold uppercase tracking-wider text-[10px]">Pincode</span>
                            <span id="modalLocationPincode" class="font-bold text-slate-800 text-sm mt-0.5 block">&mdash;</span>
                        </div>
                        <div class="md:col-span-2">
                            <span class="block text-slate-400 font-bold uppercase tracking-wider text-[10px]">Exact Coordinates (Lat, Long)</span>
                            <span id="modalCoords" class="font-mono font-bold text-slate-800 text-sm mt-0.5 block">&mdash;</span>
                        </div>
                    </div>

                    <!-- Map Action Button -->
                    <div id="modalMapActionContainer" class="pt-2 border-t border-slate-200/70 flex items-center justify-between">
                        <span class="text-[11px] text-slate-500">Live coordinates on Google Maps</span>
                        <a id="modalMapLink" href="#" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-colors shadow-2xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                            <span>Open in Google Maps</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Section 3.6: Step 5 & 6 Google Business Profile -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-slate-400 font-extrabold uppercase tracking-wider text-[11px] block">Step 5 & 6: Google Business Profile</span>
                    <span id="modalGoogleConnectedBadge"></span>
                </div>
                <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-200/80 space-y-2.5 text-xs">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <span class="block text-slate-400 font-bold uppercase tracking-wider text-[10px]">Connected Google Account</span>
                            <span id="modalGoogleEmail" class="font-bold text-slate-800 text-sm mt-0.5 block">&mdash;</span>
                        </div>
                        <div>
                            <span class="block text-slate-400 font-bold uppercase tracking-wider text-[10px]">Linked Maps Listing</span>
                            <span id="modalGoogleListingTitle" class="font-bold text-slate-800 text-sm mt-0.5 block">&mdash;</span>
                        </div>
                        <div class="md:col-span-2 flex items-center gap-4">
                            <div>
                                <span class="block text-slate-400 font-bold uppercase tracking-wider text-[10px]">Google Rating</span>
                                <span id="modalGoogleRating" class="font-bold text-amber-600 text-sm mt-0.5 flex items-center gap-1">&mdash;</span>
                            </div>
                            <div>
                                <span class="block text-slate-400 font-bold uppercase tracking-wider text-[10px]">Total Google Reviews</span>
                                <span id="modalGoogleReviews" class="font-bold text-slate-700 text-sm mt-0.5 block">&mdash;</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Associated User Account -->
            <div id="modalUserSection" class="p-4 bg-indigo-50/60 rounded-xl border border-indigo-100 text-xs">
                <span class="text-indigo-900 font-extrabold uppercase text-[10px] block mb-1">Linked App Account</span>
                <div class="flex items-center justify-between">
                    <div>
                        <span id="modalUserName" class="font-extrabold text-indigo-950 text-sm block">User Name</span>
                        <span id="modalUserEmail" class="text-indigo-700 block mt-0.5">user@example.com</span>
                    </div>
                    <span id="modalUserIdBadge" class="px-2 py-1 rounded-md bg-indigo-200/70 text-indigo-900 font-bold text-xs">
                        UID #1
                    </span>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2 rounded-b-2xl">
            <button type="button" onclick="closeDetailModal()" class="px-5 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-xl transition-colors">
                Close
            </button>
        </div>
    </div>
</div>

<script>
    const goalsMap = @json($marketingGoalsMap);

    function openDetailModal(profile) {
        document.getElementById('modalBusinessName').textContent = profile.business_name || 'Business Details';
        document.getElementById('modalAvatar').textContent = (profile.business_name ? profile.business_name.charAt(0) : 'B').toUpperCase();
        
        // Status Badge
        const isComplete = profile.onboarding_status === 'completed' || profile.current_step >= 2;
        const statusBadge = document.getElementById('modalStatusBadge');
        if (isComplete) {
            statusBadge.innerHTML = '<span class="inline-flex items-center gap-1 text-emerald-600 font-bold"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Fully Onboarded</span>';
        } else {
            statusBadge.innerHTML = '<span class="inline-flex items-center gap-1 text-amber-600 font-bold"><span class="w-2 h-2 rounded-full bg-amber-500"></span> In Progress (Step 1 Goals Selected)</span>';
        }

        // Goals
        const goalsContainer = document.getElementById('modalGoalsContainer');
        goalsContainer.innerHTML = '';
        if (profile.selected_goals && profile.selected_goals.length > 0) {
            profile.selected_goals.forEach(slug => {
                const label = goalsMap[slug] || slug.replace(/[_-]/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                const badge = document.createElement('span');
                badge.className = 'px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-100 text-amber-950 border border-amber-300 flex items-center gap-1.5 shadow-2xs';
                badge.innerHTML = '<svg class="w-3.5 h-3.5 text-amber-600 inline shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg> <span>' + label + '</span>';
                goalsContainer.appendChild(badge);
            });
        } else {
            goalsContainer.innerHTML = '<span class="text-xs text-slate-400 italic">No goals recorded</span>';
        }

        // Business Info
        document.getElementById('modalBusinessType').textContent = profile.business_type || '-';
        document.getElementById('modalPhone').textContent = profile.phone_number || '-';
        document.getElementById('modalEmail').textContent = profile.email || '-';
        
        const websiteEl = document.getElementById('modalWebsite');
        if (profile.website) {
            websiteEl.innerHTML = `<a href="${profile.website}" target="_blank" class="hover:underline flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg> ${profile.website}</a>`;
        } else {
            websiteEl.textContent = '&mdash;';
        }

        document.getElementById('modalAddress').textContent = profile.address || '-';
        document.getElementById('modalCityState').textContent = (profile.city ? profile.city + ', ' : '') + (profile.state || '-');
        document.getElementById('modalPincode').textContent = profile.pincode || '-';
        // Step 4 Location
        const locAddrEl = document.getElementById('modalLocationAddress');
        if (locAddrEl) locAddrEl.textContent = profile.address || '-';

        const landmarkEl = document.getElementById('modalLandmark');
        if (landmarkEl) landmarkEl.textContent = profile.landmark || '-';

        const pincodeEl = document.getElementById('modalLocationPincode');
        if (pincodeEl) pincodeEl.textContent = profile.pincode || '-';
        
        const coordsEl = document.getElementById('modalCoords');
        const mapLinkEl = document.getElementById('modalMapLink');
        const mapContainer = document.getElementById('modalMapActionContainer');
        const gpsBadge = document.getElementById('modalGpsDetectedBadge');

        if (profile.latitude && profile.longitude) {
            if (coordsEl) coordsEl.textContent = `${Number(profile.latitude).toFixed(6)}, ${Number(profile.longitude).toFixed(6)}`;
            if (mapLinkEl) mapLinkEl.href = `https://www.google.com/maps?q=${profile.latitude},${profile.longitude}`;
            if (mapContainer) mapContainer.classList.remove('hidden');
        } else {
            if (coordsEl) coordsEl.textContent = 'Not pinned yet';
            if (mapContainer) mapContainer.classList.add('hidden');
        }

        if (gpsBadge) {
            if (profile.is_gps_detected) {
                gpsBadge.innerHTML = '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Auto-GPS Verified</span>';
            } else if (profile.latitude && profile.longitude) {
                gpsBadge.innerHTML = '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">Manual Pin</span>';
            } else {
                gpsBadge.innerHTML = '';
            }
        }


        // Hours & Working Days
        const daysContainer = document.getElementById('modalWorkingDays');
        daysContainer.innerHTML = '';
        const allDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        const activeDays = profile.working_days || [];
        allDays.forEach(d => {
            const span = document.createElement('span');
            const isActive = activeDays.includes(d);
            span.className = `px-2 py-0.5 rounded text-[11px] font-bold ${isActive ? 'bg-amber-500 text-slate-950' : 'bg-slate-200 text-slate-400'}`;
            span.textContent = d;
            daysContainer.appendChild(span);
        });

        if (profile.is_24_hours) {
            document.getElementById('modalHours').textContent = '24 Hours Open (Always)';
        } else if (profile.opening_time && profile.closing_time) {
            document.getElementById('modalHours').textContent = profile.opening_time + ' - ' + profile.closing_time;
        } else {
            document.getElementById('modalHours').textContent = 'Standard Hours';
        }

        // User Account
        const userSection = document.getElementById('modalUserSection');
        if (profile.user) {
            userSection.classList.remove('hidden');
            document.getElementById('modalUserName').textContent = profile.user.name;
            document.getElementById('modalUserEmail').textContent = profile.user.email;
            document.getElementById('modalUserIdBadge').textContent = 'UID #' + profile.user.id + ' (' + profile.user.role + ')';
        } else {
            userSection.classList.add('hidden');
        }

        document.getElementById('detailModal').classList.remove('hidden');
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }

    // Close on backdrop click
    document.getElementById('detailModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDetailModal();
        }
    });
</script>
@endsection