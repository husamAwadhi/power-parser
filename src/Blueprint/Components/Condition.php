<?php
namespace HusamAwadhi\PowerParser\Blueprint\Components;

use HusamAwadhi\PowerParser\Blueprint\BlueprintComponentInterface;

class Condition implements BlueprintComponentInterface
{
    public readonly array $columns;
    public readonly ConditionKeyword $keyword;

    public function __construct(array $columns, ConditionKeyword $keyword)
    {
        $this->columns = $columns;
        $this->keyword = $keyword;
    }
}
