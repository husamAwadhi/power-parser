<?php

namespace HusamAwadhi\PowerParser\Parser;

use HusamAwadhi\PowerParser\Blueprint\Blueprint;
use HusamAwadhi\PowerParser\Blueprint\BlueprintBuilder;

interface PowerParserInterface
{
    /**
     * get PowerParser parser builder.
     */
    public function getParserBuilder(int $maxFileLength = 15_000): ParserBuilder;

    /**
     * create PowerParser blueprint object.
     */
    public function createBlueprint(string $stream, BlueprintBuilder $builder, bool $isPath = false): Blueprint;
}
