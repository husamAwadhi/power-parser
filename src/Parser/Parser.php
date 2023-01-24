<?php

namespace HusamAwadhi\PowerParser\Parser;

use HusamAwadhi\PowerParser\Blueprint\Blueprint;

class Parser implements ParserInterface
{
    public function __construct(
        public readonly Blueprint $blueprint,
        public readonly string $content,
        public readonly array $extensions = []
    ) {
    }

    public function getParser(): void
    {
        // code...
    }
}
