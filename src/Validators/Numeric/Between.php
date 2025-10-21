<?php

declare(strict_types=1);

namespace DtoPacker\Validators\Numeric;

use DtoPacker\Validators\AbstractValidator;

class Between extends AbstractValidator
{
    public function __construct(
        protected readonly float     $min,
        protected readonly float     $max,
        protected string|\Stringable $error = '{index} {field} must be between {min} and {max}',
    ) {
    }

    public function __invoke(mixed $value): \Generator
    {
        isset($value)
            && ($value < $this->min || $value > $this->max)
            && yield $this->exception();
    }
}
