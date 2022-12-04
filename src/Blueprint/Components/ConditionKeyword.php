<?php

namespace HusamAwadhi\PowerParser\Blueprint\Components;

enum ConditionKeyword
{
    case Is;
    case IsNot;
    case AnyOf;
    case NoneOf;

    public function keyword(): string
    {
        return match ($this) {
            ConditionKeyword::Is => 'is',
            ConditionKeyword::IsNot => 'isNot',
            ConditionKeyword::AnyOf => 'anyOf',
            ConditionKeyword::NoneOf => 'noneOf',
        };
    }
}
