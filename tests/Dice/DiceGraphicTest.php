<?php


namespace Tests\Unit\Dice;
use App\Dice\Dice;
use App\Dice\DiceHand;
use App\Dice\DiceGraphic;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Test cases för klassen DiceGraphicTest.
 */
class DiceGraphicTest extends TestCase
{
    public function testGetAsStringReturnsCorrectRepresentation()
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

    public function testGetAsStringReturnsEmptyStringWhenValueIsOutOfRange()
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

