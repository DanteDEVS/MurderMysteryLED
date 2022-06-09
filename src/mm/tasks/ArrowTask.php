<?php

namespace mm\tasks;

use mm\game\Game;
use pocketmine\scheduler\Task;

class ArrowTask extends Task{
    
    public $plugin;

    public function __construct(Game $plugin){
        $this->plugin = $plugin;
    }

    public function onRun(){
        $this->plugin->giveArrow();
    }
}
