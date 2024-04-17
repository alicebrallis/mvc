<?php

namespace Tests\Unit\Card;
use App\Card\Card;
use App\Card\Deck;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class DeckTest extends TestCase
{

    public function testDeckCanBeCreated(): void
    {
        $deck = new Deck();
        $this->assertInstanceOf(Deck::class, $deck);
    }


    public function testGetCardsReturnsArray(): void
    {
        $deck = new Deck();
        $cards = $deck->getCards();
        $this->assertIsArray($cards);
    }
    

    public function testGetNumCardsRemaining(): void
    {
        $deck = new Deck();
        $this->assertEquals(52, $deck->getNumCardsRemaining());

        $deck->getRemovedCard();
        $this->assertEquals(51, $deck->getNumCardsRemaining());
    }

    public function testUniqueDrawnCards(): void
{
    $deck = new Deck();
    $drawnCards = [];

    for ($i = 0; $i < 52; $i++) {
        $drawnCard = $deck->getOneCard();
        $this->assertNotContains($drawnCard, $drawnCards);

        $drawnCards[] = $drawnCard;
    }
}


    public function testGetNumCardsRemainingAfterRemovingCard(): void
{
    $deck = new Deck();
    $deck->getRemovedCard();
    $this->assertEquals(51, $deck->getNumCardsRemaining());
}


    public function testGetOneCard(): void
    {
        $deck = new Deck();
        $card = $deck->getOneCard();
        $this->assertInstanceOf(Card::class, $card);
    }

    public function testIfCardValueMatches(): void
    {
        $card = new Card("5", "Hjärter");
        $this->assertEquals("5", $card->getColor());
        $this->assertEquals("Hjärter", $card->getValue());
    }

    public function testCalculateTotalValue(): void
    {
        $cards = [
            new Card("Hjärter", "5"),
            new Card("Klöver", "8"),
            new Card("Spader", "10"),
        ];

        $expectedTotalValue = 23;
        $actualTotalValue = 0;
        foreach ($cards as $card) {
            $actualTotalValue += $card->getValue();
        }
        
        $this->assertEquals($expectedTotalValue, $actualTotalValue);
    }
    public function testCompareCardsForResult(): void
    {
        $deck = new Deck();

        $testCases = [
            [
                'resultPlayer' => ['totalValue' => 18],
                'resultBank' => ['totalValueBank' => 17],
                'expectedResult' => "Du (Spelaren) är vinnaren för denna omgången"
            ],
            [
                'resultPlayer' => ['totalValue' => 17],
                'resultBank' => ['totalValueBank' => 18],
                'expectedResult' => "Bankiren är vinnaren för denna omgången"
            ],
        ];

        foreach ($testCases as $testCase) {
            $result = $deck->compareCardsForResult($testCase['resultPlayer'], $testCase['resultBank']);
            $this->assertEquals($testCase['expectedResult'], $result);
        }
    }

    public function testPlayerWinsWithTotalValueEqualTo21(): void
    {
        $game = new Deck();

        $resultPlayer = ['totalValue' => 21];
        $resultBank = ['totalValueBank' => 20];

        $expectedResult = "Du (Spelaren) är vinnaren för denna omgången";

        $this->assertEquals($expectedResult, $game->compareCardsForResult($resultPlayer, $resultBank));
    }

    public function testBankPlayerWinsWithTotalValueEqualTo21(): void
    {
        $game = new Deck();
        $resultPlayer = ['totalValue' => 20];
        $resultBank = ['totalValueBank' => 21];

        $expectedResult = "Bankiren är vinnaren för denna omgången";

        $this->assertEquals($expectedResult, $game->compareCardsForResult($resultPlayer, $resultBank));
    }

    public function testCompareCardsForBankWon(): void
    {
        $deck = new Deck();

        $totalValuePlayer = 22;
        $totalValueBank = 18;

        
        
        $result = $deck->compareCardsForResult(['totalValue' => $totalValuePlayer], ['totalValueBank' => $totalValueBank]);
        $this->assertEquals('Bankiren är vinnaren för denna omgången', $result);
    }


    public function testCompareCardsForPlayerWon(): void
    {
        $deck = new Deck();

        $totalValuePlayer = 18;
        $totalValueBank = 22;

        
        
        $result = $deck->compareCardsForResult(['totalValue' => $totalValuePlayer], ['totalValueBank' => $totalValueBank]);
        $this->assertEquals('Du (Spelaren) är vinnaren för denna omgången', $result);
    }



    public function testCompareCardsForResult_Draw(): void
    {
        $deck = new Deck();

        $totalValuePlayer = 18;
        $totalValueBank = 18;
        
        $result = $deck->compareCardsForResult(['totalValue' => $totalValuePlayer], ['totalValueBank' => $totalValueBank]);
        $this->assertEquals('Ingen vinnare denna gång', $result);
    }


    public function testCompareCardsForNoWin(): void
    {
        $deck = new Deck();

        $totalValuePlayer = 22;
        $totalValueBank = 24;
        
        $result = $deck->compareCardsForResult(['totalValue' => $totalValuePlayer], ['totalValueBank' => $totalValueBank]);
        $this->assertEquals('Ingen vinnare denna gång', $result);
    }
    
    public function testNoWinnerWhenBothExceedTwentyOne(): void
    {
        $resultPlayer = ['totalValue' => 22]; 
        $resultBank = ['totalValueBank' => 23]; 

        $deck = new Deck();
    
        $result = $deck->compareCardsForResult($resultPlayer, $resultBank);
    
        $expectedResult = "Ingen vinnare denna gång";
        $this->assertEquals($expectedResult, $result);
    }
    
    

    public function testShuffle(): void
    {
        $deck = new Deck();
        $originalOrder = $deck->getCards();

        $deck->shuffle();

        $shuffledOrder = $deck->getCards();
        $this->assertNotEquals($originalOrder, $shuffledOrder);
    }

        public function testGettinhgOneCard(): void { 
            $deck = new Deck();
            $oneCard = $deck->getOneCard();

            $this->assertInstanceOf(Card::class, $oneCard);
            $drawnCards = [];
    
            for ($i = 0; $i < 52; $i++) {
                $oneCard = $deck->getOneCard();
                $this->assertNotContains($oneCard, $drawnCards);
    
                $drawnCards[] = $oneCard;
            
        
            }
    
        }

            public function testCalculateAceValues(): void
            {
                $deck = new Deck();

                $drawnCards = [];
        
                $totalValue1 = $deck->calculateAceValues(1, $drawnCards);
                $this->assertEquals(1, $totalValue1);

                $totalValue2 = $deck->calculateAceValues(14, $drawnCards);
                $this->assertEquals(14, $totalValue2);

            }


        public function testGameOverPlayer(): void
    {
        $deck = new Deck();

        $result = $deck->gameOverPlayer();

        $this->assertEquals(0, $result);
    }

    public function testGameOverBankPlayer(): void
    {
        $deck = new Deck();

        $result = $deck->gameOverBankPlayer();
        $this->assertEquals(0, $result);
    }

    public function testDrawCardsFromDeck(): void
    {
        $deck = new Deck();
        $response = $deck->drawCardsFromDeck(3, $this->createStub(SessionInterface::class));
    
        $content = $response->getContent();
        $this->assertIsString($content, "Response content is not a string");
    
        if ($content !== '') {
            $this->assertJson($content);
    
            $responseData = json_decode($content, true);
    
            if (is_array($responseData)) {
                $this->assertArrayHasKey('drawn_cards', $responseData);
                $this->assertArrayHasKey('remaining_cards', $responseData);
            }
        }
    }
    
    

    public function testGetRemovedCardWhenDeckIsEmpty(): void
{
    $deck = new Deck();
    $initialNumCards = $deck->getNumCardsRemaining();

    for ($i = 0; $i < $initialNumCards; $i++) {
        $deck->getRemovedCard();
    }
    $removedCard = $deck->getRemovedCard();
    $this->assertNull($removedCard);
}

public function testDrawMoreCardsThanAvailable(): void
{
    $deck = new Deck();
    $initialNumCards = $deck->getNumCardsRemaining();

    for ($i = 0; $i < $initialNumCards; $i++) {
        $deck->getRemovedCard();
    }

    $response = $deck->drawCardsFromDeck(1, $this->createStub(SessionInterface::class));
    $this->assertInstanceOf(JsonResponse::class, $response);
    $jsonContent = $response->getContent();
    $this->assertIsString($jsonContent);
    $responseData = json_decode($jsonContent, true);
    if ($responseData === null || !is_array($responseData)) {
        $this->fail('Failed to decode JSON or JSON data is not an array');
    }
    $this->assertArrayHasKey('message', $responseData);
    $expectedMessage = 'No cards left in the deck.';
    $this->assertEquals($expectedMessage, $responseData['message']);
}

    public function testGetRemovedCardWhenCardsRemaining(): void
    {
        $deck = new Deck();
        $newCard = new Card('Hjärter', '10');
        $cards = $deck->getCards();
        $cards[] = $newCard;
    
        $deck->setCards($cards);
    
        $drawnCard = $deck->getRemovedCard();
    
        $this->assertInstanceOf(Card::class, $drawnCard);
    }

    public function testCalculateAceValueWhenAce(): void
    {
        $game = new Deck();
        $aceValue = 11;
        $result = $game->testCalculateAceValue($aceValue);

        $this->assertEquals($aceValue, $result);
    }


    public function testCalculateCardValueForJack(): void
    {
        $deck = new Deck();
        $card = new Card('Hjärter', 'Knekt');
        $value = $deck->calculateCardValue($card);
        $expectedValue = 11;
        $this->assertEquals($expectedValue, $value);
    }
    
    public function testCalculateCardValueForQueen(): void
    {
        $deck = new Deck();
        $card = new Card('Ruter', 'Dam');
        $value = $deck->calculateCardValue($card);
        $expectedValue = 12;
        $this->assertEquals($expectedValue, $value);
    }
    
    public function testCalculateCardValueForKing(): void
    {
        $deck = new Deck();
        $card = new Card('Klöver', 'Kung');
        $value = $deck->calculateCardValue($card);
        $expectedValue = 13;
        $this->assertEquals($expectedValue, $value);
    }

    public function testSort(): void {
        $deck = new Deck();
        
        $unsortedCards = [
            new Card('Hjärter', 'Knekt'),
            new Card('Ruter', '10'),
            new Card('Spader', 'Ess'),
        ];
        $deck->setCards($unsortedCards);
        $unsortedDeck = $deck->getCards();
        $deck->sort();
        $sortedDeck = $deck->getCards();
        $this->assertEquals($unsortedDeck, $sortedDeck);
    }
    public function testUpdateSessionDeck(): void
    {
        $sessionMock = $this->createMock(SessionInterface::class);
    
        $deck = new Deck();
    
        $deck->updateSessionDeck($sessionMock);
    
        $sessionMock->expects($this->once())
                    ->method('set')
                    ->with(
                        $this->equalTo('deck'),
                        $this->identicalTo($deck)
                    );
        $deck->updateSessionDeck($sessionMock);
    }
    
    public function testSortCards(): void
    {
        $deck = new Deck();
        $deck->shuffle();
        $shuffledDeck = clone $deck;
        $sortedDeck = clone $deck;
        $shuffledDeck->sort();
        $sortedDeck->sort();
        $shuffledCards = $shuffledDeck->getCards();
        $sortedCards = $sortedDeck->getCards();
        $this->assertEquals($sortedCards, $shuffledCards);
    }
    
    

}