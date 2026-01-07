<?php

use Wjbecker\CurrentRms\Support\Collection;
use Wjbecker\CurrentRms\Support\Paginator;

it('can be created with items', function () {
    $paginator = new Paginator(
        items: [1, 2, 3],
        currentPage: 1,
        perPage: 25,
        total: 100
    );

    expect($paginator->items())->toBeInstanceOf(Collection::class);
    expect($paginator->items()->count())->toBe(3);
});

it('provides current page number', function () {
    $paginator = new Paginator([1], 3, 25, 100);

    expect($paginator->currentPage())->toBe(3);
});

it('provides per page count', function () {
    $paginator = new Paginator([1], 1, 50, 100);

    expect($paginator->perPage())->toBe(50);
});

it('provides total count', function () {
    $paginator = new Paginator([1], 1, 25, 100);

    expect($paginator->total())->toBe(100);
});

it('calculates last page', function () {
    $paginator = new Paginator([1], 1, 25, 100);

    expect($paginator->lastPage())->toBe(4);
});

it('handles odd total for last page', function () {
    $paginator = new Paginator([1], 1, 25, 101);

    expect($paginator->lastPage())->toBe(5);
});

it('returns null for last page when total is unknown', function () {
    $paginator = new Paginator([1], 1, 25, null);

    expect($paginator->lastPage())->toBeNull();
});

it('determines if has more pages', function () {
    $paginator = new Paginator([1, 2, 3], 1, 25, 100);

    expect($paginator->hasMorePages())->toBeTrue();
});

it('determines no more pages when on last page', function () {
    $paginator = new Paginator([1], 4, 25, 100);

    expect($paginator->hasMorePages())->toBeFalse();
});

it('determines if on first page', function () {
    $page1 = new Paginator([1], 1, 25, 100);
    $page2 = new Paginator([1], 2, 25, 100);

    expect($page1->onFirstPage())->toBeTrue();
    expect($page2->onFirstPage())->toBeFalse();
});

it('determines if on last page', function () {
    $page1 = new Paginator([1], 1, 25, 100);
    $page4 = new Paginator([1], 4, 25, 100);

    expect($page1->onLastPage())->toBeFalse();
    expect($page4->onLastPage())->toBeTrue();
});

it('counts items on current page', function () {
    $paginator = new Paginator([1, 2, 3], 1, 25, 100);

    expect($paginator->count())->toBe(3);
});

it('checks if empty', function () {
    $empty = new Paginator([], 1, 25, 0);
    $notEmpty = new Paginator([1], 1, 25, 1);

    expect($empty->isEmpty())->toBeTrue();
    expect($empty->isNotEmpty())->toBeFalse();
    expect($notEmpty->isEmpty())->toBeFalse();
    expect($notEmpty->isNotEmpty())->toBeTrue();
});

it('provides metadata array', function () {
    $paginator = new Paginator([1, 2, 3], 2, 25, 100);

    $meta = $paginator->meta();

    expect($meta)->toBe([
        'current_page' => 2,
        'per_page' => 25,
        'total' => 100,
        'last_page' => 4,
        'has_more_pages' => true,
        'count' => 3,
    ]);
});

it('can navigate to next page', function () {
    $pageLoader = fn (int $page) => new Paginator(
        items: [($page - 1) * 25 + 1],
        currentPage: $page,
        perPage: 25,
        total: 100,
        pageLoader: fn () => null
    );

    $page1 = new Paginator([1], 1, 25, 100, $pageLoader);

    $page2 = $page1->nextPage();

    expect($page2)->toBeInstanceOf(Paginator::class);
    expect($page2->currentPage())->toBe(2);
    expect($page2->items()->first())->toBe(26);
});

it('returns null for next page when on last page', function () {
    $paginator = new Paginator([1], 4, 25, 100);

    expect($paginator->nextPage())->toBeNull();
});

it('can navigate to previous page', function () {
    $pageLoader = fn (int $page) => new Paginator(
        items: [$page * 10],
        currentPage: $page,
        perPage: 25,
        total: 100,
        pageLoader: fn () => null
    );

    $page2 = new Paginator([20], 2, 25, 100, $pageLoader);

    $page1 = $page2->previousPage();

    expect($page1)->toBeInstanceOf(Paginator::class);
    expect($page1->currentPage())->toBe(1);
});

it('returns null for previous page when on first page', function () {
    $paginator = new Paginator([1], 1, 25, 100);

    expect($paginator->previousPage())->toBeNull();
});

it('can go to specific page', function () {
    $pageLoader = fn (int $page) => new Paginator(
        items: [$page],
        currentPage: $page,
        perPage: 25,
        total: 100,
        pageLoader: fn () => null
    );

    $page1 = new Paginator([1], 1, 25, 100, $pageLoader);

    $page3 = $page1->goToPage(3);

    expect($page3->currentPage())->toBe(3);
    expect($page3->items()->first())->toBe(3);
});

it('returns null for invalid page number', function () {
    $pageLoader = fn () => null;
    $paginator = new Paginator([1], 1, 25, 100, $pageLoader);

    expect($paginator->goToPage(0))->toBeNull();
    expect($paginator->goToPage(10))->toBeNull();
});

it('can iterate with cursor', function () {
    $pages = [
        [1, 2, 3],
        [4, 5, 6],
        [],
    ];

    $pageIndex = 0;
    $pageLoader = function (int $page) use (&$pages, &$pageIndex, &$pageLoader) {
        $items = $pages[$page - 1] ?? [];

        return new Paginator(
            items: $items,
            currentPage: $page,
            perPage: 3,
            total: 6,
            pageLoader: $pageLoader
        );
    };

    $paginator = new Paginator($pages[0], 1, 3, 6, $pageLoader);

    $allItems = [];
    foreach ($paginator->cursor() as $item) {
        $allItems[] = $item;
    }

    expect($allItems)->toBe([1, 2, 3, 4, 5, 6]);
});

it('can collect all items', function () {
    $pages = [
        [1, 2],
        [3, 4],
        [],
    ];

    $pageLoader = function (int $page) use (&$pages, &$pageLoader) {
        $items = $pages[$page - 1] ?? [];

        return new Paginator(
            items: $items,
            currentPage: $page,
            perPage: 2,
            total: 4,
            pageLoader: $pageLoader
        );
    };

    $paginator = new Paginator($pages[0], 1, 2, 4, $pageLoader);

    $collection = $paginator->collect();

    expect($collection)->toBeInstanceOf(Collection::class);
    expect($collection->all())->toBe([1, 2, 3, 4]);
});

it('is iterable for current page items', function () {
    $paginator = new Paginator([1, 2, 3], 1, 25, 100);

    $items = [];
    foreach ($paginator as $item) {
        $items[] = $item;
    }

    expect($items)->toBe([1, 2, 3]);
});
