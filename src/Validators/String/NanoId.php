<?php

declare(strict_types=1);

namespace DtoPacker\Validators\String;

use DtoPacker\Validators\AbstractValidator;

class NanoId extends AbstractValidator
{
    public function __construct(
        protected int                $length = 21,
        protected string|\Stringable $error = '{index} {field} must be a valid NanoId',
    ) {
    }

    public function __invoke(mixed $value): \Generator
    {
        isset($value)
            && (\preg_match("/^[A-Za-z0-9\-_]{{$this->length}}$/", "$value") !== 1)
            && yield $this->exception();
    }
}
