<?php

declare(strict_types=1);

namespace DtoPacker\Validators\String;

use DtoPacker\Validators\AbstractValidator;

class LengthMax extends AbstractValidator
{
    public function __construct(
        protected readonly int       $max,
        protected string|\Stringable $error = '{index} {field} may not be greater than {max} characters',
    ) {
    }

    public function __invoke(mixed $value): \Generator
    {
        (\mb_strlen("$value") > $this->max)
            && yield $this->exception();
    }
}
