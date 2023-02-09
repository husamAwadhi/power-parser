<?php

namespace HusamAwadhi\PowerParser\Blueprint\ValueObject;

use HusamAwadhi\PowerParser\Blueprint\Components\Conditions;
use HusamAwadhi\PowerParser\Blueprint\Components\Fields;
use HusamAwadhi\PowerParser\Blueprint\Type;

class Component
{
    public function __construct(
        public readonly Type $type,
        public readonly Fields $fields,
        public readonly bool $mandatory,
        public readonly bool $table,
        public readonly string $name,
        public readonly ?Conditions $conditions = null,
    ) {
    }

    public static function from(array $component): self
    {
        return new self(
            type: Type::from($component['type']),
            fields: $component['fields'],
            mandatory: (bool) ($component['mandatory'] ?? false),
            table: (bool) ($component['table'] ?? false),
            conditions: ($component['conditions'] ?? null),
            name: $component['name']
        );
    }
}
