<?php

namespace HusamAwadhi\PowerParser\Parser\Extension\Spreadsheet;

use HusamAwadhi\PowerParser\Blueprint\Blueprint;
use HusamAwadhi\PowerParser\Blueprint\ValueObject\Component;
use HusamAwadhi\PowerParser\Exception\InvalidArgumentException;
use HusamAwadhi\PowerParser\Parser\Extension\BlueprintInterpreter;
use HusamAwadhi\PowerParser\Parser\Utils\IOCapable;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet as PhpSpreadsheet;

/**
 * abstract layer for phpoffice/phpspreadsheet
 * refer to docs. https://phpspreadsheet.readthedocs.io/en/latest/.
 */
class Spreadsheet extends BlueprintInterpreter
{
    use IOCapable;

    protected PhpSpreadsheet $spreadsheet;

    protected Blueprint $blueprint;

    protected array $data;

    protected array $filtered;

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
    public function parse(string $fileContent, Blueprint $blueprint): self
    {
        $this->blueprint = $blueprint;
        $reader = IOFactory::createReader(ucfirst($blueprint->extension));
        $reader->setReadDataOnly(true);
        $reader->setReadFilter(new ReadFilter($blueprint));
        $this->spreadsheet = $reader->load($this->writeTemporaryFile(content: $fileContent));

        $sheets = $this->spreadsheet->getAllSheets();
        $this->data = [];
        foreach ($sheets as $sheet) {
            $this->data[] = [
                'title' => $sheet->getTitle(),
                'content' => $sheet->toArray(),
                // 'content' => array_filter(
                //     $sheet->toArray(),
                //     fn ($value) => \array_unique($value) !== [null]
                // ),
            ];
        }

        $this->deleteTemporaryFile();

        return $this;
    }

    public function filterSheets(): self
    {
        if (!isset($this->data)) {
            throw new InvalidArgumentException('content is not parsed yet.');
        }

        $this->filtered = [];

        foreach ($this->data as $sheet) {
            $index = 0;
            $content = $sheet['content'];

            /** @var Component */
            foreach ($this->blueprint->components as $component) {
                for ($index; $index < count($content); ++$index) {
                    if ($this->isMatch($component, $content[$index], $index)) {
                        $this->filtered[$component->name] = (
                            $component->table
                            ? $this->getTable($component, $content, $index)
                            : $this->getFields($component, $content[$index])
                        );

                        break;
                    }
                }
            }
        }

        return $this;
    }

    public function getFiltered(): array
    {
        return match (isset($this->filtered)) {
            true => $this->filtered,
            false => $this->filterSheets()->filtered,
        };
    }
}
