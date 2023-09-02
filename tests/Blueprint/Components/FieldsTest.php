<?php

declare(strict_types=1);

namespace HusamAwadhi\PowerParserTests\Blueprint\Components;

use HusamAwadhi\PowerParser\Blueprint\BlueprintHelper;
use HusamAwadhi\PowerParser\Blueprint\Components\Fields;
use HusamAwadhi\PowerParser\Blueprint\FieldFormat as FieldFormatEnum;
use HusamAwadhi\PowerParser\Blueprint\FieldType;
use HusamAwadhi\PowerParser\Blueprint\ValueObject\Field;
use HusamAwadhi\PowerParser\Blueprint\ValueObject\FieldFormat;
use HusamAwadhi\PowerParser\Exception\InvalidFieldException;
use PHPUnit\Framework\TestCase;

class FieldsTest extends TestCase
{
    /**
     * @dataProvider validParametersDataProvider
     */
    public function testCreateFromParameters(array $parametersArray, array $expected): void
    {
        $fields = Fields::from($parametersArray, new BlueprintHelper());

        $this->assertIsIterable($fields);
        $this->assertEquals($fields->fields, $expected);
    }
    public function validParametersDataProvider(): array
    {
        return [
            [
                [
                    ['name' => 'field1', 'position' => 2],
                    ['name' => 'field2', 'position' => 3],
                    ['name' => 'field3', 'position' => 25, 'type' => 'int'],
                    ['name' => 'field4', 'position' => 4, 'type' => 'bool-strict'],
                    ['name' => 'field5', 'position' => 4, 'format' => 's%5'],
                    ['name' => 'field5', 'position' => 4, 'type' => 'bool', 'format' => 'f%2'],
                ],
                [
                    Field::from('field1', 2),
                    Field::from('field2', 3),
                    Field::from('field3', 25, FieldType::INT),
                    Field::from('field4', 4, FieldType::BOOL_STRICT),
                    Field::from('field5', 4, null, FieldFormat::from(FieldFormatEnum::STRING, 5)),
                    Field::from('field5', 4, FieldType::BOOL, FieldFormat::from(FieldFormatEnum::FLOAT, 2)),
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
        $_ = Fields::from($parametersArray, new BlueprintHelper());
    }
    public function invalidParametersDataProvider(): array
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
            [
                [['name' => 'field1', 'position' => 2, 'type' => 'single'],],
                InvalidFieldException::class,
            ],
            [
                [['name' => 'field1', 'position' => 2, 'format' => 's'],],
                InvalidFieldException::class,
            ],
            [
                [['name' => 'field1', 'position' => 2, 'format' => 's%x'],],
                InvalidFieldException::class,
            ],
            [
                [['name' => 'field1', 'position' => 2, 'format' => 's% '],],
                InvalidFieldException::class,
            ],
        ];
    }
}
