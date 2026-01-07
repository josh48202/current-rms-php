<?php

namespace Wjbecker\CurrentRms\Endpoints;

use Generator;
use Wjbecker\CurrentRms\Client\CurrentRmsClient;
use Wjbecker\CurrentRms\Query\QueryBuilder;
use Wjbecker\CurrentRms\Support\Collection;
use Wjbecker\CurrentRms\Support\Paginator;

abstract class BaseEndpoint
{
    /**
     * The API endpoint path (e.g., '/opportunities').
     */
    protected string $endpoint;

    /**
     * The singular resource name (e.g., 'opportunity').
     * If not set, will be auto-derived from endpoint path.
     */
    protected ?string $resourceName = null;

    /**
     * The plural resource name (e.g., 'opportunities').
     * If not set, will be auto-derived from endpoint path.
     */
    protected ?string $resourceNamePlural = null;

    /**
     * The data class to use for mapping responses (e.g., OpportunityData::class).
     */
    protected string $dataClass;

    /**
     * Default page size for list queries.
     */
    protected int $defaultPageSize = 25;

    /**
     * Maximum allowed page size for this endpoint.
     */
    protected int $maxPageSize = 100;

    /**
     * Create a new endpoint instance.
     */
    public function __construct(
        protected CurrentRmsClient $client
    ) {
        // Only initialize if endpoint is already set
        // Child classes with dynamic endpoints should call initializeResourceNames() manually
        if (isset($this->endpoint)) {
            $this->initializeResourceNames();
        }
    }

    /**
     * Initialize resource names from endpoint path if not already set.
     */
    protected function initializeResourceNames(): void
    {
        if ($this->resourceNamePlural === null) {
            // Extract last segment from endpoint path
            // e.g., '/opportunities' -> 'opportunities'
            // e.g., '/opportunities/123/opportunity_items' -> 'opportunity_items'
            $segments = explode('/', trim($this->endpoint, '/'));
            $this->resourceNamePlural = end($segments);
        }

        if ($this->resourceName === null) {
            // Convert plural to singular
            $this->resourceName = $this->toSingular($this->resourceNamePlural);
        }
    }

    /**
     * Convert a plural word to singular form.
     *
     * Simple implementation for common API resource patterns.
     */
    protected function toSingular(string $word): string
    {
        // Handle common API patterns
        $patterns = [
            '/ies$/' => 'y',           // opportunities -> opportunity
            '/ves$/' => 'f',           // shelves -> shelf
            '/ses$/' => 's',           // addresses -> address
            '/([^s])s$/' => '$1',      // items -> item
        ];

        foreach ($patterns as $pattern => $replacement) {
            $singular = preg_replace($pattern, $replacement, $word);
            if ($singular !== $word) {
                return $singular;
            }
        }

        return $word;
    }

    /**
     * Make a GET request to the endpoint.
     *
     * Automatically applies pagination constraints for list queries (empty path).
     *
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    protected function get(string $path = '', array $query = []): array
    {
        // Apply pagination constraints for list queries (when path is empty)
        if ($path === '') {
            $query = $this->applyPaginationConstraints($query);
        }

        return $this->client->get($this->endpoint.$path, $query);
    }

    /**
     * Make a POST request to the endpoint.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function post(string $path = '', array $data = []): array
    {
        return $this->client->post($this->endpoint.$path, $data);
    }

    /**
     * Make a PUT request to the endpoint.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function put(string $path = '', array $data = []): array
    {
        return $this->client->put($this->endpoint.$path, $data);
    }

    /**
     * Make a PATCH request to the endpoint.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function patch(string $path = '', array $data = []): array
    {
        return $this->client->patch($this->endpoint.$path, $data);
    }

    /**
     * Make a DELETE request to the endpoint.
     */
    protected function delete(string $path = ''): bool
    {
        return $this->client->delete($this->endpoint.$path);
    }

    /**
     * Get access to the raw client for custom requests.
     */
    public function raw(): CurrentRmsClient
    {
        return $this->client;
    }

    /**
     * Start building a query with fluent interface.
     *
     * @return QueryBuilder
     */
    public function query(): QueryBuilder
    {
        return new QueryBuilder($this);
    }

    /**
     * List resources with optional filters and includes.
     *
     * @param  array<string, mixed>  $filters  Query filters
     * @param  array<string>  $include  Associated resources to include (deprecated, use query builder)
     * @return Collection
     */
    public function list(array $filters = [], array $include = []): Collection
    {
        if (! empty($include)) {
            $filters['include'] = $include;
        }

        $response = $this->get('', $filters);

        $items = array_map(
            fn ($item) => $this->dataClass::from($item, $this->client),
            $response[$this->resourceNamePlural] ?? []
        );

        return new Collection($items);
    }

    /**
     * Find a single resource by ID.
     *
     * @param  int  $id  Resource ID
     * @param  array<string>  $include  Associated resources to include
     * @return mixed Data object instance
     */
    public function find(int $id, array $include = []): mixed
    {
        $query = [];
        if (! empty($include)) {
            $query['include'] = $include;
        }

        $response = $this->get("/{$id}", $query);

        return $this->dataClass::from($response[$this->resourceName], $this->client);
    }

    /**
     * Create a new resource.
     *
     * @param  array<string, mixed>  $data  Resource data
     * @return mixed Data object instance
     */
    public function create(array $data): mixed
    {
        $response = $this->post('', [$this->resourceName => $data]);

        return $this->dataClass::from($response[$this->resourceName], $this->client);
    }

    /**
     * Update an existing resource.
     *
     * @param  int  $id  Resource ID
     * @param  array<string, mixed>  $data  Resource data
     * @return mixed Data object instance
     */
    public function update(int $id, array $data): mixed
    {
        $response = $this->put("/{$id}", [$this->resourceName => $data]);

        return $this->dataClass::from($response[$this->resourceName], $this->client);
    }

    /**
     * Delete a resource.
     *
     * @param  int  $id  Resource ID
     */
    public function destroy(int $id): bool
    {
        return $this->delete("/{$id}");
    }

    /**
     * Get paginated results with navigation.
     *
     * @param  int  $page  Page number (1-based)
     * @param  int  $perPage  Items per page
     * @param  array<string, mixed>  $filters  Additional query filters
     * @param  array<string>  $include  Associated resources to include
     * @return Paginator
     */
    public function paginate(int $page = 1, int $perPage = 25, array $filters = [], array $include = []): Paginator
    {
        if (! empty($include)) {
            $filters['include'] = $include;
        }

        $filters['page'] = $page;
        $filters['per_page'] = min($perPage, $this->maxPageSize);

        $response = $this->get('', $filters);

        $items = array_map(
            fn ($item) => $this->dataClass::from($item, $this->client),
            $response[$this->resourceNamePlural] ?? []
        );

        $total = $response['meta']['total_row_count'] ?? null;

        // Create page loader closure for navigation
        $pageLoader = fn (int $p) => $this->paginate($p, $perPage, array_diff_key($filters, ['page' => 1]), $include);

        return new Paginator(
            items: $items,
            currentPage: $page,
            perPage: $filters['per_page'],
            total: $total,
            pageLoader: $pageLoader
        );
    }

    /**
     * Iterate through all resources using a generator.
     *
     * Memory-efficient pagination that yields items one at a time.
     *
     * @param  array<string, mixed>  $filters  Query filters
     * @param  array<string>  $include  Associated resources to include
     * @return Generator
     */
    public function cursor(array $filters = [], array $include = []): Generator
    {
        if (! empty($include)) {
            $filters['include'] = $include;
        }

        $page = 1;
        $perPage = $this->maxPageSize;

        do {
            $filters['page'] = $page;
            $filters['per_page'] = $perPage;

            $response = $this->get('', $filters);

            $items = $response[$this->resourceNamePlural] ?? [];

            foreach ($items as $item) {
                yield $this->dataClass::from($item, $this->client);
            }

            $hasMorePages = count($items) >= $perPage;
            $page++;

            // Safety limit: max 100 pages to prevent infinite loops
            if ($page > 100) {
                break;
            }

        } while ($hasMorePages);
    }

    /**
     * Apply pagination constraints to query parameters.
     *
     * Sets default page size if not specified and enforces max page size.
     *
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    protected function applyPaginationConstraints(array $query): array
    {
        // Set default page size if not specified
        if (! isset($query['per_page'])) {
            $query['per_page'] = $this->defaultPageSize;
        }

        // Enforce max page size
        if (isset($query['per_page']) && $query['per_page'] > $this->maxPageSize) {
            $query['per_page'] = $this->maxPageSize;
        }

        return $query;
    }
}
