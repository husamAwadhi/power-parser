<?php

namespace HusamAwadhi\PowerParser\Parser\Extension;

use HusamAwadhi\PowerParser\Blueprint\Blueprint;
use HusamAwadhi\PowerParser\Blueprint\Components\ConditionKeyword;
use HusamAwadhi\PowerParser\Blueprint\Components\Fields;
use HusamAwadhi\PowerParser\Blueprint\Type;
use HusamAwadhi\PowerParser\Blueprint\ValueObject\Component;
use HusamAwadhi\PowerParser\Blueprint\ValueObject\Condition;
use HusamAwadhi\PowerParser\Blueprint\ValueObject\Field;

abstract class BlueprintInterpreter implements ParserPluginInterface
{
    protected Blueprint $blueprint;

    protected int $curser = 0;

    protected int $lastHitIndex = -1;

    public function isMatch(Component $component, array $row): bool
    {
        return match ($component->type) {
            Type::HIT => $this->matchHit($component, $row),
            Type::NEXT => $this->matchNext()
        };
    }

    protected function matchHit(Component $component, array $row): bool
    {
        $found = false;
        /** @var Condition $condition */
        foreach ($component->conditions as $condition) {
            // dump('hit', $condition, $row);
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

    protected function matchNext(): bool
    {
        // dump('next');

        return $this->lastHitIndex > -1;
    }

    protected function getFields(Fields $fields, $row): array
    {
        /** @var Field $field */
        foreach ($fields as $field) {
            // dump('field', $field, $row);
        }

        return $row;
    }

    protected function matchCondition(Condition $condition, mixed $data): bool
    {
        $return = match ($condition->keyword) {
            ConditionKeyword::AnyOf => in_array($data, explode(',', $condition->value)),
            ConditionKeyword::Is => $condition->value === $data,
            ConditionKeyword::IsNot => $condition->value !== $data,
            ConditionKeyword::NoneOf => !in_array($data, explode(',', $condition->value)),
        };
        // dump('matchCondition', $return);

        return  $return;
    }
}
