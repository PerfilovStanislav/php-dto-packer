<?php

declare(strict_types=1);

namespace DtoPacker\Validators\String;

use DtoPacker\Validators\AbstractValidator;

class Regex extends AbstractValidator
{
    public function __construct(
        protected string             $regex,
        protected string|\Stringable $error = '{index} {field} has invalid format',
    ) {
    }

    public function __invoke(mixed $value): \Generator
    {
        isset($value)
            && (\preg_match($this->regex, "$value") !== 1)
            && yield $this->exception();
    }
}
