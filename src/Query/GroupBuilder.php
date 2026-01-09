<?php

namespace Wjbecker\CurrentRms\Query;

/**
 * Builder for grouping query conditions with AND/OR logic.
 *
 * Used internally by QueryBuilder::whereOr() and QueryBuilder::whereAnd().
 */
class GroupBuilder
{
    /**
     * Groups of conditions.
     *
     * Each group is an array of conditions that will be ANDed together.
     *
     * @var array<int, array<string, mixed>>
     */
    protected array $groups = [];

    /**
     * Whether we're inside a group() callback.
     *
     * When true, multiple where* calls add to the same group.
     * When false, each where* call creates a new group.
     */
    protected bool $inGroupCallback = false;

    /**
     * Current group index being built (only used inside group() callback).
     */
    protected int $currentGroup = 0;

    /**
     * Valid Ransack predicates.
     *
     * @var array<string>
     */
    protected array $validPredicates = [
        'eq', 'not_eq', 'lt', 'lteq', 'gt', 'gteq',
        'cont', 'cont_all', 'cont_any', 'not_cont',
        'start', 'not_start', 'end', 'not_end',
        'matches', 'does_not_match',
        'in', 'not_in',
        'true', 'false', 'null', 'not_null',
        'present', 'blank',
    ];

    /**
     * Add a where clause using Ransack predicate.
     *
     * When called directly (not inside group()), each call creates a separate group.
     * When called inside group(), multiple calls add to the same group.
     *
     * @param  string  $field  The field name
     * @param  mixed  $predicateOrValue  The Ransack predicate or value for shorthand
     * @param  mixed  $value  The value to compare (when using predicate form)
     * @return $this
     */
    public function where(string $field, mixed $predicateOrValue, mixed $value = null): static
    {
        if ($value === null && ! in_array($predicateOrValue, $this->validPredicates, true)) {
            $value = $predicateOrValue;
            $predicate = 'eq';
        } else {
            $predicate = $predicateOrValue;
        }

        $key = "{$field}_{$predicate}";

        if ($this->inGroupCallback) {
            // Inside group() callback - add to current group
            $this->groups[$this->currentGroup][$key] = $value;
        } else {
            // Direct call - each condition gets its own group
            $this->groups[] = [$key => $value];
        }

        return $this;
    }

    /**
     * Add an equals condition.
     *
     * @return $this
     */
    public function whereEquals(string $field, mixed $value): static
    {
        return $this->where($field, 'eq', $value);
    }

    /**
     * Add a not equals condition.
     *
     * @return $this
     */
    public function whereNotEquals(string $field, mixed $value): static
    {
        return $this->where($field, 'not_eq', $value);
    }

    /**
     * Add a greater than condition.
     *
     * @return $this
     */
    public function whereGreaterThan(string $field, mixed $value): static
    {
        return $this->where($field, 'gt', $value);
    }

    /**
     * Add a greater than or equal condition.
     *
     * @return $this
     */
    public function whereGreaterThanOrEqual(string $field, mixed $value): static
    {
        return $this->where($field, 'gteq', $value);
    }

    /**
     * Add a less than condition.
     *
     * @return $this
     */
    public function whereLessThan(string $field, mixed $value): static
    {
        return $this->where($field, 'lt', $value);
    }

    /**
     * Add a less than or equal condition.
     *
     * @return $this
     */
    public function whereLessThanOrEqual(string $field, mixed $value): static
    {
        return $this->where($field, 'lteq', $value);
    }

    /**
     * Add a contains condition (LIKE %value%).
     *
     * @return $this
     */
    public function whereContains(string $field, string $value): static
    {
        return $this->where($field, 'cont', $value);
    }

    /**
     * Add a "contains all" condition (field must contain ALL values).
     *
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
     * @return $this
     */
    public function whereNotContains(string $field, string $value): static
    {
        return $this->where($field, 'not_cont', $value);
    }

    /**
     * Add a starts with condition.
     *
     * @return $this
     */
    public function whereStartsWith(string $field, string $value): static
    {
        return $this->where($field, 'start', $value);
    }

    /**
     * Add a "does not start with" condition.
     *
     * @return $this
     */
    public function whereNotStartsWith(string $field, string $value): static
    {
        return $this->where($field, 'not_start', $value);
    }

    /**
     * Add a ends with condition.
     *
     * @return $this
     */
    public function whereEndsWith(string $field, string $value): static
    {
        return $this->where($field, 'end', $value);
    }

    /**
     * Add a "does not end with" condition.
     *
     * @return $this
     */
    public function whereNotEndsWith(string $field, string $value): static
    {
        return $this->where($field, 'not_end', $value);
    }

    /**
     * Add a LIKE pattern match condition.
     *
     * @return $this
     */
    public function whereMatches(string $field, string $pattern): static
    {
        return $this->where($field, 'matches', $pattern);
    }

    /**
     * Add a "does not match" LIKE pattern condition.
     *
     * @return $this
     */
    public function whereNotMatches(string $field, string $pattern): static
    {
        return $this->where($field, 'does_not_match', $pattern);
    }

    /**
     * Add an "in" condition.
     *
     * @return $this
     */
    public function whereIn(string $field, array $values): static
    {
        return $this->where($field, 'in', $values);
    }

    /**
     * Add a "not in" condition.
     *
     * @return $this
     */
    public function whereNotIn(string $field, array $values): static
    {
        return $this->where($field, 'not_in', $values);
    }

    /**
     * Add a "is null" condition.
     *
     * @return $this
     */
    public function whereNull(string $field): static
    {
        return $this->where($field, 'null', true);
    }

    /**
     * Add a "is not null" condition.
     *
     * @return $this
     */
    public function whereNotNull(string $field): static
    {
        return $this->where($field, 'not_null', true);
    }

    /**
     * Add a "is true" condition.
     *
     * @return $this
     */
    public function whereTrue(string $field): static
    {
        return $this->where($field, 'true', true);
    }

    /**
     * Add a "is false" condition.
     *
     * @return $this
     */
    public function whereFalse(string $field): static
    {
        return $this->where($field, 'false', true);
    }

    /**
     * Add a "is present" condition (not null and not blank).
     *
     * @return $this
     */
    public function wherePresent(string $field): static
    {
        return $this->where($field, 'present', true);
    }

    /**
     * Add a "is blank" condition (null or empty string).
     *
     * @return $this
     */
    public function whereBlank(string $field): static
    {
        return $this->where($field, 'blank', true);
    }

    /**
     * Add a date range condition (between two dates).
     *
     * @return $this
     */
    public function whereBetween(string $field, string $start, string $end): static
    {
        return $this->whereGreaterThanOrEqual($field, $start)
            ->whereLessThanOrEqual($field, $end);
    }

    /**
     * Create a new group with multiple conditions ANDed together.
     *
     * Use this when you need multiple conditions in a single group:
     * (name = 'Bill' AND description = 'test') OR (name = 'Fred' AND description = 'other')
     *
     * @param  callable(GroupBuilder): void  $callback
     * @return $this
     */
    public function group(callable $callback): static
    {
        // Start a new group for the callback
        $this->currentGroup = count($this->groups);
        $this->groups[$this->currentGroup] = [];
        $this->inGroupCallback = true;

        $callback($this);

        // Reset state after callback
        $this->inGroupCallback = false;

        return $this;
    }

    /**
     * Get all groups.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getGroups(): array
    {
        return array_values(array_filter($this->groups, fn($g) => ! empty($g)));
    }

    /**
     * Check if there are any groups.
     */
    public function hasGroups(): bool
    {
        return ! empty($this->getGroups());
    }
}
