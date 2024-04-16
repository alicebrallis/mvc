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
        $booksUrl = $this->generateUrl('api_library_books');
        $isbn = '978014143966';

        $booksUrlIsbn = $this->generateUrl('api_library_book_by_isbn', [
            'isbn' => $isbn,
        ]);

        return $this->render('card_game_json/card_json.html.twig', [
            'quote_url' => $quoteUrl,
            'deck_url' => $deckUrl,
            'deck_shuffle' => $deckShuffle,
            'deck_draw' => $deckDraw,
            'books_url' => $booksUrl,
            'books_url_isbn' => $booksUrlIsbn,
        ]);
    }

    #[Route("/api/deck", name: "api_deck_get", methods: ["GET"])]
    public function getSortedApiDeck(SessionInterface $session): JsonResponse
    {
        $deck = $session->get('deck', null);

        if (!$deck instanceof Deck) {
            $deck = new Deck();
            $session->set('deck', $deck);
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
    public function shuffleApiDeck(SessionInterface $session): JsonResponse
    {
        $deck = $session->get('deck', null);
        if ($deck === null) {
            $deck = new Deck();
        }        
    
        if ($deck instanceof Deck) {
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
    
        return new JsonResponse(['error' => 'Failed to shuffle deck'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
    


    #[Route("/api/deck/draw", name: "draw_one_card", methods: ["POST", "GET"])]
    public function drawOneCard(SessionInterface $session): JsonResponse
    {
        $deck = $this->getOrCreateDeck($session);

        return $deck->drawCardsFromDeck(1, $session);
    }

    #[Route("/api/deck/draw/{number}", name: "draw_multiple_cards", methods: ["POST", "GET"])]
    public function drawMultipleCards(SessionInterface $session, int $number): JsonResponse
    {
        $deck = $this->getOrCreateDeck($session);

        return $deck->drawCardsFromDeck($number, $session);
    }
    private function getOrCreateDeck(SessionInterface $session): Deck
    {
        $deck = $session->get('deck', null);

        if (!$deck instanceof Deck) {
            $deck = new Deck();
            $session->set('deck', $deck);
        }

        return $deck;
    }

}
