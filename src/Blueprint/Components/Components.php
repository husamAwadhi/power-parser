<?php

namespace HusamAwadhi\PowerParser\Blueprint\Components;

use HusamAwadhi\PowerParser\Blueprint\BlueprintHelper;
use HusamAwadhi\PowerParser\Blueprint\ComponentInterface;
use HusamAwadhi\PowerParser\Blueprint\Type;
use HusamAwadhi\PowerParser\Blueprint\ValueObject\Component;
use HusamAwadhi\PowerParser\Exception\InvalidComponentException;
use Iterator;
use ReturnTypeWillChange;

class Components implements ComponentInterface, Iterator
{
    private const INVALID_TYPE = 'Element %s must be an array, given %s';
    private const MISSING_ELEMENT = 'Blueprint section %s is missing a mandatory element %s';
    private const INVALID_VALUE = 'Blueprint %s element has invalid value (%s). Acceptable value(s) [%s]';

    private $position = 0;

    /** @var Component[] */
    public readonly array $components;

    public function __construct(
        array $elements,
        protected BlueprintHelper $helper,
    ) {
        $this->position = 0;
        $this->components = $this->buildElements($elements);
    }

    protected function buildElements(array $elements = []): array
    {
        $components = [];
        foreach ($elements as $element) {
            $element['fields'] = $this->helper->createFields($element['fields']);
            $element['conditions'] = (isset($element['conditions'])
                ? $this->helper->createConditions($element['conditions'])
                : null);

            $components[] = Component::from(component: $element);
        }

        return $components;
    }

    public static function from(array $elements, BlueprintHelper $helper): self
    {
        self::validation($elements);

        return new self($elements, $helper);
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
                    \sprintf(
                        self::INVALID_VALUE,
                        "type (#$i)",
                        $element['type'],
                        implode(', ', array_column(Type::cases(), 'value'))
                    )
                );
            }

            if (!isset($element['name'])) {
                throw new InvalidComponentException(\sprintf(self::MISSING_ELEMENT, "blueprint (#$i)", 'name'));
            }

            if (!isset($element['fields'])) {
                throw new InvalidComponentException(\sprintf(self::MISSING_ELEMENT, "blueprint (#$i)", 'fields'));
            }

            if (
                ($element['table'] ?? false && !isset($element['conditions'])) &&
                ($element['type'] == Type::HIT->value && !isset($element['conditions']))
            ) {
                throw new InvalidComponentException(\sprintf(self::MISSING_ELEMENT, "blueprint (#$i)", 'conditions'));
            }

            ++$i;
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
        return $this->components[$this->position];
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
        return isset($this->components[$this->position]);
    }
}
