<?php


namespace Tests\Unit\Dice;
use App\Dice\Dice;
use App\Dice\DiceHand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Test cases för klassen Dice.
 */
class DiceTest extends TestCase
{
    /**
     * 
     * 
     */
    public function testCreateDice(): void
    {
        $die = new Dice();
        $this->assertInstanceOf("\App\Dice\Dice", $die);

        $res = $die->getAsString();
        $this->assertNotEmpty($res);
    }

    public function testRollReturnsIntegerBetweenOneAndSix(): void
    {
        $dice = new Dice();
        $value = $dice->roll();

        $this->assertGreaterThanOrEqual(1, $value);
        $this->assertLessThanOrEqual(6, $value);
    }

    public function testGetValueReturnsNullByDefault(): void
    {
        $dice = new Dice();
        $value = $dice->getValue();

        $this->assertNull($value);
    }

    public function testGetAsStringReturnsStringWithDiceValue(): void
    {
        $dice = new Dice();
        $dice->roll();
        $asString = $dice->getAsString();
        $this->assertMatchesRegularExpression('/^\[\d+\]$/', $asString);
    }
}
