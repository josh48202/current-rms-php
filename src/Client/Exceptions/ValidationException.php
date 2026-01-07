<?php

namespace Wjbecker\CurrentRms\Client\Exceptions;

class ValidationException extends CurrentRmsException
{
    /**
     * Create a new validation exception.
     */
    public function __construct(
        string $message = 'Validation failed',
        protected array $errors = [],
        ?string $responseBody = null,
        ?int $code = 0,
        ?\Exception $previous = null
    ) {
        parent::__construct($message, 422, $responseBody, $code, $previous);
    }

    /**
     * Get the validation errors.
     *
     * @return array<string, mixed>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
