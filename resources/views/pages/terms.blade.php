<x-layouts.app title="Terms & Conditions | infinitesugar">
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
                <div class="eyebrow">{{ $contentSubtitle('hero', 'Terms & Conditions') }}</div>
                <h1 class="legal-hero-title">{{ $contentTitle('hero', 'Terms and Conditions') }}</h1>
                <p class="legal-hero-summary">{!! nl2br(e($contentBody('hero', 'These terms cover how Infinite Sugar works, how subscriptions are handled, and the rules for using the website and Chrome extension in a clear, straightforward way.'))) !!}</p>
            </div>
            <div class="legal-hero-meta">
                <div class="legal-meta-card">
                    <span class="legal-meta-label">Last Updated</span>
                    <span class="legal-meta-value">2025</span>
                </div>
                <div class="legal-meta-card">
                    <span class="legal-meta-label">Applies To</span>
                    <span class="legal-meta-value">Website and extension</span>
                </div>
            </div>
        </div>
    </section>
    @endunless

    <section class="legal-doc py-5 mx-auto">
        @unless ($isHidden('intro'))
            <p>{!! nl2br(e($contentBody('intro', 'We believe in clarity, fairness, and transparency. These Terms explain how Infinite Sugar works and how we protect both you and our platform.'))) !!}</p>
        @endunless
        <p>These Terms and Conditions ("Terms") govern your access to and use of the website located at https://infinitesugar.com and the Infinite Sugar Chrome extension, operated by PeabodyandFrenchCoffee LLC ("Company," "we," "us," or "our").</p>
        <p>By accessing or using the Website or Extension, you agree to be legally bound by these Terms. If you do not agree, you must immediately stop using the Services.</p>

        <h2>I. Definitions</h2>
        <ul>
            <li><strong>Extension:</strong> The Infinite Sugar Chrome extension</li>
            <li><strong>Services:</strong> The Extension and all related features, insights, reports, and updates</li>
            <li><strong>Website:</strong> https://infinitesugar.com and all associated pages</li>
            <li><strong>User:</strong> Any individual who accesses or uses the Services</li>
            <li><strong>Privacy Notice:</strong> The Company's Privacy Notice available on the Website</li>
        </ul>

        <h2>II. Nature of Services</h2>
        <p>Infinite Sugar provides real-time behavioral insights and post-session analysis tools to enhance communication during virtual meetings.</p>
        <p>The Extension is an independent product and is not affiliated with, endorsed by, or officially connected to Zoom Video Communications, Inc. Infinite Sugar operates as a private overlay and does not interfere with, control, or modify your Zoom meetings or their participants.</p>

        <h2>III. Eligibility</h2>
        <p>You must be at least 18 years old, or the age of majority in your jurisdiction, and legally capable of entering into binding agreements. The Services are not intended for minors.</p>

        @unless ($isHidden('billing'))
            <h2>{{ $contentTitle('billing', 'IV. Free Trial and Subscription Billing') }}</h2>
            @if ($section('billing')?->body)
                <p>{!! nl2br(e($contentBody('billing', ''))) !!}</p>
            @endif
        @endunless
        <h3>Spark 30-Minute Free Call</h3>
        <ul>
            <li>New users are entitled to one 30-minute free trial call on the Spark tier.</li>
            <li>During this free trial, you receive full access to Spark's live behavioral guidance features.</li>
            <li>The free trial is strictly limited to 30 minutes of active call time and is a one-time offer per user.</li>
            <li>After the 30-minute free call ends, you must manually subscribe or upgrade to continue using the Extension.</li>
            <li>There is no automatic subscription after the free call.</li>
            <li>We track free trial usage to prevent abuse. Attempting to create multiple accounts may result in suspension.</li>
        </ul>

        <h3>Recurring Subscriptions</h3>
        <ul>
            <li>All paid subscriptions are billed monthly in advance and automatically renew unless canceled.</li>
            <li>You may cancel or pause your subscription at any time via your account settings.</li>
            <li>Cancellation or pause takes effect at the end of your current billing period.</li>
            <li>You will retain full access through the end of your paid period.</li>
            <li>Because access is granted immediately upon subscription, we generally do not offer refunds or prorated credits, but may review requests case by case.</li>
            <li>The Company may change pricing with at least 30 days' notice. Continued use after pricing changes constitutes acceptance.</li>
        </ul>
        <p>We are committed to transparent billing. You will never be charged unexpectedly, and you will always retain access through the end of any paid billing period.</p>

        <h2>V. User Responsibilities</h2>
        <p>You agree to maintain the confidentiality of your account and are responsible for all activity under your account. You must use the Services only for lawful purposes.</p>

        <h2>VI. Prohibited Conduct</h2>
        <p>You may not reverse engineer, copy, modify, distribute, or resell the Services; interfere with security or system performance; use bots, scrapers, or automated systems; share or transfer your account; upload malicious code; or violate applicable laws or third-party terms, including Zoom's Terms of Service.</p>
        <p>We reserve the right to suspend or terminate accounts engaging in abusive or unauthorized use.</p>

        <h2>VII. Intellectual Property</h2>
        <p>All content, software, designs, and technology are owned by PeabodyandFrenchCoffee LLC and protected by intellectual property laws. You are granted a limited, non-exclusive, non-transferable, revocable license for personal use only.</p>

        <h2>VIII. Privacy and Data</h2>
        <p>Your use of the Services is governed by our Privacy Notice. All behavioral analysis is performed locally on your device. We do not record, store, or transmit raw video or audio from your Zoom calls.</p>

        <h2>IX. Service Availability</h2>
        <p>We do not guarantee uninterrupted or error-free operation. Performance may be affected by browser updates, third-party platform changes, or network conditions.</p>

        <h2>X. Disclaimer of Warranties</h2>
        <p>The Services are provided "as is" and "as available" without warranties of any kind, express or implied.</p>

        <h2>XI. Limitation of Liability</h2>
        <p>To the fullest extent permitted by law, PeabodyandFrenchCoffee LLC shall not be liable for any indirect, incidental, or consequential damages, including loss of data, revenue, or business opportunities.</p>

        <h2>XII. No Professional Advice</h2>
        <p>All insights and feedback are for informational purposes only and do not constitute professional, psychological, medical, or legal advice.</p>

        <h2>XIII. Termination</h2>
        <p>We may suspend or terminate access at any time for violation of these Terms, abuse, or activity that may harm the platform or other users.</p>

        <h2>XIV. Amendments</h2>
        <p>We may update these Terms at any time. Continued use of the Services constitutes acceptance of the updated Terms.</p>

        <h2>XV. Governing Law</h2>
        <p>These Terms are governed by the laws of the State of Pennsylvania. Any disputes will be resolved exclusively in courts located in Pennsylvania.</p>

        <h2>XVI. Indemnification</h2>
        <p>You agree to indemnify and hold harmless PeabodyandFrenchCoffee LLC from any claims, damages, or expenses arising from your use of the Services or violation of these Terms.</p>

        <h2>XVII. Miscellaneous</h2>
        <ul>
            <li>If any provision is invalid, the remainder remains effective.</li>
            <li>Failure to enforce any provision does not waive our rights.</li>
            <li>You may not assign your rights; we may assign ours.</li>
            <li>These Terms constitute the entire agreement between you and the Company.</li>
        </ul>

        @unless ($isHidden('contact'))
            <h2>{{ $contentTitle('contact', 'Contact Us') }}</h2>
            <p>{!! nl2br(e($contentBody('contact', "PeabodyandFrenchCoffee LLC\nEmail: contact@infinitesugar.com"))) !!}</p>
        @endunless
    </section>
</x-layouts.app>
