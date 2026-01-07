<?php

namespace Wjbecker\CurrentRms\Client\Exceptions;

class RateLimitException extends CurrentRmsException
{
    /**
     * Create a new rate limit exception.
     */
    public function __construct(
        string $message = 'Rate limit exceeded',
        ?string $responseBody = null,
        ?int $code = 0,
        ?\Exception $previous = null
    ) {
        parent::__construct($message, 429, $responseBody, $code, $previous);
    }
}
