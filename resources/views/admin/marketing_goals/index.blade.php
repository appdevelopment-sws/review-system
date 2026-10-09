@extends('layouts.admin')

@section('title', 'Growth & Marketing Goals')
@section('page-title', 'Growth & Marketing Goals')

@section('content')
<div class="space-y-6">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-600 flex items-center justify-center text-white shadow-md shadow-amber-500/25">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Onboarding Goals</h1>
                    <p class="text-xs text-slate-500">Manage the dynamic goals shown under <strong>"What are you looking to achieve?"</strong> in the mobile app onboarding.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" onclick="openCreateGoalModal()" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white text-sm font-bold rounded-xl shadow-md shadow-amber-500/20 transition-all cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Add New Goal</span>
            </button>
        </div>
    </div>

    <!-- Overview Stats & Search Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Total Goals</span>
                <span class="text-2xl font-extrabold text-slate-900">{{ $totalGoals }}</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Active in App</span>
                <span class="text-2xl font-extrabold text-emerald-600">{{ $activeGoals }}</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Inactive / Hidden</span>
                <span class="text-2xl font-extrabold text-slate-600">{{ $inactiveGoals }}</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center">
            <form method="GET" action="{{ route('admin.marketing-goals') }}" class="relative w-full">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search goals..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 text-xs focus:outline-none focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all">
            </form>
        </div>
    </div>

    <!-- Goals Card Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse ($goals as $goal)
            <div class="bg-white border {{ $goal->is_active ? 'border-slate-200/90' : 'border-slate-200/60 opacity-75' }} rounded-2xl p-5 flex flex-col justify-between hover:shadow-lg transition-all duration-200 group relative">
                <!-- Top Header: Order badge & Actions -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 font-extrabold text-[11px] border border-slate-200">
                                #{{ $goal->sort_order }}
                            </span>
                            @if($goal->badge_text)
                                <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 font-bold text-[10px] border border-amber-200">
                                    {{ $goal->badge_text }}
                                </span>
                            @endif
                        </div>

                        <!-- Active Toggle Switch -->
                        <form method="POST" action="{{ route('admin.marketing-goals.toggle', $goal->id) }}" class="inline">
                            @csrf
                            <button type="submit" title="{{ $goal->is_active ? 'Click to deactivate' : 'Click to activate' }}"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border transition-colors cursor-pointer {{ $goal->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 border-slate-200 hover:bg-slate-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $goal->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                                <span>{{ $goal->is_active ? 'Active' : 'Inactive' }}</span>
                            </button>
                        </form>
                    </div>

                    <!-- Mini Mobile Card Replica Preview -->
                    <div class="mb-4 p-4 rounded-xl border border-slate-200 bg-slate-50/60 transition-all group-hover:border-amber-400 group-hover:bg-amber-50/30">
                        <div class="flex items-start justify-between mb-3">
                            <!-- Icon Circle -->
                            @if($goal->is_instagram)
                                <div class="w-10 h-10 rounded-full flex items-center justify-center p-0.5 shadow-xs" style="background-color: {{ $goal->icon_bg_color ?? '#FCE7F3' }};">
                                    <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-yellow-400 via-pink-500 to-purple-600 flex items-center justify-center text-white text-xs shadow-xs">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                </div>
                            @else
                                <div class="w-10 h-10 rounded-full flex items-center justify-center shadow-xs" style="background-color: {{ $goal->icon_bg_color ?? '#EEF2FF' }}; color: {{ $goal->icon_color ?? '#3B82F6' }};">
                                    @php
                                        $iconName = strtolower($goal->icon);
                                    @endphp
                                    @if(str_contains($iconName, 'star'))
                                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                    @elseif(str_contains($iconName, 'trend') || str_contains($iconName, 'chart') || str_contains($iconName, 'visibility'))
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                    @elseif(str_contains($iconName, 'camera'))
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    @elseif(str_contains($iconName, 'rocket'))
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                    @elseif(str_contains($iconName, 'target') || str_contains($iconName, 'bullseye'))
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
                                    @else
                                        <!-- Users / Default -->
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    @endif
                                </div>
                            @endif

                            <!-- Check Circle indicator -->
                            <div class="w-5 h-5 rounded-full border-2 border-amber-500 bg-amber-500 flex items-center justify-center text-white shadow-xs">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>

                        <div class="font-bold text-slate-900 text-sm leading-snug">{{ $goal->title }}</div>
                        <p class="text-slate-500 text-xs mt-1 leading-relaxed line-clamp-2">{{ $goal->description }}</p>
                    </div>

                    <!-- Meta info & Key tag -->
                    <div class="space-y-1.5 text-xs text-slate-600 mb-4">
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-slate-400 font-medium">Identifier / Key:</span>
                            <code class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-800 font-mono font-bold">{{ $goal->slug }}</code>
                        </div>
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-slate-400 font-medium">Icon style:</span>
                            <div class="flex items-center gap-1.5">
                                <span class="w-3 h-3 rounded-full border border-slate-300" style="background-color: {{ $goal->icon_bg_color }};"></span>
                                <span class="w-3 h-3 rounded-full border border-slate-300" style="background-color: {{ $goal->icon_color }};"></span>
                                <span class="font-medium text-slate-700 capitalize">{{ $goal->icon }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Actions Footer -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                    <button type="button" onclick='openEditGoalModal(@json($goal))'
                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-amber-50 text-slate-700 hover:text-amber-700 text-xs font-semibold rounded-xl transition-colors cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>Edit</span>
                    </button>

                    <form method="POST" action="{{ route('admin.marketing-goals.destroy', $goal->id) }}" 
                          onsubmit="return confirm('Are you sure you want to delete this goal? It will no longer appear in the app onboarding.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" title="Delete Goal"
                                class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-dashed border-slate-300">
                <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-slate-900">No marketing goals found</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Click "Add New Goal" above to create goals for the onboarding flow.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($goals->hasPages())
        <div class="mt-6">
            {{ $goals->links() }}
        </div>
    @endif
</div>

<!-- ================= CREATE / EDIT MODAL ================= -->
<div id="goalModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" onclick="closeGoalModal()"></div>

    <!-- Modal Dialog -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-xl bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
            <!-- Modal Header -->
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 id="modalTitle" class="text-base font-extrabold text-slate-900">Add New Goal</h3>
                        <p class="text-xs text-slate-500">Configure title, description, icon and styling for mobile app.</p>
                    </div>
                </div>
                <button type="button" onclick="closeGoalModal()" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Form -->
            <form id="goalForm" method="POST" action="{{ route('admin.marketing-goals.store') }}" class="p-6 space-y-4">
                @csrf
                <div id="methodSpoofingContainer"></div>

                <!-- Title -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Goal Title *</label>
                    <input type="text" id="goalTitleInput" name="title" required placeholder="e.g. Get more customers" 
                           oninput="updateModalPreview()"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all">
                </div>

                <!-- Subtitle / Description -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Subtitle / Description *</label>
                    <input type="text" id="goalDescriptionInput" name="description" required placeholder="e.g. Attract more customers to your business" 
                           oninput="updateModalPreview()"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Key / Slug -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Identifier / Slug *</label>
                        <input type="text" id="goalSlugInput" name="slug" placeholder="e.g. customers (auto-generated if empty)" 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm font-mono focus:outline-none focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all">
                        <span class="text-[10px] text-slate-400 mt-1 block">Unique key saved in user profile</span>
                    </div>

                    <!-- Badge Tag (Optional) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Badge Text (Optional)</label>
                        <input type="text" id="goalBadgeInput" name="badge_text" placeholder="e.g. Popular, High Demand" 
                               oninput="updateModalPreview()"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all">
                    </div>
                </div>

                <!-- Icon and Presets -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Icon *</label>
                        <select id="goalIconSelect" name="icon" onchange="updateModalPreview()"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all">
                            <option value="users">Users / Customers</option>
                            <option value="star">Star / Reviews</option>
                            <option value="trending-up">Trending / Visibility</option>
                            <option value="camera">Camera / Media</option>
                            <option value="rocket">Rocket / Growth</option>
                            <option value="target">Target / Bullseye</option>
                            <option value="globe">Globe / Online</option>
                            <option value="award">Award / Reputation</option>
                        </select>
                    </div>

                    <!-- Background Color Tint -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Icon BG Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" id="goalBgColorPicker" value="#EEF2FF" oninput="document.getElementById('goalBgColorInput').value = this.value; updateModalPreview();"
                                   class="w-10 h-10 p-0.5 border border-slate-200 rounded-xl cursor-pointer">
                            <input type="text" id="goalBgColorInput" name="icon_bg_color" value="#EEF2FF" oninput="document.getElementById('goalBgColorPicker').value = this.value; updateModalPreview();"
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-xs font-mono">
                        </div>
                    </div>

                    <!-- Icon Color -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Icon Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" id="goalIconColorPicker" value="#3B82F6" oninput="document.getElementById('goalIconColorInput').value = this.value; updateModalPreview();"
                                   class="w-10 h-10 p-0.5 border border-slate-200 rounded-xl cursor-pointer">
                            <input type="text" id="goalIconColorInput" name="icon_color" value="#3B82F6" oninput="document.getElementById('goalIconColorPicker').value = this.value; updateModalPreview();"
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-xs font-mono">
                        </div>
                    </div>
                </div>

                <!-- Toggles & Order -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Sort Order</label>
                        <input type="number" id="goalOrderInput" name="sort_order" value="1" min="0" 
                               class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm">
                    </div>

                    <div class="flex items-center pt-5">
                        <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" id="goalInstagramCheck" name="is_instagram" value="1" onchange="updateModalPreview()"
                                   class="w-4 h-4 rounded text-pink-600 focus:ring-pink-500 border-slate-300">
                            <span class="text-xs font-bold text-slate-700">Instagram Gradient</span>
                        </label>
                    </div>

                    <div class="flex items-center pt-5">
                        <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" id="goalActiveCheck" name="is_active" value="1" checked
                                   class="w-4 h-4 rounded text-amber-500 focus:ring-amber-400 border-slate-300">
                            <span class="text-xs font-bold text-slate-700">Active in Mobile App</span>
                        </label>
                    </div>
                </div>

                <!-- Live Preview Inside Modal -->
                <div class="pt-3 border-t border-slate-100">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Live Preview (Mobile App Card)</span>
                    <div class="p-4 bg-amber-50/40 border border-amber-200/80 rounded-2xl max-w-sm">
                        <div class="flex items-center justify-between mb-2">
                            <div id="previewIconWrap" class="w-10 h-10 rounded-full flex items-center justify-center" style="background-color: #EEF2FF; color: #3B82F6;">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <div class="w-5 h-5 rounded-full bg-amber-500 text-white flex items-center justify-center">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </div>
                        <div id="previewTitle" class="font-extrabold text-slate-900 text-sm">Get more customers</div>
                        <div id="previewDesc" class="text-slate-500 text-xs mt-0.5">Attract more customers to your business</div>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeGoalModal()" class="px-5 py-2.5 text-slate-600 hover:text-slate-900 font-semibold text-xs rounded-xl hover:bg-slate-100 transition-colors cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" id="saveGoalBtn" class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-bold text-xs rounded-xl shadow-md shadow-amber-500/25 transition-all cursor-pointer">
                        Save Goal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function updateModalPreview() {
        const title = document.getElementById('goalTitleInput').value || 'Goal Title';
        const desc = document.getElementById('goalDescriptionInput').value || 'Goal description preview';
        const isInstagram = document.getElementById('goalInstagramCheck').checked;
        const bgColor = document.getElementById('goalBgColorInput').value || '#EEF2FF';
        const iconColor = document.getElementById('goalIconColorInput').value || '#3B82F6';

        document.getElementById('previewTitle').innerText = title;
        document.getElementById('previewDesc').innerText = desc;

        const wrap = document.getElementById('previewIconWrap');
        wrap.style.backgroundColor = bgColor;
        wrap.style.color = iconColor;

        if (isInstagram) {
            wrap.innerHTML = '<div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-yellow-400 via-pink-500 to-purple-600 flex items-center justify-center text-white"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>';
        } else {
            const icon = document.getElementById('goalIconSelect').value;
            if (icon === 'star') {
                wrap.innerHTML = '<svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
            } else if (icon === 'trending-up') {
                wrap.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>';
            } else if (icon === 'camera') {
                wrap.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>';
            } else {
                wrap.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>';
            }
        }
    }

    function openCreateGoalModal() {
        document.getElementById('modalTitle').innerText = 'Add New Goal';
        document.getElementById('saveGoalBtn').innerText = 'Create Goal';
        document.getElementById('goalForm').action = "{{ route('admin.marketing-goals.store') }}";
        document.getElementById('methodSpoofingContainer').innerHTML = '';

        document.getElementById('goalTitleInput').value = '';
        document.getElementById('goalDescriptionInput').value = '';
        document.getElementById('goalSlugInput').value = '';
        document.getElementById('goalBadgeInput').value = '';
        document.getElementById('goalIconSelect').value = 'users';
        document.getElementById('goalBgColorInput').value = '#EEF2FF';
        document.getElementById('goalBgColorPicker').value = '#EEF2FF';
        document.getElementById('goalIconColorInput').value = '#3B82F6';
        document.getElementById('goalIconColorPicker').value = '#3B82F6';
        document.getElementById('goalOrderInput').value = '{{ $totalGoals + 1 }}';
        document.getElementById('goalInstagramCheck').checked = false;
        document.getElementById('goalActiveCheck').checked = true;

        updateModalPreview();
        document.getElementById('goalModal').classList.remove('hidden');
    }

    function openEditGoalModal(goal) {
        document.getElementById('modalTitle').innerText = 'Edit Goal: ' + goal.title;
        document.getElementById('saveGoalBtn').innerText = 'Update Goal';
        document.getElementById('goalForm').action = "/admin/marketing-goals/" + goal.id;
        document.getElementById('methodSpoofingContainer').innerHTML = '@method("PUT")';

        document.getElementById('goalTitleInput').value = goal.title;
        document.getElementById('goalDescriptionInput').value = goal.description || '';
        document.getElementById('goalSlugInput').value = goal.slug;
        document.getElementById('goalBadgeInput').value = goal.badge_text || '';
        document.getElementById('goalIconSelect').value = goal.icon || 'users';
        document.getElementById('goalBgColorInput').value = goal.icon_bg_color || '#EEF2FF';
        document.getElementById('goalBgColorPicker').value = goal.icon_bg_color || '#EEF2FF';
        document.getElementById('goalIconColorInput').value = goal.icon_color || '#3B82F6';
        document.getElementById('goalIconColorPicker').value = goal.icon_color || '#3B82F6';
        document.getElementById('goalOrderInput').value = goal.sort_order || 0;
        document.getElementById('goalInstagramCheck').checked = !!goal.is_instagram;
        document.getElementById('goalActiveCheck').checked = !!goal.is_active;

        updateModalPreview();
        document.getElementById('goalModal').classList.remove('hidden');
    }

    function closeGoalModal() {
        document.getElementById('goalModal').classList.add('hidden');
    }
</script>
@endsection