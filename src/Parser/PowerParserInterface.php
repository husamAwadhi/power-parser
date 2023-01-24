<?php

namespace HusamAwadhi\PowerParser\Parser;

use HusamAwadhi\PowerParser\Blueprint\Blueprint;

interface PowerParserInterface
{
    /**
     * get PowerParser parser builder.
     */
    public function getParserBuilder(int $maxFileLength = 15_000): ParserBuilder;

    /**
     * create PowerParser blueprint object.
     */
    public function createBlueprint(string $stream, bool $isPath = false): Blueprint;
}
