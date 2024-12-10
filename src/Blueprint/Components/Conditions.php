<?php

namespace HusamAwadhi\PowerParser\Blueprint\Components;

use HusamAwadhi\PowerParser\Blueprint\BlueprintHelper;
use HusamAwadhi\PowerParser\Blueprint\ComponentInterface;
use HusamAwadhi\PowerParser\Blueprint\ValueObject\Condition;
use HusamAwadhi\PowerParser\Exception\InvalidComponentException;
use HusamAwadhi\PowerParser\Exception\InvalidFieldException;
use Iterator;

/**
 * @implements Iterator<Condition>
 */
class Conditions implements ComponentInterface, Iterator
{
    private int $position = 0;

    public readonly array $conditions;

    public function __construct(
        /** @var Condition[] */
        array $conditions,
        protected BlueprintHelper $helper
    ) {
        $this->conditions = $this->buildConditions($conditions);
        $this->position = 0;
    }

    protected function buildConditions(array $conditions): array
    {
        $objectConditions = [];
        foreach ($conditions as $condition) {
            if (!isset($condition['keyword']) || !$condition['keyword'] instanceof ConditionKeyword) {
                $case = self::getConditionKeyword($condition);
                $value = $condition[$case->value];
            } else {
                $case = $condition['keyword'];
                $value = $condition['value'];
            }
            $objectConditions[] = Condition::from(
                columns: $condition['column'],
                keyword: $case,
                value: $value,
            );
        }

        return $objectConditions;
    }

    /**
     * Entrypoint function.
     *
     * @throws InvalidComponentException
     */
    public static function from(array $conditions, BlueprintHelper $helper): self
    {
        self::validation($conditions);

        return new self($conditions, $helper);
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

            $case = self::getConditionKeyword($condition);

            $finalConditions[] = [
                'column' => $condition['column'],
                'keyword' => $case,
                'value' => $condition[$case->value],
            ];
        }
        $conditions = $finalConditions;
    }

    protected static function getConditionKeyword(array $condition): ConditionKeyword
    {
        foreach (ConditionKeyword::cases() as $case) {
            if (
                isset($condition[$case->value]) &&
                !empty($condition[$case->value]) &&
                is_string($condition[$case->value])
            ) {
                return $case;
            }
        }

        throw new InvalidFieldException('no valid condition keyword found');
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

    public function current(): mixed
    {
        return $this->conditions[$this->position];
    }

    public function key(): mixed
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function valid(): bool
    {
        return isset($this->conditions[$this->position]);
    }
}
