<?php

namespace HusamAwadhi\PowerParser\Parser\Extension;

use HusamAwadhi\PowerParser\Blueprint\Blueprint;
use HusamAwadhi\PowerParser\Blueprint\Components\ConditionKeyword;
use HusamAwadhi\PowerParser\Blueprint\Type;
use HusamAwadhi\PowerParser\Blueprint\ValueObject\Component;
use HusamAwadhi\PowerParser\Blueprint\ValueObject\Condition;
use HusamAwadhi\PowerParser\Blueprint\ValueObject\Field;
use HusamAwadhi\PowerParser\Exception\InvalidFieldException;

abstract class BlueprintInterpreter implements ParserPluginInterface
{
    protected Blueprint $blueprint;

    protected int $curser = 0;

    protected int $lastHitIndex = -1;

    public function isMatch(Component $component, array $row, $index): bool
    {
        $found = match ($component->type) {
            Type::HIT => $this->matchHit($component, $row),
            Type::NEXT => $this->matchNext($index)
        };

        if ($found) {
            $this->lastHitIndex = $index;
        }

        return $found;
    }

    protected function matchHit(Component $component, array $row): bool
    {
        $found = false;
        /** @var Condition $condition */
        foreach ($component->conditions as $condition) {
            $passedCondition = false;
            foreach ($condition->columns as $column) {
                if (isset($row[$column - 1])) {
                    $passedCondition = $this->matchCondition($condition, $row[$column - 1]);
                }
                if (!$passedCondition) {
                    break;
                }
            }
            if (!$passedCondition) {
                $found = false;

                break;
            }
            $found = true;
        }

        return $found;
    }

    protected function matchNext($index): bool
    {
        return $this->lastHitIndex === $index - 1;
    }

    protected function getFields(Component $component, array $row): array
    {
        $filteredFields = [];
        /** @var Field $field */
        foreach ($component->fields as $field) {
            if (!array_key_exists($field->position - 1, $row)) {
                throw new InvalidFieldException("field {$field->name} does not exist in position #{$field->position}");
            }
            $filteredFields[$field->name] = $row[$field->position - 1];
        }

        return $filteredFields;
    }

    protected function getTable(Component $component, array $rows, int &$index): array
    {
        $table = [];
        while ($this->isMatch($component, $rows[$index], $index) && $index < count($rows)) {
            $table[] = $this->getFields($component, $rows[$index]);
            ++$index;
        }

        return $table;
    }

    protected function matchCondition(Condition $condition, mixed $data): bool
    {
        $return = match ($condition->keyword) {
            ConditionKeyword::AnyOf => in_array($data, explode(',', $condition->value)),
            ConditionKeyword::Is => $condition->value === $data,
            ConditionKeyword::IsNot => $condition->value !== $data,
            ConditionKeyword::NoneOf => !in_array($data, explode(',', $condition->value)),
        };

        return  $return;
    }
}
