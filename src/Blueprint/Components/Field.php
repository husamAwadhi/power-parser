<?php
namespace HusamAwadhi\PowerParser\Blueprint\Components;

use HusamAwadhi\PowerParser\Blueprint\BlueprintComponentInterface;

class Field implements BlueprintComponentInterface
{
    public readonly int $position;
    public readonly string $name;
    
    public function __construct(string $name, int $position)
    {
        $this->name = $name;
        $this->position = $position;
    }
}
