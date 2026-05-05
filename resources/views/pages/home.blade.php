<x-layouts.app title="infinitesugar">
    <style>
        .reference-home {
            margin-inline: calc(50% - 50vw);
            background: #fff;
        }

        .reference-hero {
            width: 100%;
            padding: 70px 24px 78px;
            background: #fff;
        }

        .reference-hero-wrap {
            width: 100%;
            max-width: 1220px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: minmax(0, .9fr) minmax(0, 1.35fr);
            align-items: center;
            gap: 58px;
        }

        .reference-hero-title {
            margin: 0 0 24px;
            font-family: "Playfair Display", Georgia, serif;
            font-size: 64px;
            line-height: .98;
            font-weight: 500;
            color: #a8873f;
        }

        .reference-hero-copy {
            margin: 0 0 18px;
            max-width: 480px;
            color: #8d7755;
            font-family: Inter, Arial, sans-serif;
            font-size: 22px;
            line-height: 1.35;
            font-weight: 400;
        }

        .reference-hero-kicker {
            margin: 0 0 22px;
            max-width: 520px;
            color: #9b845e;
            font-family: Inter, Arial, sans-serif;
            font-size: 24px;
            line-height: 1.08;
            font-weight: 600;
        }

        .reference-hero-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 178px;
            padding: 14px 26px;
            border: 1px solid #fff;
            border-radius: 999px;
            background: #b8a16d;
            box-shadow: 0 10px 22px rgba(0, 0, 0, .22);
            color: #fff;
            font-family: Inter, Arial, sans-serif;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
        }

        .reference-hero-video {
            width: 100%;
            display: block;
            aspect-ratio: 16 / 9;
            object-fit: cover;
            background: #111;
        }

        .forge-editorial-page {
            width: 100%;
            background: #e8e3d7;
            padding: 52px 24px 46px;
            overflow: hidden;
        }

        .forge-editorial-wrap {
            width: 100%;
            max-width: 1050px;
            margin: 0 auto;
            padding: 0;
        }

        .forge-title {
            margin: 0 0 26px;
            color: #a8873f;
            font-family: "Playfair Display", Georgia, serif;
            font-size: 74px;
            line-height: .9;
            font-weight: 500;
        }

        .forge-line {
            margin: 0 0 14px;
            font-family: "Playfair Display", Georgia, serif;
            font-size: 34px;
            line-height: 1.02;
            font-weight: 500;
        }

        .forge-shift-lines {
            margin: 2px 0 18px;
        }

        .forge-shift-lines p,
        .forge-built-line,
        .forge-bottom-lines p {
            margin: 0;
            color: #a8873f;
            font-family: "Playfair Display", Georgia, serif;
            font-size: 34px;
            line-height: .98;
            font-weight: 500;
        }

        .forge-built-line {
            margin: 0 0 22px;
            color: #111;
            line-height: 1.01;
        }

        .forge-action-block {
            margin: 0 0 22px;
        }

        .forge-action-block h2 {
            margin: 0 0 6px;
            color: #111;
            font-family: "Playfair Display", Georgia, serif;
            font-size: 34px;
            line-height: .95;
            font-weight: 500;
        }

        .forge-action-block ul {
            margin: 0;
            padding-left: 26px;
        }

        .forge-action-block li {
            margin-bottom: 1px;
            color: #111;
            font-family: "Playfair Display", Georgia, serif;
            font-size: 20px;
            line-height: 1.05;
            font-weight: 400;
        }

        .forge-bottom-lines p {
            color: #111;
        }

        .gold {
            color: #a8873f;
        }

        .black {
            color: #111;
        }

        .is-pricing-page {
            width: 100%;
            padding: 68px 14px 0;
            background: #fff;
        }

        .is-pricing-wrap {
            max-width: 620px;
            margin: 0 auto;
            text-align: center;
        }

        .is-pricing-title {
            margin: 0 0 16px;
            color: #a57b22;
            font-family: "Playfair Display", Georgia, serif;
            font-size: 76px;
            line-height: .92;
            font-weight: 500;
        }

        .is-pricing-subtitle {
            margin: 0 0 42px;
            color: #111;
            font-family: "Playfair Display", Georgia, serif;
            font-size: 42px;
            line-height: 1.02;
            font-weight: 500;
        }

        .is-pricing-panel {
            padding: 16px;
            background: #c3aa68;
        }

        .is-pricing-card {
            padding: 26px 30px 28px;
            border-radius: 16px;
            background: #fcfcfb;
            text-align: left;
        }

        .is-pricing-card + .is-pricing-card {
            margin-top: 18px;
        }

        .is-pricing-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 18px;
        }

        .is-plan-name,
        .is-plan-price {
            margin: 0;
            color: #9e7721;
            font-family: "Playfair Display", Georgia, serif;
            font-size: 40px;
            line-height: .95;
            font-weight: 500;
        }

        .is-plan-price {
            font-size: 38px;
            text-align: right;
            white-space: nowrap;
        }

        .is-plan-small,
        .is-plan-text,
        .is-plan-list {
            color: #151515;
            font-family: Inter, Arial, sans-serif;
            font-size: 16px;
            line-height: 1.45;
            font-weight: 400;
        }

        .is-plan-small {
            margin: 0 0 18px;
            line-height: 1.35;
        }

        .is-plan-text {
            margin: 0 0 28px;
        }

        .is-plan-list {
            margin: 0 0 14px;
            padding-left: 20px;
            font-size: 15px;
        }

        .is-plan-btn {
            display: block;
            width: 100%;
            margin-top: 6px;
            padding: 0;
            color: #111;
            font-family: Inter, Arial, sans-serif;
            font-size: 16px;
            line-height: 1.3;
            font-weight: 700;
            text-align: center;
            text-decoration: none;
        }

        .is-faq-section {
            width: 100%;
            padding: 70px 40px 90px;
            background: #f8f5ed;
            overflow: hidden;
        }

        .is-faq-wrap {
            max-width: 980px;
            margin: 0 auto;
        }

        .is-faq-title {
            margin: 0 0 46px;
            color: #a97a28;
            font-family: Georgia, "Times New Roman", serif;
            font-size: 76px;
            line-height: .95;
            font-weight: 500;
            text-align: center;
        }

        .is-faq-list {
            display: flex;
            flex-direction: column;
            gap: 34px;
        }

        .is-faq-question {
            display: inline;
            margin: 0;
            color: #111;
            font-family: Georgia, "Times New Roman", serif;
            font-size: 30px;
            line-height: 1.08;
            font-weight: 500;
            background-image: linear-gradient(rgba(211, 180, 112, .28), rgba(211, 180, 112, .28));
            background-repeat: no-repeat;
            background-size: 100% 12px;
            background-position: 0 86%;
            padding-bottom: 2px;
        }

        .is-faq-answer {
            max-width: 860px;
            margin: 12px 0 0;
            color: #111;
            font-family: Georgia, "Times New Roman", serif;
            font-size: 21px;
            line-height: 1.18;
            font-weight: 300;
        }

        @media (max-width: 991px) {
            .reference-hero-wrap {
                grid-template-columns: 1fr;
                gap: 34px;
            }

            .reference-hero-title {
                font-size: 52px;
            }

            .reference-hero-copy {
                font-size: 20px;
            }

            .reference-hero-kicker {
                font-size: 22px;
            }

            .forge-title {
                font-size: 58px;
            }

            .forge-line,
            .forge-shift-lines p,
            .forge-built-line,
            .forge-action-block h2,
            .forge-bottom-lines p {
                font-size: 28px;
            }

            .forge-action-block li {
                font-size: 18px;
            }

            .is-faq-section {
                padding: 55px 28px 75px;
            }

            .is-faq-title {
                font-size: 58px;
            }

            .is-faq-question {
                font-size: 26px;
            }

            .is-faq-answer {
                font-size: 19px;
            }
        }

        @media (max-width: 767px) {
            .reference-hero {
                padding: 44px 18px 54px;
            }

            .reference-hero-title {
                font-size: 42px;
            }

            .reference-hero-copy {
                font-size: 18px;
            }

            .reference-hero-kicker {
                font-size: 19px;
            }

            .forge-editorial-page {
                padding: 34px 16px 36px;
            }

            .forge-title {
                font-size: 48px;
            }

            .forge-line,
            .forge-shift-lines p,
            .forge-built-line,
            .forge-action-block h2,
            .forge-bottom-lines p {
                font-size: 24px;
            }

            .forge-action-block li {
                font-size: 16px;
            }

            .is-pricing-page {
                padding: 48px 14px 0;
            }

            .is-pricing-title {
                font-size: 50px;
            }

            .is-pricing-subtitle {
                font-size: 28px;
                margin-bottom: 28px;
            }

            .is-pricing-panel {
                padding: 12px;
            }

            .is-pricing-card {
                padding: 20px 18px 22px;
                border-radius: 14px;
            }

            .is-plan-name,
            .is-plan-price {
                font-size: 29px;
            }

            .is-plan-small,
            .is-plan-text,
            .is-plan-btn {
                font-size: 16px;
            }

            .is-plan-list {
                font-size: 15px;
            }

            .is-faq-section {
                padding: 42px 18px 60px;
            }

            .is-faq-title {
                font-size: 44px;
                margin-bottom: 28px;
            }

            .is-faq-list {
                gap: 24px;
            }

            .is-faq-question {
                font-size: 20px;
                background-size: 100% 8px;
            }

            .is-faq-answer {
                font-size: 16px;
                line-height: 1.25;
            }
        }
    </style>

    <div class="reference-home">
        <section class="reference-hero">
            <div class="reference-hero-wrap">
                <div>
                    <h1 class="reference-hero-title">Most Zoom calls fade into silence.</h1>
                    <p class="reference-hero-copy">One private window sees what others miss<br>— and speaks only to you.</p>
                    <div class="reference-hero-kicker">Presence Intelligence for High-Stakes Conversations</div>
                    <a class="reference-hero-button" href="{{ auth()->check() ? route('extension.download') : route('signup') }}">Install Extension</a>
                </div>
                <video class="reference-hero-video" autoplay muted loop playsinline controls poster="{{ asset('assets/product-coaching-preview.svg') }}">
                    <source src="{{ asset('assets/infinitesugar video (1).mp4') }}" type="video/mp4">
                </video>
            </div>
        </section>

        <section class="forge-editorial-page" id="forge">
            <div class="forge-editorial-wrap">
                <h2 class="forge-title">FORGE</h2>
                <p class="forge-line"><span class="black">Turns </span><span class="gold">live behavioral signals into measurable deal movement.</span></p>
                <p class="forge-line black">Makes timing visible — where revenue is protected or lost.</p>
                <div class="forge-shift-lines">
                    <p>Composure shifting.</p>
                    <p>Authority strengthening.</p>
                    <p>Probability changing.</p>
                    <p>During it.</p>
                </div>
                <p class="forge-built-line">Built for operators responsible for outcomes.</p>
                <div class="forge-action-block">
                    <h2>In Action</h2>
                    <ul>
                        <li>Protect pricing power under pressure</li>
                        <li>Prevent authority erosion</li>
                        <li>Time the close when control is strongest</li>
                        <li>Improve forecast accuracy through behavioral consistency</li>
                    </ul>
                </div>
                <div class="forge-bottom-lines">
                    <p>Signals surface live.</p>
                    <p>Results become measurable.</p>
                </div>
            </div>
        </section>

        <section class="forge-editorial-page" id="spark" style="background: #fff;">
            <div class="forge-editorial-wrap">
                <h2 class="forge-title">SPARK</h2>
                <p class="forge-line black">Live behavioral guidance. Private. Instant. Directional.</p>
                <p class="is-plan-text" style="max-width: 760px;">A small floating window overlays your Zoom call — visible only to you. In a second, it signals direction and presence while the moment still matters.</p>
                <a class="reference-hero-button" href="{{ auth()->check() ? route('extension.download') : route('signup') }}">Install Extension</a>
            </div>
        </section>

        <section class="is-pricing-page" id="pricing">
            <div class="is-pricing-wrap">
                <h2 class="is-pricing-title">Pricing</h2>
                <h3 class="is-pricing-subtitle">You&rsquo;re not choosing features.<br>You&rsquo;re choosing certainty.</h3>
                <div class="is-pricing-panel">
                    <div class="is-pricing-card">
                        <div class="is-pricing-card-top">
                            <h4 class="is-plan-name">Spark</h4>
                            <div class="is-plan-price">$79/month</div>
                        </div>
                        <p class="is-plan-small">Includes 1 Free Live Call · 30 minutes</p>
                        <p class="is-plan-text">Experience real-time guidance before committing.<br>Most people know within one call.</p>
                        <a class="is-plan-btn" href="{{ route('signup') }}">Start Spark — See Your first Signal</a>
                    </div>

                    <div class="is-pricing-card">
                        <div class="is-pricing-card-top">
                            <h4 class="is-plan-name">Forge</h4>
                            <div class="is-plan-price">$249/month</div>
                        </div>
                        <p class="is-plan-small">For operators who need to know:</p>
                        <ul class="is-plan-list">
                            <li>When leverage forms.</li>
                            <li>When hesitation is costly.</li>
                            <li>When to move — and when to hold.</li>
                        </ul>
                        <p class="is-plan-text">It doesn&rsquo;t add complexity.<br>It removes second-guessing.<br>Decisions get quieter.<br>Timing gets sharper.<br>Results compound.</p>
                        <a class="is-plan-btn" href="{{ route('signup') }}">Start Forge — See Your first Signal</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="is-faq-section" id="faqs">
            <div class="is-faq-wrap">
                <h2 class="is-faq-title">FAQ</h2>
                <div class="is-faq-list">
                    @foreach ([
                        ['Does Infinite Sugar record or store my calls?', 'No. It does not record audio or video, generate transcripts, or store full call content. It interprets live behavioral signals only — not spoken words.'],
                        ['Is anything visible to other people on the call?', 'No. The floating window, signals, reports, and badges are visible only to you. If you share your screen, anything visible on your screen will appear — like any application.'],
                        ['Does Infinite Sugar access my Zoom account?', 'No. It does not access your Zoom account, meeting history, or recordings. It runs as a private overlay during live calls.'],
                        ['How secure is my data?', 'Insights and summaries are stored securely and accessible only to your account. Nothing is shared, sold, or used to train other systems.'],
                        ['Does my camera need to be on?', 'Yes — for best precision. Guidance and reports rely on visual presence and engagement stability.'],
                        ['How and when do I receive Forge reports?', 'Weekly by email, with cumulative progress reflected over time. Clear, plain language. No setup required.'],
                        ['Can I try it without committing?', 'Yes. Spark includes one free call. Upgrade, pause, or cancel anytime.'],
                    ] as [$question, $answer])
                        <div>
                            <h3 class="is-faq-question">{{ $question }}</h3>
                            <p class="is-faq-answer">{{ $answer }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
</x-layouts.app>
