<?php

use GuzzleHttp\Psr7\Response;
use Wjbecker\CurrentRms\Client\Auth\ApiKeyAuth;
use Wjbecker\CurrentRms\Client\CurrentRmsClient;
use Wjbecker\CurrentRms\Data\OpportunityData;
use Wjbecker\CurrentRms\Query\QueryBuilder;
use Wjbecker\CurrentRms\Support\Collection;

it('can create query builder from endpoint', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $query = $client->opportunities()->query();

    expect($query)->toBeInstanceOf(QueryBuilder::class);
});

it('builds where equals filter', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $query = $client->opportunities()->query()->whereEquals('state', 3);

    expect($query->toArray())->toBe(['q[state_eq]' => 3]);
});

it('builds shorthand where equals', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $query = $client->opportunities()->query()->where('state', 3);

    expect($query->toArray())->toBe(['q[state_eq]' => 3]);
});

it('builds where with predicate', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $query = $client->opportunities()->query()->where('quantity', 'gteq', 5);

    expect($query->toArray())->toBe(['q[quantity_gteq]' => 5]);
});

it('builds comparison filters', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $query = $client->opportunities()->query()
        ->whereGreaterThan('amount', 100)
        ->whereGreaterThanOrEqual('quantity', 5)
        ->whereLessThan('discount', 50)
        ->whereLessThanOrEqual('price', 1000);

    expect($query->toArray())->toBe([
        'q[amount_gt]' => 100,
        'q[quantity_gteq]' => 5,
        'q[discount_lt]' => 50,
        'q[price_lteq]' => 1000,
    ]);
});

it('builds string matching filters', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $query = $client->opportunities()->query()
        ->whereContains('subject', 'Wedding')
        ->whereStartsWith('name', 'John')
        ->whereEndsWith('email', '@example.com');

    expect($query->toArray())->toBe([
        'q[subject_cont]' => 'Wedding',
        'q[name_start]' => 'John',
        'q[email_end]' => '@example.com',
    ]);
});

it('builds contains all and contains any filters', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $query = $client->opportunities()->query()
        ->whereContainsAll('tags_name', ['rental', 'priority'])
        ->whereContainsAny('subject', ['wedding', 'corporate']);

    expect($query->toArray())->toBe([
        'q[tags_name_cont_all]' => ['rental', 'priority'],
        'q[subject_cont_any]' => ['wedding', 'corporate'],
    ]);
});

it('builds not contains filter', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $query = $client->opportunities()->query()
        ->whereNotContains('subject', 'cancelled');

    expect($query->toArray())->toBe([
        'q[subject_not_cont]' => 'cancelled',
    ]);
});

it('builds negated string matching filters', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $query = $client->opportunities()->query()
        ->whereNotStartsWith('name', 'Test')
        ->whereNotEndsWith('email', '@spam.com');

    expect($query->toArray())->toBe([
        'q[name_not_start]' => 'Test',
        'q[email_not_end]' => '@spam.com',
    ]);
});

it('builds matches filters', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $query = $client->opportunities()->query()
        ->whereMatches('subject', '%Wedding%2025%')
        ->whereNotMatches('name', '%Test%');

    expect($query->toArray())->toBe([
        'q[subject_matches]' => '%Wedding%2025%',
        'q[name_does_not_match]' => '%Test%',
    ]);
});

it('builds present and blank filters', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $query = $client->opportunities()->query()
        ->wherePresent('description')
        ->whereBlank('notes');

    expect($query->toArray())->toBe([
        'q[description_present]' => true,
        'q[notes_blank]' => true,
    ]);
});

it('builds array filters', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $query = $client->opportunities()->query()
        ->whereIn('state', [1, 2, 3])
        ->whereNotIn('type', ['draft', 'cancelled']);

    expect($query->toArray())->toBe([
        'q[state_in]' => [1, 2, 3],
        'q[type_not_in]' => ['draft', 'cancelled'],
    ]);
});

it('builds null checks', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $query = $client->opportunities()->query()
        ->whereNull('deleted_at')
        ->whereNotNull('confirmed_at');

    expect($query->toArray())->toBe([
        'q[deleted_at_null]' => true,
        'q[confirmed_at_not_null]' => true,
    ]);
});

it('builds boolean checks', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $query = $client->opportunities()->query()
        ->whereTrue('active')
        ->whereFalse('archived');

    expect($query->toArray())->toBe([
        'q[active_true]' => true,
        'q[archived_false]' => true,
    ]);
});

it('builds between filter', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $query = $client->opportunities()->query()
        ->whereBetween('starts_at', '2025-01-01', '2025-12-31');

    expect($query->toArray())->toBe([
        'q[starts_at_gteq]' => '2025-01-01',
        'q[starts_at_lteq]' => '2025-12-31',
    ]);
});

it('builds convenience filters', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $query = $client->opportunities()->query()
        ->whereState(3)
        ->forMember(123)
        ->forOpportunity(456)
        ->forItem(789);

    expect($query->toArray())->toBe([
        'q[state_eq]' => 3,
        'q[member_id_eq]' => 123,
        'q[opportunity_id_eq]' => 456,
        'q[item_id_eq]' => 789,
    ]);
});

it('builds date filters', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $query = $client->opportunities()->query()
        ->createdAfter('2025-01-01')
        ->createdBefore('2025-12-31')
        ->updatedAfter('2025-06-01');

    expect($query->toArray())->toBe([
        'q[created_at_gt]' => '2025-01-01',
        'q[created_at_lt]' => '2025-12-31',
        'q[updated_at_gt]' => '2025-06-01',
    ]);
});

it('builds created between filter', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $query = $client->opportunities()->query()
        ->createdBetween('2025-01-01', '2025-12-31');

    expect($query->toArray())->toBe([
        'q[created_at_gteq]' => '2025-01-01',
        'q[created_at_lteq]' => '2025-12-31',
    ]);
});

it('includes associations', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $query = $client->opportunities()->query()
        ->include('member', 'venue');

    expect($query->toArray())->toBe([
        'include' => ['member', 'venue'],
    ]);
});

it('includes associations with with alias', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $query = $client->opportunities()->query()
        ->with('item', 'rate_definition');

    expect($query->toArray())->toBe([
        'include' => ['item', 'rate_definition'],
    ]);
});

it('sets per page', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $query = $client->opportunities()->query()->perPage(50);

    expect($query->toArray())->toBe(['per_page' => 50]);
});

it('sets limit as alias for perPage', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $query = $client->opportunities()->query()->limit(50);

    expect($query->toArray())->toBe(['per_page' => 50]);
});

it('adds raw filters', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $query = $client->opportunities()->query()
        ->filter('custom_key', 'value')
        ->filters(['another' => 'one']);

    expect($query->toArray())->toBe([
        'custom_key' => 'value',
        'another' => 'one',
    ]);
});

it('can chain multiple filters', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $query = $client->opportunities()->query()
        ->whereState(3)
        ->forMember(123)
        ->whereContains('subject', 'Wedding')
        ->with('member')
        ->perPage(50);

    expect($query->toArray())->toBe([
        'q[state_eq]' => 3,
        'q[member_id_eq]' => 123,
        'q[subject_cont]' => 'Wedding',
        'include' => ['member'],
        'per_page' => 50,
    ]);
});

it('executes get query', function () {
    $mockClient = mockGuzzleClient([
        new Response(200, [], json_encode([
            'opportunities' => [
                ['id' => 1, 'subject' => 'Test', 'state' => 3],
            ],
        ])),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $results = $client->opportunities()->query()
        ->whereState(3)
        ->get();

    expect($results)->toBeInstanceOf(Collection::class);
    expect($results->count())->toBe(1);
    expect($results->first())->toBeInstanceOf(OpportunityData::class);
});

it('executes first query', function () {
    $mockClient = mockGuzzleClient([
        new Response(200, [], json_encode([
            'opportunities' => [
                ['id' => 1, 'subject' => 'Test', 'state' => 3],
            ],
        ])),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $result = $client->opportunities()->query()
        ->whereState(3)
        ->first();

    expect($result)->toBeInstanceOf(OpportunityData::class);
    expect($result->subject)->toBe('Test');
});

it('returns null when first finds nothing', function () {
    $mockClient = mockGuzzleClient([
        new Response(200, [], json_encode([
            'opportunities' => [],
        ])),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $result = $client->opportunities()->query()->first();

    expect($result)->toBeNull();
});

it('checks exists', function () {
    $mockClient = mockGuzzleClient([
        new Response(200, [], json_encode([
            'opportunities' => [
                ['id' => 1, 'subject' => 'Test'],
            ],
        ])),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $exists = $client->opportunities()->query()
        ->whereState(3)
        ->exists();

    expect($exists)->toBeTrue();
});

it('checks not exists', function () {
    $mockClient = mockGuzzleClient([
        new Response(200, [], json_encode([
            'opportunities' => [],
        ])),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $exists = $client->opportunities()->query()
        ->whereState(99)
        ->exists();

    expect($exists)->toBeFalse();
});

// Grouping tests

it('builds simple whereOr with multiple conditions', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $query = $client->opportunities()->query()
        ->whereOr(function ($or) {
            $or->whereContains('name', 'Bill');
            $or->whereContains('name', 'Fred');
        });

    expect($query->toArray())->toBe([
        'q' => [
            'g' => [
                0 => ['name_cont' => 'Bill'],
                1 => ['name_cont' => 'Fred'],
            ],
            'm' => 'or',
        ],
    ]);
});

it('builds simple whereAnd with multiple conditions', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $query = $client->opportunities()->query()
        ->whereAnd(function ($and) {
            $and->whereContains('name', 'Bill');
            $and->whereContains('description', 'test');
        });

    expect($query->toArray())->toBe([
        'q' => [
            'g' => [
                0 => ['name_cont' => 'Bill'],
                1 => ['description_cont' => 'test'],
            ],
            'm' => 'and',
        ],
    ]);
});

it('builds whereOr with grouped conditions', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    // (name = Bill AND active = true) OR (name = Fred AND active = true)
    $query = $client->opportunities()->query()
        ->whereOr(function ($or) {
            $or->group(function ($g) {
                $g->whereEquals('name', 'Bill');
                $g->whereTrue('active');
            });
            $or->group(function ($g) {
                $g->whereEquals('name', 'Fred');
                $g->whereTrue('active');
            });
        });

    expect($query->toArray())->toBe([
        'q' => [
            'g' => [
                0 => ['name_eq' => 'Bill', 'active_true' => true],
                1 => ['name_eq' => 'Fred', 'active_true' => true],
            ],
            'm' => 'or',
        ],
    ]);
});

it('builds whereOr with various predicates', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $query = $client->opportunities()->query()
        ->whereOr(function ($or) {
            $or->whereEquals('state', 1);
            $or->whereGreaterThan('amount', 100);
            $or->whereIn('type', ['rental', 'sale']);
        });

    expect($query->toArray())->toBe([
        'q' => [
            'g' => [
                0 => ['state_eq' => 1],
                1 => ['amount_gt' => 100],
                2 => ['type_in' => ['rental', 'sale']],
            ],
            'm' => 'or',
        ],
    ]);
});

it('combines regular filters with grouped conditions', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $query = $client->opportunities()->query()
        ->whereState(3)  // Regular filter
        ->whereOr(function ($or) {
            $or->whereContains('name', 'Bill');
            $or->whereContains('name', 'Fred');
        })
        ->with('member')
        ->perPage(50);

    expect($query->toArray())->toBe([
        'q[state_eq]' => 3,
        'q' => [
            'g' => [
                0 => ['name_cont' => 'Bill'],
                1 => ['name_cont' => 'Fred'],
            ],
            'm' => 'or',
        ],
        'include' => ['member'],
        'per_page' => 50,
    ]);
});

it('builds grouped conditions with whereBetween', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $query = $client->opportunities()->query()
        ->whereOr(function ($or) {
            $or->group(function ($g) {
                $g->whereBetween('starts_at', '2025-01-01', '2025-06-30');
            });
            $or->group(function ($g) {
                $g->whereBetween('starts_at', '2025-07-01', '2025-12-31');
            });
        });

    expect($query->toArray())->toBe([
        'q' => [
            'g' => [
                0 => ['starts_at_gteq' => '2025-01-01', 'starts_at_lteq' => '2025-06-30'],
                1 => ['starts_at_gteq' => '2025-07-01', 'starts_at_lteq' => '2025-12-31'],
            ],
            'm' => 'or',
        ],
    ]);
});
