<?php

namespace App\Controller;

use App\Card\Card;
use App\Card\Deck;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    #[Route("/game", name: "game")]
    public function game(): Response
    {
        $initgameUrl = $this->generateUrl('game_init_get');
        return $this->render('game/game_21.html.twig', [
            'init' => $initgameUrl
        ]);
    }

    private function resetSession(SessionInterface $session): void
    {
        $session->set("card_shuffle", 0);
        $session->set("card_round", 0);
        $session->set("card_total", 0);
        $session->set("drawn_cards", []);
    }



    #[Route("/game/init", name: "game_init_get", methods: ['GET'])]
    public function init(SessionInterface $session): Response
    {
        $this->resetSession($session);

        return $this->render('game/init.html.twig');
    }


    #[Route("/game/init", name: "game_init_post", methods: ['POST'])]
    public function initGame(
        Request $request,
        SessionInterface $session
    ): Response {
        $numCard = $request->request->get('num_cards');


        $this->resetSession($session);

        $session->set("card_shuffle", $numCard);
        $session->set("card_round", 0);
        $session->set("card_total", 0);
        $session->clear();

        return $this->redirectToRoute('game_play');
    }
    #[Route("/game/play", name: "game_play", methods: ['GET', 'POST'])]
    public function playGame(
        //Request $request,
        SessionInterface $session
    ): Response {
        $deck = new Deck();
        //$selectedAceValue = $session->get('selectedAceValue', 14);

        //$drawnCard = $session->get('drawnCard');

        $drawnCards = $session->get("drawn_cards", []);

        if (!empty($drawnCards) && is_array($drawnCards)) {
            if ($drawnCards[0]->getValue() === 'Ess') {
                $sessionAceValue = $session->get('selectedAceValue', 14);
                $integerSessionValue = filter_var($sessionAceValue, FILTER_SANITIZE_NUMBER_INT);
                $integerSessionValue = intval($integerSessionValue);

                if ($integerSessionValue === 1) {
                    $drawnCards[count($drawnCards) - 1] = $deck->getOneCard();
                    //$totalValue = $deck->calculateAceValues($integerSessionValue, $drawnCards);
                } else {
                    //$selectedAceValue = $request->request->get('selectedAceValue', 14);
                    $drawnCards[count($drawnCards) - 1] = $deck->getOneCard();
                    //$totalValue = $deck->calculateAceValue($integerSessionValue);
                }
            } else {
                $drawnCards[count($drawnCards) - 1] = $deck->getOneCard();
                $totalValue = $deck->calculateTotalValue($drawnCards);
            }
        } else {
            if (is_array($drawnCards)) {
                $drawnCards[] = $deck->getOneCard();
                $totalValue = $deck->calculateTotalValue($drawnCards);
            }
        }
        $session->set("drawn_cards", $drawnCards);

        $cardData = [];

        if (is_array($drawnCards) && count($drawnCards) > 0 && is_object($drawnCards[0]) && method_exists($drawnCards[0], 'getColor') && method_exists($drawnCards[0], 'getValue')) {
            $cardData['color'] = $drawnCards[0]->getColor();
            $cardData['value'] = $drawnCards[0]->getValue();
        }


        $session->set("cardData", $cardData);

        $cardRound = $session->get("cardRound", 0) + 1;
        $session->set("cardRound", $cardRound);

        $totalValue = $deck->calculateTotalValue($drawnCards);


        $totalCards = $session->get("totalValue", 0) + $totalValue;



        $gameOver = "";
        if ($totalCards > 21) {
            $gameOver = "Över 21 poäng, du har blivit tjock och förlorade denna omgång";
            $deck->gameOverPlayer();
        }

        $session->set("totalValue", $totalCards);

        $session->set("resultPlayer", [
            'cardData' => $cardData,
            'gameOver' => $gameOver,
            'totalValue' => $totalCards,
        ]);

        $data = [
            "drawUrl" => $this->generateUrl('draw_card'),
            "saveUrl" => $this->generateUrl('game_save'),
            "restartUrl" => $this->generateUrl('game_init_get'),
            "cardData" => $cardData,
            "gameOver" => $gameOver,
            "cards" => $session->get("card_shuffle"),
            "cardRound" => $cardRound,
            "totalValue" => $totalCards,
        ];
        return $this->render('game/play.html.twig', $data);
    }

    #[Route("/game/bank_play", name: "bank_play", methods: ['GET'])]
    public function playBankGame(
        SessionInterface $session
    ): Response {


        $deck = new Deck();

        $drawnCardsBank = $session->get("drawn_cards_bank", []);

        if (!empty($drawnCardsBank) && is_array($drawnCardsBank)) {
            $drawnCardsBank[] = $deck->getOneCard();
        } else {
            $drawnCardsBank = [$deck->getOneCard()];
        }

        $session->set("drawn_cards_bank", $drawnCardsBank);

        $totalValueBank = $deck->calculateTotalValue($drawnCardsBank);
        $cardDataBank = [
            'color' => $drawnCardsBank[0]->getColor(),
            'value' => $drawnCardsBank[0]->getValue(),
        ];

        $session->set("cardDataBank", $cardDataBank);

        $cardRoundBank = $session->get("cardRoundBank", 0) + 1;
        $session->set("cardRoundBank", $cardRoundBank);

        $totalCardsBank = $session->get("totalValueBank", 0) + $totalValueBank;

        $gameOver = "";
        if ($totalCardsBank > 21) {
            $gameOver = "Över 21 poäng, du har blivit tjock och förlorade denna omgång";
            $deck->gameOverPlayer();
        }

        $session->set("totalValueBank", $totalCardsBank);

        $session->set("resultBank", [
            'cardDataBank' => $cardDataBank,
            'gameOver' => $gameOver,
            'totalValueBank' => $totalCardsBank,
        ]);

        $data = [
            "drawUrlBank" => $this->generateUrl('draw_card_bank'),
            "saveUrl" => $this->generateUrl('game_save'),
            "restartUrl" => $this->generateUrl('game_init_get'),
            "resultUrl" => $this->generateUrl('compare_cards'),
            "cardDataBank" => $cardDataBank,
            "gameOver" => $gameOver,
            "cards" => $session->get("card_shuffle"),
            "cardRoundBank" => $cardRoundBank,
            "totalValueBank" => $totalCardsBank,
        ];

        return $this->render('game/bank_play.html.twig', $data);
    }

    #[Route("/game/draw", name: "draw_card", methods: ['POST'])]
    public function drawCard(Request $request, SessionInterface $session): Response
    {
        $selectedAceValue = $request->request->get('selectedAceValue', 14);
        $session->set('selectedAceValue', $selectedAceValue);

        $drawnCard = $request->request->get('drawn_cards');
        $session->set('drawnCard', $drawnCard);

        //$totalValue = $session->get("totalValue");

        return $this->redirectToRoute('game_play');
    }

    #[Route("/game/draw_bank", name: "draw_card_bank", methods: ['GET', 'POST'])]
    public function drawCardBank(
        Request $request,
        SessionInterface $session
    ): Response {

        $drawnCard = json_decode($request->getContent(), true);

        if ($drawnCard instanceof Card) {
            $drawnCardsBank = $session->get("drawn_cards_bank", []);

            if (!is_array($drawnCardsBank)) {
                $drawnCardsBank = [];
            }

            $drawnCardsBank[] = $drawnCard;

            $session->set("drawn_cards_bank", $drawnCardsBank);
        }

        return $this->redirectToRoute('bank_play');
    }




    #[Route("/game/save", name: "game_save", methods: ['POST'])]
    public function saveGame(
        SessionInterface $session
    ): Response {
        $totalValue = $session->get("totalValue", 0);
        $session->set("savedTotalValue", $totalValue);


        $session->set("drawn_cards", []);


        $session->set("totalValue", 0);

        return $this->redirectToRoute('bank_play');
    }

    #[Route("/game/compare", name: "compare_cards", methods: ['GET'])]
    public function compareCards(
        SessionInterface $session
    ): Response {
        $deck = new Deck();

        $resultPlayer = $session->get("resultPlayer", []);
        $resultBank = $session->get("resultBank", []);

        $winner = null;

        if (is_array($resultPlayer) && is_array($resultBank)) {
            $winner = $deck->compareCardsForResult($resultPlayer, $resultBank);
        }

        $session->set("winner", $winner);

        return $this->render('game/compare_cards.html.twig', [
            'winner' => $winner,
            "restartUrl" => $this->generateUrl('game_init_get'),
            'drawnCardsPlayer' => $resultPlayer,
            'drawnCardsBank' => $resultBank,
        ]);
    }


    #[Route("/api/game", name:"api_game", methods: ['GET'])]
    public function apiGame(SessionInterface $session): JsonResponse
    {
        $totalValue = $session->get("totalValue", 0);
        $cardData = $session->get("cardData", []);
        $totalValueBank = $session->get("totalValueBank", 0);
        $cardDataBank = $session->get("cardDataBank", []);
        $winner = $session->get("winner", null);

        $apiData = [
            'total_value' => $totalValue,
            'card_data' => $cardData,
            'total_value_bank' => $totalValueBank,
            'card_data_bank' => $cardDataBank,
            'result_player' => $session->get("resultPlayer", []),
            'result_bank' => $session->get("resultBank", []),
            'winner' => $winner,
        ];

        $jsonResponse = new JsonResponse($apiData);
        $jsonResponse->setEncodingOptions($jsonResponse->getEncodingOptions() | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return $jsonResponse;
    }

    #[Route("/game/doc", name:"game_doc")]
    public function gameDoc(): Response
    {
        return $this->render('game/doc/game_doc.html.twig');

    }
}
