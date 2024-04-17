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
         $result = $dice->getAsString();
 
         $validRepresentations = ['⚀', '⚁', '⚂', '⚃', '⚄', '⚅'];
 
         $validRepresentations[] = '';
         $this->assertContains($result, $validRepresentations);
     }
 }
 