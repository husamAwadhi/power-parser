<?php

namespace HusamAwadhi\PowerParser\Blueprint\Components;

use HusamAwadhi\PowerParser\Blueprint\ComponentInterface;
use HusamAwadhi\PowerParser\Blueprint\Exceptions\InvalidComponentException;
use HusamAwadhi\PowerParser\Blueprint\Exceptions\InvalidFieldException;
use ReturnTypeWillChange;

class Conditions implements \Iterator, ComponentInterface
{

    public function __construct(
        public readonly array $conditions
    ) {
        $this->position = 0;
    }

    /**
     * Entrypoint function
     *
     * @param array $elements
     * @return self
     * @throws InvalidComponentException
     */
    public static function createFromParameters(array $conditions): self
    {
        self::validation($conditions);
        return new self($conditions);
    }

    /**
     * @throws InvalidComponentException
     */
    public static function validation(array $conditions): void
    {
        foreach ($conditions as $condition) {
            if (!isset($condition['column']) || empty($condition['column']) || !is_array($condition['column'])) {
                throw new InvalidFieldException('missing column');
            }

            // if (!isset($condition['type'])) {
            //     throw new InvalidComponentException(\sprintf(self::MISSING_ELEMENT, "blueprint (#$i)", 'type'));
            // }

            // if (!ConditionKeyword::tryFrom($condition['type'])) {
            //     throw new InvalidComponentException(
            //         \sprintf(self::INVALID_VALUE, "type (#$i)", $condition['type'], implode(',', array_column(Type::cases(), 'value')))
            //     );
            // }
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

    public function rewind(): void
    {
        $this->position = 0;
    }

    #[ReturnTypeWillChange]
    public function current()
    {
        return $this->fields[$this->position];
    }

    #[ReturnTypeWillChange]
    public function key()
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function valid(): bool
    {
        return isset($this->fields[$this->position]);
    }
}
