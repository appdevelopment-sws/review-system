<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Control Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            min-height: 100vh;
            background: #090d16;
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.12) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(168, 85, 247, 0.12) 0px, transparent 50%);
            color: #f3f4f6;
        }

        /* Navbar */
        .navbar {
            background: rgba(17, 24, 39, 0.8);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .nav-brand-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
        }

        .nav-brand-text {
            font-size: 1.15rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.02em;
        }

        .nav-user {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info {
            text-align: right;
        }

        .user-name {
            font-size: 0.875rem;
            font-weight: 600;
            color: #ffffff;
        }

        .user-role-badge {
            display: inline-block;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: 0.15rem 0.5rem;
            background: rgba(99, 102, 241, 0.2);
            color: #818cf8;
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 999px;
            margin-top: 0.15rem;
        }

        .btn-logout {
            padding: 0.5rem 1rem;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.25);
            border-radius: 10px;
            color: #fca5a5;
            font-size: 0.825rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.2s ease;
        }

        .btn-logout:hover {
            background: rgba(239, 68, 68, 0.2);
            color: #ffffff;
            border-color: rgba(239, 68, 68, 0.4);
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }

        /* Header */
        .dashboard-header {
            margin-bottom: 2rem;
        }

        .dashboard-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.35rem;
        }

        .dashboard-header p {
            font-size: 0.9rem;
            color: #9ca3af;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(17, 24, 39, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 18px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            border-color: rgba(255, 255, 255, 0.15);
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon.indigo {
            background: rgba(99, 102, 241, 0.15);
            color: #818cf8;
            border: 1px solid rgba(99, 102, 241, 0.25);
        }

        .stat-icon.purple {
            background: rgba(168, 85, 247, 0.15);
            color: #c084fc;
            border: 1px solid rgba(168, 85, 247, 0.25);
        }

        .stat-icon.emerald {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.25);
        }

        .stat-content {
            flex-grow: 1;
        }

        .stat-title {
            font-size: 0.8rem;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .stat-value {
            font-size: 1.6rem;
            font-weight: 700;
            color: #ffffff;
        }

        /* Users Panel */
        .content-panel {
            background: rgba(17, 24, 39, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 1.75rem;
        }

        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .panel-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #ffffff;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            padding: 0.55rem 1rem 0.55rem 2.25rem;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: #ffffff;
            font-size: 0.85rem;
            outline: none;
            width: 220px;
            transition: all 0.2s;
        }

        .search-box input:focus {
            border-color: #6366f1;
            width: 260px;
        }

        .search-box svg {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            width: 14px;
            height: 14px;
            color: #6b7280;
        }

        /* Table */
        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.875rem;
        }

        th {
            background: rgba(15, 23, 42, 0.5);
            color: #9ca3af;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 0.875rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: #e5e7eb;
        }

        tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.65rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .role-badge.admin {
            background: rgba(99, 102, 241, 0.15);
            color: #818cf8;
            border: 1px solid rgba(99, 102, 241, 0.3);
        }

        .role-badge.user {
            background: rgba(156, 163, 175, 0.15);
            color: #9ca3af;
            border: 1px solid rgba(156, 163, 175, 0.3);
        }

        .badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }

        .role-badge.admin .badge-dot { background: #818cf8; }
        .role-badge.user .badge-dot { background: #9ca3af; }

        .pagination-container {
            margin-top: 1.25rem;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="nav-brand">
        <div class="nav-brand-icon">
            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        </div>
        <span class="nav-brand-text">Admin Panel</span>
    </div>

    <div class="nav-user">
        <div class="user-info">
            <div class="user-name">{{ Auth::user()->name }}</div>
            <span class="user-role-badge">ADMINISTRATOR</span>
        </div>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="btn-logout">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span>Logout</span>
            </button>
        </form>
    </div>
</nav>

<!-- Main Container -->
<div class="container">
    <div class="dashboard-header">
        <h1>Overview & User Management</h1>
        <p>Manage system users, roles, and review administrative metrics</p>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon indigo">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-title">Total Registered</div>
                <div class="stat-value">{{ $totalUsers }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon purple">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-title">Admin Accounts</div>
                <div class="stat-value">{{ $adminCount }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon emerald">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-title">Normal Users</div>
                <div class="stat-value">{{ $userCount }}</div>
            </div>
        </div>
    </div>

    <!-- Content Panel -->
    <div class="content-panel">
        <div class="panel-header">
            <div class="panel-title">User Accounts List</div>
            <div class="search-box">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" id="tableFilter" placeholder="Filter users..." onkeyup="filterTable()">
            </div>
        </div>

        <div class="table-responsive">
            <table id="usersTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email Address</th>
                        <th>Role</th>
                        <th>Created Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>#{{ $user->id }}</td>
                            <td style="font-weight: 600; color: #ffffff;">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="role-badge {{ $user->role }}">
                                    <span class="badge-dot"></span>
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td>{{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #6b7280; padding: 2rem;">No users found in database.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="pagination-container">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

<script>
    function filterTable() {
        const input = document.getElementById('tableFilter');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('usersTable');
        const trs = table.getElementsByTagName('tr');

        for (let i = 1; i < trs.length; i++) {
            const tr = trs[i];
            const text = tr.textContent.toLowerCase();
            if (text.includes(filter)) {
                tr.style.display = '';
            } else {
                tr.style.display = 'none';
            }
        }
    }
</script>

</body>
</html>
