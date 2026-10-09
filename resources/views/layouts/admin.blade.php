<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - Control Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <!-- CKEditor 5 CDN -->
    <script src="https://cdn.ckeditor.com/ckeditor5/41.2.0/classic/ckeditor.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        /* Custom scrollbar for light theme */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* CKEditor 5 Light Theme Styling Overrides */
        .ck.ck-editor {
            border-radius: 0.75rem !important;
            overflow: hidden;
            border: 1px solid #cbd5e1 !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        }
        .ck.ck-editor__top .ck-sticky-panel .ck-toolbar {
            background-color: #f8fafc !important;
            border-bottom: 1px solid #e2e8f0 !important;
            border-radius: 0 !important;
        }
        .ck.ck-toolbar .ck-toolbar__separator {
            background: #cbd5e1 !important;
        }
        .ck.ck-button,
        .ck.ck-button.ck-off {
            color: #475569 !important;
            cursor: pointer;
        }
        .ck.ck-button:hover,
        .ck.ck-button.ck-on {
            color: #0f172a !important;
            background-color: #e2e8f0 !important;
        }
        .ck.ck-dropdown__panel {
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        }
        .ck.ck-list__item .ck-button {
            color: #334155 !important;
        }
        .ck.ck-list__item .ck-button:hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
        }
        .ck-content {
            background-color: #ffffff !important;
            color: #0f172a !important;
            min-height: 180px !important;
            font-size: 0.875rem !important;
            padding: 1rem !important;
            line-height: 1.6 !important;
        }
        .ck-content.ck-focused {
            border-color: #4f46e5 !important;
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2) !important;
        }
        .ck.ck-editor__main>.ck-editor__editable:not(.ck-focused) {
            border-color: transparent !important;
        }
        .ck.ck-labeled-field-view>.ck-labeled-field-view__input-wrapper>.ck-icon {
            fill: #64748b !important;
        }
    </style>
    @yield('styles')
</head>
<body class="h-full bg-slate-50 text-slate-800 antialiased font-sans">

<div class="min-h-full flex">
    <!-- Sidebar Navigation -->
    <aside class="w-64 bg-white border-r border-slate-200/80 flex flex-col fixed inset-y-0 z-50 shadow-sm">
        <!-- Brand Header -->
        <div class="h-16 flex items-center px-6 border-b border-slate-100 gap-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-600 flex items-center justify-center text-white shadow-md shadow-indigo-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <div>
                <h1 class="font-extrabold text-base text-slate-900 leading-tight">Admin Console</h1>
                <span class="text-[10px] uppercase font-bold tracking-widest text-indigo-600">Review Management</span>
            </div>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 py-6 px-4 space-y-1.5 overflow-y-auto">
            <div class="px-3 pb-2 text-[11px] font-bold tracking-wider text-slate-400 uppercase">Main Menu</div>

            <!-- Dashboard Link -->
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 border {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-600 border-indigo-200/80 font-semibold shadow-xs' : 'text-slate-600 border-transparent hover:bg-slate-100 hover:text-slate-900' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.dashboard') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                <span>Dashboard</span>
            </a>

            <!-- Users Link -->
            <a href="{{ route('admin.users') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 border {{ request()->routeIs('admin.users') ? 'bg-indigo-50 text-indigo-600 border-indigo-200/80 font-semibold shadow-xs' : 'text-slate-600 border-transparent hover:bg-slate-100 hover:text-slate-900' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.users') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span>Users</span>
            </a>

            <!-- Campaigns Link -->
            <a href="{{ route('admin.campaigns') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 border {{ request()->routeIs('admin.campaigns*') ? 'bg-indigo-50 text-indigo-600 border-indigo-200/80 font-semibold shadow-xs' : 'text-slate-600 border-transparent hover:bg-slate-100 hover:text-slate-900' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.campaigns*') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                </svg>
                <span>Campaigns</span>
            </a>

            <!-- Categories Link -->
            <a href="{{ route('admin.categories') }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 border {{ request()->routeIs('admin.categories*') ? 'bg-indigo-50 text-indigo-600 border-indigo-200/80 font-semibold shadow-xs' : 'text-slate-600 border-transparent hover:bg-slate-100 hover:text-slate-900' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 {{ request()->routeIs('admin.categories*') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <span>Categories</span>
                </div>
                @php
                    $categoriesCountBadge = \App\Models\Category::count();
                @endphp
                @if($categoriesCountBadge > 0)
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-600 border border-slate-200">{{ $categoriesCountBadge }}</span>
                @endif
            </a>

            <!-- Conversions & Review Proofs Link -->
            <a href="{{ route('admin.conversions') }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 border {{ request()->routeIs('admin.conversions*') ? 'bg-indigo-50 text-indigo-600 border-indigo-200/80 font-semibold shadow-xs' : 'text-slate-600 border-transparent hover:bg-slate-100 hover:text-slate-900' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 {{ request()->routeIs('admin.conversions*') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Conversions</span>
                </div>
                @php
                    $pendingCountBadge = \App\Models\CampaignParticipation::where('status', 'pending')->count();
                @endphp
                @if($pendingCountBadge > 0)
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-500 text-white shadow-xs animate-pulse">{{ $pendingCountBadge }}</span>
                @endif
            </a>

            <!-- Growth / Marketing Goals Link -->
            <a href="{{ route('admin.marketing-goals') }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 border {{ request()->routeIs('admin.marketing-goals*') ? 'bg-indigo-50 text-indigo-600 border-indigo-200/80 font-semibold shadow-xs' : 'text-slate-600 border-transparent hover:bg-slate-100 hover:text-slate-900' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 {{ request()->routeIs('admin.marketing-goals*') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>Growth Goals</span>
                </div>
                @php
                    $goalsCountBadge = \App\Models\MarketingGoal::count();
                @endphp
                @if($goalsCountBadge > 0)
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-600 border border-slate-200">{{ $goalsCountBadge }}</span>
                @endif
            </a>

            <!-- Bounty AI Users Link -->
            <a href="{{ route('admin.bounty-ai-users') }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 border {{ request()->routeIs('admin.bounty-ai-users*') ? 'bg-indigo-50 text-indigo-600 border-indigo-200/80 font-semibold shadow-xs' : 'text-slate-600 border-transparent hover:bg-slate-100 hover:text-slate-900' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 {{ request()->routeIs('admin.bounty-ai-users*') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>Bounty AI Users</span>
                </div>
                @php
                    $bountyCountBadge = \App\Models\BountyAiProfile::count();
                @endphp
                @if($bountyCountBadge > 0)
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-500 text-white shadow-xs">{{ $bountyCountBadge }}</span>
                @endif
            </a>

            <!-- Withdrawal Requests Link -->
            <a href="{{ route('admin.withdrawals') }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 border {{ request()->routeIs('admin.withdrawals*') ? 'bg-indigo-50 text-indigo-600 border-indigo-200/80 font-semibold shadow-xs' : 'text-slate-600 border-transparent hover:bg-slate-100 hover:text-slate-900' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 {{ request()->routeIs('admin.withdrawals*') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    <span>Withdrawals</span>
                </div>
                @php
                    $pendingWithdrawalsBadge = \App\Models\WithdrawalRequest::where('status', 'pending')->count();
                @endphp
                @if($pendingWithdrawalsBadge > 0)
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-indigo-600 text-white shadow-xs animate-pulse">{{ $pendingWithdrawalsBadge }}</span>
                @endif
            </a>
        </div>

        <!-- Admin Profile Footer & Logout -->
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-indigo-100 border border-indigo-200 flex items-center justify-center font-bold text-sm text-indigo-600">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-xs font-bold text-slate-900 truncate max-w-[110px]">{{ Auth::user()->name ?? 'Admin' }}</div>
                        <div class="text-[10px] text-slate-500 truncate max-w-[110px]">{{ Auth::user()->email ?? '' }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" title="Logout" class="p-2 rounded-xl text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Body -->
    <div class="pl-64 flex-1 flex flex-col min-h-screen">
        <!-- Top Navigation Header Bar -->
        <header class="h-16 bg-white/80 backdrop-blur-md border-b border-slate-200/80 px-8 flex items-center justify-between sticky top-0 z-40">
            <div class="flex items-center gap-4">
                <h2 class="text-lg font-extrabold text-slate-900">@yield('page-title', 'Overview')</h2>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    System Live
                </span>
            </div>
        </header>

        <!-- Flash Alert Messages -->
        <main class="flex-1 p-8">
            @if (session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

@yield('scripts')
</body>
</html>
