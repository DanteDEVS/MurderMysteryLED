<?php

namespace mm\game;

use pocketmine\world\{
    World,
    Position
};
use pocketmine\world\sound\{BlazeShootSound, ClickSound, PopSound};
use pocketmine\scheduler\Task;
use pocketmine\block\tile\Sign;
use pocketmine\player\GameMode;

use mm\utils\Vector;

class GameTask extends Task{

    protected $plugin;

    public $startTime = 31;
    public $gameTime = 5 * 60;
    public $restartTime = 5;

    public $restartData = [];

    public $phase = 0;
    
    public $map = null;

    public $players = [];
    
    public function __construct(Game $plugin){
        $this->plugin = $plugin;
    }

    public function onRun(): void{
        $this->reloadSign();

        if($this->plugin->setup) return;

        $this->plugin->scoreboard();
        switch($this->plugin->phase){
            case Game::PHASE_LOBBY:
                if(count($this->plugin->players) >= 2){
                    switch($this->startTime){
                        case 30:
                            foreach($this->plugin->players as $player){
                                $player->sendMessage("§eThe game starts in " . $this->startTime . " seconds!");
                            }
                        break;

                        case 20:
                            foreach($this->plugin->players as $player){
                                $player->sendMessage("§eThe game starts in " . $this->startTime . " seconds!");
                            }
                        break;

                        case 10:
                            foreach($this->plugin->players as $player){
                                $player->sendMessage("§eThe game starts in §6" . $this->startTime . "§e seconds!");
                                $player->sendTitle("§a10");
                                $this->plugin->setItem(0, 4, $player);
                                $this->plugin->map->addSound(new PopSound($player->getPosition()->asVector3()));
                            }
                        break;
                    }

                    if($this->startTime < 6 && $this->startTime > 0){
                        foreach($this->plugin->players as $player){
                            $this->plugin->map->addSound(new ClickSound($player->getPosition()->asVector3()));
                            $player->sendMessage("§eThe game starts in §c" . $this->startTime . "§e seconds!");
                            $player->sendTitle("§c" . $this->startTime);
                        }
                    }

                    if($this->startTime == 0){
                        $this->plugin->startGame();
                        foreach($this->plugin->players as $player){
                            $this->plugin->map->addSound(new BlazeShootSound());
                        }
                    }
                    $this->startTime--;
                } else {
                    $this->startTime = 31;
                }
            break;

            case Game::PHASE_GAME:
                if($this->gameTime > 285 && $this->gameTime < 291){
                    foreach($this->plugin->players as $player){
                        $player->sendMessage("§eThe Murderer gets their sword in §c" . ($this->gameTime - 285) . "§e seconds");
                        $this->plugin->map->addSound(new ClickSound($player->getPosition()->asVector3()));
                    }
                }
                switch($this->gameTime){
                    case 285:
                        foreach($this->plugin->players as $player){
                            $player->sendMessage("§eThe Murderer has received their sword");
                            $this->plugin->giveItems();
                            $player->sendMessage("§eThe game ends in §c04:45 §eminutes!");
                        }
                    break;

                    case 60 * 4:
                        foreach($this->plugin->players as $player){
                            $player->sendMessage("§eThe game ends in §c04:00 §eminutes!");
                        }
                    break;

                    case 60 * 3:
                        foreach($this->plugin->players as $player){
                            $player->sendMessage("§eThe game ends in §c03:00 §eminutes!");
                        }
                    break;

                    case 60 * 2:
                        foreach($this->plugin->players as $player){
                            $player->sendMessage("§eThe game ends in §c02:00 §eminutes!");
                        }
                    break;

                    case 60:
                        foreach($this->plugin->players as $player){
                            $player->sendMessage("§eThe game ends in §c01:00 §eminute!");
                            $player->sendTitle("§c60 §eseconds left!", "§eAfter 60 seconds the murderer will lose!");
                            if($player !== $this->plugin->getMurderer()){
                                $player->sendMessage("§cWatch out! §eThe murderer got a compass!");
                            } else {
                                $player->sendMessage("§cYou got a compass! §eThe compass points to the nearest player!");
                            }
                            $this->plugin->setItem(345, 4, $player);
                        }
                    break;

                    case 0:
                        $murderer = $this->plugin->getMurderer();
                        if($this->plugin->isPlayer($murderer)){
                            $murderer->sendTitle("§cYOU LOSE!", "§6You ran out of time!");
                            $this->plugin->changeInv[$murderer->getName()] = $murderer;
                            $murderer->getInventory()->clearAll();
                            $murderer->getArmorInventory()->clearAll();
                            $murderer->getCursorInventory()->clearAll();
                            unset($this->plugin->changeInv[$murderer->getName()]);
                            $murderer->getEffects()->clear();
                            $murderer->setGamemode(GameMode::SPECTATOR());
                            $this->plugin->disconnectPlayer($murderer);
                        }
                        $this->plugin->innocentWin();
                    break;
                }
                $this->plugin->checkPlayers();
                $this->gameTime--;
            break;

            case Game::PHASE_RESTART:
                switch($this->restartTime){
                    case 0:
                        foreach($this->plugin->players as $player){
                            if($this->plugin->isPlayer($player)){
                                $this->plugin->removeFromGame($player);
                                $player->setNameTagAlwaysVisible(true);
                                $player->setNameTagVisible();
                            }
                        }
                        foreach($this->plugin->spectators as $spectator){
                            if($this->plugin->isPlayer($spectator)){
                                $this->plugin->removeFromGame($spectator);
                                $spectator->setNameTagAlwaysVisible(true);
                                $spectator->setNameTagVisible();
                                $this->plugin->unsetSpectator($spectator);
                            }
                        }
                        $this->plugin->loadGame(true);
                        $this->reloadTimer();
                    break;
                }
                $this->restartTime--;
            break;
        }
    }

    public function reloadSign(){
        if(!is_array($this->plugin->data["joinsign"]) or empty($this->plugin->data["joinsign"])){
            return;
        }

        $signPos = Position::fromObject(Vector::fromString($this->plugin->data["joinsign"][0]), $this->plugin->plugin->getServer()->getWorldManager()->getWorldByName($this->plugin->data["joinsign"][1]));

        if(!$signPos->getWorld() instanceof World){
            return;
        }

        $signText = [
            "§eMurder Mystery",
            "§b- §7| §7[§b-§7/§b-§7]",
            "§cNot available",
            "§cPlease wait..."
        ];

        if($signPos->getWorld()->getTile($signPos) === null){
            return;
        }

        if($this->plugin->setup){
            $sign = $signPos->getWorld()->getTile($signPos);
            $sign->setText($signText[0]);
            return;
        }

        $signText[1] = "§b{$this->plugin->map->getFolderName()} §7| §7[§b" . count($this->plugin->players) . "§7/§b16§7]";

        switch($this->plugin->phase){
            case Game::PHASE_LOBBY:
                if(count($this->plugin->players) >= 16){
                    $signText[2] = "§cFull";
                    $signText[3] = "";
                } else {
                    $signText[2] = "§aTap to join";
                    $signText[3] = "";
                }
            break;

            case Game::PHASE_GAME:
                $signText[2] = "§cAlready started";
                $signText[3] = "";
            break;

            case Game::PHASE_RESTART:
                $signText[2] = "§6Restarting...";
                $signText[3] = "";
            break;
        }

        $sign = $signPos->getWorld()->getTile($signPos);
        if($sign instanceof Sign){
            $sign->setText($signText[0]);
        }
    }

    public function reloadTimer(){
        $this->startTime = 31;
        $this->gameTime = 5 * 60;
        $this->restartTime = 5;
    }
}
