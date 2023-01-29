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

    public function readCell($columnAddress, $row, $worksheetName = '')
    {
        return $row === 3;
    }
}
