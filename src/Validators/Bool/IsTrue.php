<?php

declare(strict_types=1);

namespace DtoPacker\Validators\Bool;

use DtoPacker\Validators\AbstractValidator;class IsTrue extends AbstractValidator
{
    public function __construct(
        protected string|\Stringable $error = '{index} {field} must be true',
    ) {
    }

    public function __invoke(mixed $value): \Generator
    {
        isset($value)
            && ($value !== true)
            && yield $this->exception();
    }
}
