<?php

use DtoPacker\AbstractDto;
use DtoPacker\Alias;
use DtoPacker\PreMutator;

/**
 * @property Person[] $persons
 */
class Family extends AbstractDto
{
    #[Alias('lastname', 'family_name')]
    #[PreMutator('ucfirst', [CustomMutator::class, 'change'])]
    public string $surname;

    protected array|Person $persons;

    public bool $hasCar;
}
