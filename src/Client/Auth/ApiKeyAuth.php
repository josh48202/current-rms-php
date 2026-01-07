<?php

namespace Wjbecker\CurrentRms\Client\Auth;

class ApiKeyAuth implements AuthManager
{
    /**
     * Create a new API key authenticator.
     */
    public function __construct(
        protected string $subdomain,
        protected string $apiToken
    ) {}

    /**
     * Authenticate and return the API token.
     */
    public function authenticate(): string
    {
        return $this->apiToken;
    }

    /**
     * Check if authenticated (always true for API key auth).
     */
    public function isAuthenticated(): bool
    {
        return true;
    }

    /**
     * Get authentication headers for requests.
     *
     * @return array<string, string>
     */
    public function getAuthHeaders(): array
    {
        return [
            'X-AUTH-TOKEN' => $this->apiToken,
            'X-SUBDOMAIN' => $this->subdomain,
        ];
    }

    /**
     * Refresh authentication (no-op for API key auth).
     */
    public function refresh(): void
    {
        // API key auth doesn't need refresh
    }
}
