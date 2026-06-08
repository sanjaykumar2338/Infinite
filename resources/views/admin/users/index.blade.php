<x-layouts.admin title="Users">
    <div class="admin-card p-4 mb-4">
        <div class="d-flex flex-wrap gap-3 align-items-center">
            <div>
                <h2 class="h4 fw-bold mb-1">User Management</h2>
                <p class="text-muted mb-0">Search, review Stripe details, and update controlled access fields.</p>
            </div>
            <form class="ms-auto d-flex flex-wrap gap-2">
                <input class="form-control" name="search" placeholder="Search by email" value="{{ request('search') }}">
                <button class="btn btn-admin">Search</button>
            </form>
        </div>
    </div>

    <div class="admin-card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-modern align-middle mb-0">
                <thead>
                    <tr>
                        <th>User</th><th>Plan</th><th>Status</th><th>Role</th><th>Stripe</th><th>Usage</th><th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td style="min-width: 18rem;">
                                <div class="fw-bold">{{ $user->email }}</div>
                                <div class="small text-muted">{{ $user->name ?: 'Unnamed user' }}</div>
                                <div class="small text-muted text-truncate" style="max-width: 20rem;">Firebase: {{ $user->firebase_uid ?: 'n/a' }}</div>
                                <form id="user-form-{{ $user->id }}" method="post" action="{{ route('admin.users.update', $user) }}">
                                    @csrf
                                    @method('patch')
                                </form>
                            </td>
                            <td>
                                <span class="admin-pill pill-{{ $user->plan }} mb-2">{{ $user->plan }}</span>
                                <select class="form-select form-select-sm" form="user-form-{{ $user->id }}" name="plan">
                                    @foreach (['free', 'spark', 'forge'] as $plan)
                                        <option @selected($user->plan === $plan)>{{ $plan }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <span class="admin-pill pill-{{ $user->billingStatus() }} mb-2">{{ $user->billingStatus() }}</span>
                                <select class="form-select form-select-sm" form="user-form-{{ $user->id }}" name="status">
                                    @foreach (['free', 'active', 'past_due', 'cancelled'] as $status)
                                        <option @selected($user->billingStatus() === $status)>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <span class="admin-pill pill-{{ $user->role }} mb-2">{{ $user->role }}</span>
                                <select class="form-select form-select-sm" form="user-form-{{ $user->id }}" name="role">
                                    @foreach (['user', 'tester', 'admin'] as $role)
                                        <option @selected($user->role === $role)>{{ $role }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="small" style="min-width: 18rem;">
                                <div><span class="text-muted">Customer:</span> <code>{{ $user->stripe_customer_id ?: 'n/a' }}</code></div>
                                <div><span class="text-muted">Sub:</span> <code>{{ $user->stripe_subscription_id ?: 'n/a' }}</code></div>
                                <div><span class="text-muted">Period:</span> {{ optional($user->paidThrough())->toDateString() ?: 'n/a' }}</div>
                                <div><span class="text-muted">Trial:</span> {{ optional($user->trial_ends_at)->toDateString() ?: 'n/a' }}</div>
                            </td>
                            <td style="min-width: 11rem;">
                                <label class="small fw-bold text-muted">Call minutes</label>
                                <input class="form-control form-control-sm mb-2" form="user-form-{{ $user->id }}" name="call_minutes_used" type="number" min="0" value="{{ $user->call_minutes_used }}">
                                <label class="form-check small">
                                    <input class="form-check-input" form="user-form-{{ $user->id }}" name="free_call_used" type="checkbox" value="1" @checked($user->free_call_used)>
                                    Free used
                                </label>
                            </td>
                            <td><button class="btn btn-sm btn-admin" form="user-form-{{ $user->id }}">Save</button></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-4 text-muted">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $users->links() }}</div>
</x-layouts.admin>
