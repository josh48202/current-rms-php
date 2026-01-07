<?php

use Wjbecker\CurrentRms\Client\Auth\TokenStorage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Facade;

beforeEach(function () {
    // Skip if Laravel Cache facade is not available (running outside Laravel)
    try {
        $root = Facade::getFacadeApplication();
        if (! $root || ! Cache::getFacadeRoot()) {
            $this->markTestSkipped('Laravel Cache facade not available. Run tests from Laravel app context.');
        }
    } catch (\RuntimeException $e) {
        $this->markTestSkipped('Laravel Cache facade not available. Run tests from Laravel app context.');
    }

    Cache::flush();
});

it('can store and retrieve access token', function () {
    $storage = new TokenStorage();

    $storage->store('access-token-123', 3600);

    expect($storage->getAccessToken())->toBe('access-token-123');
});

it('can store and retrieve refresh token', function () {
    $storage = new TokenStorage();

    $storage->store('access-token', 3600, 'refresh-token-456');

    expect($storage->getRefreshToken())->toBe('refresh-token-456');
});

it('returns null for missing tokens', function () {
    $storage = new TokenStorage();

    expect($storage->getAccessToken())->toBeNull();
    expect($storage->getRefreshToken())->toBeNull();
});

it('validates token is still valid', function () {
    $storage = new TokenStorage();

    $storage->store('access-token', 3600);

    expect($storage->isValid())->toBeTrue();
});

it('validates token is expired', function () {
    $storage = new TokenStorage();

    Carbon::setTestNow(now());

    $storage->store('access-token', 10); // 10 seconds

    // Move time forward past expiration (10 seconds - 60 second buffer = already expired)
    Carbon::setTestNow(now()->addSeconds(20));

    expect($storage->isValid())->toBeFalse();

    Carbon::setTestNow();
});

it('returns false when no token exists', function () {
    $storage = new TokenStorage();

    expect($storage->isValid())->toBeFalse();
});

it('can clear stored tokens', function () {
    $storage = new TokenStorage();

    $storage->store('access-token', 3600, 'refresh-token');

    $storage->clear();

    expect($storage->getAccessToken())->toBeNull();
    expect($storage->getRefreshToken())->toBeNull();
    expect($storage->isValid())->toBeFalse();
});

it('applies 60 second buffer to expiration', function () {
    $storage = new TokenStorage();

    Carbon::setTestNow(now());

    $storage->store('access-token', 120); // 120 seconds

    // Token should be valid
    expect($storage->isValid())->toBeTrue();

    // Move time forward by 50 seconds (within buffer)
    Carbon::setTestNow(now()->addSeconds(50));

    // Should still be valid (120 - 60 buffer = 60 seconds effective TTL)
    expect($storage->isValid())->toBeTrue();

    // Move time forward by another 20 seconds (total 70 seconds)
    Carbon::setTestNow(now()->addSeconds(20));

    // Should be expired now (past the 60 second effective TTL)
    expect($storage->isValid())->toBeFalse();

    Carbon::setTestNow();
});
