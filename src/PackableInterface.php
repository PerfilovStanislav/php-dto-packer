<?php

namespace DtoPacker;

interface PackableInterface
{
    public function __construct(string|array $data);

    public function toArray(): array;
}
