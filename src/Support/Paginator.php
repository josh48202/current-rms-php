<?php

namespace Wjbecker\CurrentRms\Support;

use Closure;
use Generator;
use IteratorAggregate;
use Traversable;

/**
 * A paginator that wraps API responses with pagination metadata.
 *
 * Provides lazy iteration through pages and access to pagination info.
 *
 * @template TValue
 *
 * @implements IteratorAggregate<int, TValue>
 */
class Paginator implements IteratorAggregate
{
    /**
     * Items on the current page.
     *
     * @var Collection<int, TValue>
     */
    protected Collection $items;

    /**
     * Create a new paginator instance.
     *
     * @param  array<int, TValue>  $items
     * @param  int  $currentPage
     * @param  int  $perPage
     * @param  int|null  $total
     * @param  Closure|null  $pageLoader  Function to load additional pages
     */
    public function __construct(
        array $items,
        protected int $currentPage,
        protected int $perPage,
        protected ?int $total = null,
        protected ?Closure $pageLoader = null
    ) {
        $this->items = new Collection($items);
    }

    /**
     * Get the items for the current page.
     *
     * @return Collection<int, TValue>
     */
    public function items(): Collection
    {
        return $this->items;
    }

    /**
     * Get the current page number.
     */
    public function currentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * Get the number of items per page.
     */
    public function perPage(): int
    {
        return $this->perPage;
    }

    /**
     * Get the total number of items (if known).
     */
    public function total(): ?int
    {
        return $this->total;
    }

    /**
     * Get the total number of pages (if total is known).
     */
    public function lastPage(): ?int
    {
        if ($this->total === null) {
            return null;
        }

        return (int) ceil($this->total / $this->perPage);
    }

    /**
     * Determine if there are more items after this page.
     */
    public function hasMorePages(): bool
    {
        if ($this->total !== null) {
            return $this->currentPage < $this->lastPage();
        }

        // If no total, assume more pages exist if current page is full
        return $this->items->count() >= $this->perPage;
    }

    /**
     * Determine if this is the first page.
     */
    public function onFirstPage(): bool
    {
        return $this->currentPage <= 1;
    }

    /**
     * Determine if this is the last page.
     */
    public function onLastPage(): bool
    {
        return ! $this->hasMorePages();
    }

    /**
     * Get the number of items on the current page.
     */
    public function count(): int
    {
        return $this->items->count();
    }

    /**
     * Determine if the paginator is empty.
     */
    public function isEmpty(): bool
    {
        return $this->items->isEmpty();
    }

    /**
     * Determine if the paginator is not empty.
     */
    public function isNotEmpty(): bool
    {
        return $this->items->isNotEmpty();
    }

    /**
     * Get the next page of results.
     *
     * @return static|null
     */
    public function nextPage(): ?static
    {
        if (! $this->hasMorePages() || $this->pageLoader === null) {
            return null;
        }

        return ($this->pageLoader)($this->currentPage + 1);
    }

    /**
     * Get the previous page of results.
     *
     * @return static|null
     */
    public function previousPage(): ?static
    {
        if ($this->onFirstPage() || $this->pageLoader === null) {
            return null;
        }

        return ($this->pageLoader)($this->currentPage - 1);
    }

    /**
     * Go to a specific page.
     *
     * @param  int  $page
     * @return static|null
     */
    public function goToPage(int $page): ?static
    {
        if ($this->pageLoader === null) {
            return null;
        }

        if ($page < 1) {
            return null;
        }

        if ($this->total !== null && $page > $this->lastPage()) {
            return null;
        }

        return ($this->pageLoader)($page);
    }

    /**
     * Iterate through all items across all pages.
     *
     * This is a generator that lazily loads pages as needed.
     *
     * @return Generator<int, TValue>
     */
    public function cursor(): Generator
    {
        $page = $this;

        while ($page !== null) {
            foreach ($page->items() as $item) {
                yield $item;
            }

            $page = $page->nextPage();
        }
    }

    /**
     * Collect all items from all pages into a single Collection.
     *
     * WARNING: This loads all pages into memory. Use cursor() for large datasets.
     *
     * @return Collection<int, TValue>
     */
    public function collect(): Collection
    {
        $items = [];

        foreach ($this->cursor() as $item) {
            $items[] = $item;
        }

        return new Collection($items);
    }

    /**
     * Get pagination metadata as an array.
     *
     * @return array{
     *     current_page: int,
     *     per_page: int,
     *     total: int|null,
     *     last_page: int|null,
     *     has_more_pages: bool,
     *     count: int
     * }
     */
    public function meta(): array
    {
        return [
            'current_page' => $this->currentPage,
            'per_page' => $this->perPage,
            'total' => $this->total,
            'last_page' => $this->lastPage(),
            'has_more_pages' => $this->hasMorePages(),
            'count' => $this->count(),
        ];
    }

    /**
     * Get an iterator for the items on the current page.
     *
     * @return Traversable<int, TValue>
     */
    public function getIterator(): Traversable
    {
        return $this->items->getIterator();
    }
}
