<?php

declare(strict_types=1);

namespace DtoPacker\Validators\Mixed;

use DtoPacker\Validators\AbstractValidator;

class Requires extends AbstractValidator
{
    protected string $requiredField;

    public function __construct(
        protected readonly array     $fields,
        protected string|\Stringable $error = '{index} {requiredField} is required with {field}',
    ) {
    }

    public function __invoke(mixed $value): \Generator
    {
        if (empty($value)) {
            return;
        }

        foreach ($this->fields as $key) {
            $val = $this->dto->$key ?? null;

            if (isset($this->indexes[0])) {
                foreach ($this->indexes as $i) {
                    $val = $val[$i] ?? null;
                }
            }

            if (empty($val)) {
                $this->requiredField = $this->humanName($key);

                yield $this->exception();
            }
        }
    }
}
