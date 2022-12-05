<?php

namespace HusamAwadhi\PowerParser\Blueprint\Components;

use Iterator;
use ReturnTypeWillChange;

class Components implements Iterator
{
    private $position = 0;
    private readonly array $elements;

    public function __construct($elements)
    {
        $this->position = 0;
        $this->elements = array_map(function ($element) {
            return (object) $element;
        }, $elements);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    #[ReturnTypeWillChange]
    public function current()
    {
        return $this->elements[$this->position];
    }

    #[ReturnTypeWillChange]
    public function key()
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function valid(): bool
    {
        return isset($this->elements[$this->position]);
    }
}
