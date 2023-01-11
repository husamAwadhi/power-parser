<?php

declare(strict_types=1);

namespace HusamAwadhi\PowerParserTests;

use PHPUnit\Framework\TestCase;

class JustExampleTest extends TestCase
{
    /**
     * @dataProvider exampleOneDataProvider
     */
    public function testExampleOne($expected, $actual)
    {
        $this->assertEquals($expected, $actual);
    }
    public function exampleOneDataProvider()
    {
        return [
            [1, 1],
            [2, 2],
        ];
    }

    public function testExampleTwo()
    {
        $this->assertEquals(1, 1);
    }
}
