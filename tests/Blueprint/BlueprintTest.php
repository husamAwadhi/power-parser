<?php declare(strict_types=1);
namespace Tests\Blueprint;

use HusamAwadhi\PowerParser\Blueprint\Blueprint;
use HusamAwadhi\PowerParser\Blueprint\BlueprintInterface;
use HusamAwadhi\PowerParser\Blueprint\Exceptions\InvalidBlueprintException;
use HusamAwadhi\PowerParser\Blueprint\Exceptions\InvalidComponentException;
use PHPUnit\Framework\TestCase;

class BlueprintTest extends TestCase
{
    protected string $blueprintsDirectory = '';

    protected function setUp(): void
    {
        $this->blueprintsDirectory =  dirname(dirname(__DIR__)) . '/storage/tests/blueprint/';
    }

    public function testCreateFromString()
    {
        $rawFile = file_get_contents($this->blueprintsDirectory .  'valid.yaml');
        $blueprint = Blueprint::createBlueprint($rawFile);
        $this->assertInstanceOf(BlueprintInterface::class, $blueprint);
    }

    public function testCreateFromPath()
    {
        $path = $this->blueprintsDirectory .  'valid.yaml';
        $blueprint = Blueprint::createBlueprint($path, true);
        $this->assertInstanceOf(BlueprintInterface::class, $blueprint);
    }
 
    /**
     * @dataProvider emptyStreamsProvider
     */
    public function testThrowingExceptionOnEmptyStream(string $stream, bool $isPath)
    {
        $this->expectException(InvalidBlueprintException::class);
        Blueprint::createBlueprint($stream, $isPath);
    }
    public function emptyStreamsProvider()
    {
        return [
            ['', false],
            ['', true],
        ];
    }

    /**
     * @depends testCreateFromPath
     * @dataProvider invalidFilesProvider
     */
    public function testThrowingExceptionOnInvalidBlueprint(string $fileName, string $exception)
    {
        $this->expectException($exception);
        $path = $this->blueprintsDirectory . $fileName . '.yaml';
        Blueprint::createBlueprint($path, true);
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
