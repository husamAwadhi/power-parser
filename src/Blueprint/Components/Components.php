<?php

namespace HusamAwadhi\PowerParser\Blueprint\Components;

use HusamAwadhi\PowerParser\Blueprint\ComponentInterface;
use HusamAwadhi\PowerParser\Blueprint\Exceptions\InvalidComponentException;
use HusamAwadhi\PowerParser\Blueprint\Type;
use ReturnTypeWillChange;

class Components implements \Iterator, ComponentInterface
{
    private const INVALID_TYPE = "Element %s must be an array, given %s";
    private const MISSING_ELEMENT = "Blueprint section %s is missing a mandatory element %s";
    private const INVALID_VALUE = "Blueprint %s element has invalid value (%s). Acceptable value(s) [%s]";

    private $position = 0;
    private readonly array $elements;

    protected function __construct(
        array $elements
    ) {
        $this->position = 0;
        $this->elements = $this->buildElements($elements);
    }

    protected function buildElements(array $elements = []): array
    {
        $components = [];
        foreach ($elements as $element) {
            $components[] = [
                'type' => Type::from($element['type']),
                'fields' => Fields::createFromParameters($element['fields']),
                'mandatory' => (bool) ($element['mandatory'] ?? false),
                'conditions' => ($element['type'] == Type::NEXT->value
                    ? []
                    : Conditions::createFromParameters($element['conditions'])),
            ];
        }

        return $components;
    }

    public static function createFromParameters(array $elements): self
    {
        self::validation($elements);
        return new self($elements);
    }

    /**
     * @throws InvalidComponentException
     */
    public static function validation(array &$elements): void
    {
        $i = 0;
        foreach ($elements as $element) {
            if (!is_array($element)) {
                throw new InvalidComponentException(\sprintf(self::INVALID_TYPE, "#$i", gettype($element)));
            }

            if (!isset($element['type'])) {
                throw new InvalidComponentException(\sprintf(self::MISSING_ELEMENT, "blueprint (#$i)", 'type'));
            }

            if (!Type::tryFrom($element['type'])) {
                throw new InvalidComponentException(
                    \sprintf(self::INVALID_VALUE, "type (#$i)", $element['type'], implode(',', array_column(Type::cases(), 'value')))
                );
            }

            if (!isset($element['fields'])) {
                throw new InvalidComponentException(\sprintf(self::MISSING_ELEMENT, "blueprint (#$i)", 'fields'));
            }

            $i++;
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
        return $this->elements[$this->position];
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
        return isset($this->elements[$this->position]);
    }
}
