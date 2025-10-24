<?php

declare(strict_types=1);

namespace Tests\Dto;

class Mutator
{
    public static function throw(mixed $value): void
    {
        throw new \RuntimeException('xxx');
    }
}