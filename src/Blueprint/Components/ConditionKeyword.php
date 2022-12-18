<?php

namespace HusamAwadhi\PowerParser\Blueprint\Components;

enum ConditionKeyword: string
{
    case Is = 'is';
    case IsNot = 'isNot';
    case AnyOf = 'anyOf';
    case NoneOf = 'noneOf';
}
