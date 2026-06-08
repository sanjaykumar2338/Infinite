<x-layouts.app title="Privacy Policy | infinitesugar">
    @php
        $contentSections = $pageContent ?? collect();
        $rawSection = fn (string $key) => $contentSections->get($key);
        $section = fn (string $key) => $rawSection($key)?->is_active ? $rawSection($key) : null;
        $isHidden = fn (string $key) => (bool) ($rawSection($key) && ! $rawSection($key)->is_active);
        $contentTitle = fn (string $key, string $fallback) => $section($key)?->title ?: $fallback;
        $contentSubtitle = fn (string $key, string $fallback) => $section($key)?->subtitle ?: $fallback;
        $contentBody = fn (string $key, string $fallback) => $section($key)?->body ?: $fallback;
    @endphp

    @unless ($isHidden('hero'))
    <section class="legal-hero">
        <div class="legal-hero-grid">
            <div class="legal-hero-copy">
                <div class="eyebrow">{{ $contentSubtitle('hero', 'Privacy Policy') }}</div>
                <h1 class="legal-hero-title">{{ $contentTitle('hero', 'Privacy Policy') }}</h1>
                <p class="legal-hero-summary">{!! nl2br(e($contentBody('hero', 'Infinite Sugar is built around private, local behavioral insights during Zoom calls. This page explains what information we collect, what stays on your device, and how we protect your account data.'))) !!}</p>
            </div>
            <div class="legal-hero-meta">
                <div class="legal-meta-card">
                    <span class="legal-meta-label">Effective Date</span>
                    <span class="legal-meta-value">2025</span>
                </div>
                <div class="legal-meta-card">
                    <span class="legal-meta-label">Focus</span>
                    <span class="legal-meta-value">Private local processing</span>
                </div>
            </div>
        </div>
    </section>
    @endunless

    <section class="legal-doc py-5 mx-auto">
        @unless ($isHidden('intro'))
            <p>{!! nl2br(e($contentBody('intro', 'Infinite Sugar is a Chrome extension providing private, local behavioral insights during Zoom calls. All analysis happens locally on your device. No raw video, audio, or identifiable biometric data is ever recorded, stored, or transmitted.'))) !!}</p>
        @endunless
        <p>PeabodyandFrenchCoffee LLC ("we," "us," or "our") operates the Infinite Sugar Chrome extension and website. This Privacy Notice explains how we collect, use, and protect your information. We are committed to transparency, user trust, and compliance with applicable laws, including the California Consumer Privacy Act (CCPA), General Data Protection Regulation (GDPR), and Zoom's Terms of Service.</p>

        <h2>1. Data Controller & Contact</h2>
        <p><strong>Data Controller:</strong> PeabodyandFrenchCoffee LLC<br><strong>Contact for Privacy Requests:</strong> contact@infinitesugar.com</p>
        <p>You may contact us to exercise your privacy rights, ask questions, or report concerns.</p>

        <h2>2. Information We Collect</h2>
        <h3>Behavioral Signals, Processed Locally Only</h3>
        <p>Limited behavioral signals may be processed during Zoom calls, such as gaze direction, posture, and presence indicators. These signals are used solely to provide real-time nudges, insights, and weekly reports.</p>
        <ul>
            <li>All behavioral analysis occurs locally on your device.</li>
            <li>We do not record, store, or transmit raw video or audio.</li>
            <li>We do not transmit behavioral signals to our servers.</li>
            <li>We do not share behavioral data with Zoom, Google, advertisers, or third parties.</li>
        </ul>
        <p><strong>Biometric Disclaimer:</strong> Infinite Sugar does not perform facial recognition, identity verification, or biometric identification.</p>

        <h3>Other Information We May Collect</h3>
        <ul>
            <li>Personal identifiers, such as name and email address</li>
            <li>Account information, such as username and password</li>
            <li>Subscription and billing status</li>
            <li>Communication preferences</li>
            <li>Device and usage information, collected in aggregated and anonymized form only</li>
            <li>Payment information processed securely via Stripe. We do not store or access your full payment card details.</li>
        </ul>

        <h2>3. Legal Basis for Processing</h2>
        <p>We process personal data based on user consent and legitimate interest to provide, maintain, and improve the Infinite Sugar extension and associated services.</p>

        <h2>4. How We Use Your Information</h2>
        <ul>
            <li>Provide and improve the Infinite Sugar extension, including real-time nudges, weekly reports, and charts</li>
            <li>Process subscriptions and payments</li>
            <li>Send important account and service updates</li>
            <li>Analyze usage trends in aggregated and anonymized form only</li>
            <li>Comply with legal obligations</li>
        </ul>
        <p>No automated decision-making or profiling is performed. All insights are informational only.</p>

        <h2>5. Sharing Your Information</h2>
        <p>We do not sell your personal information. We may share information only with service providers under strict contractual protections, when required by law or valid legal process, or in connection with a merger, acquisition, or business transfer with prior notice.</p>
        <p>We never share behavioral signals or Zoom call data with any third party, including Zoom itself.</p>

        <h2>CCPA-Specific Rights</h2>
        <p>California residents have the right to know the categories of personal information collected, request deletion, request correction, opt out of the sale of personal information, and exercise these rights without discrimination. We do not sell data. To exercise rights, submit a verifiable request to contact@infinitesugar.com.</p>

        <h2>6. User Control & Choices</h2>
        <ul>
            <li>Access, correct, or delete your personal information</li>
            <li>Cancel your subscription at any time</li>
            <li>Uninstall the extension to immediately stop all processing</li>
            <li>Opt out of marketing communications</li>
        </ul>

        <h2>7. Data Security</h2>
        <p>We implement industry-standard safeguards, including encryption, access controls, and local processing. No system can guarantee absolute security. By processing behavioral signals locally, exposure risk is minimized.</p>

        <h2>8. Data Retention</h2>
        <ul>
            <li>Behavioral signals are processed in real time and are never stored.</li>
            <li>Derived insights for reports are retained only as long as necessary.</li>
            <li>Account information is retained only as required to provide services or meet legal obligations.</li>
        </ul>

        <h2>9. GDPR Compliance</h2>
        <p>For EU and UK users, all behavioral analysis occurs locally on your device. We do not store or transmit raw biometric data. Rights include access, correction, deletion, and withdrawal of consent. Requests may be submitted to contact@infinitesugar.com.</p>

        <h2>10. Children's Privacy</h2>
        <p>Infinite Sugar is not intended for individuals under 13. We do not knowingly collect personal data from children.</p>

        <h2>11. Third-Party Platforms</h2>
        <p>Infinite Sugar operates as an independent overlay alongside platforms like Zoom and Google Chrome. We are not affiliated, endorsed, or connected to Zoom or Google.</p>

        <h2>12. Changes to This Privacy Notice</h2>
        <p>We may update this Privacy Notice periodically. Updates will be posted with a new effective date. Continued use of the Services constitutes acceptance of the updated Privacy Notice.</p>

        <h2>13. Disclaimer</h2>
        <p>Infinite Sugar is designed as a private self-improvement tool. We do not monitor, track, or evaluate individuals on behalf of third parties.</p>

        @unless ($isHidden('contact'))
            <h2>{{ $contentTitle('contact', '14. Contact Us') }}</h2>
            <p>{!! nl2br(e($contentBody('contact', "PeabodyandFrenchCoffee LLC\nEmail: contact@infinitesugar.com"))) !!}</p>
        @endunless
    </section>
</x-layouts.app>
