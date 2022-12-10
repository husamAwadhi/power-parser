<?php

namespace HusamAwadhi\PowerParser\Blueprint\Components;

use HusamAwadhi\PowerParser\Blueprint\Exceptions\InvalidComponentException;
use HusamAwadhi\PowerParser\Blueprint\Type;
use Iterator;
use ReturnTypeWillChange;

use function PHPSTORM_META\type;

class Components implements Iterator
{
    private const INVALID_TYPE = "Element %s must be an array, %s found";
    private const MISSING_ELEMENT = "Blueprint section %s is missing a mandatory element %s";
    private const INVALID_VALUE = "Blueprint %s element has invalid value (%s). Acceptable value(s) [%s]";

    private $position = 0;
    private readonly array $elements;

    public function __construct($elements)
    {
        $this->position = 0;
        $this->elements = \json_decode(\json_encode($elements));
    }

    /**
     * Entrypoint function
     *
     * @param array $elements
     * @return self
     * @throws InvalidComponentException
     */
    public static function createFromArray(array $elements): self
    {
        self::isValid($elements);
        return new self($elements);
    }

    /**
     * @throws InvalidComponentException
     */
    public static function isValid(array $elements)
    {
        $i = 0;
        foreach ($elements as $element) {
            if (!is_array($element)) {
                throw new InvalidComponentException(\sprintf(self::INVALID_TYPE, "#$i", type($element)));
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
