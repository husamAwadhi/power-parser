<?php declare(strict_types=1);
namespace Tests\Blueprint;

use HusamAwadhi\PowerParser\Blueprint\Blueprint;
use HusamAwadhi\PowerParser\Blueprint\BlueprintInterface;
use PHPUnit\Framework\TestCase;

class BlueprintTest extends TestCase
{
    public string $fileName = 'sample_blueprint.yaml';

    /**
     * @test
     */
    public function canCreateFromYamlFile()
    {
        $rawFile = file_get_contents(dirname(__DIR__) . '/../storage/tests/' . $this->fileName);
        $blueprint = Blueprint::createBlueprint($rawFile);
        $this->assertInstanceOf(BlueprintInterface::class, $blueprint);
    }
}
