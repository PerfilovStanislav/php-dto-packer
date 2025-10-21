<?php

declare(strict_types=1);

namespace DtoPacker\Validators\Mixed;

use DtoPacker\Validators\AbstractValidator;

class In extends AbstractValidator
{
    protected string $values;

    public function __construct(
        protected readonly mixed     $allowed,
        protected string|\Stringable $error = '{index} {field} must be one of {values}',
    ) {
    }

    public function __invoke(mixed $value): \Generator
    {
        foreach ($this->allowed as $check) {
            if ($check === $value) {
                return;
            }
        }

        $this->values = $this->value($this->allowed);

        yield $this->exception();
    }
}
