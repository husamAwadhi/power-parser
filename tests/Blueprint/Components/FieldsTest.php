<?php

declare(strict_types=1);

namespace HusamAwadhi\PowerParserTests\Blueprint\Components;

use HusamAwadhi\PowerParser\Blueprint\Components\Fields;
use HusamAwadhi\PowerParser\Exception\InvalidFieldException;
use PHPUnit\Framework\TestCase;

class FieldsTest extends TestCase
{
    /**
     * @dataProvider validParametersDataProvider
     */
    public function testCreateFromParameters($parametersArray, $expected)
    {
        $fields = Fields::createFromParameters($parametersArray);

        $this->assertIsIterable($fields);
        $this->assertEquals($fields->fields, $expected);
    }
    public function validParametersDataProvider()
    {
        return [
            [
                [
                    ['name' => 'field1', 'position' => 2],
                    ['name' => 'field2', 'position' => 3],
                ],
                [
                    ['name' => 'field1', 'position' => 2],
                    ['name' => 'field2', 'position' => 3],
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
        $_ = Fields::createFromParameters($parametersArray);
    }
    public function invalidParametersDataProvider()
    {
        return [
            [
                [['name' => false, 'position' => 2],],
                InvalidFieldException::class,
            ],
            [
                [['name' => 'field1', 'position' => 'two'],],
                InvalidFieldException::class,
            ],
            [
                [['name' => '', 'position' => 1],],
                InvalidFieldException::class,
            ],
            [
                [['position' => 'two'],],
                InvalidFieldException::class,
            ],
        ];
    }
}
