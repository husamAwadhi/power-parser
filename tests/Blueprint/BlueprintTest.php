<?php

declare(strict_types=1);

namespace HusamAwadhi\PowerParserTests\Blueprint;

use HusamAwadhi\PowerParser\Blueprint\Blueprint;
use HusamAwadhi\PowerParser\Blueprint\BlueprintHelper;
use HusamAwadhi\PowerParser\Blueprint\BlueprintInterface;
use HusamAwadhi\PowerParser\Exception\InvalidBlueprintException;
use HusamAwadhi\PowerParser\Exception\InvalidComponentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

class BlueprintTest extends TestCase
{
    protected string $blueprintsDirectory = STORAGE_DIRECTORY . '/blueprints/';

    public function testSuccessfullyCreateBlueprint(): void
    {
        $rawFile = (string) file_get_contents($this->blueprintsDirectory .  'valid.yaml');
        $blueprint = Blueprint::from(
            Yaml::parse($rawFile),
            $this->blueprintsDirectory .  'valid.yaml',
            new BlueprintHelper()
        );

        $this->assertInstanceOf(BlueprintInterface::class, $blueprint);
    }

    /**
     * @dataProvider invalidFilesProvider
     * @param class-string<\Throwable> $exception
     */
    public function testThrowingExceptionOnInvalidBlueprint(string $fileName, string $exception): void
    {
        $this->expectException($exception);
        $path = $this->blueprintsDirectory . $fileName . '.yaml';
        Blueprint::from(Yaml::parseFile($path), 'some-path', new BlueprintHelper());
    }
    public static function invalidFilesProvider(): array
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
