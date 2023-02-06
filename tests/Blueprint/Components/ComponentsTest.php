<?php

declare(strict_types=1);

namespace HusamAwadhi\PowerParserTests\Blueprint\Components;

use PHPUnit\Framework\TestCase;
use HusamAwadhi\PowerParser\Blueprint\BlueprintBuilder;
use HusamAwadhi\PowerParser\Blueprint\BlueprintHelper;
use HusamAwadhi\PowerParser\Blueprint\Components\Components;
use HusamAwadhi\PowerParser\Blueprint\Components\Conditions;
use HusamAwadhi\PowerParser\Blueprint\Components\Fields;
use HusamAwadhi\PowerParser\Exception\InvalidComponentException;
use PHPUnit\Framework\MockObject\MockObject;

class ComponentsTest extends TestCase
{
    protected string $blueprintsDirectory = STORAGE_DIRECTORY . '/blueprints/';

    protected $helper;

    public function setUp(): void
    {
        $this->helper = $this->createMock(BlueprintHelper::class);
    }

    /**
     * @dataProvider invalidFilesProvider
     */
    public function testThrowingExceptionOnInvalidBlueprint(string $fileName, string $exception)
    {
        $this->expectException($exception);
        $path = $this->blueprintsDirectory . $fileName . '.yaml';

        $builder = new BlueprintBuilder(new BlueprintHelper());
        $_ = $builder->load($path)
            ->build();
    }
    public function invalidFilesProvider()
    {
        return [
            ['invalid_component_1', InvalidComponentException::class],
        ];
    }

    /**
     * @dataProvider validParametersDataProvider
     */
    public function testCreateFromParameters($parametersArray)
    {
        $components = Components::from($parametersArray, new BlueprintHelper());

        $helper = clone $this->helper;
        foreach ($parametersArray as $component) {
            $helper->expects($this->exactly(sizeof($parametersArray)))->method('createFields')->with($component['fields'])->willReturn(new Fields($component['fields'], new BlueprintHelper()));

            if (isset($component['conditions'])) {
                $helper->method('createConditions')->with($component['conditions'])->willReturn(new Conditions($component['conditions'], new BlueprintHelper()));
            }
        }

        $expected = new Components($parametersArray, $helper);

        $this->assertIsIterable($components);
        $this->assertEquals(
            $expected->components,
            $components->components,
        );
    }
    public function validParametersDataProvider()
    {
        return [
            [
                [
                    [
                        'type' => 'hit',
                        'conditions' => [['column' => [2], 'isNot' => 'value']],
                        'fields' => [['name' => 'field1', 'position' => 2]],
                    ],
                    [
                        'type' => 'next',
                        'fields' => [['name' => 'field1', 'position' => 2]],
                    ],
                ]
            ],
        ];
    }

    /**
     * @dataProvider invalidParametersDataProvider
     */
    public function testCreateFromInvalidParameters($parametersArray, $exception)
    {
        $this->expectException($exception);
        $_ = Components::from($parametersArray, $this->helper);
    }
    public function invalidParametersDataProvider()
    {
        return [
            [
                [
                    'hi'
                ],
                InvalidComponentException::class,
            ],
            [
                [
                    [
                        'conditions' => [['column' => [2], 'isNot' => 'value']],
                        'fields' => [['name' => 'field1', 'position' => 2]],
                    ]
                ],
                InvalidComponentException::class,
            ],
            [
                [
                    [
                        'type' => 'miss',
                        'conditions' => [['column' => [2], 'isNot' => 'value']],
                        'fields' => [['name' => 'field1', 'position' => 2]],
                    ]
                ],
                InvalidComponentException::class,
            ],
            [
                [
                    [
                        'type' => 'next'
                    ]
                ],
                InvalidComponentException::class,
            ],
        ];
    }
}
