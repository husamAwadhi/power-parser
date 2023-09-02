<?php

declare(strict_types=1);

namespace HusamAwadhi\PowerParserTests;

use HusamAwadhi\PowerParser\Blueprint\BlueprintBuilder;
use HusamAwadhi\PowerParser\Blueprint\BlueprintHelper;
use HusamAwadhi\PowerParser\Blueprint\BlueprintInterface;
use HusamAwadhi\PowerParser\Parser\ParserBuilder;
use HusamAwadhi\PowerParser\PowerParser;
use PHPUnit\Framework\TestCase;

class PowerParserTest extends TestCase
{
    protected string $blueprintsDirectory = STORAGE_DIRECTORY . '/blueprints/';
    protected string $excelFile = STORAGE_DIRECTORY . '/sample.xlsx';
    protected PowerParser $powerParser;

    protected function setUp(): void
    {
        $this->powerParser =  new PowerParser();
    }

    public function testSuccessfullyCreateParserBuilder(): void
    {
        $builder = $this->powerParser->getParserBuilder(
            $this->blueprintsDirectory . 'valid.yaml',
            $this->excelFile
        );

        $this->assertInstanceOf(ParserBuilder::class, $builder);
    }

    public function testSuccessfullyCreateBlueprint(): void
    {
        $blueprint = $this->powerParser->createBlueprint(
            $this->blueprintsDirectory . 'valid.yaml',
            new BlueprintBuilder(new BlueprintHelper())
        );

        $this->assertInstanceOf(BlueprintInterface::class, $blueprint);
    }
}
