<?php

namespace mm\provider;

use mm\game\Game;
use mm\MurderMystery;
use pocketmine\utils\Config;

class Provider{

    public $plugin;
    public $cfg;

    public function __construct(MurderMystery $plugin){
        $this->plugin = $plugin;
        $this->insertData();
    }

    public function insertData(){
        if(!is_dir($this->getDataFolder())){
            @mkdir($this->getDataFolder());
        }

        if(!is_dir($this->getDataFolder() . "games")){
            @mkdir($this->getDataFolder() . "games");
        }
    }

    public function loadGames(){
        foreach(glob($this->getDataFolder() . "games" . DIRECTORY_SEPARATOR . "*.yml") as $game){
            $cfg = new Config($game, Config::YAML);
            $this->plugin->games[basename($game, ".yml")] = new Game($this->plugin, $cfg->getAll(false));
        }
    }

    public function saveGames(){
        foreach($this->plugin->games as $file => $game){
            $cfg = new Config($this->getDataFolder() . "games" . DIRECTORY_SEPARATOR . $file . ".yml", Config::YAML);
            $cfg->setAll($game->data);
            $cfg->save();
        }
    }

    private function getDataFolder(){
        return $this->plugin->getDataFolder();
    }
}