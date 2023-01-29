<?php

namespace HusamAwadhi\PowerParser\Parser;

use stdClass;

interface ParserInterface
{
    /**
     * parse file using Blueprint.
     *
     * @param string $recommendedExtension allows for overriding
     *  extension used in parsing if it supports the blueprint
     *  extension else the default extension will be used
     */
    public function parse(string $recommendedExtension = ''): self;

    /**
     * return parsed file as stdClass object.
     */
    public function getAsObject(): stdClass;

    /**
     * return parsed file as associative array.
     */
    public function getAsArray(): array;

    /**
     * return parsed file as json string.
     */
    public function getAsJson(): ?string;
}
