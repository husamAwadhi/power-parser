<?php

declare(strict_types=1);

namespace HusamAwadhi\PowerParserTests\Parser;

use HusamAwadhi\PowerParser\Blueprint\Blueprint;
use HusamAwadhi\PowerParser\Blueprint\BlueprintBuilder;
use HusamAwadhi\PowerParser\Blueprint\BlueprintHelper;
use HusamAwadhi\PowerParser\Blueprint\BlueprintInterface;
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

            public function parse(string $s, BlueprintInterface $b): self
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

            public function parse(string $s, BlueprintInterface $b): self
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

    /**
     * @dataProvider parsedContentDataProvider
     */
    public function testGettingParsedContentAsObject($expectedArray)
    {
        $parser = new Parser(
            $this->blueprint,
            \file_get_contents($this->excelFile),
            [Spreadsheet::class => new Spreadsheet()]
        );

        $parser->parse();

        $this->assertEquals(
            json_decode(json_encode($expectedArray)),
            $parser->getAsObject()
        );
    }

    /**
     * @dataProvider parsedContentDataProvider
     */
    public function testGettingParsedContentAsArray($expectedArray)
    {
        $parser = new Parser(
            $this->blueprint,
            \file_get_contents($this->excelFile),
            [Spreadsheet::class => new Spreadsheet()]
        );
        $parser->parse();

        $this->assertEquals($expectedArray, $parser->getAsArray());
    }

    /**
     * @dataProvider parsedContentDataProvider
     */
    public function testGettingParsedContentAsJson($expectedArray)
    {
        $parser = new Parser(
            $this->blueprint,
            \file_get_contents($this->excelFile),
            [Spreadsheet::class => new Spreadsheet()]
        );

        $parser->parse();

        $this->assertJsonStringEqualsJsonString(
            json_encode($expectedArray),
            json_encode($parser)
        );
    }

    public function parsedContentDataProvider(): array
    {
        return [
            [[
                "header_info" => ["currency" => "SR", "cashier" => 1],
                "balance_info" => ["opening_balance" => 9152.251285],
                "transaction_table" => [
                    [
                        "date" => "21/09/2022",
                        "type" => "Journal Entry", "document_number" => 30,
                        "description" => "Cash purchase invoices",
                        "reference_number" => null,
                        "credit" => 23380.63,
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Payment Voucher",
                        "document_number" => 331,
                        "description" => null,
                        "reference_number" => null,
                        "credit" => null,
                        "debit" => 580,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Payment Voucher",
                        "document_number" => 332,
                        "description" => null,
                        "reference_number" => null,
                        "credit" => null,
                        "debit" => 980,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Payment Voucher",
                        "document_number" => 333,
                        "description" => null,
                        "reference_number" => null,
                        "credit" => null,
                        "debit" => 170,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => 3627,
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => 639.997,
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => 3628,
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => 45.011,
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => 3629,
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => 460,
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => 3630,
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => 28.014,
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => 3631,
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => 227.0035,
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => 3632,
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => 28.014,
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => 3633,
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => 280.002,
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => 3634,
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => 104.9985,
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => 3635,
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => 220,
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => 3636,
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => 140,
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => 3637,
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => 708.009,
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => 3638,
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => 1360.013,
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => 3639,
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => 152.076,
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => 3640,
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => 90.022,
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => 3641,
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => 460,
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => 3642,
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => 180.09,
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => 3643,
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => 432.331,
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Return",
                        "document_number" => 248,
                        "description" => "Return for Invoice No. 3625",
                        "reference_number" => null,
                        "credit" => null,
                        "debit" => 269.9985,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Return",
                        "document_number" => 249,
                        "description" => "Return",
                        "reference_number" => null,
                        "credit" => null,
                        "debit" => 100.004,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Return",
                        "document_number" => 250,
                        "description" => "Return for Invoice No. 3599",
                        "reference_number" => null,
                        "credit" => null,
                        "debit" => 992.0015,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Return",
                        "document_number" => 251,
                        "description" => "Return for Invoice No. 3631",
                        "reference_number" => null,
                        "credit" => null,
                        "debit" => 111.996,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Purchase",
                        "document_number" => 127,
                        "description" => null,
                        "reference_number" => null,
                        "credit" => null,
                        "debit" => 1030.63,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Purchase",
                        "document_number" => 128,
                        "description" => null,
                        "reference_number" => null,
                        "credit" => null,
                        "debit" => 22350,
                    ]
                ],
                "total" => ["total_credit" => 28936.211, "total_debit" => 26584.63]
            ]]
        ];
    }
}
