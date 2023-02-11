<?php

namespace HusamAwadhi\PowerParser\Blueprint\ValueObject;

class Field
{
    public function __construct(
        public readonly string $name,
        public readonly int $position,
    ) {
    }

    public static function from(string $name, int $position): self
    {
        return new self(
            name: $name,
            position: $position,
        );
    }
}
