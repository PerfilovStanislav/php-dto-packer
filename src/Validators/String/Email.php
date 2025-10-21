<?php

declare(strict_types=1);

namespace DtoPacker\Validators\String;

use DtoPacker\Validators\AbstractValidator;

class Email extends AbstractValidator
{
    public function __construct(
        protected string|\Stringable $error = '{index} {field} must be a valid email address',
    ) {
    }

    public function __invoke(mixed $value): \Generator
    {
        isset($value)
            && (\filter_var("$value", FILTER_VALIDATE_EMAIL) === false)
            && yield $this->exception();
    }
}
