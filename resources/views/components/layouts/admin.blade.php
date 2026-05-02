<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Infinite Sugar Admin' }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <meta name="theme-color" content="#17110c">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --admin-ink: #17110c;
            --admin-muted: #706253;
            --admin-line: #e5d9c2;
            --admin-bg: #f8f2e4;
            --admin-dark: #17110c;
            --admin-brown: #5f3c18;
            --admin-gold: #a8873f;
            --admin-paper: #fffdf8;
        }

        body {
            min-height: 100vh;
            background: var(--admin-bg);
            color: var(--admin-ink);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .admin-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 17.5rem 1fr;
        }

        .admin-sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
            padding: 1.1rem;
            background:
                radial-gradient(circle at 20% 0%, rgba(168, 135, 63, .22), transparent 18rem),
                linear-gradient(180deg, #3b250f, #17110c);
            color: var(--admin-paper);
        }

        .admin-brand {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .75rem;
            color: var(--admin-paper);
            text-decoration: none;
        }

        .brand-mark {
            width: 2.45rem;
            height: 2.45rem;
            display: inline-grid;
            place-items: center;
            border-radius: .9rem;
            background: linear-gradient(135deg, var(--admin-gold), var(--admin-brown));
            color: var(--admin-paper);
            font-weight: 900;
        }

        .admin-nav {
            display: grid;
            gap: .35rem;
            margin-top: 1.25rem;
        }

        .admin-nav a,
        .admin-logout {
            width: 100%;
            display: flex;
            align-items: center;
            gap: .65rem;
            padding: .78rem .9rem;
            border: 1px solid transparent;
            border-radius: .95rem;
            background: transparent;
            color: rgba(255, 253, 248, .74);
            text-decoration: none;
            font-weight: 700;
            text-align: left;
        }

        .admin-nav a:hover,
        .admin-nav a.active,
        .admin-logout:hover {
            background: rgba(255, 253, 248, .08);
            border-color: rgba(255, 253, 248, .1);
            color: var(--admin-paper);
        }

        .admin-content {
            min-width: 0;
        }

        .admin-topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(95, 60, 24, .12);
            background: rgba(248, 242, 228, .86);
            backdrop-filter: blur(16px);
        }

        .admin-main {
            padding: 1.5rem;
        }

        .admin-card {
            border: 1px solid rgba(95, 60, 24, .14);
            border-radius: 1.2rem;
            background: var(--admin-paper);
            box-shadow: 0 18px 45px rgba(95, 60, 24, .06);
        }

        .stat-card {
            position: relative;
            overflow: hidden;
            min-height: 9.25rem;
            padding: 1.2rem;
        }

        .stat-card::after {
            content: "";
            position: absolute;
            inset: auto -2rem -2.5rem auto;
            width: 7rem;
            height: 7rem;
            border-radius: 50%;
            background: rgba(168, 135, 63, .12);
        }

        .stat-label {
            color: var(--admin-muted);
            font-size: .76rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0;
        }

        .table-modern {
            margin: 0;
        }

        .table-modern thead th {
            padding: .95rem 1rem;
            background: #fcfaf4;
            color: var(--admin-muted);
            font-size: .76rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0;
            border-bottom: 1px solid var(--admin-line);
        }

        .table-modern tbody td {
            padding: 1rem;
            border-color: #eee3ce;
            vertical-align: middle;
        }

        .admin-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 4.75rem;
            border-radius: 999px;
            padding: .32rem .65rem;
            font-size: .76rem;
            font-weight: 900;
            text-transform: capitalize;
        }

        .pill-active,
        .pill-forge,
        .pill-admin {
            background: rgba(168, 135, 63, .16);
            color: #5f3c18;
        }

        .pill-free,
        .pill-user {
            background: #f0e6d2;
            color: #6f6252;
        }

        .pill-spark,
        .pill-tester {
            background: rgba(95, 60, 24, .12);
            color: #5f3c18;
        }

        .pill-past_due {
            background: rgba(244, 183, 64, .18);
            color: #94650a;
        }

        .pill-cancelled {
            background: rgba(95, 60, 24, .12);
            color: #7f2f12;
        }

        .form-control,
        .form-select {
            border-color: #d8c9ad;
            border-radius: .75rem;
        }

        .btn-admin {
            --bs-btn-color: #fff;
            --bs-btn-bg: #5f3c18;
            --bs-btn-border-color: #5f3c18;
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #17110c;
            --bs-btn-hover-border-color: #17110c;
            border-radius: .75rem;
            font-weight: 800;
        }

        .btn-admin-soft {
            border: 1px solid var(--admin-line);
            border-radius: .75rem;
            background: var(--admin-paper);
            color: var(--admin-ink);
            font-weight: 800;
        }

        .upload-panel {
            border-radius: 1.2rem;
            border: 1px solid rgba(168, 135, 63, .2);
            background: linear-gradient(135deg, #fffdf8, #f4ead1);
        }

        .mobile-admin-header {
            display: none;
        }

        @media (max-width: 991.98px) {
            .admin-shell {
                display: block;
            }

            .admin-sidebar {
                position: static;
                height: auto;
                border-radius: 0 0 1.4rem 1.4rem;
            }

            .admin-nav {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .admin-topbar {
                position: static;
            }
        }

        @media (max-width: 575.98px) {
            .admin-main,
            .admin-topbar {
                padding-inline: 1rem;
            }

            .admin-nav {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <a class="admin-brand" href="{{ route('admin.dashboard') }}">
                <span class="brand-mark">IS</span>
                <span>
                    <span class="d-block fw-bold">Infinite Sugar</span>
                    <span class="small text-white-50">Admin console</span>
                </span>
            </a>
            @auth
                <nav class="admin-nav">
                    <a class="@if(request()->routeIs('admin.dashboard')) active @endif" href="{{ route('admin.dashboard') }}">Dashboard</a>
                    <a class="@if(request()->routeIs('admin.users.*')) active @endif" href="{{ route('admin.users.index') }}">Users</a>
                    <a class="@if(request()->routeIs('admin.reports.*')) active @endif" href="{{ route('admin.reports.index') }}">Reports</a>
                    <form method="post" action="{{ route('admin.logout') }}">
                        @csrf
                        <button class="admin-logout">Logout</button>
                    </form>
                </nav>
            @endauth
        </aside>
        <section class="admin-content">
            <header class="admin-topbar d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <div class="small text-muted fw-bold text-uppercase">Infinite Sugar</div>
                    <h1 class="h4 fw-bold mb-0">{{ $title ?? 'Admin' }}</h1>
                </div>
                @auth
                    <span class="admin-pill pill-admin">{{ auth()->user()->email }}</span>
                @endauth
            </header>
            <main class="admin-main">
                @if (session('status'))
                    <div class="alert alert-success rounded-4 border-0 shadow-sm">{{ session('status') }}</div>
                @endif
                {{ $slot }}
            </main>
        </section>
    </div>
</body>
</html>
