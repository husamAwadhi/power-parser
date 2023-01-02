<?php

declare(strict_types=1);

namespace HusamAwadhi\PowerParserTests\Blueprint\Components;

use HusamAwadhi\PowerParser\Blueprint\Components\Conditions;
use HusamAwadhi\PowerParser\Blueprint\Exceptions\InvalidFieldException;
use PHPUnit\Framework\TestCase;

class ConditionsTest extends TestCase
{
    /**
     * @dataProvider validParametersDataProvider
     */
    public function testCreateFromParameters($parametersArray, $expected)
    {
        $conditions = Conditions::createFromParameters($parametersArray);

        $this->assertIsIterable($conditions);
        $this->assertEquals($conditions->conditions, $expected);
    }
    public function validParametersDataProvider()
    {
        return [
            [
                [
                    ['column' => [2], 'is' => 'value', 'isNot' => 'value2'],
                    ['column' => [2], 'isNot' => 'value'],
                ],
                [
                    ['column' => [2], 'is' => 'value'],
                    ['column' => [2], 'isNot' => 'value'],
                ],
            ],
            [
                [
                    ['column' => [2,2], 'anyOf' => 'value'],
                    ['column' => [2,5,6,7], 'noneOf' => 'value'],
                ],
                [
                    ['column' => [2,2], 'anyOf' => 'value'],
                    ['column' => [2,5,6,7], 'noneOf' => 'value'],
                ],
            ],
        ];
    }

    /**
     * @dataProvider invalidParametersDataProvider
     */
    public function testCreateFromInvalidParameters($parametersArray, $exception)
    {
        $this->expectException($exception);
        $_ = Conditions::createFromParameters($parametersArray);
    }
    public function invalidParametersDataProvider()
    {
        return [
            [
                [['column' => 3, 'is' => 'value'],],
                InvalidFieldException::class,
            ],
            [
                [['column' => [2], 'maybe' => 'value'],],
                InvalidFieldException::class,
            ],
            [
                [['is' => 'value'],],
                InvalidFieldException::class,
            ],
            [
                [['column' => [2], 'is' => ''],],
                InvalidFieldException::class,
            ],
            [
                [['column' => [2]],],
                InvalidFieldException::class,
            ],
        ];
    }
}
