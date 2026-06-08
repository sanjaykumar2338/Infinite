<x-layouts.admin title="Profile">
    <div class="row g-4">
        <div class="col-lg-6">
            <form class="admin-card p-4 h-100" method="post" action="{{ route('admin.profile.update') }}">
                @csrf
                @method('patch')

                <div class="mb-4">
                    <div class="small text-muted fw-bold text-uppercase">Account</div>
                    <h2 class="h4 fw-bold mb-1">Profile Details</h2>
                    <p class="text-muted mb-0">Update the admin name and login email.</p>
                </div>

                @if ($errors->profile->any())
                    <div class="alert alert-danger rounded-4">
                        <ul class="mb-0">
                            @foreach ($errors->profile->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label fw-bold">Name</label>
                    <input class="form-control form-control-lg" name="name" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Email</label>
                    <input class="form-control form-control-lg" name="email" type="email" value="{{ old('email', $user->email) }}" required>
                </div>

                <button class="btn btn-admin px-4">Save Profile</button>
            </form>
        </div>

        <div class="col-lg-6">
            <form class="admin-card p-4 h-100" method="post" action="{{ route('admin.profile.password') }}">
                @csrf
                @method('patch')

                <div class="mb-4">
                    <div class="small text-muted fw-bold text-uppercase">Security</div>
                    <h2 class="h4 fw-bold mb-1">Change Password</h2>
                    <p class="text-muted mb-0">Use a strong password and keep it somewhere private.</p>
                </div>

                @if ($errors->password->any())
                    <div class="alert alert-danger rounded-4">
                        <ul class="mb-0">
                            @foreach ($errors->password->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label fw-bold">Current Password</label>
                    <input class="form-control form-control-lg" name="current_password" type="password" autocomplete="current-password" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">New Password</label>
                    <input class="form-control form-control-lg" name="new_password" type="password" autocomplete="new-password" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Confirm New Password</label>
                    <input class="form-control form-control-lg" name="new_password_confirmation" type="password" autocomplete="new-password" required>
                </div>

                <button class="btn btn-admin px-4">Update Password</button>
            </form>
        </div>
    </div>
</x-layouts.admin>
