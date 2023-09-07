<?php

declare(strict_types=1);

namespace HusamAwadhi\PowerParserTests\Blueprint\Components;

use HusamAwadhi\PowerParser\Blueprint\BlueprintHelper;
use HusamAwadhi\PowerParser\Blueprint\Components\ConditionKeyword;
use HusamAwadhi\PowerParser\Blueprint\Components\Conditions;
use HusamAwadhi\PowerParser\Blueprint\ValueObject\Condition;
use HusamAwadhi\PowerParser\Exception\InvalidFieldException;
use PHPUnit\Framework\TestCase;

class ConditionsTest extends TestCase
{
    /**
     * @dataProvider validParametersDataProvider
     */
    public function testCreateFromParameters(array $parametersArray, array $expected): void
    {
        $conditions = Conditions::from($parametersArray, new BlueprintHelper());

        $this->assertIsIterable($conditions);
        $this->assertEquals($conditions->conditions, $expected);
    }
    public static function validParametersDataProvider(): array
    {
        return [
            [
                [
                    ['column' => [2], 'is' => 'value', 'isNot' => 'value2'],
                    ['column' => [2], 'isNot' => 'value'],
                ],
                [
                    Condition::from([2], ConditionKeyword::Is, 'value'),
                    Condition::from([2], ConditionKeyword::IsNot, 'value'),
                ],
            ],
            [
                [
                    ['column' => [2,2], 'anyOf' => 'value'],
                    ['column' => [2,5,6,7], 'noneOf' => 'value'],
                ],
                [
                    Condition::from([2,2], ConditionKeyword::AnyOf, 'value'),
                    Condition::from([2,5,6,7], ConditionKeyword::NoneOf, 'value'),
                ],
            ],
        ];
    }

    /**
     * @dataProvider invalidParametersDataProvider
     * @param class-string<\Throwable> $exception
     */
    public function testCreateFromInvalidParameters(array $parametersArray, string $exception): void
    {
        $this->expectException($exception);
        $_ = Conditions::from($parametersArray, new BlueprintHelper());
    }
    public static function invalidParametersDataProvider(): array
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
