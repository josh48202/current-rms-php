<?php

use Wjbecker\CurrentRms\Client\Auth\ApiKeyAuth;
use Wjbecker\CurrentRms\Client\CurrentRmsClient;
use Wjbecker\CurrentRms\Client\Exceptions\ApiException;
use Wjbecker\CurrentRms\Client\Exceptions\AuthenticationException;
use Wjbecker\CurrentRms\Client\Exceptions\RateLimitException;
use Wjbecker\CurrentRms\Client\Exceptions\ValidationException;
use Wjbecker\CurrentRms\Endpoints\OpportunitiesEndpoint;
use Wjbecker\CurrentRms\Endpoints\OpportunityItemsEndpoint;
use GuzzleHttp\Psr7\Response;

it('can make a GET request', function () {
    $container = [];
    $mockClient = mockGuzzleClient([
        new Response(200, [], json_encode([
            'orders' => [
                ['id' => 1, 'name' => 'Test Order'],
            ],
        ])),
    ], $container);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $result = $client->get('/orders', ['status' => 'active']);

    expect($result)
        ->toBeArray()
        ->toHaveKey('orders')
        ->and($result['orders'])->toHaveCount(1);

    expect($container)->toHaveCount(1);
    $request = $container[0]['request'];
    expect((string) $request->getUri())->toBe('https://api.current-rms.com/api/v1/orders?status=active');
    expect($request->getMethod())->toBe('GET');
    expect($request->getHeader('X-AUTH-TOKEN'))->toBe(['test-token']);
    expect($request->getHeader('X-SUBDOMAIN'))->toBe(['mycompany']);
});

it('can make a POST request', function () {
    $container = [];
    $mockClient = mockGuzzleClient([
        new Response(201, [], json_encode([
            'opportunity' => ['id' => 123, 'name' => 'New Opportunity'],
        ])),
    ], $container);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $result = $client->post('/opportunities', ['name' => 'New Opportunity']);

    expect($result)
        ->toBeArray()
        ->toHaveKey('opportunity');

    expect($container)->toHaveCount(1);
    $request = $container[0]['request'];
    expect((string) $request->getUri())->toContain('/opportunities');
    expect($request->getMethod())->toBe('POST');
});

it('can make a PUT request', function () {
    $mockClient = mockGuzzleClient([
        new Response(200, [], json_encode([
            'order' => ['id' => 1, 'name' => 'Updated Order'],
        ])),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $result = $client->put('/orders/1', ['name' => 'Updated Order']);

    expect($result)->toBeArray()->toHaveKey('order');
});

it('can make a PATCH request', function () {
    $mockClient = mockGuzzleClient([
        new Response(200, [], json_encode([
            'order' => ['id' => 1, 'status' => 'completed'],
        ])),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $result = $client->patch('/orders/1', ['status' => 'completed']);

    expect($result)->toBeArray()->toHaveKey('order');
});

it('can make a DELETE request', function () {
    $mockClient = mockGuzzleClient([
        new Response(204),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $result = $client->delete('/orders/1');

    expect($result)->toBeTrue();
});

it('throws AuthenticationException on 401', function () {
    $mockClient = mockGuzzleClient([
        new Response(401, [], json_encode(['error' => 'Unauthorized'])),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $client->get('/orders');
})->throws(AuthenticationException::class);

it('throws ValidationException on 422', function () {
    $mockClient = mockGuzzleClient([
        new Response(422, [], json_encode([
            'errors' => ['name' => ['The name field is required']],
        ])),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $client->post('/opportunities', []);
})->throws(ValidationException::class);

it('throws RateLimitException on 429', function () {
    $mockClient = mockGuzzleClient([
        new Response(429, [], json_encode(['error' => 'Rate limit exceeded'])),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $client->get('/orders');
})->throws(RateLimitException::class);

it('throws ApiException on other errors', function () {
    $mockClient = mockGuzzleClient([
        new Response(500, [], json_encode(['error' => 'Server error'])),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $client->get('/orders');
})->throws(ApiException::class);

it('handles empty response bodies', function () {
    $mockClient = mockGuzzleClient([
        new Response(204, [], ''),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $result = $client->get('/orders');

    expect($result)->toBeArray()->toBeEmpty();
});

it('throws ApiException on invalid JSON', function () {
    $mockClient = mockGuzzleClient([
        new Response(200, [], 'invalid json{'),
    ]);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $client->get('/orders');
})->throws(ApiException::class, 'Failed to parse JSON response');

it('builds correct URL from endpoint', function () {
    $container = [];
    $mockClient = mockGuzzleClient([
        new Response(200, [], json_encode([])),
    ], $container);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $client->get('/orders');

    expect($container)->toHaveCount(1);
    $request = $container[0]['request'];
    expect((string) $request->getUri())->toBe('https://api.current-rms.com/api/v1/orders');
});

it('strips leading slash from endpoint', function () {
    $container = [];
    $mockClient = mockGuzzleClient([
        new Response(200, [], json_encode([])),
    ], $container);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $client->get('orders'); // No leading slash

    expect($container)->toHaveCount(1);
    $request = $container[0]['request'];
    expect((string) $request->getUri())->toBe('https://api.current-rms.com/api/v1/orders');
});

it('can work without auth manager', function () {
    $mockClient = mockGuzzleClient([
        new Response(200, [], json_encode(['data' => 'test'])),
    ]);

    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', null);
    $client->setHttpClient($mockClient);

    $result = $client->get('/public-endpoint');

    expect($result)->toBeArray();
});

it('encodes array query parameters without numeric indexes', function () {
    $container = [];
    $mockClient = mockGuzzleClient([
        new Response(200, [], json_encode([])),
    ], $container);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $client->get('/opportunities', [
        'q' => [
            'subject_cont_all' => ['TNVA_26_568', 'Alexis McMahan'],
        ],
    ]);

    expect($container)->toHaveCount(1);
    $request = $container[0]['request'];
    $url = (string) $request->getUri();

    // URL will be encoded, so %5B = [ and %5D = ]
    // Should contain array brackets without numeric indexes
    expect($url)->toContain('TNVA_26_568')
        ->and($url)->toContain('Alexis')
        ->and($url)->toContain('McMahan')
        // Should NOT contain numeric indexes (neither encoded nor decoded)
        ->and($url)->not->toContain('%5B0%5D') // [0]
        ->and($url)->not->toContain('%5B1%5D') // [1]
        ->and($url)->not->toContain('[0]')
        ->and($url)->not->toContain('[1]');
});

it('encodes nested associative arrays in query parameters', function () {
    $container = [];
    $mockClient = mockGuzzleClient([
        new Response(200, [], json_encode([])),
    ], $container);

    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);
    $client->setHttpClient($mockClient);

    $client->get('/opportunities', [
        'q' => [
            'state_eq' => 1,
            'member_id_eq' => 123,
        ],
    ]);

    expect($container)->toHaveCount(1);
    $request = $container[0]['request'];
    $url = (string) $request->getUri();

    // URL will be encoded, so %5B = [ and %5D = ]
    expect($url)->toMatch('/(q%5Bstate_eq%5D=1|q\[state_eq\]=1)/')
        ->and($url)->toMatch('/(q%5Bmember_id_eq%5D=123|q\[member_id_eq\]=123)/');
});

it('returns opportunities endpoint', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $endpoint = $client->opportunities();

    expect($endpoint)->toBeInstanceOf(OpportunitiesEndpoint::class);
});

it('returns opportunity items endpoint', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');
    $client = new CurrentRmsClient('https://api.current-rms.com/api/v1', $auth);

    $endpoint = $client->opportunityItems();

    expect($endpoint)->toBeInstanceOf(OpportunityItemsEndpoint::class);
});
