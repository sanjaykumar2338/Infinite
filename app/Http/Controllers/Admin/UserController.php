<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->when($request->filled('search'), fn ($query) => $query->where('email', 'like', '%'.$request->search.'%'))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'plan' => ['required', 'in:free,spark,forge'],
            'status' => ['required', 'in:free,active,past_due,cancelled'],
            'role' => ['required', 'in:user,tester,admin'],
            'free_call_used' => ['nullable', 'boolean'],
            'call_minutes_used' => ['required', 'integer', 'min:0'],
        ]);

        $data['free_call_used'] = $request->boolean('free_call_used');
        $data['subscription_status'] = $data['status'];
        $user->update($data);

        return back()->with('status', 'User updated.');
    }
}
