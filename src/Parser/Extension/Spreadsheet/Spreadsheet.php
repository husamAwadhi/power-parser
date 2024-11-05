<?php

namespace HusamAwadhi\PowerParser\Parser\Extension\Spreadsheet;

use HusamAwadhi\PowerParser\Blueprint\Blueprint;
use HusamAwadhi\PowerParser\Parser\Extension\BlueprintInterpreter;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet as PhpSpreadsheet;

/**
 * abstract layer for phpoffice/phpspreadsheet
 * refer to docs. https://phpspreadsheet.readthedocs.io/en/latest/.
 */
class Spreadsheet extends BlueprintInterpreter
{
    protected PhpSpreadsheet $spreadsheet;

    protected Blueprint $blueprint;

    // reference: https://phpspreadsheet.readthedocs.io/en/latest/topics/file-formats/
    protected array $supportedExtensions = [
        'ods', // Open Document Format/OASIS
        'xlsx', // Office Open XML Excel 2007 and above
        'xls', // BIFF 8 Excel 95,97 and above
        'xml', // SpreadsheetML Excel 2003
        'html',
        'sylk',
        'csv',
    ];

    public function __construct(
        public readonly int $maxFileLength = 15000
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getSupportedExtensions(): array
    {
        return $this->supportedExtensions;
    }

    /**
     * @inheritDoc
     */
    public function parse(string $path, Blueprint $blueprint): self
    {
        $this->blueprint = $blueprint;
        $reader = IOFactory::createReader(ucfirst($blueprint->extension));
        $reader->setReadDataOnly(true);
        $reader->setReadFilter(new ReadFilter($blueprint));

        $this->spreadsheet = $reader->load($path);

        $sheets = $this->spreadsheet->getAllSheets();
        $this->data = [];
        foreach ($sheets as $sheet) {
            $this->data[] = [
                'title' => $sheet->getTitle(),
                'content' => $sheet->toArray(),
            ];
        }

        return $this;
    }
}
