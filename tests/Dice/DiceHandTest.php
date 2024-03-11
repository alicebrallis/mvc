<?php

namespace Tests\App\Dice;

use PHPUnit\Framework\TestCase;
use App\Dice\Dice;
use App\Dice\DiceHand;

class DiceHandTest extends TestCase
{
    public function testAddDiceToHand(): void
    {
        $hand = new DiceHand();
        $hand->add(new Dice());
        $hand->add(new Dice());
        
        $this->assertEquals(2, $hand->getNumberDices());
    }
}