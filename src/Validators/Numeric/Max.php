<?php

declare(strict_types=1);

namespace DtoPacker\Validators\Numeric;

use DtoPacker\Validators\AbstractValidator;

class Max extends AbstractValidator
{
    public function __construct(
        protected readonly float     $max,
        protected string|\Stringable $error = '{index} {field} may not be greater than {max}',
    ) {
    }

    public function __invoke(mixed $value): \Generator
    {
        isset($value)
            && $value > $this->max
            && yield $this->exception();
    }
}
