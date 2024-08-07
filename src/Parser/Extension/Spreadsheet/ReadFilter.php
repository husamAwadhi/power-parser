<?php

namespace HusamAwadhi\PowerParser\Parser\Extension\Spreadsheet;

use HusamAwadhi\PowerParser\Blueprint\Blueprint;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class ReadFilter implements IReadFilter
{
    public function __construct(
        public readonly Blueprint $blueprint
    ) {
    }

    public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool
    {
        //TODO: find a useful way of pre filtering unwanted rows.
        return true;
    }
}
