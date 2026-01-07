<?php

namespace Wjbecker\CurrentRms\Client;

use Wjbecker\CurrentRms\Client\Auth\AuthManager;
use Wjbecker\CurrentRms\Client\Exceptions\ApiException;
use Wjbecker\CurrentRms\Client\Exceptions\AuthenticationException;
use Wjbecker\CurrentRms\Client\Exceptions\RateLimitException;
use Wjbecker\CurrentRms\Client\Exceptions\ValidationException;
use Wjbecker\CurrentRms\Endpoints\OpportunitiesEndpoint;
use Wjbecker\CurrentRms\Endpoints\OpportunityItemsEndpoint;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class CurrentRmsClient
{
    protected string $baseUrl;

    protected bool $authenticated = false;

    protected Client $httpClient;

    /**
     * Create a new Current RMS client.
     */
    public function __construct(
        string                 $baseUrl,
        protected ?AuthManager $auth = null,
        protected int          $timeout = 30,
        protected int          $connectTimeout = 10,
        protected bool         $verifySsl = true
    )
    {
        $this->baseUrl = $baseUrl;
        $this->httpClient = new Client([
            'timeout' => $this->timeout,
            'connect_timeout' => $this->connectTimeout,
            'verify' => $this->verifySsl,
        ]);
    }

    /**
     * Make a GET request.
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function get(string $endpoint, array $query = []): array
    {
        // Build query string manually to avoid numeric indexes in arrays
        // Current RMS expects: q[key][]=value1&q[key][]=value2
        // Not: q[key][0]=value1&q[key][1]=value2
        if (!empty($query)) {
            $queryString = $this->buildQueryString($query);
            $endpoint = $endpoint . '?' . $queryString;
        }

        return $this->sendRequest('GET', $endpoint, []);
    }

    /**
     * Make a POST request.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function post(string $endpoint, array $data = []): array
    {
        return $this->sendRequest('POST', $endpoint, ['json' => $data]);
    }

    /**
     * Make a PUT request.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function put(string $endpoint, array $data = []): array
    {
        return $this->sendRequest('PUT', $endpoint, ['json' => $data]);
    }

    /**
     * Make a PATCH request.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function patch(string $endpoint, array $data = []): array
    {
        return $this->sendRequest('PATCH', $endpoint, ['json' => $data]);
    }

    /**
     * Make a DELETE request.
     */
    public function delete(string $endpoint): bool
    {
        $this->sendRequest('DELETE', $endpoint);

        return true;
    }

    /**
     * Make a raw HTTP request.
     *
     * @param array<string, mixed> $options
     */
    public function request(
        string $method,
        string $endpoint,
        array  $options = []
    ): ResponseInterface
    {
        $this->ensureAuthenticated();

        $url = $this->buildUrl($endpoint);

        $headers = array_merge(
            [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            $this->auth?->getAuthHeaders() ?? []
        );

        $options['headers'] = array_merge($headers, $options['headers'] ?? []);

        try {
            return $this->httpClient->request($method, $url, $options);
        } catch (RequestException $e) {
            $this->handleRequestException($e);
        }
    }

    /**
     * Send a request and parse JSON response.
     *
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    protected function sendRequest(string $method, string $endpoint, array $options = []): array
    {
        try {
            $response = $this->request($method, $endpoint, $options);

            return $this->parseResponse($response);
        } catch (RequestException $e) {
            $this->handleRequestException($e);
        }
    }

    /**
     * Parse JSON response.
     *
     * @return array<string, mixed>
     */
    protected function parseResponse(ResponseInterface $response): array
    {
        $body = (string) $response->getBody();

        if (empty($body)) {
            return [];
        }

        $decoded = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiException(
                'Failed to parse JSON response: ' . json_last_error_msg(),
                $response->getStatusCode(),
                $body
            );
        }

        return $decoded;
    }

    /**
     * Handle request exceptions.
     *
     * @throws AuthenticationException
     * @throws ValidationException
     * @throws RateLimitException
     * @throws ApiException
     */
    protected function handleRequestException(RequestException $e): never
    {
        $response = $e->getResponse();
        $statusCode = $response?->getStatusCode() ?? 0;
        $body = $response ? (string) $response->getBody() : '';

        if (!$response) {
            throw new ApiException(
                'Request failed: ' . $e->getMessage(),
                0,
                null,
                $e->getCode(),
                $e
            );
        }

        match ($statusCode) {
            401 => throw new AuthenticationException(
                'Authentication failed',
                $body,
                $e->getCode(),
                $e
            ),
            422 => throw new ValidationException(
                'Validation failed',
                $this->parseValidationErrors($body),
                $body,
                $e->getCode(),
                $e
            ),
            429 => throw new RateLimitException(
                'Rate limit exceeded',
                $body,
                $e->getCode(),
                $e
            ),
            default => throw new ApiException(
                "API request failed with status {$statusCode}",
                $statusCode,
                $body,
                $e->getCode(),
                $e
            ),
        };
    }

    /**
     * Parse validation errors from response body.
     *
     * @return array<string, mixed>
     */
    protected function parseValidationErrors(string $body): array
    {
        $decoded = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [];
        }

        return $decoded['errors'] ?? [];
    }

    /**
     * Build full URL from endpoint.
     */
    protected function buildUrl(string $endpoint): string
    {
        return rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');
    }

    /**
     * Ensure the client is authenticated.
     */
    protected function ensureAuthenticated(): void
    {
        if ($this->authenticated || !$this->auth) {
            return;
        }

        $this->auth->authenticate();
        $this->authenticated = true;
    }

    /**
     * Build query string with proper array encoding for Current RMS.
     *
     * Current RMS expects arrays without numeric indexes:
     * Correct:   q[key][]=value1&q[key][]=value2
     * Incorrect: q[key][0]=value1&q[key][1]=value2
     *
     * @param array<string, mixed> $params
     */
    protected function buildQueryString(array $params, string $prefix = ''): string
    {
        $parts = [];

        foreach ($params as $key => $value) {
            $encodedKey = $prefix ? "{$prefix}[{$key}]" : $key;

            if (is_array($value)) {
                // Check if it's a sequential array (0, 1, 2...)
                $isSequential = array_keys($value) === range(0, count($value) - 1);

                if ($isSequential) {
                    // Sequential array - use [] notation WITHOUT numeric indexes
                    foreach ($value as $item) {
                        if (is_scalar($item) || $item === null) {
                            $parts[] = urlencode($encodedKey . '[]') . '=' . urlencode((string) $item);
                        }
                    }
                } else {
                    // Associative array - recurse with the key as prefix
                    $nested = $this->buildQueryString($value, $encodedKey);
                    if ($nested) {
                        $parts[] = $nested;
                    }
                }
            } else {
                // Scalar value
                $parts[] = urlencode($encodedKey) . '=' . urlencode((string) $value);
            }
        }

        return implode('&', $parts);
    }

    /**
     * Get the HTTP client instance.
     */
    public function getHttpClient(): Client
    {
        return $this->httpClient;
    }

    /**
     * Set a custom HTTP client (useful for testing).
     */
    public function setHttpClient(Client $client): void
    {
        $this->httpClient = $client;
    }

    /**
     * Get the opportunities endpoint.
     */
    public function opportunities(): OpportunitiesEndpoint
    {
        return new OpportunitiesEndpoint($this);
    }

    /**
     * Get the opportunity items endpoint.
     */
    public function opportunityItems(): OpportunityItemsEndpoint
    {
        return new OpportunityItemsEndpoint($this);
    }
}
