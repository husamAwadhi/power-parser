<?php

namespace HusamAwadhi\PowerParser;

use HusamAwadhi\PowerParser\Blueprint\Blueprint;
use HusamAwadhi\PowerParser\Blueprint\BlueprintBuilder;
use HusamAwadhi\PowerParser\Blueprint\BlueprintHelper;
use HusamAwadhi\PowerParser\Parser\ParserBuilder;

interface PowerParserInterface
{
    /**
     * get PowerParser parser builder. it adds default extension
     * and load blueprint in the process.
     */
    public static function getParserBuilder(
        string $stream,
        string $file,
        int $maxFileLength = 15_000,
        ?BlueprintBuilder $blueprintBuilder = null,
        ?BlueprintHelper $blueprintHelper = null,
    ): ParserBuilder;

    /**
     * create PowerParser blueprint object.
     */
    public static function createBlueprint(string $stream, BlueprintBuilder $builder): Blueprint;
}
