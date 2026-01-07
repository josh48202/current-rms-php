<?php

namespace Wjbecker\CurrentRms\Client\Auth;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class TokenStorage
{
    protected string $cacheKey = 'current_rms_oauth_token';

    /**
     * Store OAuth token with expiration.
     */
    public function store(
        string $accessToken,
        int $expiresIn,
        ?string $refreshToken = null
    ): void {
        $expiresAt = now()->addSeconds($expiresIn - 60); // 60 second buffer

        Cache::put($this->cacheKey, [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_at' => $expiresAt->toIso8601String(),
        ], $expiresIn);
    }

    /**
     * Get the access token.
     */
    public function getAccessToken(): ?string
    {
        $token = Cache::get($this->cacheKey);

        return $token['access_token'] ?? null;
    }

    /**
     * Get the refresh token.
     */
    public function getRefreshToken(): ?string
    {
        $token = Cache::get($this->cacheKey);

        return $token['refresh_token'] ?? null;
    }

    /**
     * Check if the token is valid.
     */
    public function isValid(): bool
    {
        $token = Cache::get($this->cacheKey);

        if (! $token || ! isset($token['expires_at'])) {
            return false;
        }

        $expiresAt = Carbon::parse($token['expires_at']);

        return $expiresAt->isFuture();
    }

    /**
     * Clear the stored token.
     */
    public function clear(): void
    {
        Cache::forget($this->cacheKey);
    }
}
