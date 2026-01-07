<?php

namespace Wjbecker\CurrentRms\Query;

use Generator;
use Wjbecker\CurrentRms\Endpoints\BaseEndpoint;
use Wjbecker\CurrentRms\Support\Collection;
use Wjbecker\CurrentRms\Support\Paginator;

/**
 * Fluent query builder for API requests.
 *
 * Supports Current RMS's Ransack query syntax.
 *
 * @template TData
 */
class QueryBuilder
{
    /**
     * The query filters.
     *
     * @var array<string, mixed>
     */
    protected array $filters = [];

    /**
     * Associations to include.
     *
     * @var array<string>
     */
    protected array $includes = [];

    /**
     * Items per page.
     */
    protected ?int $perPage = null;

    /**
     * Create a new query builder.
     */
    public function __construct(
        protected BaseEndpoint $endpoint
    ) {}

    /**
     * Add a where clause using Ransack predicate.
     *
     * Common predicates:
     * - eq: equals
     * - not_eq: not equals
     * - lt: less than
     * - lteq: less than or equal
     * - gt: greater than
     * - gteq: greater than or equal
     * - cont: contains
     * - cont_all: contains all values (AND)
     * - cont_any: contains any value (OR)
     * - not_cont: does not contain
     * - start: starts with
     * - not_start: does not start with
     * - end: ends with
     * - not_end: does not end with
     * - matches: SQL LIKE pattern match
     * - does_not_match: does not match LIKE pattern
     * - true: is true
     * - false: is false
     * - null: is null
     * - not_null: is not null
     * - present: is present (not null and not blank)
     * - blank: is blank (null or empty string)
     * - in: in array
     * - not_in: not in array
     *
     * @param  string  $field  The field name
     * @param  mixed  $predicateOrValue  The Ransack predicate (eq, cont, gteq, etc.) or value for shorthand
     * @param  mixed  $value  The value to compare (when using predicate form)
     * @return $this
     */
    public function where(string $field, mixed $predicateOrValue, mixed $value = null): static
    {
        // Detect if this is shorthand form: where('field', $value)
        // versus full form: where('field', 'predicate', $value)
        $validPredicates = [
            'eq', 'not_eq', 'lt', 'lteq', 'gt', 'gteq',
            'cont', 'cont_all', 'cont_any', 'not_cont',
            'start', 'not_start', 'end', 'not_end',
            'matches', 'does_not_match',
            'in', 'not_in',
            'true', 'false', 'null', 'not_null',
            'present', 'blank',
        ];

        if ($value === null && ! in_array($predicateOrValue, $validPredicates, true)) {
            // Shorthand form: where('field', $value) -> defaults to 'eq'
            $value = $predicateOrValue;
            $predicate = 'eq';
        } else {
            $predicate = $predicateOrValue;
        }

        $key = "q[{$field}_{$predicate}]";
        $this->filters[$key] = $value;

        return $this;
    }

    /**
     * Add an equals condition.
     *
     * @param  string  $field
     * @param  mixed  $value
     * @return $this
     */
    public function whereEquals(string $field, mixed $value): static
    {
        return $this->where($field, 'eq', $value);
    }

    /**
     * Add a not equals condition.
     *
     * @param  string  $field
     * @param  mixed  $value
     * @return $this
     */
    public function whereNotEquals(string $field, mixed $value): static
    {
        return $this->where($field, 'not_eq', $value);
    }

    /**
     * Add a greater than condition.
     *
     * @param  string  $field
     * @param  mixed  $value
     * @return $this
     */
    public function whereGreaterThan(string $field, mixed $value): static
    {
        return $this->where($field, 'gt', $value);
    }

    /**
     * Add a greater than or equal condition.
     *
     * @param  string  $field
     * @param  mixed  $value
     * @return $this
     */
    public function whereGreaterThanOrEqual(string $field, mixed $value): static
    {
        return $this->where($field, 'gteq', $value);
    }

    /**
     * Add a less than condition.
     *
     * @param  string  $field
     * @param  mixed  $value
     * @return $this
     */
    public function whereLessThan(string $field, mixed $value): static
    {
        return $this->where($field, 'lt', $value);
    }

    /**
     * Add a less than or equal condition.
     *
     * @param  string  $field
     * @param  mixed  $value
     * @return $this
     */
    public function whereLessThanOrEqual(string $field, mixed $value): static
    {
        return $this->where($field, 'lteq', $value);
    }

    /**
     * Add a contains condition (LIKE %value%).
     *
     * @param  string  $field
     * @param  string  $value
     * @return $this
     */
    public function whereContains(string $field, string $value): static
    {
        return $this->where($field, 'cont', $value);
    }

    /**
     * Add a "contains all" condition (field must contain ALL values).
     *
     * @param  string  $field
     * @param  array<string>  $values
     * @return $this
     */
    public function whereContainsAll(string $field, array $values): static
    {
        return $this->where($field, 'cont_all', $values);
    }

    /**
     * Add a "contains any" condition (field must contain at least one value).
     *
     * @param  string  $field
     * @param  array<string>  $values
     * @return $this
     */
    public function whereContainsAny(string $field, array $values): static
    {
        return $this->where($field, 'cont_any', $values);
    }

    /**
     * Add a "does not contain" condition.
     *
     * @param  string  $field
     * @param  string  $value
     * @return $this
     */
    public function whereNotContains(string $field, string $value): static
    {
        return $this->where($field, 'not_cont', $value);
    }

    /**
     * Add a starts with condition.
     *
     * @param  string  $field
     * @param  string  $value
     * @return $this
     */
    public function whereStartsWith(string $field, string $value): static
    {
        return $this->where($field, 'start', $value);
    }

    /**
     * Add a "does not start with" condition.
     *
     * @param  string  $field
     * @param  string  $value
     * @return $this
     */
    public function whereNotStartsWith(string $field, string $value): static
    {
        return $this->where($field, 'not_start', $value);
    }

    /**
     * Add a ends with condition.
     *
     * @param  string  $field
     * @param  string  $value
     * @return $this
     */
    public function whereEndsWith(string $field, string $value): static
    {
        return $this->where($field, 'end', $value);
    }

    /**
     * Add a "does not end with" condition.
     *
     * @param  string  $field
     * @param  string  $value
     * @return $this
     */
    public function whereNotEndsWith(string $field, string $value): static
    {
        return $this->where($field, 'not_end', $value);
    }

    /**
     * Add a LIKE pattern match condition.
     *
     * @param  string  $field
     * @param  string  $pattern
     * @return $this
     */
    public function whereMatches(string $field, string $pattern): static
    {
        return $this->where($field, 'matches', $pattern);
    }

    /**
     * Add a "does not match" LIKE pattern condition.
     *
     * @param  string  $field
     * @param  string  $pattern
     * @return $this
     */
    public function whereNotMatches(string $field, string $pattern): static
    {
        return $this->where($field, 'does_not_match', $pattern);
    }

    /**
     * Add an "in" condition.
     *
     * @param  string  $field
     * @param  array  $values
     * @return $this
     */
    public function whereIn(string $field, array $values): static
    {
        return $this->where($field, 'in', $values);
    }

    /**
     * Add a "not in" condition.
     *
     * @param  string  $field
     * @param  array  $values
     * @return $this
     */
    public function whereNotIn(string $field, array $values): static
    {
        return $this->where($field, 'not_in', $values);
    }

    /**
     * Add a "is null" condition.
     *
     * @param  string  $field
     * @return $this
     */
    public function whereNull(string $field): static
    {
        return $this->where($field, 'null', true);
    }

    /**
     * Add a "is not null" condition.
     *
     * @param  string  $field
     * @return $this
     */
    public function whereNotNull(string $field): static
    {
        return $this->where($field, 'not_null', true);
    }

    /**
     * Add a "is true" condition.
     *
     * @param  string  $field
     * @return $this
     */
    public function whereTrue(string $field): static
    {
        return $this->where($field, 'true', true);
    }

    /**
     * Add a "is false" condition.
     *
     * @param  string  $field
     * @return $this
     */
    public function whereFalse(string $field): static
    {
        return $this->where($field, 'false', true);
    }

    /**
     * Add a "is present" condition (not null and not blank).
     *
     * @param  string  $field
     * @return $this
     */
    public function wherePresent(string $field): static
    {
        return $this->where($field, 'present', true);
    }

    /**
     * Add a "is blank" condition (null or empty string).
     *
     * @param  string  $field
     * @return $this
     */
    public function whereBlank(string $field): static
    {
        return $this->where($field, 'blank', true);
    }

    /**
     * Add a date range condition (between two dates).
     *
     * @param  string  $field
     * @param  string  $start
     * @param  string  $end
     * @return $this
     */
    public function whereBetween(string $field, string $start, string $end): static
    {
        return $this->whereGreaterThanOrEqual($field, $start)
            ->whereLessThanOrEqual($field, $end);
    }

    /**
     * Filter by state (convenience for opportunities).
     *
     * @param  int  $state  1=Draft, 2=Provisional, 3=Confirmed, etc.
     * @return $this
     */
    public function whereState(int $state): static
    {
        return $this->whereEquals('state', $state);
    }

    /**
     * Filter by member ID.
     *
     * @param  int  $memberId
     * @return $this
     */
    public function forMember(int $memberId): static
    {
        return $this->whereEquals('member_id', $memberId);
    }

    /**
     * Filter by opportunity ID.
     *
     * @param  int  $opportunityId
     * @return $this
     */
    public function forOpportunity(int $opportunityId): static
    {
        return $this->whereEquals('opportunity_id', $opportunityId);
    }

    /**
     * Filter by item ID.
     *
     * @param  int  $itemId
     * @return $this
     */
    public function forItem(int $itemId): static
    {
        return $this->whereEquals('item_id', $itemId);
    }

    /**
     * Filter by created date range.
     *
     * @param  string  $start
     * @param  string|null  $end
     * @return $this
     */
    public function createdBetween(string $start, ?string $end = null): static
    {
        $this->whereGreaterThanOrEqual('created_at', $start);

        if ($end !== null) {
            $this->whereLessThanOrEqual('created_at', $end);
        }

        return $this;
    }

    /**
     * Filter items created after a date.
     *
     * @param  string  $date
     * @return $this
     */
    public function createdAfter(string $date): static
    {
        return $this->whereGreaterThan('created_at', $date);
    }

    /**
     * Filter items created before a date.
     *
     * @param  string  $date
     * @return $this
     */
    public function createdBefore(string $date): static
    {
        return $this->whereLessThan('created_at', $date);
    }

    /**
     * Filter items updated after a date.
     *
     * @param  string  $date
     * @return $this
     */
    public function updatedAfter(string $date): static
    {
        return $this->whereGreaterThan('updated_at', $date);
    }

    /**
     * Include related associations.
     *
     * @param  string|array  $associations
     * @return $this
     */
    public function include(string|array $associations): static
    {
        $associations = is_array($associations) ? $associations : func_get_args();
        $this->includes = array_merge($this->includes, $associations);

        return $this;
    }

    /**
     * Alias for include().
     *
     * @param  string|array  $associations
     * @return $this
     */
    public function with(string|array $associations): static
    {
        $associations = is_array($associations) ? $associations : func_get_args();

        return $this->include($associations);
    }

    /**
     * Set the number of items per page.
     *
     * @param  int  $perPage
     * @return $this
     */
    public function perPage(int $perPage): static
    {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * Alias for perPage().
     *
     * @param  int  $limit
     * @return $this
     */
    public function limit(int $limit): static
    {
        return $this->perPage($limit);
    }

    /**
     * Add a raw filter.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function filter(string $key, mixed $value): static
    {
        $this->filters[$key] = $value;

        return $this;
    }

    /**
     * Add multiple raw filters.
     *
     * @param  array<string, mixed>  $filters
     * @return $this
     */
    public function filters(array $filters): static
    {
        $this->filters = array_merge($this->filters, $filters);

        return $this;
    }

    /**
     * Build the query parameters array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $params = $this->filters;

        if (! empty($this->includes)) {
            $params['include'] = $this->includes;
        }

        if ($this->perPage !== null) {
            $params['per_page'] = $this->perPage;
        }

        return $params;
    }

    /**
     * Execute the query and get results.
     *
     * @return Collection<int, TData>
     */
    public function get(): Collection
    {
        return $this->endpoint->list($this->toArray());
    }

    /**
     * Execute the query and get the first result.
     *
     * @return TData|null
     */
    public function first(): mixed
    {
        return $this->perPage(1)->get()->first();
    }

    /**
     * Execute the query and get a paginated result.
     *
     * @param  int  $page
     * @return Paginator<TData>
     */
    public function paginate(int $page = 1): Paginator
    {
        return $this->endpoint->paginate($page, $this->perPage ?? 25, $this->filters, $this->includes);
    }

    /**
     * Execute the query and iterate through all results.
     *
     * @return Generator<int, TData>
     */
    public function cursor(): Generator
    {
        return $this->endpoint->cursor($this->toArray());
    }

    /**
     * Count the number of matching results.
     *
     * Note: This makes an API request to get the first page and reads the total from meta.
     *
     * @return int|null
     */
    public function count(): ?int
    {
        return $this->paginate(1)->total();
    }

    /**
     * Check if any matching results exist.
     *
     * @return bool
     */
    public function exists(): bool
    {
        return $this->first() !== null;
    }
}
