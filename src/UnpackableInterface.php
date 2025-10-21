<?php

namespace DtoPacker;

interface UnpackableInterface
{
    public function toArray(): array;

    public function __get(string $name): mixed;
}
