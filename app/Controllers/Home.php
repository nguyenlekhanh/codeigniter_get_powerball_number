<?php

namespace App\Controllers;
require_once __DIR__ . '/../../vendor/autoload.php'; 
//require './vendor/autoload.php'; 

use PHPHtmlParser\Dom;

class Home extends BaseController
{
    public function index(): string
    {

        $dom = new Dom;
        $dom->loadFromFile('Previous Results _ Powerball.html');
        $gameBallGroup = $dom->find('.game-ball-group');

        $allPowerBallNumber = '';
        $allPowerBallSpecialNumber = '';
        foreach ($gameBallGroup as $itemBall)
        {
            // get the class attr
            $itemPowerballs = $itemBall->find('.item-powerball');
            
            $count = 1;

            foreach ($itemPowerballs as $powerballNumberEle) {
                $powerballNumber = $powerballNumberEle->text;
                if($count <= 5) {
                    $allPowerBallNumber .= ",$powerballNumber";
                } else {
                    $allPowerBallSpecialNumber .= ",$powerballNumber";
                }
                $count++;
            }
        }
        echo $allPowerBallNumber, '<br />', $allPowerBallSpecialNumber;

        exit();
        return view('welcome_message');
    }
}
