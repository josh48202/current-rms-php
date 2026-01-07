<?php

namespace Wjbecker\CurrentRms\Client\Exceptions;

class AuthenticationException extends CurrentRmsException
{
    /**
     * Create a new authentication exception.
     */
    public function __construct(
        string $message = 'Authentication failed',
        ?string $responseBody = null,
        ?int $code = 0,
        ?\Exception $previous = null
    ) {
        parent::__construct($message, 401, $responseBody, $code, $previous);
    }
}
