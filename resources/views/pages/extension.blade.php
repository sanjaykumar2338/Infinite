<x-layouts.app title="Install Chrome Extension | infinitesugar">
    @php
        $extensionSizeLabel = $extensionSize ? number_format($extensionSize / 1024 / 1024, 1).' MB' : null;
    @endphp

    <section class="row align-items-start g-4 my-5">
        <div class="col-lg-5">
            <div class="eyebrow mb-3">Chrome desktop extension</div>
            <h1 class="section-title fw-bold mb-3">Install InfiniteSugar for Zoom web meetings.</h1>
            <p class="lead-copy mb-4">Download the production extension ZIP, extract it once, and load the extracted folder in Chrome.</p>

            <div class="surface-card p-4 mb-4">
                <div class="text-muted small fw-bold text-uppercase mb-2">Current download</div>
                <div class="h5 fw-bold mb-1">{{ $extensionFilename }}</div>
                <div class="text-muted mb-3">Version {{ $extensionVersion }}@if ($extensionSizeLabel) · {{ $extensionSizeLabel }}@endif</div>

                @if ($extensionAvailable)
                    <a
                        id="extension-download-button"
                        class="btn btn-sugar"
                        href="{{ route('extension.download') }}"
                        data-filename="{{ $extensionFilename }}"
                    >Download Extension</a>
                @else
                    <button class="btn btn-sugar" type="button" disabled>Download unavailable</button>
                @endif
                <a class="btn btn-soft ms-sm-2 mt-2 mt-sm-0" href="#install-instructions">View Installation Instructions</a>

                <div id="extension-download-confirmation" class="alert alert-success mt-4 mb-0 d-none" role="status">
                    <div class="fw-bold">Your InfiniteSugar extension download has started.</div>
                    <div>After the ZIP finishes downloading, extract it and follow the installation instructions below.</div>
                    <div class="mt-2">Downloaded filename: <strong id="extension-downloaded-filename">{{ $extensionFilename }}</strong></div>
                    <a class="btn btn-soft mt-3" href="{{ route('extension.download') }}">Download Again</a>
                </div>
            </div>

            <div class="feature-card">
                <div class="text-muted small fw-bold text-uppercase mb-2">Compatibility</div>
                <p class="mb-0">InfiniteSugar is currently designed for Google Chrome on desktop or laptop computers and supported Zoom web meeting pages. It is not designed for mobile Chrome.</p>
            </div>
        </div>

        <div class="col-lg-7">
            <section id="install-instructions" class="surface-card p-4 p-lg-5">
                <h2 class="h3 fw-bold mb-3">How to Install and Use</h2>
                <ol class="lead-copy mb-0">
                    <li>Download the InfiniteSugar extension ZIP.</li>
                    <li>Wait for the download to finish.</li>
                    <li>Extract/unzip the downloaded file once.</li>
                    <li>Open Google Chrome on a desktop or laptop.</li>
                    <li>Go to: <code>chrome://extensions</code></li>
                    <li>Turn on Developer Mode.</li>
                    <li>Click <strong>Load unpacked</strong>.</li>
                    <li>Select the extracted folder containing <code>manifest.json</code>.</li>
                    <li>Confirm InfiniteSugar appears in the Chrome extensions list.</li>
                    <li>Pin InfiniteSugar from the Chrome Extensions menu.</li>
                    <li>Open the extension.</li>
                    <li>Log in using the same account used on InfiniteSugar.com.</li>
                    <li>Open a supported Zoom meeting page in Chrome.</li>
                    <li>Open the extension and click <strong>Start Guidance</strong>.</li>
                </ol>
            </section>
        </div>
    </section>

    <script>
        (() => {
            const button = document.getElementById('extension-download-button');
            const confirmation = document.getElementById('extension-download-confirmation');
            const filename = document.getElementById('extension-downloaded-filename');

            if (!button || !confirmation || !filename) {
                return;
            }

            button.addEventListener('click', () => {
                filename.textContent = button.dataset.filename || '{{ $extensionFilename }}';
                confirmation.classList.remove('d-none');
            });
        })();
    </script>
</x-layouts.app>
