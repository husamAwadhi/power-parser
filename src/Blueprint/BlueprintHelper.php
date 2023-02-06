<?php

namespace HusamAwadhi\PowerParser\Blueprint;

use HusamAwadhi\PowerParser\Blueprint\Components\Components;
use HusamAwadhi\PowerParser\Blueprint\Components\Conditions;
use HusamAwadhi\PowerParser\Blueprint\Components\Fields;

class BlueprintHelper
{
    public function createComponents(array $blueprint): Components
    {
        return Components::from($blueprint, $this);
    }

    public function createConditions(array $conditions): Conditions
    {
        return Conditions::from($conditions, $this);
    }

    public function createFields(array $fields): Fields
    {
        return Fields::from($fields, $this);
    }
}
