<?php

declare(strict_types=1);

namespace DtoPacker\Validators\Numeric;

use DtoPacker\Validators\AbstractValidator;

class Min extends AbstractValidator
{
    public function __construct(
        protected readonly float     $min,
        protected string|\Stringable $error = '{index} {field} must be at least {min}',
    ) {
    }

    public function __invoke(mixed $value): \Generator
    {
        isset($value)
            && $value < $this->min
            && yield $this->exception();
    }
}
