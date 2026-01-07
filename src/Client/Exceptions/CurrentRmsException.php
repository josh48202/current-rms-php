<?php

namespace Wjbecker\CurrentRms\Client\Exceptions;

use Exception;

class CurrentRmsException extends Exception
{
    /**
     * Create a new Current RMS exception.
     */
    public function __construct(
        string $message,
        protected ?int $statusCode = null,
        protected ?string $responseBody = null,
        ?int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code ?? 0, $previous);
    }

    /**
     * Get the HTTP status code.
     */
    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    /**
     * Get the response body.
     */
    public function getResponseBody(): ?string
    {
        return $this->responseBody;
    }
}
