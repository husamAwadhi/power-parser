<?php

declare(strict_types=1);

namespace HusamAwadhi\PowerParserTests;

use Exception;
use HusamAwadhi\PowerParser\Helper;
use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    /**
     * @dataProvider arraysDataProvider
     */
    public function testCanProcessArray(array $raw, array $actual, string $separator)
    {

        $this->assertEquals(Helper::toOneDimensionArray(array: $raw, separator: $separator), $actual);
    }
    public function arraysDataProvider()
    {
        return [
            [
                ['i' => ['hate' => ['myself' => false], 'love' => ['you' => 2]]],
                ['i.hate.myself' => false, 'i.love.you' => 2],
                '.',
            ],
            [
                ['i' => ['hate' => [false], 'love' => [2]]],
                ['i-hate-0' => false, 'i-love-0' => 2],
                '-',
            ],
            [
                [],
                [],
                '',
            ],
        ];
    }

    public function testCanDetectDuplicateKeys()
    {
        $array = ['i' => ['hate.you' => false, 'hate' => ['you' => true]]];

        $this->expectException(Exception::class);
        $a = Helper::toOneDimensionArray($array, separator: '.');
    }
}
