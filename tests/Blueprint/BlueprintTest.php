<?php

declare(strict_types=1);

namespace HusamAwadhi\PowerParserTests\Blueprint;

use HusamAwadhi\PowerParser\Blueprint\Blueprint;
use HusamAwadhi\PowerParser\Blueprint\BlueprintHelper;
use HusamAwadhi\PowerParser\Blueprint\BlueprintInterface;
use HusamAwadhi\PowerParser\Exception\InvalidBlueprintException;
use HusamAwadhi\PowerParser\Exception\InvalidComponentException;
use PHPUnit\Framework\TestCase;

class BlueprintTest extends TestCase
{
    protected string $blueprintsDirectory = STORAGE_DIRECTORY . '/blueprints/';

    public function testSuccessfullyCreateBlueprint()
    {
        $rawFile = file_get_contents($this->blueprintsDirectory .  'valid.yaml');
        $blueprint = Blueprint::from(
            \yaml_parse($rawFile),
            $this->blueprintsDirectory .  'valid.yaml',
            new BlueprintHelper()
        );

        $this->assertInstanceOf(BlueprintInterface::class, $blueprint);
    }

    /**
     * @dataProvider invalidFilesProvider
     */
    public function testThrowingExceptionOnInvalidBlueprint(string $fileName, string $exception)
    {
        $this->expectException($exception);
        $path = $this->blueprintsDirectory . $fileName . '.yaml';
        Blueprint::from(\yaml_parse_file($path), 'some-path', new BlueprintHelper());
    }
    public function invalidFilesProvider()
    {
        return [
            ['invalid_blueprint_1', InvalidBlueprintException::class],
            ['invalid_blueprint_2', InvalidBlueprintException::class],
            ['invalid_blueprint_3', InvalidBlueprintException::class],
            ['invalid_blueprint_4', InvalidBlueprintException::class],
            ['invalid_component_1', InvalidComponentException::class],
        ];
    }
}
