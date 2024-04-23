<?php

namespace App\Controller;

use App\Card\Card;
use App\Card\Deck;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CardGameController extends AbstractController
{
    #[Route("/card", name: "card")]
    public function card(): Response
    {
        return $this->render('card_game/card.html.twig');
    }

    #[Route("/card/deck", name: "view_deck")]
    public function viewDeck(Request $request): Response
    {
        $session = $request->getSession();
        $session->remove('deck');
        $deck = $session->get('deck', []);
        $uniqueCards = [];

        if ($deck instanceof Deck) {
            $cards = $deck->getCards();

            usort($cards, function ($a, $b) {
                $colorsOrder = ['Klöver', 'Ruter', 'Hjärter', 'Spader'];
                $valuesOrder = ['Ess', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'Knekt', 'Dam', 'Kung'];

                $colorComparison = array_search($a->getColor(), $colorsOrder) - array_search($b->getColor(), $colorsOrder);

                if ($colorComparison !== 0) {
                    return $colorComparison;
                }

                return array_search($a->getValue(), $valuesOrder) - array_search($b->getValue(), $valuesOrder);
            });

            foreach ($cards as $card) {
                $uniqueKey = $card->getColor() . $card->getValue();
                if (!isset($uniqueCards[$uniqueKey])) {
                    $uniqueCards[$uniqueKey] = $card;
                }
            }

            $uniqueCards = array_values($uniqueCards);
        }

        return $this->render('card_game/test/shuffle.html.twig', [
            'cards' => $uniqueCards,
        ]);
    }

    #[Route("/card/deck/draw", name: "draw_cards")]
    public function drawCards(Request $request): Response
    {
        $session = $request->getSession();
        $deck = $session->get('deck');

        // Kontrollera om $deck är null eller inte en array innan du använder den
        if ($deck === null || !is_array($deck)) {
            // Skapa en ny lek om $deck är null eller inte en array
            $deck = new Deck();
            $deck->shuffle();
            $session->set('deck', $deck);
        }


        $drawnCards = $session->get('drawn_cards', []);

        // Kontrollera om $deck är en array och att den har minst ett kort innan du fortsätter
        if (is_array($deck) && count($deck) > 0) {
            $card = $deck[0];
            // Ta bort det första kortet från $deck
            array_shift($deck);
            $session->set('deck', $deck);

            /** @var Card[] $drawnCards */
            $drawnCards[] = $card;

            $session->set('drawn_cards', $drawnCards);

            return $this->render('card_game/test/draw_cards.html.twig', [
                'drawnCard' => $card,
                'numCards' => count($deck) // Antalet kvarvarande kort i $deck
            ]);
        }

        return $this->render('card_game/test/error.html.twig');
    }



    #[Route("/card/deck/draw/{number<\d+>}", name: "draw_cards_number")]
    public function drawCardsNumber(Request $request, int $number): Response
    {
        $session = $request->getSession();
        $deck = $session->get('deck', null);
        $drawnCards = $session->get('drawn_cards', []);

        if ($deck === null || !$deck instanceof Deck || count($deck->getCards()) === 0) {
            return $this->render('card_game/test/no_cards_left.html.twig');
        }

        $cards = $deck->getCards();
        $shuffledCards = [];

        for ($i = 0; $i < $number; $i++) {
            if (count($cards) === 0) {
                return $this->render('card_game/test/no_cards_left.html.twig');
            }

            $card = $deck->getOneCard();
            $shuffledCards[] = $card;
            $key = array_search($card, $cards);
            if ($key !== false) {
                unset($cards[$key]);
            }
            /** @var Card[] $drawnCards */
            $drawnCards[] = $card;
        }

        $deck->setCards($cards);
        $session->set('deck', $deck);
        $session->set('drawn_cards', $drawnCards);

        return $this->render('card_game/test/cardhand.html.twig', [
            'numCards' => count($cards),
            'shuffledCards' => $shuffledCards,
        ]);
    }

    #[Route("/card/deck/shuffle", name: "shuffle_deck")]
    public function shuffleDeck(Request $request): Response
    {
        $session = $request->getSession();
        $session->remove('deck');

        $deck = new Deck();
        $deck->shuffle();
        $session->set('deck', $deck);

        $session->remove('drawn_cards');

        return $this->redirectToRoute('draw_cards');
    }
}
