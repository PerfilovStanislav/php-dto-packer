<?php

declare(strict_types=1);

namespace DtoPacker\Validators;

use DtoPacker\UnpackableInterface;

abstract class AbstractValidator
{
    use HumanReadableErrorTrait;
    use HumanReadableTypeTrait;

    protected UnpackableInterface $dto;
    protected string $field;
    protected array $indexes;

    protected string|\Stringable $error = '{index} {field} has validation error';

    public function __invoke(mixed $value): \Generator
    {
        yield $this->exception();
    }

    public function setData(UnpackableInterface $dto, string $field, array $indexes = []): void
    {
        $this->dto = $dto;
        $this->field = $field;
        $this->indexes = $indexes;
    }

    public function exception(): \Throwable
    {
        $values = [
            '{field}' => $this->humanName($this->field),
        ] + $this->values();

        if (empty($this->indexes)) {
            $values['{index}'] = null;
        } else {
            $values['{index}'] = '[' . \implode('][', $this->indexes) . ']';
        }

        return new \RuntimeException(
            \ucfirst(\mb_trim(\strtr("$this->error", $values))),
            422,
        );
    }
}
