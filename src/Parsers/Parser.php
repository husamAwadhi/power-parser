<?php

namespace HusamAwadhi\PowerParser\Parsers;

use HusamAwadhi\PowerParser\Blueprint\Blueprint;
use HusamAwadhi\PowerParser\Parsers\Extensions\ParserExtensionInterface;

class Parser implements ParserInterface
{
    protected array $extensions;

    public function __construct(
        protected Blueprint $blueprint,
        protected string $content
    ) {
    }

    public function addExtension(ParserExtensionInterface $ext): void
    {
        $this->extensions[$ext::class] = $ext;
    }

    public function getParser(): void
    {
        // code...
    }
}
