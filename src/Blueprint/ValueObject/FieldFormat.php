<?php

namespace HusamAwadhi\PowerParser\Blueprint\ValueObject;

use HusamAwadhi\PowerParser\Blueprint\FieldFormat as FieldFormatEnum;

class FieldFormat
{
    public function __construct(
        public readonly FieldFormatEnum $type,
        public readonly string $argument,
    ) {
    }

    public static function from(FieldFormatEnum $type, string $argument): self
    {
        return new self(
            type: $type,
            argument: $argument,
        );
    }
}
