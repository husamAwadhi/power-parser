<?php

declare(strict_types=1);

namespace HusamAwadhi\PowerParserTests\Blueprint;

use HusamAwadhi\PowerParser\Blueprint\BlueprintBuilder;
use HusamAwadhi\PowerParser\Blueprint\BlueprintHelper;
use HusamAwadhi\PowerParser\Blueprint\BlueprintInterface;
use HusamAwadhi\PowerParser\Exception\InvalidBlueprintException;
use PHPUnit\Framework\TestCase;

class BlueprintBuilderTest extends TestCase
{
    protected string $blueprintsDirectory = STORAGE_DIRECTORY . '/blueprints/';


    public function testCreateFromString()
    {
        $rawFile = file_get_contents($this->blueprintsDirectory .  'valid.yaml');

        $builder = new BlueprintBuilder(new BlueprintHelper());
        $builder->parse($rawFile);
        $blueprint = $builder->build();

        $this->assertInstanceOf(BlueprintInterface::class, $blueprint);
    }

    public function testCreateFromPath()
    {
        $path = $this->blueprintsDirectory .  'valid.yaml';

        $builder = new BlueprintBuilder(new BlueprintHelper());
        $builder->load($path);
        $blueprint = $builder->build();

        $this->assertInstanceOf(BlueprintInterface::class, $blueprint);
    }

    /**
     * @dataProvider emptyStreamsProvider
     */
    public function testThrowingExceptionOnEmptyStream(string $stream, bool $isPath)
    {
        $this->expectException(InvalidBlueprintException::class);
        $builder = new BlueprintBuilder(new BlueprintHelper());
        if ($isPath) {
            $builder->load($stream);
        } else {
            $builder->parse($stream);
        }
        $_ = $builder->build();
    }
    public function emptyStreamsProvider()
    {
        return [
            ['', false],
            ['', true],
        ];
    }
}
