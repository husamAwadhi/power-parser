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
    protected string $csvFile = STORAGE_DIRECTORY . '/sample.csv';
    protected Blueprint $blueprint;
    protected $dummyPlugin;
    protected $dummyPlugin2;

    public function setUp(): void
    {
        $builder = new BlueprintBuilder(new BlueprintHelper());
        $builder->load($this->blueprintsDirectory . 'valid.yaml');
        $this->blueprint = $builder->build();

        $this->dummyPlugin = $this->createStub(Spreadsheet::class);
        $this->dummyPlugin->method('getSupportedExtensions')
            ->willReturn(['exe']);

        $this->dummyPlugin2 = $this->createStub(Spreadsheet::class);
        $this->dummyPlugin2->method('getSupportedExtensions')
            ->willReturn(['exe']);
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
                $this->dummyPlugin::class => $this->dummyPlugin
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
                $this->dummyPlugin::class => $this->dummyPlugin,
                $this->dummyPlugin2::class => $this->dummyPlugin2,
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
                $this->dummyPlugin::class => $this->dummyPlugin,
                $this->dummyPlugin2::class => $this->dummyPlugin2,
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
                $this->dummyPlugin::class => $this->dummyPlugin
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
            [$this->dummyPlugin::class => $this->dummyPlugin]
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

        $parser->getParsedContentPlugin();
    }

    /**
     * @dataProvider parsedCSVContentDataProvider
     */
    public function testGettingParsedCSVContentAsJson($expectedArray)
    {
        $builder = new BlueprintBuilder(new BlueprintHelper());
        $builder->load($this->blueprintsDirectory . 'valid_csv.yaml');
        $blueprint = $builder->build();

        $parser = new Parser(
            $blueprint,
            \file_get_contents($this->csvFile),
            [Spreadsheet::class => new Spreadsheet()]
        );

        $parser->parse();

        $this->assertJsonStringEqualsJsonString(
            json_encode($expectedArray),
            json_encode($parser)
        );
    }
    public function parsedCSVContentDataProvider(): array
    {
        return [[[
            "header" => [
                "number",
                "first_name",
                "last_name",
                "email",
                "profession",
                "age",
                "date"
            ],
            "info" => [
                [
                    "12000",
                    "Grier",
                    "Arvo",
                    "Grier.Arvo@email.com",
                    "developer",
                    "31",
                    "2022-12-17"
                ],
                [
                    "12001",
                    "Tierney",
                    "Tremayne",
                    "Tierney.Tremayne@email.com",
                    "worker",
                    "51",
                    "2022-08-28"
                ],
                [
                    "12002",
                    "Rori",
                    "Virgin",
                    "Rori.Virgin@email.com",
                    "police officer",
                    "18",
                    "2022-07-11"
                ],
                [
                    "12003",
                    "Doralynne",
                    "Tiffa",
                    "Doralynne.Tiffa@email.com",
                    "doctor",
                    "50",
                    "2023-01-25"
                ],
                [
                    "12004",
                    "Ardenia",
                    "O'Rourke",
                    "Ardenia.O'Rourke@email.com",
                    "doctor",
                    "36",
                    "2022-09-07"
                ],
                [
                    "12005",
                    "Dale",
                    "Francyne",
                    "Dale.Francyne@email.com",
                    "firefighter",
                    "54",
                    "2022-02-12"
                ],
                [
                    "12006",
                    "Kittie",
                    "Yorick",
                    "Kittie.Yorick@email.com",
                    "doctor",
                    "58",
                    "2022-03-27"
                ],
                [
                    "12007",
                    "Layla",
                    "Krystle",
                    "Layla.Krystle@email.com",
                    "doctor",
                    "45",
                    "2022-09-27"
                ],
                [
                    "12008",
                    "Rosene",
                    "Donell",
                    "Rosene.Donell@email.com",
                    "doctor",
                    "40",
                    "2022-12-24"
                ],
                [
                    "12009",
                    "Jessamyn",
                    "McCutcheon",
                    "Jessamyn.McCutcheon@email.com",
                    "developer",
                    "21",
                    "2022-11-23"
                ]
            ]
        ]]];
    }

    /**
     * @dataProvider parsedExcelContentDataProvider
     */
    public function testGettingParsedExcelContentAsObject($expectedArray)
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
     * @dataProvider parsedExcelContentDataProvider
     */
    public function testGettingParsedExcelContentAsArray($expectedArray)
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
     * @dataProvider parsedExcelContentDataProvider
     */
    public function testGettingParsedExcelContentAsJson($expectedArray)
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

    public function parsedExcelContentDataProvider(): array
    {
        return [
            [[
                "header_info" => ["currency" => "SR", "cashier" => "1"],
                "balance_info" => ["opening_balance" => "9152.251285"],
                "transaction_table" => [
                    [
                        "date" => "21/09/2022",
                        "type" => "Journal Entry", "document_number" => "30",
                        "description" => "Cash purchase invoices",
                        "reference_number" => null,
                        "credit" => "23380.63",
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Payment Voucher",
                        "document_number" => "331",
                        "description" => null,
                        "reference_number" => null,
                        "credit" => null,
                        "debit" => "580",
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Payment Voucher",
                        "document_number" => "332",
                        "description" => null,
                        "reference_number" => null,
                        "credit" => null,
                        "debit" => "980",
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Payment Voucher",
                        "document_number" => "333",
                        "description" => null,
                        "reference_number" => null,
                        "credit" => null,
                        "debit" => "170",
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => "3627",
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => "639.997",
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => "3628",
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => "45.011",
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => "3629",
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => "460",
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => "3630",
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => "28.014",
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => "3631",
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => "227.0035",
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => "3632",
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => "28.014",
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => "3633",
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => "280.002",
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => "3634",
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => "104.9985",
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => "3635",
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => "220",
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => "3636",
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => "140",
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => "3637",
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => "708.009",
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => "3638",
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => "1360.013",
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => "3639",
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => "152.076",
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => "3640",
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => "90.022",
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => "3641",
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => "460",
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => "3642",
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => "180.09",
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Invoice",
                        "document_number" => "3643",
                        "description" => "Sales",
                        "reference_number" => null,
                        "credit" => "432.331",
                        "debit" => null,
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Return",
                        "document_number" => "248",
                        "description" => "Return for Invoice No. 3625",
                        "reference_number" => null,
                        "credit" => null,
                        "debit" => "269.9985",
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Return",
                        "document_number" => "249",
                        "description" => "Return",
                        "reference_number" => null,
                        "credit" => null,
                        "debit" => "100.004",
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Return",
                        "document_number" => "250",
                        "description" => "Return for Invoice No. 3599",
                        "reference_number" => null,
                        "credit" => null,
                        "debit" =>"992.0015",
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Return",
                        "document_number" => "251",
                        "description" => "Return for Invoice No. 3631",
                        "reference_number" => null,
                        "credit" => null,
                        "debit" => "111.996",
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Purchase",
                        "document_number" => "127",
                        "description" => null,
                        "reference_number" => null,
                        "credit" => null,
                        "debit" => "1030.63",
                    ], [
                        "date" => "21/09/2022",
                        "type" => "Purchase",
                        "document_number" => "128",
                        "description" => null,
                        "reference_number" => null,
                        "credit" => null,
                        "debit" => "22350",
                    ]
                ],
                "total" => ["total_credit" => "28936.211", "total_debit" => "26584.63"]
            ]]
        ];
    }

    public function testThrowsExceptionOnMissingMandatoryComponent()
    {
        $this->expectException(InvalidArgumentException::class);

        $builder = new BlueprintBuilder(new BlueprintHelper());
        $builder->load($this->blueprintsDirectory . 'missing_mandatory_csv.yaml');
        $blueprint = $builder->build();

        $parser = new Parser(
            $blueprint,
            \file_get_contents($this->csvFile),
            [Spreadsheet::class => new Spreadsheet()]
        );

        $parser->parse()->getAsArray();
    }
}
