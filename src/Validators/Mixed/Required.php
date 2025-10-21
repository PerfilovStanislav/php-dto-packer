<?php

declare(strict_types=1);

namespace DtoPacker\Validators\Mixed;

use DtoPacker\Validators\AbstractValidator;

class Required extends AbstractValidator
{
    public function __construct(
        protected string|\Stringable $error = '{index} {field} is required',
    ) {
    }

    public function __invoke(mixed $value): \Generator
    {
        empty($value)
            && yield $this->exception();
    }
}
