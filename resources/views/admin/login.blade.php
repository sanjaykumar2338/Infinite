<x-layouts.admin title="Admin Login">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5 col-xl-4">
            <div class="admin-card overflow-hidden">
                <div class="p-4 p-lg-5">
                    <span class="admin-pill pill-spark mb-3">Secure admin</span>
                    <h1 class="h3 fw-bold mb-2">Welcome back</h1>
                    <p class="text-muted mb-4">Manage Infinite Sugar users, plans, reports, and Stripe webhook history.</p>
                    <form method="post" action="{{ route('admin.login.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input class="form-control @error('email') is-invalid @enderror" name="email" type="email" value="{{ old('email') }}" required autofocus>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Password</label>
                            <input class="form-control" name="password" type="password" required>
                        </div>
                        <button class="btn btn-admin w-100 py-2">Sign in</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
