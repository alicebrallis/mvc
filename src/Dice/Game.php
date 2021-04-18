<?php

declare(strict_types=1);

namespace aloo20\Dice;

use function Mos\Functions\{
    redirectTo,
    renderView,
    sendResponse,
    url
};

use aloo20\Dice\Dice;
use aloo20\Dice\Dicehand;
use aloo20\Dice\DiceGraphic;
use aloo20\Dice\Game21;

/**
 * Class Game.
 */

class Game
{
    public function playGame(): void
    {
        $data = [
            "header" => "Dice",
            "message" => "Hey!",
        ];
        $die = new Dice();
        $die->roll();

        $dicehand = new Dicehand();
        $dicehand->roll();

        $data["dieLastRoll"] = $die->getLastRoll();
        $data["diehandRoll"] = $dicehand->getLastRoll();
        $dicehand->roll();

        $dice = new DiceGraphic();
        $data["hand"] = [];
        for ($i = 0; $i < 3; $i++) {
            $dice->roll();
            array_push($data["hand"], $dice->graphic());
        }


        $dice = new Game21();
        $data["output"] = [];
        for ($i = 0; $i < 1; $i++) {
            $dice->roll();
            array_push($data["output"], $dice->game21());
        }

        $body = renderView("layout/dice.php", $data);
        sendResponse($body);
    }
}
