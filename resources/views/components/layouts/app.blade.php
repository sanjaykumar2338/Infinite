<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Infinite Sugar' }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <meta name="theme-color" content="#7c5cff">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --sugar-ink: #17202a;
            --sugar-muted: #6a7280;
            --sugar-line: #e8edf3;
            --sugar-soft: #f7f4ff;
            --sugar-pink: #f66f9b;
            --sugar-violet: #7c5cff;
            --sugar-mint: #22c7a9;
            --sugar-gold: #f4b740;
        }

        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at 12% 0%, rgba(246, 111, 155, .14), transparent 34rem),
                radial-gradient(circle at 92% 10%, rgba(34, 199, 169, .13), transparent 30rem),
                linear-gradient(180deg, #fff 0%, #fbfcff 44%, #f6f8fb 100%);
            color: var(--sugar-ink);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .site-shell {
            overflow: hidden;
        }

        .site-nav {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(255, 255, 255, .82);
            backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(232, 237, 243, .88);
        }

        .brand-mark {
            width: 2.35rem;
            height: 2.35rem;
            display: inline-grid;
            place-items: center;
            border-radius: .9rem;
            background: linear-gradient(135deg, var(--sugar-pink), var(--sugar-violet));
            color: #fff;
            font-weight: 800;
            box-shadow: 0 12px 28px rgba(124, 92, 255, .24);
        }

        .nav-link {
            color: #4b5563;
            font-weight: 600;
        }

        .nav-link:hover {
            color: var(--sugar-violet);
        }

        .btn-sugar {
            --bs-btn-color: #fff;
            --bs-btn-bg: #181d27;
            --bs-btn-border-color: #181d27;
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #2a3140;
            --bs-btn-hover-border-color: #2a3140;
            border-radius: 999px;
            box-shadow: 0 16px 34px rgba(24, 29, 39, .18);
            font-weight: 700;
            padding: .8rem 1.2rem;
        }

        .btn-soft {
            border: 1px solid var(--sugar-line);
            border-radius: 999px;
            background: rgba(255, 255, 255, .78);
            color: var(--sugar-ink);
            font-weight: 700;
            padding: .8rem 1.2rem;
        }

        .btn-soft:hover {
            border-color: rgba(124, 92, 255, .38);
            color: var(--sugar-violet);
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .42rem .75rem;
            border: 1px solid rgba(124, 92, 255, .16);
            border-radius: 999px;
            background: rgba(255, 255, 255, .7);
            color: #6954cb;
            font-size: .78rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0;
        }

        .hero-title {
            max-width: 13ch;
            font-size: clamp(3.05rem, 7vw, 6.8rem);
            line-height: .95;
            letter-spacing: 0;
        }

        .section-title {
            font-size: clamp(2rem, 4vw, 3.65rem);
            line-height: 1;
            letter-spacing: 0;
        }

        .lead-copy {
            color: var(--sugar-muted);
            font-size: clamp(1.05rem, 1.8vw, 1.28rem);
            line-height: 1.7;
        }

        .surface-card {
            border: 1px solid rgba(232, 237, 243, .96);
            border-radius: 1.5rem;
            background: rgba(255, 255, 255, .86);
            box-shadow: 0 24px 70px rgba(31, 41, 55, .08);
        }

        .auth-shell {
            max-width: 78rem;
            margin-inline: auto;
            padding: clamp(1.25rem, 3vw, 2rem);
        }

        .auth-layout {
            --bs-gutter-x: clamp(3rem, 7vw, 6.5rem);
            --bs-gutter-y: 2rem;
        }

        .auth-panel {
            min-height: 100%;
            border-radius: 1.2rem;
            padding: clamp(2rem, 4.5vw, 3.5rem);
            background:
                linear-gradient(135deg, rgba(246,111,155,.16), rgba(124,92,255,.12)),
                #fff;
        }

        .auth-panel .section-title {
            max-width: 9.8ch;
            font-size: clamp(2.45rem, 3.45vw, 3.35rem);
            line-height: 1.08;
        }

        .auth-panel .lead-copy {
            max-width: 22rem;
            font-size: clamp(1rem, 1.4vw, 1.12rem);
            line-height: 1.75;
        }

        .auth-media {
            width: 100%;
            display: block;
            margin-top: clamp(2rem, 5vw, 3.5rem);
            border-radius: 1.1rem;
            box-shadow: 0 22px 52px rgba(89, 74, 137, .12);
        }

        .auth-form-panel {
            min-height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: clamp(2rem, 5vw, 4.25rem) 0;
        }

        .auth-form-panel > * {
            width: 100%;
            max-width: 35rem;
        }

        .auth-tabs {
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .auth-form-panel .form-control {
            border-color: #dce3ec;
            border-radius: .85rem;
            padding: .78rem 1rem;
        }

        .auth-form-panel .form-label {
            margin-bottom: .45rem;
        }

        .auth-form-panel form .mb-3 {
            margin-bottom: 1.25rem !important;
        }

        .theme-media {
            width: 100%;
            display: block;
            border-radius: 1.25rem;
            border: 1px solid rgba(232, 237, 243, .92);
            box-shadow: 0 24px 58px rgba(31,41,55,.08);
        }

        .feature-card {
            min-height: 100%;
            border: 1px solid rgba(232, 237, 243, .92);
            border-radius: 1.35rem;
            background: rgba(255, 255, 255, .78);
            padding: 1.35rem;
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 22px 44px rgba(31, 41, 55, .1);
        }

        .icon-pill {
            width: 2.65rem;
            height: 2.65rem;
            display: inline-grid;
            place-items: center;
            border-radius: 1rem;
            background: #f2efff;
            color: var(--sugar-violet);
            font-weight: 900;
        }

        .metric-pill {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            border-radius: 999px;
            background: rgba(34, 199, 169, .12);
            color: #117f6e;
            padding: .38rem .7rem;
            font-size: .78rem;
            font-weight: 800;
        }

        .dashboard-visual {
            position: relative;
            border-radius: 1.7rem;
            padding: 1rem;
            background:
                linear-gradient(135deg, rgba(255,255,255,.86), rgba(255,255,255,.56)),
                linear-gradient(135deg, rgba(246,111,155,.22), rgba(124,92,255,.2));
            border: 1px solid rgba(255, 255, 255, .72);
            box-shadow: 0 28px 80px rgba(89, 74, 137, .2);
        }

        .app-window {
            border-radius: 1.25rem;
            background: #121723;
            color: #fff;
            overflow: hidden;
        }

        .app-window-top {
            display: flex;
            gap: .42rem;
            padding: .85rem 1rem;
            background: #1f2634;
        }

        .app-dot {
            width: .7rem;
            height: .7rem;
            border-radius: 999px;
            background: #f66f9b;
        }

        .app-dot:nth-child(2) { background: #f4b740; }
        .app-dot:nth-child(3) { background: #22c7a9; }

        .insight-line {
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 1rem;
            background: rgba(255,255,255,.05);
            padding: .9rem;
        }

        .placeholder-media {
            min-height: 15rem;
            border: 1px dashed rgba(124, 92, 255, .36);
            border-radius: 1.4rem;
            background: linear-gradient(135deg, rgba(124, 92, 255, .08), rgba(34, 199, 169, .08));
            display: grid;
            place-items: center;
            color: #6954cb;
            font-weight: 800;
            text-align: center;
            padding: 2rem;
        }

        .pricing-card {
            border: 1px solid rgba(232, 237, 243, .92);
            border-radius: 1.5rem;
            background: rgba(255, 255, 255, .85);
            padding: 1.6rem;
            min-height: 100%;
            box-shadow: 0 20px 54px rgba(31,41,55,.07);
        }

        .pricing-card.featured {
            border-color: rgba(124, 92, 255, .28);
            background: linear-gradient(180deg, #fff, #f8f6ff);
            box-shadow: 0 26px 70px rgba(124, 92, 255, .15);
        }

        .check-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .check-list li {
            display: flex;
            gap: .6rem;
            margin-bottom: .75rem;
            color: #4b5563;
        }

        .check-list li::before {
            content: "+";
            color: #119b83;
            font-weight: 900;
        }

        main {
            padding-block: 4rem 5rem;
        }

        @media (max-width: 767.98px) {
            .site-nav .container {
                align-items: flex-start;
                gap: 1rem;
            }

            .navbar-nav {
                flex-direction: row;
                flex-wrap: wrap;
                gap: .1rem .7rem;
            }

            .hero-title {
                font-size: 3.15rem;
            }

            main {
                padding-block: 2.5rem 4rem;
            }
        }
    </style>
</head>
<body>
    <div class="site-shell">
    <nav class="navbar navbar-expand-lg site-nav">
        <div class="container py-2">
            <a class="navbar-brand fw-bold d-inline-flex align-items-center gap-2" href="{{ route('home') }}">
                <span class="brand-mark">IS</span>
                <span>Infinite Sugar</span>
            </a>
            <div class="navbar-nav ms-auto align-items-center">
                <a class="nav-link" href="{{ route('pricing') }}">Pricing</a>
                <a class="nav-link" href="{{ route('reports.showcase') }}">Reports</a>
                @auth
                    <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                    <form method="post" action="{{ route('logout') }}" class="ms-lg-2">
                        @csrf
                        <button class="btn btn-sm btn-soft">Logout</button>
                    </form>
                @else
                    <a class="btn btn-sm btn-soft ms-lg-2" href="{{ route('login') }}">Login</a>
                    <a class="btn btn-sm btn-sugar ms-lg-1 py-2" href="{{ route('signup') }}">Sign up</a>
                @endauth
            </div>
        </div>
    </nav>
    <main class="container">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        {{ $slot }}
    </main>
    </div>
</body>
</html>
