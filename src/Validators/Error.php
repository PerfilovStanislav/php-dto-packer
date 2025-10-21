<?php

declare(strict_types=1);

namespace DtoPacker\Validators;

use DtoPacker\UnpackableInterface;

class Error extends AbstractValidator
{
    protected string|\Stringable $error = '{field} has error';

    public function __construct(
        protected UnpackableInterface $dto,
        protected string $field,
    ) {
    }

    public function error(): string
    {
        return $this->exception()->getMessage();
    }
}
