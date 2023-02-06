<?php

namespace HusamAwadhi\PowerParser\Parser\Extension;

use HusamAwadhi\PowerParser\Blueprint\Blueprint;
use HusamAwadhi\PowerParser\Blueprint\Type;
use HusamAwadhi\PowerParser\Blueprint\ValueObject\Component;

abstract class BlueprintInterpreter implements ParserPluginInterface
{
    protected Blueprint $blueprint;

    public function isMatch(Component $component, array $row): bool
    {
        return match ($component->type) {
            Type::HIT => $this->matchHit($component, $row),
            Type::NEXT => $this->matchNext($component, $row)
        };
    }

    public function matchHit(Component $component, array $row): bool
    {
        return false;
    }

    public function matchNext(Component $component, array $row): bool
    {
        return false;
    }
}
