<?php

namespace mm\utils;

use mm\MurderMystery;

class GameChooser{

    public $plugin;

    public function __construct(MurderMystery $plugin){
        $this->plugin = $plugin;
    }

    public function getRandomGame(){
        $availableGames = [];
        foreach($this->plugin->games as $index => $game){
            $availableGames[$index] = $game;
        }

        foreach($availableGames as $index => $game){
            if($game->phase !== 0 || $game->setup){
                unset($availableGames[$index]);
            }
        }

        $gamesByPlayers = [];
        foreach($availableGames as $index => $game){
            $gamesByPlayers[$index] = count($game->players);
        }

        arsort($gamesByPlayers);
        $top = -1;
        $availableGames = [];

        foreach($gamesByPlayers as $index => $players){
            if($top == -1){
                $top = $players;
                $availableGames[] = $index;
            } else {
                if($top == $players){
                    $availableGames[] = $index;
                }
            }
        }

        if(empty($availableGames)){
            return null;
        }

        return $this->plugin->games[$availableGames[array_rand($availableGames, 1)]];
    }
}