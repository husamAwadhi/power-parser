<?php

namespace HusamAwadhi\PowerParser\Blueprint\ValueObject;

use HusamAwadhi\PowerParser\Blueprint\FieldType;

class Field
{
    public function __construct(
        public readonly string $name,
        public readonly int $position,
        public readonly ?FieldType $type,
        public readonly ?FieldFormat $format,
    ) {
    }

    public static function from(string $name, int $position, ?FieldType $type = null, ?FieldFormat $format = null): self
    {
        return new self(
            name: $name,
            position: $position,
            type: $type,
            format: $format,
        );
    }
}
