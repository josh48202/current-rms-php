<?php

use Wjbecker\CurrentRms\Client\Exceptions\ApiException;
use Wjbecker\CurrentRms\Client\Exceptions\AuthenticationException;
use Wjbecker\CurrentRms\Client\Exceptions\CurrentRmsException;
use Wjbecker\CurrentRms\Client\Exceptions\RateLimitException;
use Wjbecker\CurrentRms\Client\Exceptions\ValidationException;

it('creates base CurrentRmsException with status code and body', function () {
    $exception = new CurrentRmsException(
        'Test error',
        500,
        '{"error": "Server error"}'
    );

    expect($exception->getMessage())->toBe('Test error');
    expect($exception->getStatusCode())->toBe(500);
    expect($exception->getResponseBody())->toBe('{"error": "Server error"}');
});

it('creates CurrentRmsException without status code', function () {
    $exception = new CurrentRmsException('Test error');

    expect($exception->getMessage())->toBe('Test error');
    expect($exception->getStatusCode())->toBeNull();
    expect($exception->getResponseBody())->toBeNull();
});

it('creates AuthenticationException with 401 status', function () {
    $exception = new AuthenticationException('Auth failed', '{"error": "Unauthorized"}');

    expect($exception->getMessage())->toBe('Auth failed');
    expect($exception->getStatusCode())->toBe(401);
    expect($exception->getResponseBody())->toBe('{"error": "Unauthorized"}');
});

it('creates AuthenticationException with default message', function () {
    $exception = new AuthenticationException();

    expect($exception->getMessage())->toBe('Authentication failed');
    expect($exception->getStatusCode())->toBe(401);
});

it('creates ApiException with custom status code', function () {
    $exception = new ApiException('Server error', 503, '{"error": "Service unavailable"}');

    expect($exception->getMessage())->toBe('Server error');
    expect($exception->getStatusCode())->toBe(503);
    expect($exception->getResponseBody())->toBe('{"error": "Service unavailable"}');
});

it('creates RateLimitException with 429 status', function () {
    $exception = new RateLimitException('Too many requests', '{"error": "Rate limit"}');

    expect($exception->getMessage())->toBe('Too many requests');
    expect($exception->getStatusCode())->toBe(429);
    expect($exception->getResponseBody())->toBe('{"error": "Rate limit"}');
});

it('creates RateLimitException with default message', function () {
    $exception = new RateLimitException();

    expect($exception->getMessage())->toBe('Rate limit exceeded');
    expect($exception->getStatusCode())->toBe(429);
});

it('creates ValidationException with errors', function () {
    $errors = ['name' => ['The name field is required']];
    $exception = new ValidationException(
        'Validation failed',
        $errors,
        '{"errors": {...}}'
    );

    expect($exception->getMessage())->toBe('Validation failed');
    expect($exception->getStatusCode())->toBe(422);
    expect($exception->getErrors())->toBe($errors);
    expect($exception->getResponseBody())->toBe('{"errors": {...}}');
});

it('creates ValidationException with default message', function () {
    $exception = new ValidationException();

    expect($exception->getMessage())->toBe('Validation failed');
    expect($exception->getStatusCode())->toBe(422);
    expect($exception->getErrors())->toBeArray()->toBeEmpty();
});

it('exceptions can be caught as Exception', function () {
    try {
        throw new AuthenticationException('Auth failed');
    } catch (Exception $e) {
        expect($e)->toBeInstanceOf(Exception::class);
        expect($e->getMessage())->toBe('Auth failed');
    }
});
