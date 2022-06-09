<?php

namespace mm\tasks;

use pocketmine\scheduler\Task;
use mm\utils\SwordEntity;

class DespawnSwordEntity extends Task{
    
    public $sword;

    public function __construct(SwordEntity $entity){
        $this->sword = $entity;
    }

    public function onRun(): void{
        if(!$this->sword->isClosed()){
            $this->sword->close();
        }
    }
}
