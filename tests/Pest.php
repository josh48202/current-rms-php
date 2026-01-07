<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
*/

/**
 * Create a mock Guzzle client for testing.
 *
 * @param  array  $responses  Array of Response objects to queue
 * @param  array  $container  Reference to array that will store request history
 * @return Client
 */
function mockGuzzleClient(array $responses, array &$container = []): Client
{
    $mock = new MockHandler($responses);
    $handlerStack = HandlerStack::create($mock);

    // Add middleware to capture requests
    $history = Middleware::history($container);
    $handlerStack->push($history);

    return new Client(['handler' => $handlerStack]);
}
