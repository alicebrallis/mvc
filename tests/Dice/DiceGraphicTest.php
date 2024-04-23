<?php

namespace Tests\Unit\Dice;

use App\Dice\DiceGraphic;
use PHPUnit\Framework\TestCase;

/**
 * Test cases för klassen DiceGraphicTest.
 */
class DiceGraphicTest extends TestCase
{
    public function testGetAsStringReturnsCorrectRepresentation(): void
    {
        $dice = new DiceGraphic();

        for ($value = 1; $value <= 6; $value++) {
            $reflection = new \ReflectionClass($dice);
            $property = $reflection->getProperty('value');
            $property->setAccessible(true);
            $property->setValue($dice, $value);

            $result = $dice->getAsString();
            $validRepresentations = ['⚀', '⚁', '⚂', '⚃', '⚄', '⚅'];
            $this->assertContains($result, $validRepresentations);
        }
    }

    public function testGetAsStringReturnsEmptyStringWhenValueIsOutOfRange(): void
    {
        $dice = new DiceGraphic();

        $reflection = new \ReflectionClass($dice);
        $property = $reflection->getProperty('value');
        $property->setAccessible(true);
        $property->setValue($dice, 7);

        $result = $dice->getAsString();
        $this->assertSame('', $result);
    }
}
