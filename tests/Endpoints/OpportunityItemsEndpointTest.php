<?php

use Wjbecker\CurrentRms\Client\Auth\ApiKeyAuth;
use Wjbecker\CurrentRms\Client\CurrentRmsClient;
use Wjbecker\CurrentRms\Data\OpportunityItemData;
use GuzzleHttp\Psr7\Response;

it('can list opportunity items', function () {
    $mockClient = mockGuzzleClient([
        new Response(200, [], json_encode([
            'opportunity_items' => [
                ['id' => 1, 'name' => 'Product 1', 'item_type' => 'rental', 'quantity' => 2],
                ['id' => 2, 'name' => 'Product 2', 'item_type' => 'sale', 'quantity' => 1],
            ],
        ])),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $items = $client->opportunityItems()->list();

    expect($items)->toHaveCount(2);
    expect($items->first())->toBeInstanceOf(OpportunityItemData::class);
    expect($items->first()->name)->toBe('Product 1');
});

it('can find a single opportunity item', function () {
    $mockClient = mockGuzzleClient([
        new Response(200, [], json_encode([
            'opportunity_item' => [
                'id' => 1,
                'name' => 'Test Product',
                'item_type' => 'rental',
                'quantity' => 5,
            ],
        ])),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $item = $client->opportunityItems()->find(1);

    expect($item)->toBeInstanceOf(OpportunityItemData::class);
    expect($item->id)->toBe(1);
    expect($item->name)->toBe('Test Product');
});

it('can find opportunity item with includes', function () {
    $mockClient = mockGuzzleClient([
        new Response(200, [], json_encode([
            'opportunity_item' => ['id' => 1, 'name' => 'Test'],
        ])),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $client->opportunityItems()->find(1, ['product', 'opportunity']);
});

it('can create an opportunity item', function () {
    $mockClient = mockGuzzleClient([
        new Response(201, [], json_encode([
            'opportunity_item' => [
                'id' => 123,
                'name' => 'New Product',
                'item_type' => 'rental',
                'quantity' => 3,
            ],
        ])),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $item = $client->opportunityItems()->create([
        'opportunity_id' => 100,
        'product_id' => 50,
        'quantity' => 3,
    ]);

    expect($item)->toBeInstanceOf(OpportunityItemData::class);
    expect($item->id)->toBe(123);
    expect($item->name)->toBe('New Product');
});

it('can update an opportunity item', function () {
    $mockClient = mockGuzzleClient([
        new Response(200, [], json_encode([
            'opportunity_item' => [
                'id' => 1,
                'name' => 'Updated Product',
                'quantity' => 10,
            ],
        ])),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $item = $client->opportunityItems()->update(1, [
        'quantity' => 10,
    ]);

    expect($item)->toBeInstanceOf(OpportunityItemData::class);
    expect($item->quantity)->toBe(10);
});

it('can delete an opportunity item', function () {
    $mockClient = mockGuzzleClient([
        new Response(204),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $result = $client->opportunityItems()->destroy(1);

    expect($result)->toBeTrue();
});

it('has access to raw client', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $rawClient = $client->opportunityItems()->raw();

    expect($rawClient)->toBeInstanceOf(CurrentRmsClient::class);
});
