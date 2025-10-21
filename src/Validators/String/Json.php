<?php

declare(strict_types=1);

namespace DtoPacker\Validators\String;

use DtoPacker\Validators\AbstractValidator;

class Json extends AbstractValidator
{
    public function __construct(
        protected string|\Stringable $error = '{index} {field} must be json',
    ) {
    }

    public function __invoke(mixed $value): \Generator
    {
        isset($value)
            && (\json_decode("$value") === null)
            && yield $this->exception();
    }
}
