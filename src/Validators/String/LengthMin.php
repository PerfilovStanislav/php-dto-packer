<?php

declare(strict_types=1);

namespace DtoPacker\Validators\String;

use DtoPacker\Validators\AbstractValidator;

class LengthMin extends AbstractValidator
{
    public function __construct(
        protected readonly int       $min,
        protected string|\Stringable $error = '{index} {field} must be at least {min} characters',
    ) {
    }

    public function __invoke(mixed $value): \Generator
    {
        isset($value)
            && (\mb_strlen("$value") < $this->min)
            && yield $this->exception();
    }
}
