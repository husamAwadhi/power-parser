<?php

declare(strict_types=1);

namespace HusamAwadhi\PowerParserTests\Parser;

use HusamAwadhi\PowerParser\Blueprint\Blueprint;
use HusamAwadhi\PowerParser\Blueprint\BlueprintBuilder;
use HusamAwadhi\PowerParser\Blueprint\BlueprintHelper;
use HusamAwadhi\PowerParser\Exception\InvalidArgumentException;
use HusamAwadhi\PowerParser\Exception\UnsupportedExtensionException;
use HusamAwadhi\PowerParser\Parser\Extension\ParserPluginInterface;
use HusamAwadhi\PowerParser\Parser\Extension\Spreadsheet\Spreadsheet;
use HusamAwadhi\PowerParser\Parser\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    protected string $blueprintsDirectory = STORAGE_DIRECTORY . '/blueprints/';
    protected string $excelFile = STORAGE_DIRECTORY . '/sample.xlsx';
    protected Blueprint $blueprint;
    protected ParserPluginInterface $dummyPlugin;
    protected ParserPluginInterface $dummyPlugin2;

    public function setUp(): void
    {
        $builder = new BlueprintBuilder(new BlueprintHelper());
        $builder->load($this->blueprintsDirectory . 'valid.yaml');
        $this->blueprint = $builder->build();

        $this->dummyPlugin = new class implements ParserPluginInterface
        {
            public function getSupportedExtensions(): array
            {
                return ['exe'];
            }

            public function parse(string $s, Blueprint $b): self
            {
                return $this;
            }

            public function getFiltered(): array
            {
                return [];
            }
        };

        $this->dummyPlugin2 = new class implements ParserPluginInterface
        {
            public function getSupportedExtensions(): array
            {
                return ['exe'];
            }

            public function parse(string $s, Blueprint $b): self
            {
                return $this;
            }

            public function getFiltered(): array
            {
                return [];
            }
        };
    }

    public function testParserCreatedSuccessfully()
    {
        $parser = new Parser(
            $this->blueprint,
            \file_get_contents($this->excelFile),
            [Spreadsheet::class => new Spreadsheet()]
        );

        $this->assertInstanceOf(Parser::class, $parser);
    }

    public function testListOfSupportedExtensions()
    {

        $parser = new Parser(
            $this->blueprint,
            \file_get_contents($this->excelFile),
            [
                Spreadsheet::class => new Spreadsheet(),
                $this->dummyPlugin::class => new $this->dummyPlugin()
            ]
        );

        $this->assertEquals(
            [],
            array_diff(
                ['ods', 'xlsx', 'xls', 'xml', 'html', 'sylk', 'csv', 'exe'],
                $parser->getSupportedExtensions()
            )
        );
    }

    public function testSuccessfullyGetRecommendedPlugin()
    {
        $builder = new BlueprintBuilder(new BlueprintHelper());
        $builder->load($this->blueprintsDirectory . 'valid_exe.yaml');
        $blueprint = $builder->build();

        $parser = new Parser(
            $blueprint,
            \file_get_contents($this->excelFile),
            [
                $this->dummyPlugin::class => new $this->dummyPlugin(),
                $this->dummyPlugin2::class => new $this->dummyPlugin2(),
            ]
        );

        $this->assertEquals($this->dummyPlugin::class, $parser->getRecommendedPluginName());
    }

    public function testSuccessfullyOverrideRecommendedPlugin()
    {
        $builder = new BlueprintBuilder(new BlueprintHelper());
        $builder->load($this->blueprintsDirectory . 'valid_exe.yaml');
        $blueprint = $builder->build();

        $parser = new Parser(
            $blueprint,
            \file_get_contents($this->excelFile),
            [
                $this->dummyPlugin::class => new $this->dummyPlugin(),
                $this->dummyPlugin2::class => new $this->dummyPlugin2(),
            ]
        );

        $this->assertEquals(
            $this->dummyPlugin2::class,
            $parser->getRecommendedPluginName($this->dummyPlugin2::class)
        );
    }

    public function testSuccessfullyIgnoreOverridingIfExtensionDoesNotSupport()
    {
        $parser = new Parser(
            $this->blueprint,
            \file_get_contents($this->excelFile),
            [
                Spreadsheet::class => new Spreadsheet(),
                $this->dummyPlugin::class => new $this->dummyPlugin()
            ]
        );

        $this->assertEquals(
            Spreadsheet::class,
            $parser->getRecommendedPluginName($this->dummyPlugin2::class)
        );
    }

    public function testThrowsExceptionOnUnsupportedExtension()
    {
        $this->expectException(UnsupportedExtensionException::class);

        $parser = new Parser(
            $this->blueprint,
            \file_get_contents($this->excelFile),
            [$this->dummyPlugin::class => new $this->dummyPlugin()]
        );

        $_ = $parser->getRecommendedPluginName();
    }

    public function testParseContent()
    {
        $parser = new Parser(
            $this->blueprint,
            \file_get_contents($this->excelFile),
            [Spreadsheet::class => new Spreadsheet()]
        );

        $parser->parse();

        $this->assertInstanceOf(ParserPluginInterface::class, $parser->getParsedContentPlugin());
    }

    public function testThrowsExceptionIfContentNotParsedYet()
    {
        $this->expectException(InvalidArgumentException::class);

        $parser = new Parser(
            $this->blueprint,
            \file_get_contents($this->excelFile),
            [Spreadsheet::class => new Spreadsheet()]
        );

        $this->assertInstanceOf(ParserPluginInterface::class, $parser->getParsedContentPlugin());
    }


    // public function testGettingParsedContentAsObject()
    // {
    //     $parser = new Parser(
    //         $this->blueprint,
    //         \file_get_contents($this->excelFile),
    //         [Spreadsheet::class => new Spreadsheet()]
    //     );

    //     $parser->parse();

    //     $this->assertEquals(new stdClass(), $parser->getAsObject());
    // }

    // public function testGettingParsedContentAsArray()
    // {
    //     $parser = new Parser(
    //         $this->blueprint,
    //         \file_get_contents($this->excelFile),
    //         [Spreadsheet::class => new Spreadsheet()]
    //     );

    //     $parser->parse();

    //     $this->assertEquals([], $parser->getAsArray());
    // }

    // public function testGettingParsedContentAsJson()
    // {
    //     $parser = new Parser(
    //         $this->blueprint,
    //         \file_get_contents($this->excelFile),
    //         [Spreadsheet::class => new Spreadsheet()]
    //     );

    //     $parser->parse();

    //     $this->assertEquals(json_encode([]), $parser->getAsJson());
    // }
}
