<?php

namespace HusamAwadhi\PowerParser\Blueprint\Components;

use HusamAwadhi\PowerParser\Blueprint\ComponentInterface;

class Condition implements ComponentInterface
{

    public function __construct(
        public readonly array $columns,
        public readonly ConditionKeyword $keyword
    ) {
    }

    /**
     * Entrypoint function
     *
     * @param array $elements
     * @return self
     * @throws InvalidComponentException
     */
    public static function createFromParameters(array $element): self
    {
        self::validation($element);
        return new self($element['columns'], $element['keyword']);
    }

    /**
     * @throws InvalidComponentException
     */
    public static function validation(array $element): void
    {
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
