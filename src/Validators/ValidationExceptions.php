<?php

declare(strict_types=1);

namespace DtoPacker\Validators;

use DtoPacker\UnpackableInterface;
use Throwable;

class ValidationExceptions extends \RuntimeException
{
    public array $exceptions = [];

    /** @var FieldErrorDto[]|ArrayErrorDto[]  */
    public array $errors = [];

    public function __construct(string $message = 'Validation exception', int $code = 422, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function addFieldException(string $field, string $message, Throwable $exception): void
    {
        $this->exceptions[] = $exception;

        $this->errors[] = new FieldErrorDto([
            'field' => $field,
            'error' => $message,
            'path'  => $field,
        ]);
    }

    public function addArrayException(string $field, array $indexes, string $message, Throwable $exception): void
    {
        $this->exceptions[] = $exception;

        $this->errors[] = new ArrayErrorDto([
            'field' => $field,
            'index' => $indexes,
            'error' => $message,
            'path'  => "{$field}[" . \implode('][', $indexes) . ']',
        ]);
    }

    public function toArray(): array
    {
        return \array_map(static fn (UnpackableInterface $e): array => $e->toArray(), $this->errors);
    }

    public function getExceptions(): array
    {
        return $this->exceptions;
    }

    public function __toString(): string
    {
        return \json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }
}
