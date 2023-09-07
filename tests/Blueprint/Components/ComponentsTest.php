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

class ComponentsTest extends TestCase
{
    protected string $blueprintsDirectory = STORAGE_DIRECTORY . '/blueprints/';

    protected mixed $helper;

    public function setUp(): void
    {
        $this->helper = $this->createMock(BlueprintHelper::class);
    }

    /**
     * @dataProvider invalidFilesProvider
     * @param class-string<\Throwable> $exception
     */
    public function testThrowingExceptionOnInvalidBlueprint(string $fileName, string $exception): void
    {
        $this->expectException($exception);
        $path = $this->blueprintsDirectory . $fileName . '.yaml';

        $builder = new BlueprintBuilder(new BlueprintHelper());
        $_ = $builder->load($path)
            ->build();
    }
    public static function invalidFilesProvider(): array
    {
        return [
            ['invalid_component_1', InvalidComponentException::class],
        ];
    }

    /**
     * @dataProvider validParametersDataProvider
     */
    public function testCreateFromParameters(array $parametersArray): void
    {
        $components = Components::from($parametersArray, new BlueprintHelper());

        $helper = clone $this->helper;
        foreach ($parametersArray as $component) {
            $helper->expects($this->exactly(sizeof($parametersArray)))
                ->method('createFields')->with($component['fields'])
                ->willReturn(new Fields($component['fields'], new BlueprintHelper()));

            if (isset($component['conditions'])) {
                $helper->method('createConditions')->with($component['conditions'])
                    ->willReturn(new Conditions($component['conditions'], new BlueprintHelper()));
            }
        }

        $expected = new Components($parametersArray, $helper);

        $this->assertIsIterable($components);
        $this->assertEquals(
            $expected->components,
            $components->components,
        );
    }
    public static function validParametersDataProvider(): array
    {
        return [
            [
                [
                    [
                        'name' => 'its me',
                        'type' => 'hit',
                        'conditions' => [['column' => [2], 'isNot' => 'value']],
                        'fields' => [['name' => 'field1', 'position' => 2]],
                    ],
                    [
                        'name' => 'its me',
                        'type' => 'next',
                        'fields' => [['name' => 'field1', 'position' => 2]],
                    ],
                ]
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
        $_ = Components::from($parametersArray, $this->helper);
    }
    public static function invalidParametersDataProvider(): array
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
                        'name' => 'me',
                        'conditions' => [['column' => [2], 'isNot' => 'value']],
                        'fields' => [['name' => 'field1', 'position' => 2]],
                    ]
                ],
                InvalidComponentException::class,
            ],
            [
                [
                    [
                        'name' => 'me',
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
                        'type' => 'next',
                        'name' => 'me',
                    ]
                ],
                InvalidComponentException::class,
            ],
            [
                [
                    [
                        'type' => 'next',
                    ]
                ],
                InvalidComponentException::class,
            ],
        ];
    }
}
