<?php

namespace Tests\Unit\Card;
use App\Controller\CardGameController;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CardGameControllerTest extends WebTestCase
{
    public function testControllerCanBeCreated()
    {
        $controller = new CardGameController();
        $this->assertInstanceOf(CardGameController::class, $controller);
    }

}
