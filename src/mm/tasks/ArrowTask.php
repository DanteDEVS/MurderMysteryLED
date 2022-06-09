<?php

namespace mm\tasks;

use mm\game\Game;
use pocketmine\scheduler\Task;

class ArrowTask extends Task{

    public function __construct(Game $plugin){
        $this->plugin = $plugin;
    }

    public function onRun(int $ct){
        $this->plugin->giveArrow();
    }
}