<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'infinitesugar' }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <meta name="theme-color" content="#f8f2e4">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html {
            scroll-behavior: smooth;
        }

        :root {
            --is-cream: #f8f2e4;
            --is-cream-soft: #fcfaf4;
            --is-paper: #fffdf8;
            --is-nav: rgba(248, 242, 228, .94);
            --is-ink: #17110c;
            --is-brown: #5f3c18;
            --is-brown-soft: #8b6a3e;
            --is-gold: #a8873f;
            --is-gold-rich: #b08b3b;
            --is-gold-soft: #d6bf78;
            --is-line: #e5d9c2;
            --is-muted: #6f6252;
            --is-content: 1050px;
            --is-pricing: 620px;
            --is-faq: 980px;
        }

        * {
            letter-spacing: 0;
        }

        body {
            min-height: 100vh;
            background: #fff;
            color: var(--is-ink);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        a {
            color: var(--is-brown);
        }

        a:hover {
            color: var(--is-gold);
        }

        .site-shell {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .site-nav {
            position: sticky;
            top: 0;
            z-index: 50;
            border-bottom: 1px solid rgba(95, 60, 24, .1);
            background: var(--is-nav);
            backdrop-filter: blur(18px);
        }

        .site-nav .container {
            width: min(82vw, 1420px);
            min-height: 4.65rem;
            max-width: none;
            padding-inline: 0;
        }

        .brand-word {
            color: #a8873f;
            font-family: "Playfair Display", Georgia, serif;
            font-size: clamp(2rem, 2.45vw, 2.45rem);
            font-weight: 600;
            line-height: 1;
            padding-block: 0;
            text-decoration: none;
        }

        .nav-link {
            color: rgba(23, 17, 12, .62);
            font-size: .92rem;
            font-weight: 750;
            letter-spacing: .01em;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--is-brown);
        }

        .nav-link[href$="#faq"] {
            color: #7d6845;
        }

        .nav-logout-form {
            display: inline-flex;
            margin: 0;
        }

        .nav-logout-button {
            border: 0;
            background: transparent;
            padding: .5rem 0;
            color: rgba(23, 17, 12, .68);
            font: inherit;
            font-size: .92rem;
            font-weight: 750;
        }

        .nav-logout-button:hover {
            color: var(--is-brown);
        }

        .site-nav .navbar-nav {
            flex-direction: row;
            flex-wrap: wrap;
            gap: .15rem 1.18rem;
            justify-content: flex-end;
        }

        .text-muted {
            color: var(--is-muted) !important;
        }

        main {
            flex: 1 0 auto;
            padding-block: 0;
        }

        .page-wrap {
            max-width: var(--is-content);
            margin-inline: auto;
            padding-inline: clamp(1.25rem, 3vw, 2rem);
        }

        .section-block {
            padding-block: clamp(3.5rem, 8vw, 7rem);
        }

        .section-band {
            margin-inline: calc(50% - 50vw);
            padding-inline: max(1.25rem, calc((100vw - var(--is-content)) / 2 + 2rem));
            background: #e9e2d5;
            border-block: 0;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            color: var(--is-gold);
            font-size: .75rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .eyebrow::before {
            content: "";
            width: 2.4rem;
            height: 1px;
            background: currentColor;
        }

        .display-serif,
        .hero-title,
        .section-title {
            color: var(--is-ink);
            font-family: "Playfair Display", Georgia, "Times New Roman", serif;
            font-weight: 600;
        }

        .hero-title {
            max-width: 11ch;
            font-size: clamp(3.25rem, 8.4vw, 7.25rem);
            line-height: .98;
        }

        .section-title {
            font-size: clamp(2rem, 4vw, 3rem);
            line-height: 1.04;
        }

        .lead-copy {
            color: var(--is-muted);
            font-size: clamp(1rem, 1.35vw, 1.125rem);
            line-height: 1.72;
        }

        .btn-sugar {
            --bs-btn-color: #fffdf8;
            --bs-btn-bg: #b59a63;
            --bs-btn-border-color: #fffdf8;
            --bs-btn-hover-color: #fffdf8;
            --bs-btn-hover-bg: #96763b;
            --bs-btn-hover-border-color: #fffdf8;
            border-radius: 999px;
            box-shadow: 0 12px 22px rgba(95, 60, 24, .22);
            font-weight: 800;
            padding: .95rem 1.65rem;
        }

        .btn-soft {
            border: 1px solid rgba(95, 60, 24, .24);
            border-radius: 999px;
            background: rgba(255, 253, 248, .64);
            color: var(--is-brown);
            font-weight: 800;
            padding: .78rem 1.22rem;
        }

        .btn-soft:hover {
            border-color: var(--is-gold);
            background: var(--is-paper);
            color: var(--is-ink);
        }

        .surface-card,
        .feature-card,
        .pricing-card {
            border: 1px solid rgba(95, 60, 24, .14);
            border-radius: .5rem;
            background: rgba(255, 253, 248, .72);
        }

        .surface-card {
            box-shadow: 0 18px 54px rgba(95, 60, 24, .06);
        }

        .feature-card {
            height: 100%;
            padding: clamp(1.15rem, 2.4vw, 1.75rem);
        }

        .pricing-card {
            height: 100%;
            padding: clamp(1.5rem, 3vw, 2.25rem);
        }

        .pricing-card.featured {
            border-color: rgba(168, 135, 63, .44);
            background: linear-gradient(180deg, rgba(255,253,248,.96), rgba(248,242,228,.96));
            box-shadow: 0 22px 58px rgba(95, 60, 24, .09);
        }

        .metric-pill {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            border: 1px solid rgba(168, 135, 63, .28);
            border-radius: 999px;
            background: rgba(255, 253, 248, .74);
            color: var(--is-brown);
            padding: .38rem .75rem;
            font-size: .78rem;
            font-weight: 800;
        }

        .icon-pill {
            width: 2.65rem;
            height: 2.65rem;
            display: inline-grid;
            place-items: center;
            border: 1px solid rgba(168, 135, 63, .24);
            border-radius: 50%;
            background: #f4ead1;
            color: var(--is-brown);
            font-family: "Playfair Display", Georgia, serif;
            font-weight: 700;
        }

        .theme-media,
        .hero-video {
            width: 100%;
            display: block;
            border: 1px solid rgba(95, 60, 24, .16);
            border-radius: .5rem;
            background: #111;
            box-shadow: 0 24px 70px rgba(95, 60, 24, .13);
        }

        .hero-video {
            aspect-ratio: 16 / 9;
            object-fit: cover;
        }

        .home-hero {
            width: min(calc(100vw - 2.5rem), 1320px);
            min-height: 40rem;
            margin-inline: 50%;
            padding-block: clamp(4rem, 7vw, 6rem) clamp(3.5rem, 6vw, 5.4rem);
            background: #fff;
            transform: translateX(-50%);
        }

        .home-hero .hero-title {
            color: var(--is-gold-rich);
            font-size: clamp(3.35rem, 4.5vw, 5.125rem);
            line-height: 1.03;
        }

        .home-hero .lead-copy {
            max-width: 33rem;
            color: #8b7657;
            font-size: clamp(1.25rem, 1.8vw, 1.55rem);
            line-height: 1.35;
        }

        .hero-kicker {
            max-width: 35rem;
            color: #9b845e;
            font-size: clamp(1.28rem, 1.8vw, 1.65rem);
            font-weight: 600;
            line-height: 1.15;
        }

        .home-hero .hero-video {
            border: 0;
            border-radius: 0;
            box-shadow: none;
        }

        .home-hero-media {
            display: flex;
            justify-content: flex-end;
        }

        .home-hero-media .hero-video {
            max-width: 760px;
        }

        .forge-story {
            padding-block: clamp(4.5rem, 7vw, 6.5rem);
        }

        .forge-story-title {
            color: var(--is-gold-rich);
            font-family: "Playfair Display", Georgia, serif;
            font-size: clamp(3.625rem, 7vw, 5.125rem);
            font-weight: 600;
            line-height: .95;
        }

        .forge-story-copy {
            max-width: var(--is-content);
            color: var(--is-ink);
            font-family: "Playfair Display", Georgia, serif;
            font-size: clamp(2rem, 3.6vw, 2.375rem);
            font-weight: 600;
            line-height: 1.06;
        }

        .text-gold {
            color: var(--is-gold-rich);
        }

        .pricing-stage {
            max-width: var(--is-pricing);
            margin-inline: auto;
            padding: 1rem;
            background: #c7af69;
        }

        .pricing-panel {
            border-radius: 1.1rem;
            background: #fff;
            padding: clamp(1.35rem, 3vw, 1.9rem);
        }

        .pricing-panel + .pricing-panel {
            margin-top: 1.25rem;
        }

        .pricing-plan-row {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 1.5rem;
            margin-bottom: 1rem;
        }

        .pricing-plan-name,
        .pricing-plan-price {
            color: var(--is-gold-rich);
            font-family: "Playfair Display", Georgia, serif;
            font-size: clamp(2.25rem, 3.4vw, 2.75rem);
            font-weight: 600;
            line-height: 1;
        }

        .pricing-panel p,
        .pricing-panel li {
            color: #171717;
            font-size: clamp(1rem, 1.3vw, 1.125rem);
            line-height: 1.4;
        }

        .pricing-panel .btn-linkish {
            display: block;
            margin-top: 2rem;
            color: #171717;
            font-size: clamp(1rem, 1.3vw, 1.125rem);
            font-weight: 900;
            text-align: center;
            text-decoration: none;
        }

        .check-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .check-list li {
            display: flex;
            gap: .7rem;
            color: var(--is-muted);
            margin-bottom: .78rem;
        }

        .check-list li::before {
            content: "";
            width: .45rem;
            height: .45rem;
            flex: 0 0 .45rem;
            margin-top: .55rem;
            border-radius: 50%;
            background: var(--is-gold);
        }

        .placeholder-media {
            min-height: 14rem;
            display: grid;
            place-items: center;
            border: 1px dashed rgba(95, 60, 24, .26);
            border-radius: .5rem;
            background: rgba(255, 253, 248, .52);
            color: var(--is-brown-soft);
            font-weight: 800;
            padding: 2rem;
            text-align: center;
        }

        .auth-shell {
            max-width: var(--is-content);
            margin-inline: auto;
            padding: clamp(1rem, 3vw, 1.5rem);
        }

        .auth-panel {
            min-height: 100%;
            border-radius: .5rem;
            padding: clamp(2rem, 5vw, 3.75rem);
            background: var(--is-cream-soft);
        }

        .auth-media {
            width: 100%;
            display: block;
            margin-top: clamp(2rem, 5vw, 3.5rem);
            border: 1px solid rgba(95, 60, 24, .12);
            border-radius: .5rem;
            background: var(--is-paper);
        }

        .auth-form-panel {
            min-height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: clamp(2rem, 5vw, 4rem) 0;
        }

        .auth-form-panel > * {
            width: 100%;
            max-width: 35rem;
            margin-inline: auto;
        }

        .auth-tabs {
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .form-control,
        .form-select {
            border-color: rgba(95, 60, 24, .18);
            border-radius: .5rem;
            background-color: var(--is-paper);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--is-gold);
            box-shadow: 0 0 0 .2rem rgba(168, 135, 63, .18);
        }

        .legal-doc {
            max-width: 860px;
            color: var(--is-muted);
            font-size: 1rem;
            line-height: 1.78;
        }

        .legal-hero {
            max-width: 980px;
            margin-inline: auto;
            padding: clamp(1.5rem, 3vw, 2rem);
            border: 1px solid rgba(95, 60, 24, .12);
            border-radius: 1.1rem;
            background: rgba(255, 253, 248, .88);
            box-shadow: 0 16px 34px rgba(95, 60, 24, .05);
        }

        .legal-hero-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .legal-hero-copy .eyebrow {
            margin-bottom: .85rem;
        }

        .legal-hero-title {
            margin: 0;
            color: var(--is-ink);
            font-family: "Playfair Display", Georgia, serif;
            font-size: clamp(2.5rem, 4.5vw, 4rem);
            font-weight: 600;
            line-height: 1;
        }

        .legal-hero-summary {
            max-width: 42rem;
            margin: .85rem 0 0;
            color: var(--is-muted);
            font-size: 1rem;
            line-height: 1.65;
        }

        .legal-hero-meta {
            display: flex;
            flex-wrap: wrap;
            gap: .75rem;
            align-items: center;
        }

        .legal-meta-card {
            width: auto;
            padding: .75rem .95rem;
            border: 1px solid rgba(95, 60, 24, .12);
            border-radius: 999px;
            background: #fffdf8;
        }

        .legal-meta-label {
            display: inline;
            margin-bottom: 0;
            color: var(--is-brown-soft);
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .legal-meta-value {
            margin-left: .45rem;
            color: var(--is-brown);
            font-family: Inter, Arial, sans-serif;
            font-size: .94rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .legal-doc h2 {
            margin-top: 2.1rem;
            color: var(--is-brown);
            font-family: "Playfair Display", Georgia, serif;
            font-size: clamp(1.45rem, 2.5vw, 2rem);
            font-weight: 600;
        }

        .legal-doc h3 {
            margin-top: 1.5rem;
            color: var(--is-ink);
            font-size: 1rem;
            font-weight: 800;
        }

        .legal-doc p,
        .legal-doc ul {
            margin-bottom: 1rem;
        }

        .legal-doc li {
            margin-bottom: .45rem;
        }

        .site-footer {
            flex-shrink: 0;
            margin-top: clamp(3rem, 7vw, 6rem);
            border-top: 1px solid rgba(95, 60, 24, .12);
            background: #f3ead6;
        }

        .footer-link {
            color: rgba(23, 17, 12, .7);
            font-weight: 700;
            text-decoration: none;
        }

        @media (max-width: 991.98px) {
            .site-nav .container {
                width: 100%;
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
                padding-inline: clamp(1rem, 4vw, 1.5rem);
            }

            .site-nav .navbar-nav {
                width: 100%;
                margin-left: 0 !important;
                align-items: flex-start !important;
                justify-content: flex-start;
            }

            .hero-video {
                aspect-ratio: 16 / 10;
            }

            .home-hero {
                width: 100%;
                margin-inline: 0;
                transform: none;
            }

            .home-hero-media .hero-video {
                max-width: none;
            }

            .legal-hero-meta {
                align-items: stretch;
            }
        }

        @media (max-width: 575.98px) {
            .brand-word {
                font-size: 2rem;
            }

            .hero-title {
                font-size: 3.15rem;
            }

            .section-title {
                font-size: 2.45rem;
            }

            .pricing-plan-row {
                display: block;
            }

            .pricing-plan-price {
                margin-top: .6rem;
            }

            .legal-hero {
                padding: 1.25rem;
            }

            .legal-hero-summary {
                line-height: 1.6;
            }

            .legal-meta-card {
                width: 100%;
                border-radius: .85rem;
            }

            .legal-meta-label,
            .legal-meta-value {
                display: block;
                margin-left: 0;
            }

            .legal-meta-value {
                margin-top: .25rem;
            }
        }
    </style>
</head>
<body>
    <div class="site-shell">
        <nav class="navbar site-nav">
            <div class="container py-2">
                <a class="navbar-brand brand-word" href="{{ route('home') }}">infinitesugar</a>
                <div class="navbar-nav ms-auto align-items-center">
                    <a class="nav-link" href="{{ route('home') }}#top">Home</a>
                    <a class="nav-link" href="{{ route('home') }}#spark">Spark</a>
                    <a class="nav-link" href="{{ route('home') }}#forge">Forge</a>
                    <a class="nav-link" href="{{ route('home') }}#intelligence">Intelligence</a>
                    <a class="nav-link" href="{{ route('home') }}#executive-briefings">Executive Briefings</a>
                    <a class="nav-link" href="{{ route('home') }}#pricing">Pricing</a>
                    <a class="nav-link" href="{{ route('home') }}#faq">FAQ</a>
                    @auth
                        <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                        <a class="nav-link" href="{{ route('extension.download') }}">Install Extension</a>
                        <form class="nav-logout-form" method="post" action="{{ route('logout') }}">
                            @csrf
                            <button class="nav-logout-button" type="submit">Logout</button>
                        </form>
                    @else
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    @endauth
                </div>
            </div>
        </nav>

        <main>
            <div class="page-wrap">
                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                {{ $slot }}
            </div>
        </main>

        <footer class="site-footer">
            <div class="container d-flex flex-column flex-md-row justify-content-between gap-3 py-4">
                <div class="fw-bold text-lowercase">infinitesugar</div>
                <div class="d-flex flex-wrap gap-3">
                    <a class="footer-link" href="{{ route('privacy') }}">Privacy Policy</a>
                    <a class="footer-link" href="{{ route('terms') }}">Terms & Conditions</a>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
