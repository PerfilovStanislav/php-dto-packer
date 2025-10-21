<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class DtoPackerTest extends TestCase
{
    public function testUserDtoWithPurchases(): void
    {
        $data = [
            'id' => 1,
            'name' => 'Elon',
        ];

        $userDto = new ExampleDto($data);

        $this->assertInstanceOf(ExampleDto::class, $userDto);
        $this->assertSame(1, $userDto->id);
        $this->assertSame('Elon', $userDto->name);
    }
}