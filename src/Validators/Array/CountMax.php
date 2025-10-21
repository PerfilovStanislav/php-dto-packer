<?php

declare(strict_types=1);

namespace DtoPacker\Validators\Array;

use DtoPacker\Validators\AbstractValidator;

class CountMax extends AbstractValidator
{
    public function __construct(
        protected readonly int       $max,
        protected string|\Stringable $error = '{index} {field} may not have more than {max} items',
    ) {
    }

    public function __invoke(mixed $value): \Generator
    {
        isset($value[0])
            && (\count($value) > $this->max)
            && yield $this->exception();
    }
}
