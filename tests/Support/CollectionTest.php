<?php

use Wjbecker\CurrentRms\Support\Collection;

it('can be created with an array', function () {
    $collection = new Collection([1, 2, 3]);

    expect($collection->count())->toBe(3);
    expect($collection->all())->toBe([1, 2, 3]);
});

it('can use make factory method', function () {
    $collection = Collection::make([1, 2, 3]);

    expect($collection)->toBeInstanceOf(Collection::class);
    expect($collection->count())->toBe(3);
});

it('can get first item', function () {
    $collection = new Collection([1, 2, 3]);

    expect($collection->first())->toBe(1);
});

it('returns default when first on empty collection', function () {
    $collection = new Collection([]);

    expect($collection->first())->toBeNull();
    expect($collection->first(default: 'default'))->toBe('default');
});

it('can get first item with callback', function () {
    $collection = new Collection([1, 2, 3, 4, 5]);

    $result = $collection->first(fn ($item) => $item > 3);

    expect($result)->toBe(4);
});

it('can get last item', function () {
    $collection = new Collection([1, 2, 3]);

    expect($collection->last())->toBe(3);
});

it('can map items', function () {
    $collection = new Collection([1, 2, 3]);

    $mapped = $collection->map(fn ($item) => $item * 2);

    expect($mapped->all())->toBe([2, 4, 6]);
});

it('can filter items', function () {
    $collection = new Collection([1, 2, 3, 4, 5]);

    $filtered = $collection->filter(fn ($item) => $item > 2);

    expect($filtered->values()->all())->toBe([3, 4, 5]);
});

it('can filter by key value', function () {
    $collection = new Collection([
        (object) ['name' => 'John', 'age' => 30],
        (object) ['name' => 'Jane', 'age' => 25],
    ]);

    $filtered = $collection->where('name', 'Jane');

    expect($filtered->count())->toBe(1);
    expect($filtered->first()->name)->toBe('Jane');
});

it('can pluck values', function () {
    $collection = new Collection([
        (object) ['name' => 'John'],
        (object) ['name' => 'Jane'],
    ]);

    $names = $collection->pluck('name');

    expect($names->all())->toBe(['John', 'Jane']);
});

it('can sum values', function () {
    $collection = new Collection([1, 2, 3, 4, 5]);

    expect($collection->sum())->toBe(15);
});

it('can sum by key', function () {
    $collection = new Collection([
        (object) ['amount' => 10],
        (object) ['amount' => 20],
    ]);

    expect($collection->sum('amount'))->toBe(30);
});

it('can get average', function () {
    $collection = new Collection([1, 2, 3, 4, 5]);

    expect($collection->avg())->toEqual(3);
});

it('can check if contains value', function () {
    $collection = new Collection([1, 2, 3]);

    expect($collection->contains(2))->toBeTrue();
    expect($collection->contains(5))->toBeFalse();
});

it('can iterate with each', function () {
    $collection = new Collection([1, 2, 3]);
    $sum = 0;

    $collection->each(function ($item) use (&$sum) {
        $sum += $item;
    });

    expect($sum)->toBe(6);
});

it('can reduce to single value', function () {
    $collection = new Collection([1, 2, 3, 4, 5]);

    $sum = $collection->reduce(fn ($carry, $item) => $carry + $item, 0);

    expect($sum)->toBe(15);
});

it('can merge collections', function () {
    $collection1 = new Collection([1, 2]);
    $collection2 = new Collection([3, 4]);

    $merged = $collection1->merge($collection2);

    expect($merged->all())->toBe([1, 2, 3, 4]);
});

it('can sort items', function () {
    $collection = new Collection([3, 1, 2]);

    $sorted = $collection->sort();

    expect($sorted->values()->all())->toBe([1, 2, 3]);
});

it('can sort by key', function () {
    $collection = new Collection([
        (object) ['name' => 'Zoe'],
        (object) ['name' => 'Alice'],
    ]);

    $sorted = $collection->sortBy('name');

    expect($sorted->first()->name)->toBe('Alice');
});

it('can take items', function () {
    $collection = new Collection([1, 2, 3, 4, 5]);

    expect($collection->take(2)->all())->toBe([1, 2]);
    expect($collection->take(-2)->all())->toBe([4, 5]);
});

it('can skip items', function () {
    $collection = new Collection([1, 2, 3, 4, 5]);

    expect($collection->skip(2)->values()->all())->toBe([3, 4, 5]);
});

it('can chunk items', function () {
    $collection = new Collection([1, 2, 3, 4, 5]);

    $chunks = $collection->chunk(2);

    expect($chunks->count())->toBe(3);
    expect($chunks->first()->all())->toBe([1, 2]);
});

it('supports array access', function () {
    $collection = new Collection(['a' => 1, 'b' => 2]);

    expect($collection['a'])->toBe(1);
    expect(isset($collection['a']))->toBeTrue();
    expect(isset($collection['c']))->toBeFalse();

    $collection['c'] = 3;
    expect($collection['c'])->toBe(3);

    unset($collection['c']);
    expect(isset($collection['c']))->toBeFalse();
});

it('is iterable', function () {
    $collection = new Collection([1, 2, 3]);
    $items = [];

    foreach ($collection as $item) {
        $items[] = $item;
    }

    expect($items)->toBe([1, 2, 3]);
});

it('can check if empty', function () {
    $empty = new Collection([]);
    $notEmpty = new Collection([1]);

    expect($empty->isEmpty())->toBeTrue();
    expect($empty->isNotEmpty())->toBeFalse();
    expect($notEmpty->isEmpty())->toBeFalse();
    expect($notEmpty->isNotEmpty())->toBeTrue();
});

it('can convert to array', function () {
    $collection = new Collection([1, 2, 3]);

    expect($collection->toArray())->toBe([1, 2, 3]);
});
