<?php

namespace Wjbecker\CurrentRms\Client\Exceptions;

class ApiException extends CurrentRmsException
{
    /**
     * Create a new API exception.
     */
    public function __construct(
        string $message,
        int $statusCode,
        ?string $responseBody = null,
        ?int $code = 0,
        ?\Exception $previous = null
    ) {
        parent::__construct($message, $statusCode, $responseBody, $code, $previous);
    }
}
