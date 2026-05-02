<x-layouts.app :title="$mode === 'signup' ? 'Sign up' : 'Login'">
    <section>
        <div class="auth-shell surface-card">
            <div class="row auth-layout align-items-stretch">
                <div class="col-lg-7">
                    <div class="auth-panel">
                        <div class="eyebrow mb-3">Private access</div>
                        <h1 class="section-title mb-3">{{ $mode === 'signup' ? 'Create your infinitesugar account.' : 'Welcome back to infinitesugar.' }}</h1>
                        <p class="lead-copy mb-4">Sign in to review your plan, Spark access, Forge reports, charts, and badge summaries.</p>
                        <img class="auth-media" src="{{ asset('assets/auth-access-preview.svg') }}" alt="infinitesugar dashboard preview">
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="auth-form-panel">
                        @if (empty($firebaseConfig['apiKey']) || empty($firebaseConfig['projectId']))
                            <div class="alert alert-warning rounded-4 mb-4">
                                Firebase browser config is missing. Add `FIREBASE_API_KEY`, `FIREBASE_AUTH_DOMAIN`, `FIREBASE_PROJECT_ID`, and `FIREBASE_APP_ID` to `.env`.
                            </div>
                        @endif

                        <div class="d-flex flex-wrap auth-tabs">
                            <a class="btn {{ $mode === 'login' ? 'btn-sugar' : 'btn-soft' }}" href="{{ route('login') }}">Login</a>
                            <a class="btn {{ $mode === 'signup' ? 'btn-sugar' : 'btn-soft' }}" href="{{ route('signup') }}">Sign up</a>
                        </div>

                        <form id="firebase-auth-form" data-mode="{{ $mode }}" data-session-url="{{ route('login.firebase') }}">
                            @if($mode === 'signup')
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Name</label>
                                    <input class="form-control form-control-lg" name="name" autocomplete="name" placeholder="Your name">
                                </div>
                            @endif
                            <div class="mb-3">
                                <label class="form-label fw-bold">Email</label>
                                <input class="form-control form-control-lg" name="email" type="email" autocomplete="email" placeholder="you@example.com" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Password</label>
                                <input class="form-control form-control-lg" name="password" type="password" autocomplete="{{ $mode === 'signup' ? 'new-password' : 'current-password' }}" placeholder="Minimum 6 characters" required>
                            </div>
                            <div id="firebase-auth-message" class="alert d-none rounded-4"></div>
                            <button class="btn btn-sugar w-100" type="submit">{{ $mode === 'signup' ? 'Create account' : 'Login' }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://www.gstatic.com/firebasejs/10.12.5/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.12.5/firebase-auth-compat.js"></script>
    <script>
        const firebaseConfig = @json($firebaseConfig);
        const form = document.getElementById('firebase-auth-form');
        const message = document.getElementById('firebase-auth-message');

        function showMessage(text, type = 'danger') {
            message.className = `alert alert-${type} rounded-4`;
            message.textContent = text;
        }

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            if (!firebaseConfig.apiKey || !firebaseConfig.projectId) {
                showMessage('Firebase browser config is missing. Update .env before using user login.');
                return;
            }

            try {
                if (!firebase.apps.length) {
                    firebase.initializeApp(firebaseConfig);
                }

                const mode = form.dataset.mode;
                const email = form.elements.email.value;
                const password = form.elements.password.value;
                const name = form.elements.name ? form.elements.name.value : '';

                const credential = mode === 'signup'
                    ? await firebase.auth().createUserWithEmailAndPassword(email, password)
                    : await firebase.auth().signInWithEmailAndPassword(email, password);

                if (mode === 'signup' && name) {
                    await credential.user.updateProfile({ displayName: name });
                    await credential.user.reload();
                }

                const idToken = await firebase.auth().currentUser.getIdToken(true);
                const response = await fetch(form.dataset.sessionUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ id_token: idToken }),
                });

                const payload = await response.json();

                if (!response.ok) {
                    throw new Error(payload.message || 'Login failed.');
                }

                showMessage('Login successful. Redirecting...', 'success');
                window.location.href = payload.redirect;
            } catch (error) {
                showMessage(error.message || 'Unable to authenticate.');
            }
        });
    </script>
</x-layouts.app>
