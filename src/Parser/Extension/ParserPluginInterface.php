<?php

namespace HusamAwadhi\PowerParser\Parser\Extension;

use HusamAwadhi\PowerParser\Blueprint\Blueprint;

interface ParserPluginInterface
{
    /**
     * return array of plugin supported file extensions.
     */
    public function getSupportedExtensions(): array;

    /**
     * load file content and perform the data extraction.
     */
    public function parse(string $fileContent, Blueprint $blueprint): self;

    /**
     * filter loaded content and return as array.
     */
    public function getFiltered(): array;
}
