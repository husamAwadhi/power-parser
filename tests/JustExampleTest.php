<?php

declare(strict_types=1);

namespace HusamAwadhi\PowerParserTests;

use PHPUnit\Framework\TestCase;

class JustExampleTest extends TestCase
{
    /**
     * @dataProvider exampleOneDataProvider
     */
    public function testExampleOne(int $expected, int $actual): void
    {
        $this->assertEquals($expected, $actual);
    }
    public static function exampleOneDataProvider(): array
    {
        return [
            [1, 1],
            [2, 2],
        ];
    }

    public function testExampleTwo(): void
    {
        $this->assertEquals(1, 1);
    }
}
