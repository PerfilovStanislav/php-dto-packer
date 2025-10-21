<?php

declare(strict_types=1);

namespace DtoPacker\Validators\Array;

use DtoPacker\Validators\AbstractValidator;

class CountMin extends AbstractValidator
{
    public function __construct(
        protected readonly int       $min,
        protected string|\Stringable $error = '{index} {field} must have at least {min} items',
    ) {
    }

    public function __invoke(mixed $value): \Generator
    {
        isset($value[0])
            && (\count($value) < $this->min)
            && yield $this->exception();
    }
}
