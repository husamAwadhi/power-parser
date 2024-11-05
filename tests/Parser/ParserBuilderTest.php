<?php

declare(strict_types=1);

namespace HusamAwadhi\PowerParserTests\Parser;

use HusamAwadhi\PowerParser\Blueprint\BlueprintBuilder;
use HusamAwadhi\PowerParser\Blueprint\BlueprintHelper;
use HusamAwadhi\PowerParser\Exception\InvalidArgumentException;
use HusamAwadhi\PowerParser\Exception\InvalidPLuginException;
use HusamAwadhi\PowerParser\Exception\MissingElementException;
use HusamAwadhi\PowerParser\Parser\Extension\Spreadsheet\Spreadsheet;
use HusamAwadhi\PowerParser\Parser\Parser;
use HusamAwadhi\PowerParser\Parser\ParserBuilder;
use PHPUnit\Framework\TestCase;

class ParserBuilderTest extends TestCase
{
    protected string $blueprintsDirectory = STORAGE_DIRECTORY . '/blueprints/';
    protected string $excelFile = STORAGE_DIRECTORY . '/sample.xlsx';

    public function testParserCreatedSuccessfully(): void
    {
        $builder = new ParserBuilder();
        $blueprintBuilder = new BlueprintBuilder(new BlueprintHelper());
        $blueprintBuilder->load($this->blueprintsDirectory . 'valid.yaml');
        $blueprint = $blueprintBuilder->build();

        $parser = $builder->addBlueprint($blueprint)
            ->addFile($this->excelFile)
            ->registerExtension(new Spreadsheet())
            ->build();

        $this->assertInstanceOf(Parser::class, $parser);
    }

    public function testExceptionWhenNoBlueprint(): void
    {
        $this->expectException(MissingElementException::class);

        $builder = new ParserBuilder();

        $builder->addFile($this->excelFile)
            ->registerExtension(new Spreadsheet())
            ->build();
    }

    public function testExceptionWhenNoFile(): void
    {
        $this->expectException(MissingElementException::class);

        $builder = new ParserBuilder();
        $blueprintBuilder = new BlueprintBuilder(new BlueprintHelper());
        $blueprintBuilder->load($this->blueprintsDirectory . 'valid.yaml');
        $blueprint = $blueprintBuilder->build();

        $builder->addBlueprint($blueprint)
            ->registerExtension(new Spreadsheet())
            ->build();
    }

    public function testExceptionWhenFileNotFound(): void
    {
        $this->expectException(MissingElementException::class);

        $builder = new ParserBuilder();
        $blueprintBuilder = new BlueprintBuilder(new BlueprintHelper());
        $blueprintBuilder->load($this->blueprintsDirectory . 'valid.yaml');
        $blueprint = $blueprintBuilder->build();

        $builder->addBlueprint($blueprint)
            ->addFile($this->excelFile . 'legit')
            ->registerExtension(new Spreadsheet())
            ->build();
    }

    public function testExceptionWhenRegisteringDuplicateExtension(): void
    {
        $this->expectException(InvalidPLuginException::class);

        $builder = new ParserBuilder();

        $builder->registerExtension(new Spreadsheet())
            ->registerExtension(new Spreadsheet())
            ->build();
    }
}
