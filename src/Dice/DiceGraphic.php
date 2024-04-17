<?php

namespace App\Dice;

class DiceGraphic extends Dice
{
    /** @var string[] */
    private $representation = [
        '⚀',
        '⚁',
        '⚂',
        '⚃',
        '⚄',
        '⚅',
    ];

    public function __construct()
    {
        parent::__construct();
    }


    public function getAsString(): string
    {
        if ($this->value >= 1 && $this->value <= 6) {
            return $this->representation[$this->value - 1];
        } else {
            return '';
        }
    }

}
