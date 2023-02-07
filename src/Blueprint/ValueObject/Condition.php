<?php

namespace HusamAwadhi\PowerParser\Blueprint\ValueObject;

use HusamAwadhi\PowerParser\Blueprint\Components\ConditionKeyword;

class Condition
{
    public readonly mixed $value;

    public function __construct(
        public readonly array $columns,
        public readonly ConditionKeyword $keyword,
        mixed $value,
    ) {
        $this->value = match ($value) {
            '{null}' => null,
            default => $value,
        };
    }

    public static function from(array $columns, ConditionKeyword $keyword, mixed $value): self
    {
        return new self(
            columns: $columns,
            keyword: $keyword,
            value: $value,
        );
    }
}
