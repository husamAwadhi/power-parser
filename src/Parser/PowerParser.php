<?php

namespace HusamAwadhi\PowerParser\Parser;

use HusamAwadhi\PowerParser\Blueprint\Blueprint;
use HusamAwadhi\PowerParser\Blueprint\BlueprintBuilder;

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
    public function createBlueprint(string $stream, BlueprintBuilder $builder, bool $isPath = false): Blueprint
    {
        if ($isPath) {
            $builder->load($stream);
        } else {
            $builder->parse($stream);
        }

        return $builder->build();
    }
}
