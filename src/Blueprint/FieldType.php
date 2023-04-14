<?php

namespace HusamAwadhi\PowerParser\Blueprint;

enum FieldType: string
{
    case INT = 'int';
    case FLOAT = 'float';
    case BOOL = 'bool';
    case BOOL_STRICT = 'bool-strict';
}
