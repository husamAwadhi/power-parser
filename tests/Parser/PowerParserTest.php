<?php

declare(strict_types=1);

namespace HusamAwadhi\PowerParserTests\Parser;

use HusamAwadhi\PowerParser\Blueprint\Blueprint;
use HusamAwadhi\PowerParser\Blueprint\BlueprintBuilder;
use HusamAwadhi\PowerParser\Blueprint\BlueprintHelper;
use HusamAwadhi\PowerParser\Parser\ParserBuilder;
use HusamAwadhi\PowerParser\Parser\PowerParser;
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

    public function testSuccessfullyCreateParserBuilder()
    {
        $builder = $this->powerParser->getParserBuilder();

        $this->assertInstanceOf(ParserBuilder::class, $builder);
    }

    public function testSuccessfullyCreateBlueprint()
    {
        $blueprint = $this->powerParser->createBlueprint(
            $this->blueprintsDirectory . 'valid.yaml',
            new BlueprintBuilder(new BlueprintHelper()),
            true
        );

        $this->assertInstanceOf(Blueprint::class, $blueprint);
    }
}
