<?php

namespace mm\tasks;

use pocketmine\scheduler\Task;
use pocketmine\item\Item;
use mm\game\Game;

class CooldownTask extends Task{

    public function __construct(Game $plugin){
        $this->plugin = $plugin;
    }

    public function onRun(int $ct){
        foreach($this->plugin->players as $player){
            if(isset($this->plugin->cooldown[$player->getName()])){
                $item = $player->getInventory()->getItemInHand()->getId();
                if($item == \pocketmine\item\ItemIds::BOW && $this->plugin->getDetective() === $player or $item == \pocketmine\item\ItemIds::IRON_SWORD){
                    $time = (int) $this->plugin->cooldown[$player->getName()];
                    $timeLeft = $time - microtime(true);
                    if($time > microtime(true)){
                        $player->sendTip("§bCooldown: §7[" . $this->getCharacterColor($timeLeft) . "§7] §6" . round($timeLeft, 1) . " sec.");
                    } else {
                        unset($this->plugin->cooldown[$player->getName()]);
                    }
                }
            }
        }
    }

    public function getCharacterColor($time){
        $time = round($time);
        if($time == 7){
            return "§c||||||||||||||";
        }
        if($time == 6.5){
            return "§c|||||||||||||§a|";
        }
        if($time == 6){
            return "§c||||||||||||§a||";
        }
        if($time == 5.5){
            return "§c|||||||||||§a|||";
        }
        if($time == 5){
            return "§c||||||||||§a||||";
        }
        if($time == 4.5){
            return "§c|||||||||§a|||||";
        }
        if($time == 4){
            return "§c||||||||§a||||||";
        }
        if($time == 3.5){
            return "§c|||||||§a|||||||";
        }
        if($time == 3){
            return "§c||||||§a||||||||";
        }
        if($time == 2.5){
            return "§c|||||§a|||||||||";
        }
        if($time == 2){
            return "§c||||§a||||||||||";
        }
        if($time == 1.5){
            return "§c|||§a|||||||||||";
        }
        if($time == 1){
            return "§c||§a||||||||||||";
        }
        if($time == 0.5){
            return "§c|§a|||||||||||||";
        }
        if($time == 0){
            return "§a||||||||||||||";
        }
    }
}