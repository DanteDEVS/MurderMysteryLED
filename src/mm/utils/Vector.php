<?php

namespace mm\utils;

use pocketmine\math\Vector3;

class Vector extends Vector3{

    public function __toString(){
        return "$this->x,$this->y,$this->z";
    }

    public static function fromString(string $string){
        return new Vector((int)explode(",", $string)[0], (int)explode(",", $string)[1], (int)explode(",", $string)[2]);
    }
}