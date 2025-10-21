<?php

declare(strict_types=1);

namespace DtoPacker\Validators\String;

use DtoPacker\Validators\AbstractValidator;

class Url extends AbstractValidator
{
    public function __construct(
        protected string|\Stringable $error = '{index} {field} has invalid url format',
    ) {
    }

    public function __invoke(mixed $value): \Generator
    {
        isset($value)
            && (\filter_var("$value", FILTER_VALIDATE_URL) === false)
            && yield $this->exception();
    }
}
