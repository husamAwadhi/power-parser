<?php

namespace HusamAwadhi\PowerParser\Blueprint;

interface BlueprintInterface
{
    public const MISSING_SECTION = 'Blueprint is missing %s section';
    public const MISSING_ELEMENT = 'Blueprint section %s is missing a mandatory element %s';
    public const CANNOT_PARSE = 'Failed to parse yaml file';
}
