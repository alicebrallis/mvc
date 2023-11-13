<?php

namespace App\Controller;

use App\Card\Card;
use App\Card\Deck;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\VarDumper\VarDumper;

class CardGameControllerJSON extends AbstractController
{
    #[Route("/api/quote", name: "quote")]
    public function jsonQuote(): Response
    {

        $quotes = array(
        "Försök att göra någon glad varje dag, om det så bara är dig själv. - Okänd",
        "För mycket av det goda kan vara underbart. - Mae West",
        "Det viktigaste är inte varifrån man kommer utan vart man är på väg. - Bernie Rhodes");
        shuffle($quotes);
        foreach ($quotes as $quote) {
            //u($quote)->normalize(UnicodeString::NFC);
        }
        $currentDate = date('Y-m-d h:i:s');

        $data = [

            'Citat' => $quotes[0],
            'Datum' => $currentDate,
        ];


        $response = new JsonResponse($data);
        $response->setEncodingOptions($response->getEncodingOptions() | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return $response;
    }

    #[Route("/api", name: "api")]
    public function api(): Response
    {
        $quoteUrl = $this->generateUrl('quote');
        $deckUrl = $this->generateUrl('api_deck_get');
        $deckShuffle = $this->generateUrl('shuffle_api_deck');
        $deckDraw  = $this->generateUrl('draw_one_card');


        return $this->render('card_game_json/card_json.html.twig', [
            'quote_url' => $quoteUrl,
            'deck_url' => $deckUrl,
            'deck_shuffle' => $deckShuffle,
            'deck_draw' => $deckDraw,
        ]);
    }

    #[Route("/api/deck", name: "api_deck_get", methods: ["GET"])]
    public function getSortedApiDeck(Request $request, SessionInterface $session): JsonResponse
    {
        $deck = $session->get('deck', null);

        if ($deck === null) {
            $deck = new Deck();
        }
        $deck->sort();
        $cards = $deck->getCards();

        $cardsData = array_map(function ($card) {
            return [
                'color' => $card->getColor(),
                'value' => $card->getValue(),
            ];
        }, $cards);

        $jsonResponse = new JsonResponse(['sorted_deck' => $cardsData]);
        $jsonResponse->setEncodingOptions(JSON_UNESCAPED_UNICODE);

        return $jsonResponse;
    }



    #[Route("/api/deck/shuffle", name: "shuffle_api_deck", methods: ["POST", "GET"])]
    public function shuffleApiDeck(Request $request, SessionInterface $session): JsonResponse
    {
        $deck = $session->get('deck', null);

        if ($deck === null) {
            $deck = new Deck();
        }
        $deck->shuffle();

        $session->set('deck', $deck);

        $cards = $deck->getCards();

        $cardsData = array_map(function ($card) {
            return [
                'color' => $card->getColor(),
                'value' => $card->getValue(),
            ];
        }, $cards);


        $jsonResponse = new JsonResponse(['shuffled_deck' => $cardsData]);
        $jsonResponse->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        return $jsonResponse;
    }

    #[Route("/api/deck/draw", name: "draw_one_card", methods: ["POST", "GET"])]
    public function drawOneCard(Request $request, SessionInterface $session): JsonResponse
    {
        $deck = $this->getOrCreateDeck($session);

        return $deck->drawCardsFromDeck(1, $session);
    }

    #[Route("/api/deck/draw/{number}", name: "draw_multiple_cards", methods: ["POST", "GET"])]
    public function drawMultipleCards(Request $request, SessionInterface $session, int $number): JsonResponse
    {
        $deck = $this->getOrCreateDeck($session);

        return $deck->drawCardsFromDeck($number, $session);
    }
    private function getOrCreateDeck(SessionInterface $session): Deck
    {
        return $session->get('deck', new Deck());
    }

    private function drawCardsFromDeck(Deck $deck, int $number, SessionInterface $session): JsonResponse
    {
        $drawnCards = [];

        for ($i = 0; $i < $number; $i++) {
            if (count($deck->getCards()) === 0) {
                return $this->json(['message' => 'No cards left in the deck.'], JsonResponse::HTTP_BAD_REQUEST);
            }

            $drawnCard = $deck->getOneCard();
            $drawnCards[] = [
                'color' => $drawnCard->getColor(),
                'value' => $drawnCard->getValue(),
            ];
        }

        $this->updateSessionDeck($deck, $session);

        return $this->json([
            'drawn_cards' => $drawnCards,
            'remaining_cards' => count($deck->getCards()),
        ]);
    }

    private function updateSessionDeck(Deck $deck, SessionInterface $session): void
    {
        $session->set('deck', $deck);
    }
}
