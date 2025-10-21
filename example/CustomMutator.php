<?php

declare(strict_types=1);

class CustomMutator
{
    public static function addPrefixMr(string $value): string
    {
        return "Mr $value";
    }
}
