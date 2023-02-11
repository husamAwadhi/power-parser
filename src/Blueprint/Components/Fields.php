<?php

namespace HusamAwadhi\PowerParser\Blueprint\Components;

use HusamAwadhi\PowerParser\Blueprint\BlueprintHelper;
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

    public readonly array $fields;

    public function __construct(
        /** @var Field[] */
        array $fields,
        protected BlueprintHelper $helper,
    ) {
        $this->fields = $this->buildFields($fields);
        $this->position = 0;
    }

    protected function buildFields(array $fields): array
    {
        $objectFields = [];
        foreach ($fields as $field) {
            $objectFields[] = Field::from($field['name'], $field['position']);
        }

        return $objectFields;
    }

    public static function from(array $fields, BlueprintHelper $helper): self
    {
        self::validation($fields);

        return new self($fields, $helper);
    }

    /**
     * @throws InvalidFieldException
     */
    public static function validation(array &$fields): void
    {
        foreach ($fields as $field) {
            if (
                !array_key_exists('name', $field) ||
                strlen($field['name']) == 0
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
