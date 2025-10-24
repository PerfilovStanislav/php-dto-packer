<?php

namespace Tests;

use DtoPacker\AbstractDto;
use DtoPacker\Validators\AbstractValidator;
use DtoPacker\Validators\ValidationExceptions;
use PHPUnit\Framework\TestCase;
use Tests\Dto\BackedEnum;
use Tests\Dto\FailDto;
use Tests\Dto\SuccessDto;
use Tests\Dto\UnitEnum;

class DtoPackerTest extends TestCase
{
    public function testSuccessPacking(): void
    {
        $data = [
            'fake'  => null,
            'null'  => null,
            'string'  => 's',
            'strings' => ['ss', 'aa'],
            'strings2' => [['ss', 'aa']],
            'int' => 5,
            'ints' => [5],
            'ints2' => [[5]],
            'float' => 5.5,
            'floats' => [5.5],
            'floats2' => [[5.5]],
            'bool' => true,
            'bools' => [false],
            'bools2' => [[true]],
            'object' => new \stdClass(),
            'objects' => [new \stdClass()],
            'objects2' => [[new \stdClass()]],
            'backedEnum' => BackedEnum::BE,
            'backedEnums' => [BackedEnum::BE],
            'backedEnums2' => [[BackedEnum::BE]],
            'unitEnum' => UnitEnum::UE,
            'unitEnums' => [UnitEnum::UE],
            'unitEnums2' => [[UnitEnum::UE]],
            'datetime' => new \DateTime(),
            'datetimes' => [new \DateTime()],
            'datetimes2' => [[new \DateTime()]],
            'dto' => [
                'object' => [],
                'objects' => [[]],
                'objects2' => [[[]]],
                'backedEnum' => 'be',
                'backedEnums' => ['be'],
                'backedEnums2' => [['be']],
                'unitEnum' => 'UE',
                'unitEnums' => ['UE'],
                'unitEnums2' => [['UE']],
                'datetime' => '2020-01-01',
                'datetimes' => ['2020-01-01'],
                'datetimes2' => [['2020-01-01']],
                'dtos' => [[]],
                'dtos2' => [[[]]],
            ],
            'dtos' => [new SuccessDto([])],
            'dtos2' => [[new SuccessDto([])]],
        ];

        $dto = new SuccessDto($data);
        $dto->pack();
        $dto->initialized();
        $dto->has('fake');
        \json_encode($dto);
        \unserialize(\serialize($dto));
        $dto->dto->string = 's';
        isset($dto->string);
        isset($dto['string']);
        $dto['string'];
        $dto['string'] = 's';
        unset($dto->string);
        unset($dto['string']);

        $this->assertInstanceOf(SuccessDto::class, $dto);
        $str = "$dto";

        $x = (new class() extends AbstractValidator {});
        $x->setData($dto, 'string');
        foreach ($x('xxx') as $v) {

        }
    }

    public function testFails(): void
    {
        $data = [
            'string'  => 'xxx',
            'strings'  => ['aaaaaaa', 'aaaaaaa'],
            'dto' => [
                'string' => '4242-4242-4242-4242',
                'strings' => [null],
                'be' => UnitEnum::UE,
            ],
            'dto2' => [
                'string' => '',
            ],
            'dtos' => [
                [
                    'string' => null,
                ],
            ],
            'dtos2' => [[['be' => UnitEnum::UE]]],
            'x1' => [1, 2, 3],
            'x2' => [10, 20],
            'x3' => -1,
        ];

        try {
            $dto = new FailDto($data);
        } catch (ValidationExceptions $e) {
            $this->assertIsArray($e->toArray());
            $x = $e->getExceptions();
            $x = "$e";
        }
    }

    public function testUnknownType(): void
    {
        try {
            new Fail2Dto([]);
        } catch (\Throwable $e) {
            $this->assertStringEndsWith('no handler', $e->getMessage());
        }
    }
}

class Fail2Dto extends AbstractDto
{
    protected Fail2Dto&FailDto $dto;
}