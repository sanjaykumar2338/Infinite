<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\FirebaseAuthService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class UserAuthController extends Controller
{
    public function create(Request $request): View
    {
        return view('pages.login', [
            'mode' => $request->routeIs('signup') ? 'signup' : 'login',
            'firebaseConfig' => [
                'apiKey' => config('services.firebase.api_key'),
                'authDomain' => config('services.firebase.auth_domain'),
                'projectId' => config('services.firebase.project_id'),
                'appId' => config('services.firebase.app_id'),
            ],
        ]);
    }

    public function firebaseSession(Request $request, FirebaseAuthService $firebase): JsonResponse
    {
        $data = $request->validate([
            'id_token' => ['required', 'string'],
        ]);

        try {
            $firebaseUser = $firebase->verifyIdToken($data['id_token']);
        } catch (Throwable) {
            return response()->json(['message' => 'Invalid Firebase token.'], 401);
        }

        if (empty($firebaseUser['email'])) {
            return response()->json(['message' => 'Firebase email is required.'], 422);
        }

        $user = User::query()->where('firebase_uid', $firebaseUser['uid'])->first()
            ?: User::query()
                ->where('email', $firebaseUser['email'])
                ->where('role', 'user')
                ->whereNull('firebase_uid')
                ->first();

        if (! $user && User::query()->where('email', $firebaseUser['email'])->exists()) {
            return response()->json(['message' => 'This email is already connected to another account.'], 409);
        }

        $user ??= User::query()->make([
            'firebase_uid' => $firebaseUser['uid'],
            'email' => $firebaseUser['email'],
            'name' => $firebaseUser['name'] ?: $firebaseUser['email'],
            'plan' => 'free',
            'status' => 'free',
            'subscription_status' => 'free',
            'role' => 'user',
        ]);

        $user->fill([
            'firebase_uid' => $firebaseUser['uid'],
            'email' => $firebaseUser['email'],
            'name' => $firebaseUser['name'] ?: $user->name ?: $firebaseUser['email'],
        ])->save();

        Auth::login($user, true);
        $request->session()->regenerate();

        $checkoutPlan = $request->session()->pull('checkout_plan');
        $redirect = in_array($checkoutPlan, ['spark', 'forge'], true)
            ? route('billing.checkout', $checkoutPlan)
            : route('dashboard');

        return response()->json([
            'redirect' => $redirect,
        ]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'You have been logged out.');
    }
}
