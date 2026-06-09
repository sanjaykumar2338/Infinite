<x-layouts.app title="infinitesugar">
    @php
        $contentSections = $pageContent ?? collect();
        $rawSection = fn (string $key) => $contentSections->get($key);
        $section = fn (string $key) => $rawSection($key)?->is_active ? $rawSection($key) : null;
        $isHidden = fn (string $key) => (bool) ($rawSection($key) && ! $rawSection($key)->is_active);
        $contentTitle = fn (string $key, string $fallback) => $section($key)?->title ?: $fallback;
        $contentSubtitle = fn (string $key, string $fallback) => $section($key)?->subtitle ?: $fallback;
        $contentBody = fn (string $key, string $fallback) => $section($key)?->body ?: $fallback;
        $contentButton = fn (string $key, string $fallback) => $section($key)?->button_text ?: $fallback;
        $contentImage = fn (string $key, string $fallback) => $section($key)?->image_url ?: asset($fallback);
        $paragraphs = fn (string $text) => array_values(array_filter(preg_split('/\R{2,}|\R/', trim($text)) ?: [], fn ($line) => trim($line) !== ''));
        $currentUser = auth()->user();
        $isCurrentPlan = fn (string $plan) => $currentUser
            && $currentUser->plan === $plan
            && ($currentUser->billingStatus() === 'active' || (bool) $currentUser->paidThrough()?->isFuture());
    @endphp

    <style>
        .landing-page {
            --landing-cream: #f8f2e4;
            --landing-paper: #fffdf8;
            --landing-paper-soft: #fffaf1;
            --landing-ink: #17110c;
            --landing-brown: #5f3c18;
            --landing-muted: #806f58;
            --landing-gold: #a8873f;
            --landing-gold-deep: #8f6b31;
            --landing-olive: #56624f;
            --landing-line: rgba(95, 60, 24, .14);
            margin-inline: calc(50% - 50vw);
            background: linear-gradient(180deg, #fffdf8 0%, #f8f2e4 42%, #fffaf1 100%);
            color: var(--landing-ink);
        }

        .landing-page section[id] {
            scroll-margin-top: 7rem;
        }

        .landing-shell {
            width: min(1180px, calc(100vw - 2.5rem));
            margin: 0 auto;
        }

        .landing-section {
            padding: clamp(4.5rem, 8vw, 7.5rem) 0;
        }

        #hero.landing-section {
            padding: clamp(3rem, 5.2vw, 4.75rem) 0 clamp(3.4rem, 5.6vw, 5.25rem);
        }

        .landing-section + .landing-section {
            border-top: 1px solid var(--landing-line);
        }

        .landing-eyebrow,
        .tier-label,
        .card-kicker,
        .pricing-small,
        .briefing-label {
            color: var(--landing-muted);
            font-family: Inter, Arial, sans-serif;
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .landing-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: .7rem;
            margin-bottom: 1.25rem;
        }

        .landing-eyebrow::before {
            content: "";
            width: 2.4rem;
            height: 1px;
            background: currentColor;
        }

        .landing-title,
        .section-heading,
        .tier-name,
        .preview-card-title,
        .pricing-title,
        .pricing-plan-name,
        .pricing-plan-price,
        .briefing-title,
        .faq-question {
            font-family: "Playfair Display", Georgia, serif;
        }

        .hero-grid {
            display: grid;
            width: min(1260px, calc(100vw - 2.5rem));
            margin-inline: 50%;
            grid-template-columns: minmax(420px, 520px) minmax(620px, 720px);
            gap: clamp(2.2rem, 3.4vw, 4.25rem);
            align-items: start;
            justify-content: space-between;
            transform: translateX(-50%);
            min-height: min(640px, calc(100vh - 4.65rem));
        }

        .hero-copy {
            max-width: 520px;
            padding-top: clamp(.35rem, 1.2vw, 1rem);
        }

        .landing-title {
            max-width: 10.5ch;
            margin: 0 0 1.25rem;
            color: var(--landing-gold);
            font-size: clamp(3.6rem, 5.35vw, 5.25rem);
            font-weight: 600;
            line-height: .98;
        }

        .hero-summary {
            margin: 0 0 1.2rem;
            color: var(--landing-brown);
            font-size: clamp(1.2rem, 1.8vw, 1.48rem);
            line-height: 1.45;
        }

        .hero-positioning {
            margin: 0 0 1.1rem;
            color: var(--landing-olive);
            font-family: Inter, Arial, sans-serif;
            font-size: clamp(1.08rem, 1.45vw, 1.38rem);
            font-weight: 800;
            line-height: 1.18;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .section-copy,
        .hero-detail,
        .feature-list li,
        .preview-card-copy,
        .preview-meta,
        .pricing-copy,
        .pricing-list li,
        .faq-answer,
        .briefing-copy {
            color: var(--landing-muted);
            font-family: Inter, Arial, sans-serif;
        }

        .section-copy,
        .hero-detail,
        .preview-card-copy,
        .pricing-copy,
        .faq-answer,
        .briefing-copy {
            margin: 0;
            font-size: 1rem;
            line-height: 1.72;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 2rem;
        }

        .hero-button,
        .pricing-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 3.25rem;
            padding: .85rem 1.4rem;
            border-radius: 999px;
            background: linear-gradient(135deg, #b89a5d, #96763b);
            box-shadow: 0 16px 36px rgba(95, 60, 24, .16);
            color: #fffdf8;
            font-family: Inter, Arial, sans-serif;
            font-size: .98rem;
            font-weight: 800;
            text-decoration: none;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .hero-button:hover,
        .pricing-button:hover {
            color: #fffdf8;
            transform: translateY(-1px);
            box-shadow: 0 18px 42px rgba(95, 60, 24, .2);
        }

        .hero-video-frame {
            position: relative;
            overflow: hidden;
            width: 100%;
            max-width: 720px;
            margin-left: auto;
            border: 1px solid rgba(95, 60, 24, .16);
            border-radius: .5rem;
            background:
                linear-gradient(180deg, rgba(255, 250, 241, .28), rgba(32, 23, 15, .08)),
                #20170f;
            box-shadow: 0 34px 90px rgba(95, 60, 24, .18);
        }

        .hero-video-frame::before {
            content: "";
            position: absolute;
            inset: .85rem;
            z-index: 2;
            pointer-events: none;
            border: 1px solid rgba(255, 253, 248, .36);
            border-radius: .35rem;
        }

        .hero-video-frame::after {
            content: "Presence Window";
            position: absolute;
            top: 1.45rem;
            left: 1.45rem;
            z-index: 3;
            padding: .45rem .72rem;
            border: 1px solid rgba(168, 135, 63, .24);
            border-radius: 999px;
            background: rgba(255, 250, 241, .92);
            color: var(--landing-brown);
            font-family: Inter, Arial, sans-serif;
            font-size: .74rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .hero-video {
            width: 100%;
            display: block;
            aspect-ratio: 16 / 10;
            object-fit: cover;
        }

        .section-header {
            display: grid;
            gap: 1rem;
            max-width: 48rem;
            margin-bottom: 2.1rem;
        }

        .section-heading {
            margin: 0;
            color: var(--landing-brown);
            font-size: clamp(2.3rem, 4vw, 3.45rem);
            font-weight: 600;
            line-height: 1;
        }

        .tier-grid,
        .pricing-grid,
        .preview-grid {
            display: grid;
            gap: 1.5rem;
        }

        .tier-grid,
        .pricing-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .preview-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .tier-card,
        .preview-card,
        .pricing-card,
        .briefing-panel {
            border: 1px solid var(--landing-line);
            border-radius: .5rem;
            background: rgba(255, 253, 248, .78);
            box-shadow: 0 18px 48px rgba(95, 60, 24, .06);
        }

        .tier-card,
        .preview-card,
        .pricing-card {
            padding: clamp(1.45rem, 2.6vw, 2rem);
        }

        .tier-label {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            margin-bottom: 1rem;
        }

        .tier-label::before {
            content: "";
            width: .55rem;
            height: .55rem;
            border-radius: 50%;
            background: var(--landing-gold);
        }

        .tier-name {
            margin: 0 0 .85rem;
            color: var(--landing-gold);
            font-size: clamp(2rem, 3.4vw, 2.85rem);
            font-weight: 600;
            line-height: 1;
        }

        .tier-window {
            display: grid;
            gap: .9rem;
            margin-top: 1.65rem;
            padding: 1.25rem;
            border: 1px solid rgba(168, 135, 63, .2);
            border-radius: .5rem;
            background: linear-gradient(180deg, rgba(255, 250, 241, .96), rgba(247, 240, 227, .96));
        }

        .window-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            color: var(--landing-brown);
            font-family: Inter, Arial, sans-serif;
            font-size: .83rem;
            font-weight: 800;
        }

        .window-dots {
            display: inline-flex;
            gap: .4rem;
        }

        .window-dots span {
            width: .55rem;
            height: .55rem;
            border-radius: 50%;
            background: rgba(95, 60, 24, .26);
        }

        .window-message {
            padding: 1rem 1.05rem;
            border-radius: .5rem;
            background: #fffdf8;
            color: var(--landing-brown);
            font-family: Inter, Arial, sans-serif;
            font-size: .98rem;
            font-weight: 700;
            line-height: 1.55;
        }

        .feature-list,
        .pricing-list,
        .preview-list {
            margin: 1.35rem 0 0;
            padding-left: 1.1rem;
        }

        .feature-list li,
        .pricing-list li,
        .preview-list li {
            margin-bottom: .7rem;
            font-size: .98rem;
            line-height: 1.55;
        }

        .tier-metrics {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .85rem;
            margin-top: 1.65rem;
        }

        .metric-card {
            min-height: 6.5rem;
            display: grid;
            align-content: center;
            padding: 1rem;
            border: 1px solid rgba(168, 135, 63, .14);
            border-radius: .5rem;
            background: linear-gradient(180deg, rgba(248, 242, 228, .95), rgba(255, 253, 248, .95));
        }

        .metric-label {
            color: var(--landing-muted);
            font-family: Inter, Arial, sans-serif;
            font-size: .76rem;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .metric-value {
            margin-top: .35rem;
            color: var(--landing-brown);
            font-family: "Playfair Display", Georgia, serif;
            font-size: 1.45rem;
            font-weight: 600;
            line-height: 1.08;
        }

        .preview-card-title {
            margin: 0 0 .85rem;
            color: var(--landing-brown);
            font-size: 1.72rem;
            font-weight: 600;
            line-height: 1.05;
        }

        .preview-card-copy {
            min-height: 5.2rem;
        }

        .preview-list {
            color: var(--landing-muted);
            font-family: Inter, Arial, sans-serif;
        }

        .preview-visual {
            min-height: 9rem;
            margin-top: 1.35rem;
            padding: 1rem;
            border: 1px solid rgba(168, 135, 63, .14);
            border-radius: .5rem;
            background: linear-gradient(180deg, rgba(248, 242, 228, .96), rgba(255, 253, 248, .96));
        }

        .preview-lines {
            display: grid;
            gap: .55rem;
        }

        .preview-lines span,
        .preview-bars span {
            display: block;
            border-radius: 999px;
            background: rgba(168, 135, 63, .24);
        }

        .preview-lines span:nth-child(1) {
            width: 92%;
            height: .8rem;
        }

        .preview-lines span:nth-child(2) {
            width: 78%;
            height: .7rem;
        }

        .preview-lines span:nth-child(3) {
            width: 64%;
            height: .7rem;
        }

        .preview-bars {
            display: flex;
            align-items: end;
            gap: .55rem;
            min-height: 5.5rem;
        }

        .preview-bars span {
            width: 18%;
        }

        .preview-bars span:nth-child(1) {
            height: 42%;
        }

        .preview-bars span:nth-child(2) {
            height: 78%;
            background: rgba(86, 98, 79, .34);
        }

        .preview-bars span:nth-child(3) {
            height: 58%;
        }

        .preview-bars span:nth-child(4) {
            height: 92%;
            background: rgba(168, 135, 63, .44);
        }

        .badge-medallion {
            width: 6.3rem;
            height: 6.3rem;
            display: grid;
            place-items: center;
            margin: 0 auto;
            border: 1px solid rgba(168, 135, 63, .38);
            border-radius: 50%;
            background: #f4ead1;
            color: var(--landing-gold-deep);
            font-family: "Playfair Display", Georgia, serif;
            font-size: 2.1rem;
            font-weight: 700;
        }

        .briefing-grid {
            display: grid;
            grid-template-columns: minmax(0, .92fr) minmax(0, 1.08fr);
            gap: clamp(1.5rem, 4vw, 3.5rem);
            align-items: center;
        }

        .briefing-panel {
            position: relative;
            min-height: 28rem;
            padding: clamp(1.25rem, 3vw, 2rem);
            background:
                linear-gradient(180deg, rgba(255, 253, 248, .9), rgba(248, 242, 228, .8)),
                #f7f0e3;
        }

        .briefing-sheet {
            position: absolute;
            width: 58%;
            min-height: 17rem;
            padding: 1.25rem;
            border: 1px solid rgba(95, 60, 24, .14);
            border-radius: .35rem;
            background: #fffdf8;
            box-shadow: 0 20px 42px rgba(95, 60, 24, .11);
        }

        .briefing-sheet.primary {
            right: 2rem;
            bottom: 2rem;
            z-index: 3;
        }

        .briefing-sheet.secondary {
            top: 2rem;
            left: 2rem;
            z-index: 2;
            transform: rotate(-2deg);
        }

        .briefing-sheet.tertiary {
            right: 5.4rem;
            top: 3.6rem;
            z-index: 1;
            transform: rotate(2deg);
            opacity: .78;
        }

        .briefing-title {
            margin: 0;
            color: var(--landing-brown);
            font-size: clamp(2.2rem, 4vw, 3.35rem);
            font-weight: 600;
            line-height: 1;
        }

        .briefing-line {
            height: .55rem;
            margin-top: .75rem;
            border-radius: 999px;
            background: rgba(168, 135, 63, .22);
        }

        .briefing-line.short {
            width: 62%;
        }

        .briefing-verdict {
            margin-top: 1.5rem;
            color: var(--landing-olive);
            font-family: "Playfair Display", Georgia, serif;
            font-size: 1.35rem;
            font-weight: 600;
            line-height: 1.15;
        }

        .briefing-image-card {
            margin: 0;
            padding: clamp(.65rem, 1.6vw, .95rem);
            border: 1px solid var(--landing-line);
            border-radius: .5rem;
            background: rgba(255, 253, 248, .78);
            box-shadow: 0 24px 58px rgba(95, 60, 24, .1);
        }

        .briefing-image-card img {
            display: block;
            max-width: 100%;
            width: 100%;
            height: auto;
            object-fit: contain;
            border-radius: .42rem;
            box-shadow: 0 18px 42px rgba(95, 60, 24, .11);
        }

        .pricing-card {
            display: grid;
            gap: 1rem;
        }

        .pricing-header {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 1rem;
        }

        .pricing-plan-name,
        .pricing-plan-price {
            margin: 0;
            color: var(--landing-brown);
            font-size: 2rem;
            font-weight: 600;
            line-height: 1;
        }

        .pricing-button {
            width: 100%;
            margin-top: .35rem;
        }

        .pricing-form {
            margin: 0;
        }

        button.pricing-button {
            border: 0;
        }

        .pricing-button:disabled {
            cursor: not-allowed;
            opacity: .72;
            box-shadow: none;
        }

        .faq-list {
            display: grid;
            gap: 1.1rem;
        }

        .faq-item {
            padding: 1.25rem 1.35rem;
            border: 1px solid var(--landing-line);
            border-radius: .5rem;
            background: rgba(255, 253, 248, .78);
        }

        .faq-question {
            margin: 0 0 .65rem;
            color: var(--landing-brown);
            font-size: 1.42rem;
            font-weight: 600;
            line-height: 1.15;
        }

        @media (max-width: 991.98px) {
            .hero-grid,
            .tier-grid,
            .pricing-grid,
            .preview-grid,
            .briefing-grid {
                grid-template-columns: 1fr;
            }

            .hero-grid {
                min-height: auto;
                width: min(100%, calc(100vw - 2rem));
            }

            .hero-copy {
                max-width: none;
                padding-top: 0;
            }

            .landing-title {
                max-width: 11.5ch;
                overflow-wrap: break-word;
            }

            .hero-video-frame {
                max-width: none;
                margin-left: 0;
            }

            .tier-metrics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .landing-shell {
                width: min(100%, calc(100vw - 2rem));
            }

            .landing-section {
                padding: 3.5rem 0;
            }

            #hero.landing-section {
                padding: 3rem 0 3.5rem;
            }

            .landing-title {
                max-width: 100%;
                font-size: clamp(3rem, 15vw, 4.15rem);
                line-height: 1;
            }

            .hero-summary,
            .hero-detail,
            .section-heading,
            .briefing-title {
                max-width: 100%;
                overflow-wrap: break-word;
            }

            .hero-positioning {
                font-size: clamp(1rem, 4.7vw, 1.18rem);
                line-height: 1.25;
            }

            .hero-button,
            .pricing-button {
                width: 100%;
            }

            .tier-metrics {
                grid-template-columns: 1fr;
            }

            .briefing-panel {
                min-height: 24rem;
            }

            .briefing-sheet {
                width: 70%;
                min-height: 14rem;
            }

            .pricing-header {
                display: grid;
                gap: .55rem;
            }
        }
    </style>

    <div class="landing-page" id="top">
        <div class="landing-shell">
            @unless ($isHidden('hero'))
            <section class="landing-section" id="hero">
                <div class="hero-grid">
                    <div class="hero-copy">
                        <h1 class="landing-title">{{ $contentTitle('hero', 'Most Zoom calls fade into silence.') }}</h1>
                        <p class="hero-summary">{{ $contentSubtitle('hero', 'One private window sees what others miss — and speaks only to you.') }}</p>
                        <p class="hero-positioning">Presence Intelligence for High-Stakes Conversations</p>
                        <p class="hero-detail">{!! nl2br(e($contentBody('hero', 'Infinite Sugar reads presence, timing, and conversational movement while the call is still alive, then returns calm guidance that helps the next sentence land with control.'))) !!}</p>
                        <div class="hero-actions">
                            <a class="hero-button" href="{{ $section('hero')?->button_url ?: (auth()->check() ? route('extension.download') : route('signup')) }}">{{ $contentButton('hero', 'Install Extension') }}</a>
                        </div>
                    </div>
                    <div class="hero-video-frame">
                        <video class="hero-video" autoplay muted loop playsinline controls poster="{{ $contentImage('hero', 'assets/product-coaching-preview.svg') }}">
                            <source src="{{ asset('assets/infinitesugar video (1).mp4') }}" type="video/mp4">
                        </video>
                    </div>
                </div>
            </section>
            @endunless

            @unless ($isHidden('spark_intro'))
            <section class="landing-section" id="spark">
                <div class="section-header">
                    <div class="landing-eyebrow">{{ $contentSubtitle('spark_intro', 'First tier') }}</div>
                    <h2 class="section-heading">{{ $contentTitle('spark_intro', 'Spark keeps it simple: a floating window and real-time insights.') }}</h2>
                    <p class="section-copy">{!! nl2br(e($contentBody('spark_intro', 'Spark is the first tier. It gives you live behavioral guidance in a small floating window that only you can see, so the next move is clearer before the moment passes.'))) !!}</p>
                </div>
                <div class="tier-grid">
                    @unless ($isHidden('spark_live'))
                    <article class="tier-card">
                        <div class="tier-label">{{ $contentSubtitle('spark_live', 'Spark') }}</div>
                        <h3 class="tier-name">{{ $contentTitle('spark_live', 'Live behavioral guidance. Private. Instant. Directional.') }}</h3>
                        <p class="section-copy">{!! nl2br(e($contentBody('spark_live', 'A small floating window overlays your Zoom call, visible only to you. In a second, it signals direction and presence while the moment still matters.'))) !!}</p>
                        <div class="tier-window">
                            <div class="window-bar">
                                <span class="window-dots"><span></span><span></span><span></span></span>
                                <span>Floating window</span>
                            </div>
                            <div class="window-message">Pause before pitching. Let the client finish the concern.</div>
                            <div class="window-message">Presence steady. Ask one clarifying question next.</div>
                        </div>
                    </article>
                    @endunless
                    @unless ($isHidden('spark_help'))
                    <article class="tier-card">
                        <div class="tier-label">{{ $contentSubtitle('spark_help', 'Why it helps') }}</div>
                        <h3 class="tier-name">{{ $contentTitle('spark_help', 'Clear enough to use immediately.') }}</h3>
                        @foreach ($paragraphs($contentBody('spark_help', 'Spark focuses on the live moment. It does not overwhelm users with extra layers. It gives private guidance and real-time insights while you stay in the call.'."\n\n".'Awareness is powerful. Knowing what drives impact begins with Forge.')) as $index => $copy)
                            <p class="section-copy {{ $index > 0 ? 'mt-3' : '' }}">{{ $copy }}</p>
                        @endforeach
                        <ul class="feature-list">
                            <li>Private live window visible only to you</li>
                            <li>Real-time insights during the conversation</li>
                            <li>Simple prompts that help you adjust in the moment</li>
                        </ul>
                    </article>
                    @endunless
                </div>
            </section>
            @endunless

            @unless ($isHidden('forge_intro'))
            <section class="landing-section" id="forge">
                <div class="section-header">
                    <div class="landing-eyebrow">{{ $contentSubtitle('forge_intro', 'Second tier') }}</div>
                    <h2 class="section-heading">{{ $contentTitle('forge_intro', 'Forge adds strategic insight, reports, and charts without making the experience feel heavy.') }}</h2>
                    <p class="section-copy">{!! nl2br(e($contentBody('forge_intro', "Forge is the second tier. It carries Spark's live guidance into weekly reports, Sunday charts, measurable progress, and monthly progress badges so long-term patterns become easier to understand over time."))) !!}</p>
                </div>
                <div class="tier-grid">
                    @unless ($isHidden('forge_behavioral'))
                    <article class="tier-card">
                        <div class="tier-label">{{ $contentSubtitle('forge_behavioral', 'Forge') }}</div>
                        <h3 class="tier-name">{{ $contentTitle('forge_behavioral', 'Behavioral intelligence that becomes easier to trust over time.') }}</h3>
                        <p class="section-copy">{!! nl2br(e($contentBody('forge_behavioral', 'The live signal remains calm and private. The follow-through becomes more strategic, turning presence into patterns that feel visible, earned, and actionable.'))) !!}</p>
                    </article>
                    @endunless
                    @unless ($isHidden('forge_included'))
                    <article class="tier-card">
                        <div class="tier-label">{{ $contentSubtitle('forge_included', 'Included') }}</div>
                        <h3 class="tier-name">{{ $contentTitle('forge_included', 'Live guidance, then quiet executive follow-through.') }}</h3>
                        <div class="tier-metrics">
                            <div class="metric-card">
                                <div class="metric-label">Guidance</div>
                                <div class="metric-value">Live insights</div>
                            </div>
                            <div class="metric-card">
                                <div class="metric-label">Reports</div>
                                <div class="metric-value">Weekly reports</div>
                            </div>
                            <div class="metric-card">
                                <div class="metric-label">Charts</div>
                                <div class="metric-value">Sunday charts</div>
                            </div>
                            <div class="metric-card">
                                <div class="metric-label">Progress</div>
                                <div class="metric-value">Monday progress summaries</div>
                            </div>
                        </div>
                    </article>
                    @endunless
                </div>
            </section>
            @endunless

            @unless ($isHidden('intelligence_intro'))
            <section class="landing-section" id="intelligence">
                <div class="section-header">
                    <div class="landing-eyebrow">{{ $contentSubtitle('intelligence_intro', 'Intelligence') }}</div>
                    <h2 class="section-heading">{{ $contentTitle('intelligence_intro', 'Forge Reports & Briefings') }}</h2>
                    @foreach ($paragraphs($contentBody('intelligence_intro', 'Plain-language deliverables that show what users receive after the live call.'."\n".'Forge customers receive weekly reports, Sunday charts, and monthly progress summaries. The goal is simple: make progress visible without making users decode complicated analytics.')) as $copy)
                        <p class="section-copy">{{ $copy }}</p>
                    @endforeach
                </div>
                <div class="preview-grid">
                    @unless ($isHidden('intelligence_live_signals'))
                    <article class="preview-card">
                        <div class="card-kicker">{{ $contentSubtitle('intelligence_live_signals', 'Live Signals') }}</div>
                        <h3 class="preview-card-title">{{ $contentTitle('intelligence_live_signals', 'Turns live behavioral signals into measurable deal movement.') }}</h3>
                        <p class="preview-card-copy">{!! nl2br(e($contentBody('intelligence_live_signals', 'Makes timing visible, where revenue is protected or lost. Built for operators responsible for outcomes.'))) !!}</p>
                        <ul class="preview-list">
                            <li>Protect pricing power under pressure</li>
                            <li>Prevent authority erosion</li>
                            <li>Time the close when control is strongest</li>
                            <li>Improve forecast accuracy through behavioral consistency</li>
                        </ul>
                    </article>
                    @endunless
                    @unless ($isHidden('intelligence_sunday'))
                    <article class="preview-card">
                        <div class="card-kicker">{{ $contentSubtitle('intelligence_sunday', 'WEEKLY PERFORMANCE BRIEF • SUNDAY 9 PM') }}</div>
                        <h3 class="preview-card-title">{{ $contentTitle('intelligence_sunday', 'Weekly Intelligence Brief') }}</h3>
                        <ul class="preview-list preview-card-copy">
                            @foreach ($paragraphs($contentBody('intelligence_sunday', "Meaningful patterns surfaced\nEmerging opportunities identified\nKey moments highlighted\nStrategic observations delivered")) as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                        <div class="preview-visual">
                            <div class="preview-bars">
                                <span></span>
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </div>
                        <p class="preview-meta">{{ $contentButton('intelligence_sunday', 'The week, distilled.') }}</p>
                    </article>
                    @endunless
                    @unless ($isHidden('intelligence_badge'))
                    <article class="preview-card">
                        <div class="card-kicker">{{ $contentSubtitle('intelligence_badge', 'MONTHLY PERFORMANCE SUMMARY • MONDAY 8 AM') }}</div>
                        <h3 class="preview-card-title">{{ $contentTitle('intelligence_badge', 'Achievement Review') }}</h3>
                        <ul class="preview-list preview-card-copy">
                            @foreach ($paragraphs($contentBody('intelligence_badge', "One earned badge\nPerformance milestones recognized\nConsistency measured over time\nGrowth documented month after month")) as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                        <div class="preview-visual">
                            <div class="badge-medallion">F</div>
                        </div>
                        <p class="preview-meta">{{ $contentButton('intelligence_badge', 'No fluff. Just proof the edge is repeatable.') }}</p>
                    </article>
                    @endunless
                </div>
            </section>
            @endunless

            @unless ($isHidden('executive_briefings'))
            <section class="landing-section" id="executive-briefings">
                <div class="briefing-grid">
                    <div>
                        <div class="landing-eyebrow">{{ $contentSubtitle('executive_briefings', 'Executive Briefings') }}</div>
                        <h2 class="briefing-title">{{ $contentTitle('executive_briefings', 'Reports and charts designed like luxury executive deliverables.') }}</h2>
                        <p class="briefing-copy mt-3">{!! nl2br(e($contentBody('executive_briefings', 'Every Forge output is interpretive before it is visual. The system frames what changed, why it matters, and what identity shift the user is building, without exposing internal mechanics or raw metric noise.'))) !!}</p>
                    </div>
                    <figure class="briefing-image-card" aria-label="Executive report and chart preview">
                        <img
                            src="{{ $contentImage('executive_briefings', 'images/briefings-and-reports.jpg') }}"
                            alt="InfiniteSugar executive report and chart preview"
                            loading="lazy"
                        >
                    </figure>
                </div>
            </section>
            @endunless

            @unless ($isHidden('pricing_intro'))
            <section class="landing-section" id="pricing">
                <div class="section-header">
                    <div class="landing-eyebrow">{{ $contentSubtitle('pricing_intro', 'Pricing') }}</div>
                    <h2 class="section-heading">{{ $contentTitle('pricing_intro', 'Choose Spark for live guidance or Forge for guidance plus reports and charts.') }}</h2>
                    <p class="section-copy">{!! nl2br(e($contentBody('pricing_intro', 'Checkout stays inside the existing Stripe flow. Spark gives new users a 30-minute free call allowance before paid access continues.'))) !!}</p>
                </div>
                <div class="pricing-grid">
                    @unless ($isHidden('pricing_spark'))
                    <article class="pricing-card">
                        <div class="pricing-header">
                            <div>
                                <div class="pricing-small">{{ $contentTitle('pricing_spark', 'Spark') }}</div>
                                <h3 class="pricing-plan-name">{{ $contentTitle('pricing_spark', 'Spark') }}</h3>
                            </div>
                            <div class="pricing-plan-price">{{ $contentSubtitle('pricing_spark', '$79/month') }}</div>
                        </div>
                        <p class="pricing-copy">{!! nl2br(e($contentBody('pricing_spark', 'Includes one free 30-minute Spark call, then continued private live guidance through Stripe-managed access.'))) !!}</p>
                        <ul class="pricing-list">
                            <li>Private floating window during the call</li>
                            <li>Real-time insights while the moment still matters</li>
                            <li>Simple live guidance without extra reporting layers</li>
                        </ul>
                        @if ($isCurrentPlan('spark'))
                            <button class="pricing-button" type="button" disabled>Current Spark Plan</button>
                        @else
                            <form class="pricing-form" method="post" action="{{ route('billing.checkout', 'spark') }}">
                                @csrf
                                <button class="pricing-button" type="submit">{{ $contentButton('pricing_spark', 'Start Spark') }}</button>
                            </form>
                        @endif
                    </article>
                    @endunless
                    @unless ($isHidden('pricing_forge'))
                    <article class="pricing-card">
                        <div class="pricing-header">
                            <div>
                                <div class="pricing-small">{{ $contentTitle('pricing_forge', 'Forge') }}</div>
                                <h3 class="pricing-plan-name">{{ $contentTitle('pricing_forge', 'Forge') }}</h3>
                            </div>
                            <div class="pricing-plan-price">{{ $contentSubtitle('pricing_forge', '$249/month') }}</div>
                        </div>
                        <p class="pricing-copy">{!! nl2br(e($contentBody('pricing_forge', 'For operators who need live insights plus weekly reports, Sunday charts, and Monday progress summaries.'))) !!}</p>
                        <ul class="pricing-list">
                            <li>Includes the live behavioral signals from Spark</li>
                            <li>Adds executive reporting that makes progress visible</li>
                            <li>Reinforces repeatable behavior through earned badges</li>
                        </ul>
                        @if ($isCurrentPlan('forge'))
                            <button class="pricing-button" type="button" disabled>Current Forge Plan</button>
                        @else
                            <form class="pricing-form" method="post" action="{{ route('billing.checkout', 'forge') }}">
                                @csrf
                                <button class="pricing-button" type="submit">{{ $contentButton('pricing_forge', 'Start Forge') }}</button>
                            </form>
                        @endif
                    </article>
                    @endunless
                </div>
            </section>
            @endunless

            @unless ($isHidden('faq_intro'))
            <section class="landing-section" id="faq">
                <div class="section-header">
                    <div class="landing-eyebrow">{{ $contentSubtitle('faq_intro', 'FAQ') }}</div>
                    <h2 class="section-heading">{{ $contentTitle('faq_intro', 'Simple answers on the same page.') }}</h2>
                    <p class="section-copy">{!! nl2br(e($contentBody('faq_intro', 'Users can keep scrolling and get the core answers without leaving the landing experience.'))) !!}</p>
                </div>
                <div class="faq-list">
                    @foreach ([
                        ['Does Infinite Sugar record or store my calls?', 'No. It does not record audio or video, generate transcripts, or store full call content. It interprets live behavioral signals only, not spoken words.'],
                        ['Is anything visible to other people on the call?', 'No. The floating window, signals, reports, and badges are visible only to you. If you share your screen, anything visible on your screen will appear like any application.'],
                        ['Does Infinite Sugar access my Zoom account?', 'No. It does not access your Zoom account, meeting history, or recordings. It runs as a private overlay during live calls.'],
                        ['How secure is my data?', 'Insights and summaries are stored securely and accessible only to your account. Nothing is shared, sold, or used to train other systems.'],
                        ['Does my camera need to be on?', 'Yes. For best precision, guidance and reports rely on visual presence and engagement stability.'],
                        ['How and when do I receive Forge reports?', 'Weekly by email, with Sunday charts and Monday progress summaries carrying the same calm executive presentation over time.'],
                        ['Can I try it without committing?', 'Yes. Spark includes one free 30-minute call allowance. Upgrade, pause, or cancel anytime.'],
                    ] as [$question, $answer])
                        <article class="faq-item">
                            <h3 class="faq-question">{{ $question }}</h3>
                            <p class="faq-answer">{{ $answer }}</p>
                        </article>
                    @endforeach
                </div>
            </section>
            @endunless
        </div>
    </div>
</x-layouts.app>
