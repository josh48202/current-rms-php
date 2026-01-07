<?php

namespace Wjbecker\CurrentRms\Support;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * A simple, framework-agnostic collection class.
 *
 * @template TKey of array-key
 * @template TValue
 *
 * @implements ArrayAccess<TKey, TValue>
 * @implements IteratorAggregate<TKey, TValue>
 */
class Collection implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * The items in the collection.
     *
     * @var array<TKey, TValue>
     */
    protected array $items = [];

    /**
     * Create a new collection.
     *
     * @param  array<TKey, TValue>  $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * Create a new collection instance.
     *
     * @param  array<TKey, TValue>  $items
     * @return static
     */
    public static function make(array $items = []): static
    {
        return new static($items);
    }

    /**
     * Get all items in the collection.
     *
     * @return array<TKey, TValue>
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Get all items as an array (alias for all()).
     *
     * @return array<TKey, TValue>
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * Get the first item in the collection.
     *
     * @param  callable|null  $callback
     * @param  mixed  $default
     * @return TValue|mixed
     */
    public function first(callable $callback = null, mixed $default = null): mixed
    {
        if ($callback === null) {
            if (empty($this->items)) {
                return $default;
            }

            return reset($this->items);
        }

        foreach ($this->items as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return $default;
    }

    /**
     * Get the last item in the collection.
     *
     * @param  callable|null  $callback
     * @param  mixed  $default
     * @return TValue|mixed
     */
    public function last(callable $callback = null, mixed $default = null): mixed
    {
        if ($callback === null) {
            if (empty($this->items)) {
                return $default;
            }

            return end($this->items);
        }

        return $this->reverse()->first($callback, $default);
    }

    /**
     * Reverse items order.
     *
     * @return static
     */
    public function reverse(): static
    {
        return new static(array_reverse($this->items, true));
    }

    /**
     * Run a map over each of the items.
     *
     * @template TMapValue
     *
     * @param  callable(TValue, TKey): TMapValue  $callback
     * @return static<TKey, TMapValue>
     */
    public function map(callable $callback): static
    {
        $keys = array_keys($this->items);
        $values = array_map($callback, $this->items, $keys);

        return new static(array_combine($keys, $values));
    }

    /**
     * Run a filter over each of the items.
     *
     * @param  callable|null  $callback
     * @return static
     */
    public function filter(callable $callback = null): static
    {
        if ($callback) {
            return new static(array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH));
        }

        return new static(array_filter($this->items));
    }

    /**
     * Filter items by the given key value pair.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return static
     */
    public function where(string $key, mixed $value): static
    {
        return $this->filter(function ($item) use ($key, $value) {
            return $this->dataGet($item, $key) === $value;
        });
    }

    /**
     * Filter items where the key is not null.
     *
     * @param  string  $key
     * @return static
     */
    public function whereNotNull(string $key): static
    {
        return $this->filter(function ($item) use ($key) {
            return $this->dataGet($item, $key) !== null;
        });
    }

    /**
     * Execute a callback over each item.
     *
     * @param  callable(TValue, TKey): mixed  $callback
     * @return $this
     */
    public function each(callable $callback): static
    {
        foreach ($this->items as $key => $value) {
            if ($callback($value, $key) === false) {
                break;
            }
        }

        return $this;
    }

    /**
     * Reduce the collection to a single value.
     *
     * @template TReduceValue
     *
     * @param  callable(TReduceValue, TValue, TKey): TReduceValue  $callback
     * @param  TReduceValue  $initial
     * @return TReduceValue
     */
    public function reduce(callable $callback, mixed $initial = null): mixed
    {
        $result = $initial;

        foreach ($this->items as $key => $value) {
            $result = $callback($result, $value, $key);
        }

        return $result;
    }

    /**
     * Get the values of a given key.
     *
     * @param  string  $key
     * @return static
     */
    public function pluck(string $key): static
    {
        return $this->map(fn ($item) => $this->dataGet($item, $key));
    }

    /**
     * Get the sum of the given values.
     *
     * @param  callable|string|null  $callback
     * @return int|float
     */
    public function sum(callable|string $callback = null): int|float
    {
        if ($callback === null) {
            return array_sum($this->items);
        }

        if (is_string($callback)) {
            return $this->pluck($callback)->sum();
        }

        return $this->reduce(fn ($result, $item) => $result + $callback($item), 0);
    }

    /**
     * Get the average of the given values.
     *
     * @param  callable|string|null  $callback
     * @return int|float|null
     */
    public function avg(callable|string $callback = null): int|float|null
    {
        $count = $this->count();

        if ($count === 0) {
            return null;
        }

        return $this->sum($callback) / $count;
    }

    /**
     * Get the min value of a given key.
     *
     * @param  callable|string|null  $callback
     * @return mixed
     */
    public function min(callable|string $callback = null): mixed
    {
        $values = $callback !== null ? $this->pluck($callback)->all() : $this->items;

        return min($values);
    }

    /**
     * Get the max value of a given key.
     *
     * @param  callable|string|null  $callback
     * @return mixed
     */
    public function max(callable|string $callback = null): mixed
    {
        $values = $callback !== null ? $this->pluck($callback)->all() : $this->items;

        return max($values);
    }

    /**
     * Determine if an item exists in the collection.
     *
     * @param  mixed  $key
     * @return bool
     */
    public function contains(mixed $key): bool
    {
        if (is_callable($key)) {
            return $this->first($key) !== null;
        }

        return in_array($key, $this->items, true);
    }

    /**
     * Get and remove the first item from the collection.
     *
     * @return TValue|null
     */
    public function shift(): mixed
    {
        return array_shift($this->items);
    }

    /**
     * Get and remove the last item from the collection.
     *
     * @return TValue|null
     */
    public function pop(): mixed
    {
        return array_pop($this->items);
    }

    /**
     * Push an item onto the end of the collection.
     *
     * @param  TValue  $value
     * @return $this
     */
    public function push(mixed $value): static
    {
        $this->items[] = $value;

        return $this;
    }

    /**
     * Merge the collection with the given items.
     *
     * @param  array|Collection  $items
     * @return static
     */
    public function merge(array|Collection $items): static
    {
        if ($items instanceof Collection) {
            $items = $items->all();
        }

        return new static(array_merge($this->items, $items));
    }

    /**
     * Sort through each item with a callback.
     *
     * @param  callable|null  $callback
     * @return static
     */
    public function sort(callable $callback = null): static
    {
        $items = $this->items;

        $callback ? uasort($items, $callback) : asort($items);

        return new static($items);
    }

    /**
     * Sort items by a given key.
     *
     * @param  string  $key
     * @param  bool  $descending
     * @return static
     */
    public function sortBy(string $key, bool $descending = false): static
    {
        return $this->sort(function ($a, $b) use ($key, $descending) {
            $aValue = $this->dataGet($a, $key);
            $bValue = $this->dataGet($b, $key);

            $result = $aValue <=> $bValue;

            return $descending ? -$result : $result;
        });
    }

    /**
     * Reset the keys on the underlying array.
     *
     * @return static
     */
    public function values(): static
    {
        return new static(array_values($this->items));
    }

    /**
     * Get the keys of the collection items.
     *
     * @return static
     */
    public function keys(): static
    {
        return new static(array_keys($this->items));
    }

    /**
     * Chunk the collection into chunks of the given size.
     *
     * @param  int  $size
     * @return static
     */
    public function chunk(int $size): static
    {
        $chunks = [];

        foreach (array_chunk($this->items, $size, true) as $chunk) {
            $chunks[] = new static($chunk);
        }

        return new static($chunks);
    }

    /**
     * Take the first or last {$limit} items.
     *
     * @param  int  $limit
     * @return static
     */
    public function take(int $limit): static
    {
        if ($limit < 0) {
            return new static(array_slice($this->items, $limit, abs($limit)));
        }

        return new static(array_slice($this->items, 0, $limit));
    }

    /**
     * Skip the first {$count} items.
     *
     * @param  int  $count
     * @return static
     */
    public function skip(int $count): static
    {
        return new static(array_slice($this->items, $count));
    }

    /**
     * Determine if the collection is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    /**
     * Determine if the collection is not empty.
     *
     * @return bool
     */
    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }

    /**
     * Count the number of items in the collection.
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Get an iterator for the items.
     *
     * @return Traversable<TKey, TValue>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Determine if an item exists at an offset.
     *
     * @param  TKey  $offset
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->items[$offset]);
    }

    /**
     * Get an item at a given offset.
     *
     * @param  TKey  $offset
     * @return TValue
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->items[$offset];
    }

    /**
     * Set the item at a given offset.
     *
     * @param  TKey|null  $offset
     * @param  TValue  $value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset === null) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    /**
     * Unset the item at a given offset.
     *
     * @param  TKey  $offset
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->items[$offset]);
    }

    /**
     * Get a value from an item using "dot" notation.
     */
    protected function dataGet(mixed $target, string $key): mixed
    {
        if (is_array($target)) {
            return $target[$key] ?? null;
        }

        if (is_object($target)) {
            return $target->$key ?? null;
        }

        return null;
    }
}
