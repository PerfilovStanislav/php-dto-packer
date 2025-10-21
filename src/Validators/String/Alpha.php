<?php

declare(strict_types=1);

namespace DtoPacker\Validators\String;

use DtoPacker\Validators\AbstractValidator;

class Alpha extends AbstractValidator
{
    public function __construct(
        protected string|\Stringable $error = '{index} {field} may only contain letters',
    ) {
    }

    public function __invoke(mixed $value): \Generator
    {
        isset($value)
            && (\ctype_alpha($value) === false)
            && yield $this->exception();
    }
}
