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
    public function testRoll(): void
    {
        $hand = new DiceHand();
        $hand->add(new Dice());
        $hand->add(new Dice());

        $hand->roll();
        foreach ($hand->getValues() as $value) {
            $this->assertGreaterThanOrEqual(1, $value);
            $this->assertLessThanOrEqual(6, $value);
        }
    }

    public function testGetValuesReturnsArrayOrNullWhenHandIsEmpty(): void
    {
        $hand = new DiceHand();
        $result = $hand->getValues();
        $this->assertNull($result);
    }

    public function testGetStringReturnsArrayOfStrings(): void
    {
        $hand = new DiceHand();
        $hand->add(new Dice());
        $hand->add(new Dice());

        $result = $hand->getString();
        $this->assertIsArray($result);
        $this->assertContainsOnly('string', $result);
    }
}