<?php

namespace HusamAwadhi\PowerParser;

use HusamAwadhi\PowerParser\Blueprint\Blueprint;
use HusamAwadhi\PowerParser\Blueprint\BlueprintBuilder;
use HusamAwadhi\PowerParser\Parser\ParserBuilder;

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
