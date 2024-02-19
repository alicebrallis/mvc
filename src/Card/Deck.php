<?php

namespace App\Card;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class Deck extends Card
{
    /** @var array<Card> */
    private $cards = [];

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
      * @return Card[]
      */
    public function getCards(): array
    {
        return $this->cards;
    }

    /**
     * @return Card
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
     * @param Card[] $cards
     * @return int
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
     * @param int $aceValue
     * @param Card[] $drawnCards
     * @return int
     */
    public function calculateAceValues(int $aceValue, array $drawnCards): int
    {
        $totalValue = $this->calculateTotalValue($drawnCards);

        if ($aceValue === 1 || $aceValue === 14) {
            $totalValue += $aceValue;
        }

        return $totalValue;
    }



    public function calculateCardValue(Card $card): int
    {

        $one = 1;
        $fourteen = 14;

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
     * @param int $aceValue
     * @return int
     */
    public function calculateAceValue(int $aceValue): int
    {
        $value = 'Ess';

        if ($value === 'Ess') {
            return $aceValue;
        }
    }

    /**
     * @param int $totalPoints
     * @return void
     */
    public function player(int $totalPoints): void
    {
        print_r($totalPoints);
    }


    /**
     * @param int $totalPoints
     * @return void
     */
    public function bankPlayer(int $totalPoints): void
    {
        print_r($totalPoints);
    }


    /**
     * @return int
     */
    public function gameOverPlayer(): int
    {
        $totalPoints = 0;

        $this->bankPlayer($totalPoints + 1);

        return $totalPoints;
    }


    /**
     * @return int
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
     * @return string
     */
    public function compareCardsForResult(array $resultPlayer, array $resultBank): string
    {
        $playerWon = "Du (Spelaren) är vinnaren för denna omgången";
        $bankWon = "Bankiren är vinnaren för denna omgången";

        $totalValuePlayer = $resultPlayer['totalValue'];
        $totalValueBank = $resultBank['totalValueBank'];

        if ($totalValuePlayer > $totalValueBank && $totalValuePlayer < 21) {
            return $playerWon;
        } elseif ($totalValueBank > $totalValuePlayer && $totalValueBank < 21) {
            return $bankWon;
        } elseif ($totalValuePlayer > 21 && $totalValueBank < 21) {
            return $bankWon;
        } elseif ($totalValueBank > 21 && $totalValuePlayer < 21) {
            return $playerWon;
        } elseif($totalValueBank === $totalValuePlayer) {
            return $bankWon;
        } elseif ($totalValuePlayer === 21 && $totalValueBank < 21) {
            return $playerWon;
        } elseif ($totalValueBank === 21 && $totalValuePlayer < 21) {
            return $bankWon;
        } elseif ($totalValueBank > 21 && $totalValuePlayer > 21) {
            return "Ingen vinnare denna gång";
        } else {
            return "Ingen vinnare denna gång";
        }
    }




    public function getNumCardsRemaining(): int
    {
        return count($this->cards);
    }
    /**
     * @param array<Card> $cards
     * @return void
     */
    public function setCards(array $cards): void
    {
        $this->cards = $cards;
    }

    /**
     * @return void
     */
    public function shuffle(): void
    {
        shuffle($this->cards);
    }

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
     * @return Card|null
     */
    public function getRemovedCard(): ?Card
    {
        if (count($this->cards) === 0) {
            return null;
        }

        $drawnCard = array_shift($this->cards);

        return $drawnCard;
    }

    public function updateSessionDeck(SessionInterface $session): void
    {
        $session->set('deck', $this);
    }
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
