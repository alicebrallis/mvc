<?php

namespace App\Card;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class Deck extends Card
{
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

    public function getCards()
    {
        return $this->cards;
    }


    private function fillDeck()
    {
        $colors = ['Hjärter', 'Ruter', 'Klöver', 'Spader'];
        $values = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'Knekt', 'Dam', 'Kung', 'Ess'];

        foreach ($colors as $color) {
            foreach ($values as $value) {
                $card = new Card($color, $value);
                $this->cards[] = $card;
            }
        }
    }

    public function getOneCard()
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

    public function getNumCardsRemaining(): int
    {
        return count($this->cards);
    }

    public function setCards($cards)
    {
        $this->cards = $cards;
    }

    public function shuffle()
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





    public function getRemovedCard()
    {
        if (count($this->cards) === 0) {
            return null;
        }

        $drawnCard = array_shift($this->cards);

        return $drawnCard;
    }
    public static function getOrCreateDeckFromSession(SessionInterface $session): self
    {
        $deck = $session->get('deck', null);

        if ($deck === null) {
            $deck = new self();
            $deck->fillDeck();
        }

        return $deck;

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
