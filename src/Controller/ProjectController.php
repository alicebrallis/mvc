<?php

namespace App\Controller;
use App\Card\Card;
use App\Card\Deck;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController {
    #[Route("/proj", name: "proj")]
    public function projectView(): Response
    {
        return $this->render('proj/project.html.twig');
    }

    #[Route("/proj/about", name: "proj/about")]
    public function aboutView(): Response
    {
        return $this->render('proj/about.html.twig');
    }

    private function resetSession(SessionInterface $session): void
    {
        //$session->set("handValue", 0);
        $session->set("handCount", 0);
        $session->set("player_name", "");
        $session->set("betAmount", 0);
        $session->set("drawn_cards", []);
        $session->set("drawn_banker_cards", []);
        $session->set("bankAccount", 100);
    }

    #[Route("/proj/game", name: "proj/game", methods: ['GET'])]
    public function gameView(SessionInterface $session): Response
    {
        $this->resetSession($session);

        return $this->render('proj/game/start_game.html.twig', [
        ]);
    }

#[Route("/proj/game", name: "proj/game/init", methods: ['POST'])]
public function initCallback(
    Request $request,
    SessionInterface $session
): Response {
    $playerName = $request->request->get('player_name');
    $session->set("player_name", $playerName);
    $betAmount = $request->request->get('bet_amount');
    $action = $request->request->get('action');

    $deck = new Deck();
    $drawnTwoCards = $deck->drawTwoCards();

    $drawnCards = $session->get("drawn_cards", []);
    $drawnCards = array_merge($drawnCards, $drawnTwoCards);
    $session->set("drawn_cards", $drawnCards);

    $handValue = $deck->calculateHandValue($drawnCards);
    $session->set('handValue', $handValue);

    $session->set('betAmount', $betAmount);

    return $this->redirectToRoute('proj_game_play');
}

#[Route("proj/game/play", name: "proj_game_play", methods: ['GET', 'POST'])]
public function playBlackJack(
    SessionInterface $session
): Response {

    // Bankirens drag
    $drawnBankerCards = $session->get("drawn_banker_cards", []);
    $bankerHandValue = $session->get("bankerHandValue", 0);
    $deck = new Deck();

    while ($drawnBankerCards === []) {
        $drawnBankerCards = $deck->drawTwoCards();
        $bankerHandValue = $deck->calculateHandValue($drawnBankerCards);

    }

    $session->set('drawn_banker_cards', $drawnBankerCards);
    $session->set('bankerHandValue', $bankerHandValue);

    $drawnCards = $session->get("drawn_cards", []);
    $bankAccount = $session->get("bankAccount", 100);
    $handCount = $session->get("handCount");
    $handValue = $session->get("handValue");
    $gameOver = $session->get("gameOver");



    $data = [
        "drawnBankerCards" => $drawnBankerCards,
        "bankerHandValue" => $bankerHandValue,
        "handValue" => $handValue,
        "handCount" => $handCount,
        "drawnCards" => $drawnCards,
        "handData" => $drawnCards,
        "cardData" => $session->get("cardData"),
        "playerName" => $session->get("player_name"),
        "betAmount" => $session->get("betAmount"),
        "gameOver" => $gameOver,
        "bankAccount" => $bankAccount
    ];

    return $this->render('proj/game/play.html.twig', ['data' => $data]);
}

    #[Route("/proj/game/move", name: "make_move", methods: ['POST'])]
    public function makeMove(
        Request $request,
        SessionInterface $session
    ): Response {
        $drawnCards = $session->get("drawn_cards", []);
        $action = $request->request->get('action');
        $betAmount = (int) $request->request->get('betAmount');
        
        if ($action === 'hit') {
            $deck = new Deck();
            $handCount = (int) $request->request->get('handCount');

            $currentCardCount = count($drawnCards);
            $cardsToDraw = min($handCount, 3) - $currentCardCount;
        
            switch ($handCount) {
                case 1:
                    $currentCardCount = count($drawnCards);
                    $drawnCards[] = $deck->getOneCard();      
                    break;
                case 2:
                    $currentCardCount = count($drawnCards); 
                    $newCards = $deck->getTwoCards();
                    $drawnCards = array_merge($drawnCards, $newCards);
                    break;
                case 3:
                    $currentCardCount = count($drawnCards); 
                    $newThreeCards = $deck->getThreeCards(); 
                    $drawnCards = array_merge($drawnCards, $newThreeCards);
                    break;
            }

            $currentCardCount += $cardsToDraw;
        
            $handValue = $deck->calculateHandValue($drawnCards);

            $gameOver = "";

            if($handValue > 21) {
                $gameOver = "Över 21 poäng, du har blivit tjock och förlorade denna omgång";
                $deck->gameOverPlayer();
            }
            $session->set('handValue', $handValue);
            $session->set('handCount', $handCount);
            $session->set('gameOver', $gameOver);
        }

        $deck = new Deck();


        $bankerHandValue = $session->get("bankerHandValue", 0);
        $drawnBankerCards = $session->get('drawn_banker_cards', []);


        if ($action === 'stand') { 
            $handValue = $session->get('handValue', 0);
            $bankerHandValue = $session->get("bankerHandValue", 0);
            $resultForBank = $bankerHandValue;
            $result = $deck->determineWinner($handValue, $resultForBank);

            $betAmount = $session->get('betAmount', 0);
            $bankAccount = $session->get("bankAccount", 100);
            $playerName = $session->get("player_name");

            return $this->render('proj/game/result_play.html.twig', [
                'playerCards' => $drawnCards,
                'playerHandValue' => $handValue,
                'bankerCards' => $drawnBankerCards,
                'bankerHandValue' => $resultForBank,
                'result' => $result,
                'betAmount' => $betAmount,
                'bankAccount' => $bankAccount,
                'player_name' => $playerName
            ]);
        }

        if ($action === 'invest') {
            $bankAccount = $session->get("bankAccount", 100);
            if ($betAmount > 0 && $betAmount <= $bankAccount) {
                $bankAccount -= $betAmount;
                $session->set("bankAccount", $bankAccount);
                $session->set("betAmount", $betAmount);
                
            }
            $message = "Nu har du satsat ditt valda belopp. Klicka på 'Stanna' för att avsluta din omgång och så är det bankirens tur.";
            $this->addFlash('success', $message);
        
            return $this->redirectToRoute('proj_game_play');

         }

        $session->set("drawn_cards", $drawnCards);
        
        $cardData = [];
        foreach ($drawnCards as $hand) {
            foreach ($hand as $card) {
                if (is_object($card) && method_exists($card, 'getColor') && method_exists($card, 'getValue')) {
                    $cardData[] = [
                        'color' => $card->getColor(),
                        'value' => $card->getValue()
                    ];
                }
            }
        }
        $session->set("cardData", $cardData);
        $session->set("handData", $drawnCards);
        
        return $this->redirectToRoute('proj_game_play');
    }
}