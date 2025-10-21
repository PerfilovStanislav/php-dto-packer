<?php

declare(strict_types=1);

namespace DtoPacker\Validators\String\Uuid;

use DtoPacker\Validators\AbstractValidator;

abstract class AbstractUuid extends AbstractValidator
{
    protected string $ver = '[1-7]';

    public function __construct(
        protected string|\Stringable $error = '{index} {field} must be a valid UUID',
    ) {
    }

    public function __invoke(mixed $value): \Generator
    {
        $regex = "/^[0-9a-f]{8}-[0-9a-f]{4}-$this->ver[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i";

        isset($value)
            && (\preg_match($regex, "$value") !== 1)
            && yield $this->exception();
    }
}
