<?php

namespace HusamAwadhi\PowerParser\Parser;

use HusamAwadhi\PowerParser\Blueprint\Blueprint;

class PowerParser implements PowerParserInterface
{
    /**
     * @inheritDoc
     */
    public function getParserBuilder(int $maxFileLength = 15_000): ParserBuilder
    {
        return new ParserBuilder(maxFileLength: 15_000);
    }

    /**
     * @inheritDoc
     */
    public function createBlueprint(string $stream, bool $isPath = false): Blueprint
    {
        return Blueprint::createBlueprint(stream: $stream, isPath: $isPath);
    }
}
