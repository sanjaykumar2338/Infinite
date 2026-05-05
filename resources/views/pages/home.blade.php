<x-layouts.app title="infinitesugar">
    <style>
        .landing-page {
            --landing-cream: #f8f2e4;
            --landing-cream-soft: #fffaf1;
            --landing-paper: #fffdf9;
            --landing-brown: #5d4327;
            --landing-brown-soft: #7b6248;
            --landing-gold: #ac8a46;
            --landing-gold-soft: #dbc28c;
            --landing-line: rgba(93, 67, 39, .12);
            margin-inline: calc(50% - 50vw);
            background:
                radial-gradient(circle at top left, rgba(219, 194, 140, .28), transparent 30%),
                linear-gradient(180deg, #fffdf8 0%, #f9f3e8 48%, #fffaf2 100%);
            color: #1a140f;
        }

        .landing-page section[id] {
            scroll-margin-top: 7rem;
        }

        .landing-shell {
            width: min(1120px, calc(100vw - 2.5rem));
            margin: 0 auto;
        }

        .landing-section {
            padding: clamp(4rem, 8vw, 7rem) 0;
        }

        .landing-section + .landing-section {
            border-top: 1px solid var(--landing-line);
        }

        .landing-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: .7rem;
            margin-bottom: 1.25rem;
            color: var(--landing-brown-soft);
            font-size: .8rem;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .landing-eyebrow::before {
            content: "";
            width: 2.5rem;
            height: 1px;
            background: currentColor;
        }

        .landing-title,
        .landing-subtitle,
        .tier-name,
        .preview-card-title,
        .pricing-title,
        .faq-question {
            font-family: "Playfair Display", Georgia, serif;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: minmax(0, .9fr) minmax(0, 1.1fr);
            gap: clamp(2rem, 4vw, 4rem);
            align-items: center;
        }

        .hero-copy {
            max-width: 32rem;
        }

        .landing-title {
            margin: 0 0 1.25rem;
            color: var(--landing-gold);
            font-size: clamp(3.3rem, 7vw, 5.75rem);
            line-height: .94;
            font-weight: 600;
        }

        .hero-summary {
            margin: 0 0 1.15rem;
            color: var(--landing-brown);
            font-size: clamp(1.16rem, 1.8vw, 1.45rem);
            line-height: 1.45;
        }

        .hero-detail,
        .section-copy,
        .feature-list li,
        .preview-card-copy,
        .preview-meta,
        .pricing-copy,
        .pricing-list li,
        .faq-answer {
            color: var(--landing-brown-soft);
            font-family: Inter, Arial, sans-serif;
        }

        .hero-detail,
        .section-copy,
        .preview-card-copy,
        .pricing-copy,
        .faq-answer {
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
        .anchor-button,
        .pricing-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 3.25rem;
            padding: .85rem 1.4rem;
            border-radius: 999px;
            font-family: Inter, Arial, sans-serif;
            font-size: .98rem;
            font-weight: 700;
            text-decoration: none;
            transition: transform .2s ease, box-shadow .2s ease, background-color .2s ease, color .2s ease;
        }

        .hero-button,
        .pricing-button {
            background: linear-gradient(135deg, #b89a5d, #9d7838);
            box-shadow: 0 16px 36px rgba(93, 67, 39, .16);
            color: #fffef9;
        }

        .anchor-button {
            border: 1px solid rgba(93, 67, 39, .18);
            background: rgba(255, 253, 249, .82);
            color: var(--landing-brown);
        }

        .hero-button:hover,
        .anchor-button:hover,
        .pricing-button:hover {
            transform: translateY(-1px);
        }

        .hero-button:hover,
        .pricing-button:hover {
            color: #fffef9;
        }

        .anchor-button:hover {
            color: #1a140f;
            border-color: rgba(172, 138, 70, .52);
            background: #fffefb;
        }

        .hero-video-wrap {
            position: relative;
        }

        .hero-video-frame {
            position: relative;
            border: 1px solid rgba(93, 67, 39, .12);
            border-radius: 1.8rem;
            background: #20170f;
            overflow: hidden;
            box-shadow: 0 30px 70px rgba(93, 67, 39, .16);
        }

        .hero-video-frame::before {
            content: "Live Zoom Window";
            position: absolute;
            top: 1rem;
            left: 1rem;
            z-index: 2;
            padding: .45rem .75rem;
            border-radius: 999px;
            background: rgba(255, 250, 241, .92);
            color: var(--landing-brown);
            font-family: Inter, Arial, sans-serif;
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .hero-video {
            width: 100%;
            display: block;
            aspect-ratio: 16 / 9;
            object-fit: cover;
        }

        .hero-note {
            display: grid;
            gap: .3rem;
            max-width: 18rem;
            margin-top: 1rem;
            margin-left: auto;
            padding: 1rem 1.1rem;
            border: 1px solid rgba(172, 138, 70, .24);
            border-radius: 1.1rem;
            background: rgba(255, 250, 241, .9);
            box-shadow: 0 18px 36px rgba(93, 67, 39, .08);
        }

        .hero-note strong,
        .section-heading,
        .pricing-plan-name,
        .pricing-plan-price {
            color: var(--landing-brown);
            font-family: "Playfair Display", Georgia, serif;
        }

        .hero-note strong {
            font-size: 1.08rem;
            line-height: 1.1;
        }

        .hero-note span {
            color: var(--landing-brown-soft);
            font-family: Inter, Arial, sans-serif;
            font-size: .95rem;
            line-height: 1.55;
        }

        .section-header {
            display: grid;
            gap: 1rem;
            max-width: 44rem;
            margin-bottom: 2rem;
        }

        .section-heading {
            margin: 0;
            font-size: clamp(2.25rem, 4vw, 3.4rem);
            line-height: .98;
            font-weight: 600;
        }

        .tier-grid,
        .pricing-grid,
        .preview-grid {
            display: grid;
            gap: 1.5rem;
        }

        .tier-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            align-items: stretch;
        }

        .tier-card,
        .preview-card,
        .pricing-card {
            border: 1px solid var(--landing-line);
            border-radius: 1.6rem;
            background: rgba(255, 253, 249, .8);
            box-shadow: 0 18px 48px rgba(93, 67, 39, .06);
        }

        .tier-card {
            padding: clamp(1.5rem, 2.6vw, 2rem);
        }

        .tier-label {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            margin-bottom: 1rem;
            color: var(--landing-brown-soft);
            font-family: Inter, Arial, sans-serif;
            font-size: .82rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .tier-label::before {
            content: "";
            width: .6rem;
            height: .6rem;
            border-radius: 50%;
            background: var(--landing-gold);
        }

        .tier-name {
            margin: 0 0 .85rem;
            color: var(--landing-gold);
            font-size: clamp(2.1rem, 4vw, 3rem);
            line-height: .95;
            font-weight: 600;
        }

        .tier-window {
            display: grid;
            gap: .9rem;
            margin-top: 1.65rem;
            padding: 1.25rem;
            border: 1px solid rgba(172, 138, 70, .18);
            border-radius: 1.25rem;
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
            font-weight: 700;
        }

        .window-dots {
            display: inline-flex;
            gap: .4rem;
        }

        .window-dots span {
            width: .55rem;
            height: .55rem;
            border-radius: 50%;
            background: rgba(93, 67, 39, .26);
        }

        .window-message {
            padding: 1rem 1.05rem;
            border-radius: 1rem;
            background: #fffdf8;
            color: var(--landing-brown);
            font-family: Inter, Arial, sans-serif;
            font-size: .98rem;
            font-weight: 600;
            line-height: 1.55;
        }

        .tier-metrics {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .85rem;
            margin-top: 1.65rem;
        }

        .metric-card {
            padding: 1rem;
            border-radius: 1.1rem;
            background: linear-gradient(180deg, rgba(248, 242, 228, .95), rgba(255, 253, 249, .95));
        }

        .metric-label,
        .preview-badge,
        .pricing-small {
            color: var(--landing-brown-soft);
            font-family: Inter, Arial, sans-serif;
            font-size: .8rem;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .metric-value {
            margin-top: .35rem;
            color: var(--landing-brown);
            font-family: "Playfair Display", Georgia, serif;
            font-size: 1.55rem;
            line-height: 1.05;
            font-weight: 600;
        }

        .feature-list,
        .pricing-list {
            margin: 1.35rem 0 0;
            padding-left: 1.1rem;
        }

        .feature-list li,
        .pricing-list li {
            margin-bottom: .7rem;
            font-size: .98rem;
            line-height: 1.55;
        }

        .preview-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .preview-card {
            padding: 1.45rem;
        }

        .preview-badge {
            display: inline-flex;
            margin-bottom: .9rem;
        }

        .preview-card-title {
            margin: 0 0 .85rem;
            color: var(--landing-brown);
            font-size: 1.7rem;
            line-height: 1;
            font-weight: 600;
        }

        .preview-card-copy {
            min-height: 4.8rem;
        }

        .preview-visual {
            margin-top: 1.35rem;
            padding: 1rem;
            border-radius: 1.1rem;
            background: linear-gradient(180deg, rgba(248, 242, 228, .96), rgba(255, 253, 249, .96));
        }

        .preview-lines {
            display: grid;
            gap: .5rem;
        }

        .preview-lines span,
        .preview-bars span {
            display: block;
            border-radius: 999px;
            background: rgba(172, 138, 70, .24);
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

        .preview-bars span:nth-child(1) {
            width: 18%;
            height: 42%;
        }

        .preview-bars span:nth-child(2) {
            width: 18%;
            height: 78%;
            background: rgba(172, 138, 70, .44);
        }

        .preview-bars span:nth-child(3) {
            width: 18%;
            height: 58%;
        }

        .preview-bars span:nth-child(4) {
            width: 18%;
            height: 92%;
            background: rgba(172, 138, 70, .44);
        }

        .preview-progress {
            display: grid;
            gap: .8rem;
        }

        .preview-progress-track {
            height: .9rem;
            border-radius: 999px;
            background: rgba(172, 138, 70, .18);
            overflow: hidden;
        }

        .preview-progress-track span {
            display: block;
            width: 71%;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #b89a5d, #8f6b31);
        }

        .preview-meta {
            margin-top: .85rem;
            font-size: .92rem;
            line-height: 1.6;
        }

        .pricing-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .pricing-card {
            display: grid;
            gap: 1rem;
            padding: clamp(1.6rem, 2.8vw, 2.1rem);
        }

        .pricing-card.featured {
            background: linear-gradient(180deg, rgba(247, 240, 227, .98), rgba(255, 253, 249, .98));
            border-color: rgba(172, 138, 70, .28);
        }

        .pricing-header {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 1rem;
        }

        .pricing-title {
            margin: 0;
            color: var(--landing-gold);
            font-size: clamp(2rem, 3.5vw, 2.8rem);
            line-height: .96;
            font-weight: 600;
        }

        .pricing-plan-name,
        .pricing-plan-price {
            margin: 0;
            font-size: 2rem;
            line-height: 1;
            font-weight: 600;
        }

        .pricing-small {
            display: inline-flex;
        }

        .pricing-button {
            width: 100%;
            margin-top: .35rem;
        }

        .faq-list {
            display: grid;
            gap: 1.2rem;
        }

        .faq-item {
            padding: 1.3rem 1.35rem;
            border: 1px solid var(--landing-line);
            border-radius: 1.25rem;
            background: rgba(255, 253, 249, .78);
        }

        .faq-question {
            margin: 0 0 .65rem;
            color: var(--landing-brown);
            font-size: 1.45rem;
            line-height: 1.15;
            font-weight: 600;
        }

        @media (max-width: 991.98px) {
            .hero-grid,
            .tier-grid,
            .pricing-grid,
            .preview-grid {
                grid-template-columns: 1fr;
            }

            .hero-copy {
                max-width: none;
            }

            .hero-note {
                margin-left: 0;
            }

            .tier-metrics {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .landing-shell {
                width: min(100%, calc(100vw - 2rem));
            }

            .landing-section {
                padding: 3.4rem 0;
            }

            .hero-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .hero-button,
            .anchor-button,
            .pricing-button {
                width: 100%;
            }

            .tier-metrics {
                grid-template-columns: 1fr;
            }

            .pricing-header {
                display: grid;
                gap: .4rem;
            }

            .faq-question {
                font-size: 1.28rem;
            }
        }
    </style>

    <div class="landing-page" id="top">
        <div class="landing-shell">
            <section class="landing-section" id="hero">
                <div class="hero-grid">
                    <div class="hero-copy">
                        <div class="landing-eyebrow">Private live guidance</div>
                        <h1 class="landing-title">Most Zoom calls fade into silence.</h1>
                        <p class="hero-summary">Infinite Sugar gives you a private live window with guidance and insights while the call is still happening.</p>
                        <p class="hero-detail">One private live window sees what others miss and speaks only to you, so you can stay present, read the moment, and move with more certainty.</p>
                        <div class="hero-actions">
                            <a class="hero-button" href="{{ auth()->check() ? route('extension.download') : route('signup') }}">Install Extension</a>
                            <a class="anchor-button" href="#pricing">See Pricing</a>
                        </div>
                    </div>
                    <div class="hero-video-wrap">
                        <div class="hero-video-frame">
                            <video class="hero-video" autoplay muted loop playsinline controls poster="{{ asset('assets/product-coaching-preview.svg') }}">
                                <source src="{{ asset('assets/infinitesugar video (1).mp4') }}" type="video/mp4">
                            </video>
                        </div>
                        <div class="hero-note">
                            <strong>What users see</strong>
                            <span>A live private window with directional guidance, behavioral signals, and simple prompts during the conversation.</span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="landing-section" id="spark">
                <div class="section-header">
                    <div class="landing-eyebrow">First tier</div>
                    <h2 class="section-heading">Spark keeps it simple: a floating window and real-time insights.</h2>
                    <p class="section-copy">Spark is the first tier. It gives you live behavioral guidance in a small floating window that only you can see, so the next move is clearer before the moment passes.</p>
                </div>
                <div class="tier-grid">
                    <article class="tier-card">
                        <div class="tier-label">Spark</div>
                        <h3 class="tier-name">Live behavioral guidance. Private. Instant. Directional.</h3>
                        <p class="section-copy">A small floating window overlays your Zoom call, visible only to you. In a second, it signals direction and presence while the moment still matters.</p>
                        <div class="tier-window">
                            <div class="window-bar">
                                <span class="window-dots"><span></span><span></span><span></span></span>
                                <span>Floating window</span>
                            </div>
                            <div class="window-message">Pause before pitching. Let the client finish the concern.</div>
                            <div class="window-message">Presence steady. Ask one clarifying question next.</div>
                        </div>
                    </article>
                    <article class="tier-card">
                        <div class="tier-label">Why it helps</div>
                        <h3 class="tier-name">Clear enough to use immediately.</h3>
                        <p class="section-copy">Spark focuses on the live moment. It does not overwhelm users with extra layers. It gives private guidance and real-time insights while you stay in the call.</p>
                        <ul class="feature-list">
                            <li>Private live window visible only to you</li>
                            <li>Real-time insights during the conversation</li>
                            <li>Simple prompts that help you adjust in the moment</li>
                        </ul>
                    </article>
                </div>
            </section>

            <section class="landing-section" id="forge">
                <div class="section-header">
                    <div class="landing-eyebrow">Second tier</div>
                    <h2 class="section-heading">Forge adds insights, reports, and charts without making the experience feel heavy.</h2>
                    <p class="section-copy">Forge is the second tier. It includes the live behavioral signals from Spark, then carries those insights into weekly reports, Sunday charts, and badge progress so results become measurable.</p>
                </div>
                <div class="tier-grid">
                    <article class="tier-card">
                        <div class="tier-label">Forge</div>
                        <h3 class="tier-name">Turns live behavioral signals into measurable deal movement.</h3>
                        <p class="section-copy">Makes timing visible, where revenue is protected or lost. Built for operators responsible for outcomes.</p>
                        <ul class="feature-list">
                            <li>Protect pricing power under pressure</li>
                            <li>Prevent authority erosion</li>
                            <li>Time the close when control is strongest</li>
                            <li>Improve forecast accuracy through behavioral consistency</li>
                        </ul>
                    </article>
                    <article class="tier-card">
                        <div class="tier-label">What’s included</div>
                        <h3 class="tier-name">Live signals plus reporting you can actually use.</h3>
                        <p class="section-copy">Forge keeps the live guidance, then adds simple follow-through after the call so users understand what changed and what to do next.</p>
                        <div class="tier-metrics">
                            <div class="metric-card">
                                <div class="metric-label">Insights</div>
                                <div class="metric-value">Live</div>
                            </div>
                            <div class="metric-card">
                                <div class="metric-label">Reports</div>
                                <div class="metric-value">Weekly</div>
                            </div>
                            <div class="metric-card">
                                <div class="metric-label">Charts</div>
                                <div class="metric-value">Sunday</div>
                            </div>
                        </div>
                    </article>
                </div>
            </section>

            <section class="landing-section" id="reports">
                <div class="section-header">
                    <div class="landing-eyebrow">Charts and reports</div>
                    <h2 class="section-heading">Plain-language deliverables that show what users receive after the live call.</h2>
                    <p class="section-copy">Forge customers receive weekly reports, Sunday charts, and monthly badge summaries. The goal is simple: make progress visible without making users decode complicated analytics.</p>
                </div>
                <div class="preview-grid">
                    <article class="preview-card">
                        <div class="preview-badge">Weekly Reports</div>
                        <h3 class="preview-card-title">Weekly Reports</h3>
                        <p class="preview-card-copy">A plain-language summary of behavioral signals, coaching opportunities, and progress notes.</p>
                        <div class="preview-visual">
                            <div class="preview-lines">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </div>
                        <p class="preview-meta">Users receive a simple written recap of what stood out and what to keep doing next.</p>
                    </article>
                    <article class="preview-card">
                        <div class="preview-badge">Sunday Charts</div>
                        <h3 class="preview-card-title">Sunday Charts</h3>
                        <p class="preview-card-copy">Two visual KPI snapshots for trend tracking and review, delivered in a format that is easy to scan.</p>
                        <div class="preview-visual">
                            <div class="preview-bars">
                                <span></span>
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </div>
                        <p class="preview-meta">Users get quick chart views that make progress and movement easier to spot over time.</p>
                    </article>
                    <article class="preview-card">
                        <div class="preview-badge">Badge Summaries</div>
                        <h3 class="preview-card-title">Monthly Badge Reports</h3>
                        <p class="preview-card-copy">Milestone reports that turn progress into a clear retention moment without extra setup.</p>
                        <div class="preview-visual">
                            <div class="preview-progress">
                                <div class="preview-progress-track"><span></span></div>
                                <div class="preview-progress-track"><span style="width: 84%;"></span></div>
                            </div>
                        </div>
                        <p class="preview-meta">Users see progress in a way that feels concrete, earned, and easy to understand.</p>
                    </article>
                </div>
            </section>

            <section class="landing-section" id="pricing">
                <div class="section-header">
                    <div class="landing-eyebrow">Pricing</div>
                    <h2 class="section-heading">Choose Spark for live guidance or Forge for guidance plus reports and charts.</h2>
                    <p class="section-copy">The homepage now carries the full pricing story in one place, while checkout still runs through the existing Stripe flow.</p>
                </div>
                <div class="pricing-grid">
                    <article class="pricing-card">
                        <div class="pricing-header">
                            <div>
                                <div class="pricing-small">Spark</div>
                                <h3 class="pricing-plan-name">Spark</h3>
                            </div>
                            <div class="pricing-plan-price">$79/month</div>
                        </div>
                        <p class="pricing-copy">Includes 1 free live call for 30 minutes. Experience real-time guidance before committing. Most people know within one call.</p>
                        <ul class="pricing-list">
                            <li>Private floating window during the call</li>
                            <li>Real-time insights while the moment still matters</li>
                            <li>Simple live guidance without extra reporting layers</li>
                        </ul>
                        <a class="pricing-button" href="{{ route('billing.checkout', 'spark') }}">Start Spark</a>
                    </article>
                    <article class="pricing-card featured">
                        <div class="pricing-header">
                            <div>
                                <div class="pricing-small">Forge</div>
                                <h3 class="pricing-plan-name">Forge</h3>
                            </div>
                            <div class="pricing-plan-price">$249/month</div>
                        </div>
                        <p class="pricing-copy">For operators who need live insights plus weekly reports, Sunday charts, and monthly badge summaries.</p>
                        <ul class="pricing-list">
                            <li>Includes the live behavioral signals from Spark</li>
                            <li>Adds simple reporting that makes progress visible</li>
                            <li>Helps users understand what changed and what to do next</li>
                        </ul>
                        <a class="pricing-button" href="{{ route('billing.checkout', 'forge') }}">Start Forge</a>
                    </article>
                </div>
            </section>

            <section class="landing-section" id="faqs">
                <div class="section-header">
                    <div class="landing-eyebrow">FAQ</div>
                    <h2 class="section-heading">Simple answers on the same page.</h2>
                    <p class="section-copy">Users can keep scrolling and get the core answers without leaving the landing experience.</p>
                </div>
                <div class="faq-list">
                    @foreach ([
                        ['Does Infinite Sugar record or store my calls?', 'No. It does not record audio or video, generate transcripts, or store full call content. It interprets live behavioral signals only, not spoken words.'],
                        ['Is anything visible to other people on the call?', 'No. The floating window, signals, reports, and badges are visible only to you. If you share your screen, anything visible on your screen will appear like any application.'],
                        ['Does Infinite Sugar access my Zoom account?', 'No. It does not access your Zoom account, meeting history, or recordings. It runs as a private overlay during live calls.'],
                        ['How secure is my data?', 'Insights and summaries are stored securely and accessible only to your account. Nothing is shared, sold, or used to train other systems.'],
                        ['Does my camera need to be on?', 'Yes. For best precision, guidance and reports rely on visual presence and engagement stability.'],
                        ['How and when do I receive Forge reports?', 'Weekly by email, with cumulative progress reflected over time. Sunday charts and monthly badge summaries keep that progress easy to follow.'],
                        ['Can I try it without committing?', 'Yes. Spark includes one free call. Upgrade, pause, or cancel anytime.'],
                    ] as [$question, $answer])
                        <article class="faq-item">
                            <h3 class="faq-question">{{ $question }}</h3>
                            <p class="faq-answer">{{ $answer }}</p>
                        </article>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
</x-layouts.app>
