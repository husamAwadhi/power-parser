<?php

namespace HusamAwadhi\PowerParser\Parser\Extension;

use HusamAwadhi\PowerParser\Blueprint\Blueprint;
use HusamAwadhi\PowerParser\Blueprint\Components\ConditionKeyword;
use HusamAwadhi\PowerParser\Blueprint\FieldFormat;
use HusamAwadhi\PowerParser\Blueprint\FieldType;
use HusamAwadhi\PowerParser\Blueprint\Type;
use HusamAwadhi\PowerParser\Blueprint\ValueObject\Component;
use HusamAwadhi\PowerParser\Blueprint\ValueObject\Condition;
use HusamAwadhi\PowerParser\Blueprint\ValueObject\Field;
use HusamAwadhi\PowerParser\Exception\InvalidArgumentException;
use HusamAwadhi\PowerParser\Exception\InvalidFieldException;
use PhpOffice\PhpSpreadsheet\Shared\Date;

abstract class BlueprintInterpreter implements ParserPluginInterface
{
    protected Blueprint $blueprint;

    protected int $curser = 0;

    protected int $lastHitIndex = -1;

    protected array $data;

    protected array $filtered;

    protected function filter(): self
    {
        if (!isset($this->data)) {
            throw new InvalidArgumentException('content is not parsed yet.');
        }

        $this->filtered = [];
        foreach ($this->data as $page) {
            $content = $page['content'];

            /** @var Component */
            foreach ($this->blueprint->components as $component) {
                $found = false;
                $index = $this->lastHitIndex + 1;
                for ($index; $index < count($content); ++$index) {
                    if ($this->isMatch($component, $content[$index], $index)) {
                        $this->filtered[$component->name] = (
                            $component->table
                            ? $this->getTable($component, $content, $index)
                            : $this->getFields($component, $content[$index])
                        );
                        $found = true;

                        break;
                    }
                }

                if (!$found && $component->mandatory) {
                    throw new InvalidArgumentException(
                        "File {$this->blueprint->name} does not contain mandatory field: {$component->name}"
                    );
                }
            }
        }

        return $this;
    }

    public function isMatch(Component $component, array $row, int $index, bool $isTable = false): bool
    {
        $found = ($component->type === Type::NEXT && !$isTable
            ? $this->matchNext($index)
            : $this->matchHit($component, $row));

        if ($found) {
            $this->lastHitIndex = $index;
        }

        return $found;
    }

    protected function matchHit(Component $component, array $row): bool
    {
        $found = false;
        /** @var Condition $condition */
        foreach ($component->conditions ?? [] as $condition) {
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

    protected function matchNext(int $index): bool
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
            $fieldValue = $row[$field->position - 1];
            $filteredFields[$field->name] = $this->postProcessField($fieldValue, $field);
        }

        return $filteredFields;
    }

    protected function getTable(Component $component, array $rows, int &$index): array
    {
        $table = [];
        while ($index < count($rows) && $this->isMatch($component, $rows[$index], $index, true)) {
            $table[] = $this->getFields($component, $rows[$index]);
            ++$index;
        }

        return $table;
    }

    protected function matchCondition(Condition $condition, mixed $data): bool
    {
        return match ($condition->keyword) {
            ConditionKeyword::AnyOf => in_array($data, explode(',', $condition->value)),
            ConditionKeyword::Is => $condition->value === $data,
            ConditionKeyword::IsNot => $condition->value !== $data,
            ConditionKeyword::NoneOf => !in_array($data, explode(',', $condition->value)),
        };
    }

    protected function postProcessField(mixed $value, Field $field): mixed
    {
        if (null !== $field->type) {
            $value = match ($field->type) {
                FieldType::BOOL => strtolower((string) $value) == 'true' || $value == true || $value == '1',
                FieldType::BOOL_STRICT => $value == true,
                FieldType::INT => (int) $value,
                FieldType::FLOAT => (float) $value,
                FieldType::DATE => Date::excelToDateTimeObject($value),
            };
        }
        if (null !== $field->format) {
            $value = match ($field->format->type) {
                FieldFormat::STRING => substr($value, 0, $field->format->argument),
                FieldFormat::FLOAT => round((float) $value, $field->format->argument, PHP_ROUND_HALF_UP),
                FieldFormat::DATE => $value->format($field->format->argument),
            };
        }

        return $value;
    }

    public function getFiltered(): array
    {
        return match (isset($this->filtered)) {
            true => $this->filtered,
            false => $this->filter()->filtered,
        };
    }
}
