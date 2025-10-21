<?php

declare(strict_types=1);

namespace DtoPacker\Validators\String;

use DtoPacker\Validators\AbstractValidator;

class Ulid extends AbstractValidator
{
    public function __construct(
        protected string|\Stringable $error = '{index} {field} must be a valid ULID',
    ) {
    }

    public function __invoke(mixed $value): \Generator
    {
        isset($value)
            && (\preg_match('/^[0-9A-HJ-NP-TV-Z]{26}$/', "$value") !== 1)
            && yield $this->exception();
    }
}
