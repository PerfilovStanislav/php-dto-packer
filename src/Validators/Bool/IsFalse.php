<?php

declare(strict_types=1);

namespace DtoPacker\Validators\Bool;

use DtoPacker\Validators\AbstractValidator;

class IsFalse extends AbstractValidator
{
    public function __construct(
        protected string|\Stringable $error = '{index} {field} must be false',
    ) {
    }

    public function __invoke(mixed $value): \Generator
    {
        ($value ?? false)
            && yield $this->exception();
    }
}
