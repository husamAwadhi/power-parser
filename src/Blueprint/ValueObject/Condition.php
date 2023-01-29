<?php

namespace HusamAwadhi\PowerParser\Blueprint\ValueObject;

use HusamAwadhi\PowerParser\Blueprint\Components\ConditionKeyword;

class Condition
{
    public function __construct(
        public readonly array $column,
        public readonly ConditionKeyword $keyword,
        public readonly string $value,
    ) {
    }

    public static function from(array $column, ConditionKeyword $keyword, string $value): self
    {
        return new self(
            column: $column,
            keyword: $keyword,
            value: $value,
        );
    }
}
