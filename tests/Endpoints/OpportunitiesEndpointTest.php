<?php

use Wjbecker\CurrentRms\Client\Auth\ApiKeyAuth;
use Wjbecker\CurrentRms\Client\CurrentRmsClient;
use Wjbecker\CurrentRms\Data\OpportunityData;
use GuzzleHttp\Psr7\Response;

it('can list opportunities', function () {
    $mockClient = mockGuzzleClient([
        new Response(200, [], json_encode([
            'opportunities' => [
                ['id' => 1, 'subject' => 'Test Opportunity', 'state' => 1],
                ['id' => 2, 'subject' => 'Another Opportunity', 'state' => 2],
            ],
        ])),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $opportunities = $client->opportunities()->list();

    expect($opportunities)->toHaveCount(2);
    expect($opportunities->first())->toBeInstanceOf(OpportunityData::class);
    expect($opportunities->first()->subject)->toBe('Test Opportunity');
});

it('can find a single opportunity', function () {
    $mockClient = mockGuzzleClient([
        new Response(200, [], json_encode([
            'opportunity' => [
                'id' => 1,
                'subject' => 'Test Opportunity',
                'state' => 1,
                'status' => 0,
            ],
        ])),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $opportunity = $client->opportunities()->find(1);

    expect($opportunity)->toBeInstanceOf(OpportunityData::class);
    expect($opportunity->id)->toBe(1);
    expect($opportunity->subject)->toBe('Test Opportunity');
});

it('can find opportunity with includes', function () {
    $mockClient = mockGuzzleClient([
        new Response(200, [], json_encode([
            'opportunity' => ['id' => 1, 'subject' => 'Test'],
        ])),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $client->opportunities()->find(1, ['owner', 'member']);
});

it('can create an opportunity', function () {
    $mockClient = mockGuzzleClient([
        new Response(201, [], json_encode([
            'opportunity' => [
                'id' => 123,
                'subject' => 'New Opportunity',
                'state' => 1,
            ],
        ])),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $opportunity = $client->opportunities()->create([
        'subject' => 'New Opportunity',
        'member_id' => 1,
    ]);

    expect($opportunity)->toBeInstanceOf(OpportunityData::class);
    expect($opportunity->id)->toBe(123);
    expect($opportunity->subject)->toBe('New Opportunity');
});

it('can update an opportunity', function () {
    $mockClient = mockGuzzleClient([
        new Response(200, [], json_encode([
            'opportunity' => [
                'id' => 1,
                'subject' => 'Updated Opportunity',
                'state' => 2,
            ],
        ])),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $opportunity = $client->opportunities()->update(1, [
        'subject' => 'Updated Opportunity',
    ]);

    expect($opportunity)->toBeInstanceOf(OpportunityData::class);
    expect($opportunity->subject)->toBe('Updated Opportunity');
});

it('can delete an opportunity', function () {
    $mockClient = mockGuzzleClient([
        new Response(204),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $result = $client->opportunities()->destroy(1);

    expect($result)->toBeTrue();
});

it('can checkout an opportunity', function () {
    $mockClient = mockGuzzleClient([
        new Response(200, [], json_encode([
            'opportunity' => [
                'id' => 1,
                'subject' => 'Checked Out',
                'state' => 3,
            ],
        ])),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $opportunity = $client->opportunities()->checkout(['opportunity_id' => 1]);

    expect($opportunity)->toBeInstanceOf(OpportunityData::class);
});

it('can clone an opportunity', function () {
    $mockClient = mockGuzzleClient([
        new Response(200, [], json_encode([
            'opportunity' => [
                'id' => 2,
                'subject' => 'Cloned Opportunity',
                'state' => 1,
            ],
        ])),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $opportunity = $client->opportunities()->clone(1);

    expect($opportunity)->toBeInstanceOf(OpportunityData::class);
    expect($opportunity->id)->toBe(2);
});

it('can finalize check-in for an opportunity', function () {
    $mockClient = mockGuzzleClient([
        new Response(200, [], json_encode([
            'opportunity' => [
                'id' => 1,
                'subject' => 'Finalized Opportunity',
                'state' => 3,
            ],
        ])),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $opportunity = $client->opportunities()->finalizeCheckIn(1, [
        'return' => [
            'return_at' => '2025-01-15T18:00:00.000Z',
        ],
        'move_outstanding' => false,
        'complete_sales_items' => true,
    ]);

    expect($opportunity)->toBeInstanceOf(OpportunityData::class);
    expect($opportunity->id)->toBe(1);
    expect($opportunity->subject)->toBe('Finalized Opportunity');
});

it('has access to raw client', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $rawClient = $client->opportunities()->raw();

    expect($rawClient)->toBeInstanceOf(CurrentRmsClient::class);
});
