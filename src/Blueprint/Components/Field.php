<?php

namespace HusamAwadhi\PowerParser\Blueprint\Components;

use HusamAwadhi\PowerParser\Blueprint\ComponentInterface;
use HusamAwadhi\PowerParser\Blueprint\Exceptions\InvalidFieldException;

class Field implements ComponentInterface
{

    public function __construct(
        public readonly string $name,
        public readonly int $position
    ) {
    }

    public static function createFromParameters(array $field): self
    {
        self::validation($field);
        return new self($field['name'], $field['position']);
    }

    /**
     * @throws InvalidFieldException
     */
    public static function validation(array $field): void
    {
        if (!isset($element['name']) || empty($element['name'])) {
            throw new InvalidFieldException(\sprintf(self::MISSING_ELEMENT, "blueprint (#$i)", 'fields'));
        }
    }

    public static function getMandatoryElements()  : array
    {
        return [];
    }

    public static function getOptionalElements()  : array
    {
        return [];
    }
}
