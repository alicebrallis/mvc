<?php

declare(strict_types=1);

namespace aloo20\Dice;

/**
 * Class Dicegraphic.
 */

class DiceGraphic extends Dice
{
    public function graphic()
    {
        return "dice-" . $this->getLastRoll();
    }
}
