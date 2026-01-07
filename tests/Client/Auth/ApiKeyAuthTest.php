<?php

use Wjbecker\CurrentRms\Client\Auth\ApiKeyAuth;

it('returns API token when authenticating', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');

    $token = $auth->authenticate();

    expect($token)->toBe('test-token');
});

it('is always authenticated', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');

    expect($auth->isAuthenticated())->toBeTrue();
});

it('returns correct auth headers with subdomain', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');

    $headers = $auth->getAuthHeaders();

    expect($headers)->toBe([
        'X-AUTH-TOKEN' => 'test-token',
        'X-SUBDOMAIN' => 'mycompany',
    ]);
});

it('refresh does nothing for API key auth', function () {
    $auth = new ApiKeyAuth('mycompany', 'test-token');

    $auth->refresh();

    expect($auth->isAuthenticated())->toBeTrue();
});
