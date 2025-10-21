<?php

namespace DtoPacker;

interface PackableInterface
{
    public function __construct(string|array $data, bool $withMutators = true);

    public function fromArray(array $data, bool $withMutators = true): static;

    public function __set(string $name, $value): void;
}
