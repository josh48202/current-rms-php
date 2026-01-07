<?php

use Wjbecker\CurrentRms\Client\Auth\ApiKeyAuth;
use Wjbecker\CurrentRms\Client\CurrentRmsClient;
use Wjbecker\CurrentRms\Data\OpportunityItemData;
use Wjbecker\CurrentRms\Endpoints\ScopedOpportunityItemsEndpoint;
use GuzzleHttp\Psr7\Response;

it('returns scoped opportunity items endpoint', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $scopedEndpoint = $client->opportunities()->items(123);

    expect($scopedEndpoint)->toBeInstanceOf(ScopedOpportunityItemsEndpoint::class);
});

it('can list items for a specific opportunity using scoped endpoint', function () {
    $mockClient = mockGuzzleClient([
        new Response(200, [], json_encode([
            'opportunity_items' => [
                ['id' => 1, 'name' => 'Product 1', 'quantity' => 2, 'item_type' => 'rental'],
                ['id' => 2, 'name' => 'Product 2', 'quantity' => 1, 'item_type' => 'sale'],
            ],
        ])),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $items = $client->opportunities()->items(123)->list();

    expect($items)->toHaveCount(2);
    expect($items->first())->toBeInstanceOf(OpportunityItemData::class);
    expect($items->first()->name)->toBe('Product 1');
});

it('can list items with filters using scoped endpoint', function () {
    $mockClient = mockGuzzleClient([
        new Response(200, [], json_encode([
            'opportunity_items' => [
                ['id' => 1, 'name' => 'Rental Item', 'item_type' => 'rental'],
            ],
        ])),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $client->opportunities()->items(123)->list([
        'q' => ['item_type_eq' => 'rental'],
    ]);
});

it('can find a specific item within an opportunity using scoped endpoint', function () {
    $mockClient = mockGuzzleClient([
        new Response(200, [], json_encode([
            'opportunity_item' => [
                'id' => 456,
                'name' => 'Test Product',
                'quantity' => 5,
            ],
        ])),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $item = $client->opportunities()->items(123)->find(456);

    expect($item)->toBeInstanceOf(OpportunityItemData::class);
    expect($item->id)->toBe(456);
    expect($item->name)->toBe('Test Product');
});

it('can find item with includes using scoped endpoint', function () {
    $mockClient = mockGuzzleClient([
        new Response(200, [], json_encode([
            'opportunity_item' => ['id' => 456, 'name' => 'Test'],
        ])),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $client->opportunities()->items(123)->find(456, ['product']);
});

it('can create item within an opportunity using scoped endpoint', function () {
    $mockClient = mockGuzzleClient([
        new Response(201, [], json_encode([
            'opportunity_item' => [
                'id' => 789,
                'name' => 'New Product',
                'quantity' => 3,
            ],
        ])),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $item = $client->opportunities()->items(123)->create([
        'product_id' => 50,
        'quantity' => 3,
    ]);

    expect($item)->toBeInstanceOf(OpportunityItemData::class);
    expect($item->id)->toBe(789);
    expect($item->name)->toBe('New Product');
});

it('can update item within an opportunity using scoped endpoint', function () {
    $mockClient = mockGuzzleClient([
        new Response(200, [], json_encode([
            'opportunity_item' => [
                'id' => 456,
                'quantity' => 10,
            ],
        ])),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $item = $client->opportunities()->items(123)->update(456, [
        'quantity' => 10,
    ]);

    expect($item)->toBeInstanceOf(OpportunityItemData::class);
    expect($item->quantity)->toBe(10);
});

it('can delete item within an opportunity using scoped endpoint', function () {
    $mockClient = mockGuzzleClient([
        new Response(204),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $result = $client->opportunities()->items(123)->destroy(456);

    expect($result)->toBeTrue();
});

it('has access to raw client from scoped endpoint', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $rawClient = $client->opportunities()->items(123)->raw();

    expect($rawClient)->toBeInstanceOf(CurrentRmsClient::class);
});
