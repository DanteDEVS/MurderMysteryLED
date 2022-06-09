<?php

namespace mm\tasks;

use pocketmine\scheduler\Task;
use mm\utils\SwordEntity;

class DespawnSwordEntity extends Task{
    
    public $entity;

    public function __construct(SwordEntity $entity){
        $this->sword = $entity;
    }

    public function onRun(){
        if(!$this->sword->isClosed()){
            $this->sword->close();
        }
    }
}
