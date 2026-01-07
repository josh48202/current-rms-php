<?php

use Wjbecker\CurrentRms\Data\OpportunityData;

it('creates opportunity data from array', function () {
    $data = [
        'id' => 1,
        'subject' => 'Test Opportunity',
        'state' => 1,
        'status' => 0,
        'member' => ['name' => 'John Doe'],
        'owner' => ['name' => 'Jane Smith'],
    ];

    $opportunity = OpportunityData::from($data);

    expect($opportunity->id)->toBe(1);
    expect($opportunity->subject)->toBe('Test Opportunity');
    expect($opportunity->state)->toBe(1);
    expect($opportunity->status)->toBe(0);
});

it('checks if opportunity is draft', function () {
    $opportunity = OpportunityData::from(['state' => 1]);

    expect($opportunity->isDraft())->toBeTrue();
});

it('checks if opportunity is not draft', function () {
    $opportunity = OpportunityData::from(['state' => 2]);

    expect($opportunity->isDraft())->toBeFalse();
});

it('checks if opportunity is open', function () {
    $opportunity = OpportunityData::from(['status' => 0]);

    expect($opportunity->isOpen())->toBeTrue();
});

it('checks if opportunity is not open', function () {
    $opportunity = OpportunityData::from(['status' => 1]);

    expect($opportunity->isOpen())->toBeFalse();
});

it('gets title from subject', function () {
    $opportunity = OpportunityData::from(['subject' => 'Test Title']);

    expect($opportunity->getTitle())->toBe('Test Title');
});

it('gets member name from nested array', function () {
    $opportunity = OpportunityData::from([
        'member' => ['name' => 'John Doe'],
    ]);

    expect($opportunity->getMemberName())->toBe('John Doe');
});

it('returns null when member name is not set', function () {
    $opportunity = OpportunityData::from([]);

    expect($opportunity->getMemberName())->toBeNull();
});

it('gets owner name from nested array', function () {
    $opportunity = OpportunityData::from([
        'owner' => ['name' => 'Jane Smith'],
    ]);

    expect($opportunity->getOwnerName())->toBe('Jane Smith');
});

it('gets custom field value', function () {
    $opportunity = OpportunityData::from([
        'custom_fields' => [
            'field1' => 'value1',
            'field2' => 'value2',
        ],
    ]);

    expect($opportunity->getCustomField('field1'))->toBe('value1');
    expect($opportunity->getCustomField('field2'))->toBe('value2');
});

it('returns null for non-existent custom field', function () {
    $opportunity = OpportunityData::from([
        'custom_fields' => ['field1' => 'value1'],
    ]);

    expect($opportunity->getCustomField('nonexistent'))->toBeNull();
});

it('throws exception when calling items() without client', function () {
    $opportunity = OpportunityData::from(['id' => 123]);

    expect(fn () => $opportunity->items())
        ->toThrow(RuntimeException::class, 'Cannot lazy load items: no client available');
});

it('throws exception when calling items() without id', function () {
    $auth = new \Wjbecker\CurrentRms\Client\Auth\ApiKeyAuth('mycompany', 'test-token');
    $client = new \Wjbecker\CurrentRms\Client\CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $opportunity = OpportunityData::from(['subject' => 'Test'], $client);

    expect(fn () => $opportunity->items())
        ->toThrow(RuntimeException::class, 'Cannot lazy load items: opportunity has no ID');
});

it('returns scoped endpoint when calling items() with client', function () {
    $auth = new \Wjbecker\CurrentRms\Client\Auth\ApiKeyAuth('mycompany', 'test-token');
    $client = new \Wjbecker\CurrentRms\Client\CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $opportunity = OpportunityData::from(['id' => 123], $client);

    $endpoint = $opportunity->items();

    expect($endpoint)->toBeInstanceOf(\Wjbecker\CurrentRms\Endpoints\ScopedOpportunityItemsEndpoint::class);
});

it('can lazy load items from opportunity returned by endpoint', function () {
    $mockClient = mockGuzzleClient([
        new \GuzzleHttp\Psr7\Response(200, [], json_encode([
            'opportunities' => [
                ['id' => 1, 'subject' => 'Test'],
            ],
        ])),
        new \GuzzleHttp\Psr7\Response(200, [], json_encode([
            'opportunity_items' => [
                ['id' => 101, 'name' => 'Item 1'],
                ['id' => 102, 'name' => 'Item 2'],
            ],
        ])),
    ]);

    $auth = new \Wjbecker\CurrentRms\Client\Auth\ApiKeyAuth('mycompany', 'test-token');
    $client = new \Wjbecker\CurrentRms\Client\CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    // Get opportunity from endpoint (client is injected)
    $opportunity = $client->opportunities()->list()->first();

    // Lazy load items
    $items = $opportunity->items()->list();

    expect($items)->toHaveCount(2);
    expect($items->first()->name)->toBe('Item 1');
});
