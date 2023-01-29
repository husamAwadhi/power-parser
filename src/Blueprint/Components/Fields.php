<?php

namespace HusamAwadhi\PowerParser\Blueprint\Components;

use HusamAwadhi\PowerParser\Blueprint\ComponentInterface;
use HusamAwadhi\PowerParser\Blueprint\ValueObject\Field;
use HusamAwadhi\PowerParser\Dictionary;
use HusamAwadhi\PowerParser\Exception\InvalidFieldException;
use Iterator;
use ReturnTypeWillChange;

class Fields implements ComponentInterface, Iterator
{
    protected Dictionary $dict;

    private int $position = 0;

    public function __construct(
        /** @var Field[] */
        public readonly array $fields,
    ) {
        $this->position = 0;
    }

    public static function createFromParameters(array $fields): self
    {
        self::validation($fields);

        $objectFields = [];
        foreach ($fields as $field) {
            $objectFields[] = Field::from($field['name'], $field['position']);
        }

        return new self($objectFields);
    }

    /**
     * @throws InvalidFieldException
     */
    public static function validation(array &$fields): void
    {
        foreach ($fields as $field) {
            if (
                !isset($field['name']) ||
                empty($field['name']) ||
                !is_string($field['name'])
            ) {
                throw new InvalidFieldException('missing or invalid name');
            }
            if (
                !isset($field['position']) ||
                empty($field['position']) ||
                !is_int($field['position'])
            ) {
                throw new InvalidFieldException('missing or invalid position');
            }
        }
    }

    public static function getMandatoryElements(): array
    {
        return [];
    }

    public static function getOptionalElements(): array
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
