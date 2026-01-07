<?php

namespace Wjbecker\CurrentRms\Client\Auth;

interface AuthManager
{
    /**
     * Authenticate and return access token/credentials.
     */
    public function authenticate(): string;

    /**
     * Check if currently authenticated.
     */
    public function isAuthenticated(): bool;

    /**
     * Get headers to add to requests.
     *
     * @return array<string, string>
     */
    public function getAuthHeaders(): array;

    /**
     * Refresh authentication if needed.
     */
    public function refresh(): void;
}
