<?php

namespace App\Card;

class Card
{
    private $color;
    private $value;

    public function __construct($color, $value)
    {
        $this->color = $color;
        $this->value = $value;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function getValue()
    {
        return $this->value;
    }
}
