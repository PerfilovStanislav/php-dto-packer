<?php

declare(strict_types=1);

namespace DtoPacker\Validators\Array;

use DtoPacker\Validators\AbstractValidator;

class CountBetween extends AbstractValidator
{
    public function __construct(
        protected readonly int       $min,
        protected readonly int       $max,
        protected string|\Stringable $error = '{index} {field} must have between {min} and {max} items',
    ) {
    }

    public function __invoke(mixed $value): \Generator
    {
        isset($value[0])
            && (($cnt = \count($value)) < $this->min || $cnt > $this->max)
            && yield $this->exception();
    }
}
