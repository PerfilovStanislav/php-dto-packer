<?php

declare(strict_types=1);

namespace Example;

class CustomMutator
{
    public static function addPrefixMr(string $value): string
    {
        return "Mr $value";
    }
}
