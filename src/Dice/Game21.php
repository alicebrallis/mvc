<?php

declare(strict_types=1);

namespace aloo20\Dice;

/**
 * Class Game21.
 */

class Game21 extends Dicehand
{
    public function game21()
    {
        return $this->getLastRoll();
    }
}
