<?php

declare(strict_types=1);

namespace HusamAwadhi\PowerParserTests\Parsers;

use HusamAwadhi\PowerParser\Blueprint\Blueprint;
use HusamAwadhi\PowerParser\Exception\InvalidArgumentException;
use HusamAwadhi\PowerParser\Exception\InvalidExtensionException;
use HusamAwadhi\PowerParser\Exception\MissingElementException;
use HusamAwadhi\PowerParser\Parsers\Extensions\Excel;
use HusamAwadhi\PowerParser\Parsers\Parser;
use HusamAwadhi\PowerParser\Parsers\ParserBuilder;
use PHPUnit\Framework\TestCase;

class ParserBuilderTest extends TestCase
{
    protected string $blueprintsDirectory = STORAGE_DIRECTORY . '/blueprints/';
    protected string $excelFile = STORAGE_DIRECTORY . '/sample.xlsx';

    public function testParserCreatedSuccessfully()
    {
        $builder = new ParserBuilder();
        $blueprint = Blueprint::createBlueprint($this->blueprintsDirectory . 'valid.yaml', true);

        $parser = $builder->addBlueprint($blueprint)
            ->addFileFromPath($this->excelFile)
            ->registerExtension(new Excel())
            ->build();

        $this->assertInstanceOf(Parser::class, $parser);
    }

    public function testExceptionWhenNoBlueprint()
    {
        $this->expectException(MissingElementException::class);

        $builder = new ParserBuilder();

        $builder->addFileFromPath($this->excelFile)
            ->registerExtension(new Excel())
            ->build();
    }

    public function testExceptionWhenNoFile()
    {
        $this->expectException(MissingElementException::class);

        $builder = new ParserBuilder();
        $blueprint = Blueprint::createBlueprint($this->blueprintsDirectory . 'valid.yaml', true);

        $builder->addBlueprint($blueprint)
            ->registerExtension(new Excel())
            ->build();
    }

    public function testExceptionWhenFileNotFound()
    {
        $this->expectException(InvalidArgumentException::class);

        $builder = new ParserBuilder();

        $builder->addFileFromPath($this->excelFile . 'legit')
            ->registerExtension(new Excel())
            ->build();
    }

    public function testExceptionWhenRegisteringDuplicateExtension()
    {
        $this->expectException(InvalidExtensionException::class);

        $builder = new ParserBuilder();

        $builder->registerExtension(new Excel())
            ->registerExtension(new Excel())
            ->build();
    }
}
