<?php

namespace App\Card;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class Deck extends Card
{
    /** @var array<Card> */
    private $cards = [];


    /**
     * Skapar en ny kortlek.
     */
    public function __construct()
    {
        parent::__construct('', '');

        $colors = ['Hjärter', 'Ruter', 'Klöver', 'Spader'];
        $values = ['2', '3', '4', '5', '6', '7', '8', '9', '10','Knekt', 'Dam', 'Kung', 'Ess'];

        foreach ($colors as $color) {
            foreach ($values as $value) {
                $card = new Card($color, $value);
                $this->cards[] = $card;
            }
        }
    }
    /**
     * Hämtar korten i kortleken
     *
      * @return Card[] Korten i kortleken i en array
      */
    public function getCards(): array
    {
        return $this->cards;
    }

    /**
     * Hämtar ett kort från kortleken
     * @return Card Ett slumpmässigt valt kort från kortleken dras
     */
    public function getOneCard(): Card
    {
        $colors = ['Hjärter', 'Ruter', 'Klöver', 'Spader'];
        $values = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'Knekt', 'Dam', 'Kung', 'Ess'];

        $allCards = [];

        foreach ($colors as $color) {
            foreach ($values as $value) {
                $card = new Card($color, $value);
                $allCards[] = $card;
            }
        }

        shuffle($allCards);
        $oneCard = array_shift($allCards);

        return $oneCard;
    }




    /**
     * Beräknar totalvärdet av given lista med kort.
     * @param Card[] $cards Lista med kort att beräkna totalvärdet för.
     * @return int Totalvärdet av korten
     */
    public function calculateTotalValue(array $cards): int
    {
        $totalValue = 0;

        foreach ($cards as $card) {
            if ($card !== null) {
                $totalValue += $this->calculateCardValue($card);
            }
        }

        return $totalValue;
    }


    /**
     * Beräknar totalvärdet av ess-kortet i kortleken.
     * @param int $aceValue  Värdet av ess-kortet.
     * @param Card[] $drawnCards Array med de dragna korten.
     * @return int Det totala värdet av ess-kortet
     */
    public function calculateAceValues(int $aceValue, array $drawnCards): int
    {
        $totalValue = $this->calculateTotalValue($drawnCards);

        if ($aceValue === 1 || $aceValue === 14) {
            $totalValue += $aceValue;
        }

        return $totalValue;
    }

    /**
     * Beräknar värdet av ett kort baserat på dess värde.
     *
     * @param Card $card Kortet vars värde ska beräknas.
     * @return int Värdet av kortet enligt kortspelet 21-reglerna.
     */
    public function calculateCardValue(Card $card): int
    {

        $value = $card->getValue();

        if ($value === 'Knekt') {
            return 11;
        } elseif ($value === 'Dam') {
            return 12;
        } elseif ($value === 'Kung') {
            return 13;
        } else {
            return (int)$value;
        }
    }

    /**
     * @param int $aceValue Beräknar ess-värdet totalvärde
     * @return int Värdet av ess-kortet.
     */
    public function calculateAceValue(int $aceValue): int
    {
        //$value = 'Ess';

        return $aceValue;
        
    }

    /**
     * Visar totalpoängen för spelaren.
     *
     * @param int $totalPoints Totalpoängen för spelaren.
     * @return void
     */
    public function player(int $totalPoints): void
    {
        print_r($totalPoints);
    }

    /**
     * Visar totalpoängen för bankiren.
     *
     * @param int $totalPoints Totalpoängen för bankiren.
     * @return void
     */
    public function bankPlayer(int $totalPoints): void
    {
        print_r($totalPoints);
    }


    /**
     * Avslutar spelet för spelaren och returnerar totalpoängen.
     *
     * @return int Totalpoängen för spelaren.
     */
    public function gameOverPlayer(): int
    {
        $totalPoints = 0;

        $this->bankPlayer($totalPoints + 1);

        return $totalPoints;
    }


    /**
      * Avslutar spelet för bankiren och returnerar totalpoängen.
      *
      * @return int Totalpoängen för bankiren.
      */
    public function gameOverBankPlayer(): int
    {
        $totalPoints = 0;

        $this->player($totalPoints + 1);

        return $totalPoints;
    }

    /**
     * @param array<string, mixed> $resultPlayer
     * @param array<string, mixed> $resultBank
     * @return string Returnerar resultatet för spelet i en sträng baserat på spelet 21 spelregler.
     */
    public function compareCardsForResult(array $resultPlayer, array $resultBank): string
    {
        $totalValuePlayer = $resultPlayer['totalValue'];
        $totalValueBank = $resultBank['totalValueBank'];
    
        $results = [
            21 => "Du (Spelaren) är vinnaren för denna omgången",
            -21 => "Bankiren är vinnaren för denna omgången",
            0 => "Ingen vinnare denna gång"
        ];
    
        $difference = $totalValuePlayer - $totalValueBank;
        return $results[$difference] ?? "Ingen vinnare denna gång";
    }
    


    /**
     * Returnerar antalet kvarvarande kort i leken.
     *
     * @return int Antalet kvarvarande kort i leken.
     */
    public function getNumCardsRemaining(): int
    {
        return count($this->cards);
    }

    /**
     * Ställer in en ny uppsättning kort i leken.
     *
     * @param array<Card> $cards En array med kort att ställa in i leken.
     * @return void
     */
    public function setCards(array $cards): void
    {
        $this->cards = $cards;
    }

    /**
     * Blandar korten i leken.
     *
     * @return void
     */
    public function shuffle(): void
    {
        shuffle($this->cards);
    }

    /**
     * Sorterar korten i leken enligt en specifik ordning.
     *
     * @return void
     */
    public function sort(): void
    {
        usort($this->cards, function ($a, $b) {
            $colorsOrder = ['Hjärter', 'Ruter', 'Klöver', 'Spader'];
            $valuesOrder = ['Ess', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'Knekt', 'Dam', 'Kung'];

            $colorComparison = array_search($a->getColor(), $colorsOrder) - array_search($b->getColor(), $colorsOrder);

            if ($colorComparison !== 0) {
                return $colorComparison;
            }

            $aValueOrder = array_search($a->getValue(), $valuesOrder);
            $bValueOrder = array_search($b->getValue(), $valuesOrder);

            return $aValueOrder - $bValueOrder;
        });
    }

    /**
     * Hämtar och returnerar det översta kortet från leken och tar bort det från leken.
     * Returnerar null om leken är tom.
     *
     * @return Card|null Det översta kortet från leken, eller null om leken är tom.
     */
    public function getRemovedCard(): ?Card
    {
        if (count($this->cards) === 0) {
            return null;
        }

        $drawnCard = array_shift($this->cards);

        return $drawnCard;
    }

    /**
     * Uppdaterar leken i sessionen med den aktuella leken.
     *
     * @param SessionInterface $session Sessionen där leken ska uppdateras.
     * @return void
     */
    public function updateSessionDeck(SessionInterface $session): void
    {
        $session->set('deck', $this);
    }

    /**
     * Drar ett angivet antal kort från leken och uppdaterar sessionen.
     *
     * @param int $number Antal kort som ska dras från leken.
     * @param SessionInterface $session Sessionen där leken ska uppdateras.
     * @return JsonResponse Ett JSON-svar som innehåller dragna kort och antalet kvarvarande kort i leken.
     */
    public function drawCardsFromDeck(int $number, SessionInterface $session): JsonResponse
    {
        $drawnCards = [];

        for ($i = 0; $i < $number; $i++) {
            $drawnCard = $this->getRemovedCard();

            if ($drawnCard === null) {
                return new JsonResponse(['message' => 'No cards left in the deck.'], JsonResponse::HTTP_BAD_REQUEST);
            }

            $drawnCards[] = [
                'color' => $drawnCard->getColor(),
                'value' => $drawnCard->getValue(),
            ];
        }

        $this->updateSessionDeck($session);
        return new JsonResponse([
            'drawn_cards' => $drawnCards,
            'remaining_cards' => count($this->getCards()),
        ]);
    }





}
