<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\FirebaseAuthService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class VerifyFirebaseToken
{
    public function __construct(private FirebaseAuthService $firebase) {}

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['message' => 'Missing bearer token.'], 401);
        }

        try {
            $firebaseUser = $this->firebase->verifyIdToken($token);
        } catch (Throwable) {
            return response()->json(['message' => 'Invalid Firebase token.'], 401);
        }

        if (empty($firebaseUser['email'])) {
            return response()->json(['message' => 'Firebase token email is required.'], 422);
        }

        $user = User::query()->firstOrCreate(
            ['firebase_uid' => $firebaseUser['uid']],
            [
                'email' => $firebaseUser['email'],
                'name' => $firebaseUser['name'] ?: $firebaseUser['email'],
                'plan' => 'free',
                'status' => 'free',
                'role' => 'user',
            ]
        );

        $user->fill([
            'email' => $firebaseUser['email'],
            'name' => $user->name ?: ($firebaseUser['name'] ?: $firebaseUser['email']),
        ])->save();

        Auth::setUser($user);
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
