<?php

namespace HusamAwadhi\PowerParser\Blueprint\Components;

use HusamAwadhi\PowerParser\Blueprint\ComponentInterface;
use HusamAwadhi\PowerParser\Blueprint\Exceptions\InvalidComponentException;
use HusamAwadhi\PowerParser\Blueprint\Exceptions\InvalidFieldException;
use Iterator;
use ReturnTypeWillChange;

class Conditions implements ComponentInterface, Iterator
{
    private int $position = 0;

    public function __construct(
        public readonly array $conditions
    ) {
        $this->position = 0;
    }

    /**
     * Entrypoint function.
     *
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
    public static function validation(array &$conditions): void
    {
        $finalConditions = [];
        foreach ($conditions as $condition) {
            if (
                !isset($condition['column']) ||
                empty($condition['column']) ||
                !is_array($condition['column'])
            ) {
                throw new InvalidFieldException('missing or empty column');
            }

            $conditionKeyword = [];
            foreach (ConditionKeyword::cases() as $case) {
                if (
                    isset($condition[$case->value]) &&
                    !empty($condition[$case->value]) &&
                    is_string($condition[$case->value])
                ) {
                    $conditionKeyword = [
                        $case->value,
                        $condition[$case->value],
                    ];

                    break;
                }
            }

            if (count($conditionKeyword) == 0) {
                throw new InvalidFieldException('no valid condition found');
            }
            $finalConditions[] = [
                'column' => $condition['column'],
                $conditionKeyword[0] => $conditionKeyword[1],
            ];
        }
        $conditions = $finalConditions;
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
        return $this->conditions[$this->position];
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
