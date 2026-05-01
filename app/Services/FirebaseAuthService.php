<?php

namespace App\Services;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class FirebaseAuthService
{
    public function verifyIdToken(string $token): array
    {
        $projectId = config('services.firebase.project_id');

        if (! $projectId) {
            throw new RuntimeException('Firebase project ID is not configured.');
        }

        $keys = Cache::remember('firebase_public_keys', now()->addHour(), function () {
            $response = Http::get('https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com');

            if (! $response->ok()) {
                throw new RuntimeException('Unable to fetch Firebase public keys.');
            }

            return $response->json();
        });

        $decoded = (array) JWT::decode($token, JWK::parseKeySet($this->x509ToJwkSet($keys)));

        if (($decoded['aud'] ?? null) !== $projectId) {
            throw new RuntimeException('Firebase token audience mismatch.');
        }

        if (($decoded['iss'] ?? null) !== 'https://securetoken.google.com/'.$projectId) {
            throw new RuntimeException('Firebase token issuer mismatch.');
        }

        if (empty($decoded['sub'])) {
            throw new RuntimeException('Firebase token subject is missing.');
        }

        return [
            'uid' => $decoded['sub'],
            'email' => $decoded['email'] ?? null,
            'name' => $decoded['name'] ?? null,
            'email_verified' => (bool) ($decoded['email_verified'] ?? false),
        ];
    }

    private function x509ToJwkSet(array $certificates): array
    {
        $keys = [];

        foreach ($certificates as $kid => $certificate) {
            $details = openssl_pkey_get_details(openssl_pkey_get_public($certificate));

            $keys[] = [
                'kid' => $kid,
                'kty' => 'RSA',
                'alg' => 'RS256',
                'use' => 'sig',
                'n' => JWT::urlsafeB64Encode($details['rsa']['n']),
                'e' => JWT::urlsafeB64Encode($details['rsa']['e']),
            ];
        }

        return ['keys' => $keys];
    }
}
