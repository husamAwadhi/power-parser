<?php

namespace HusamAwadhi\PowerParser\Blueprint\ValueObject;

use HusamAwadhi\PowerParser\Blueprint\FieldFormat as FieldFormatEnum;

class FieldFormat
{
    public function __construct(
        public readonly FieldFormatEnum $type,
        public readonly int $argument,
    ) {
    }

    public static function from(FieldFormatEnum $type, int $argument): self
    {
        return new self(
            type: $type,
            argument: $argument,
        );
    }
}
