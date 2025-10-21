<?php

declare(strict_types=1);

namespace DtoPacker\Validators\Array;

use DtoPacker\Validators\AbstractValidator;

/**
 * Faster than Unique
 * Available for strings and integers
 */
class UniqueStrings extends AbstractValidator
{
    public function __construct(
        protected string|\Stringable $error = '{index} {field} must have unique values',
    ) {
    }

    public function __invoke(mixed $value): \Generator
    {
        isset($value[0])
            && (\count(\array_flip($value)) !== \count($value)) 
            && yield $this->exception();
    }
}
