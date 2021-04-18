<?php

declare(strict_types=1);

namespace aloo20\Dice;

/**
 * Class Dicehand.
 */

class Dicehand
{
    private array $dices;
    private int $sum;

    public function __construct()
    {
        for ($i = 0; $i <= 3; $i++) {
            $this->dices[$i] = new Dice();
        }
    }

    public function roll(): void
    {
        $len = count($this->dices);

        $this->sum = 0;
        for ($i = 0; $i <= 3; $i++) {
            $this->sum += $this->dices[$i]->roll();
        }
    }

    public function getLastRoll(): string
    {
        $res = "";
        for ($i = 0; $i <= 3; $i++) {
            $res .= $this->dices[$i]->getLastRoll() . ", ";
        }
        return $res . " = " . $this->sum;
    }
}
