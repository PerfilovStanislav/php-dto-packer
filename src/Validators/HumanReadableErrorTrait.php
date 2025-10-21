<?php

declare(strict_types=1);

namespace DtoPacker\Validators;

trait HumanReadableErrorTrait
{
    protected function humanName(string $field): string
    {
        return \strtolower(
            \preg_replace(
                '/([a-z])([A-Z])/',
                '$1 $2',
                \str_replace('_', ' ', $field)
            )
        );
    }

    protected function values(): array
    {
        $result = [];
        $vars = \get_object_vars($this);

        foreach ($vars as $key => $value) {
            $result["{{$key}}"] = $this->value($value);
        }

        return $result;
    }
}
