<?php

namespace mm\utils;

use pocketmine\player\Player;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\entity\projectile\Throwable;

class SwordEntity extends Throwable{
	
   public $networkProperties;

   public static function getNetworkTypeId() : string{ return EntityIds::ARMOR_STAND; }
    
   protected function getInitialSizeInfo() : EntitySizeInfo{ return new EntitySizeInfo(1.5, 1.5); }	
	
    protected function sendSpawnPacket(Player $player) : void{
        parent::sendSpawnPacket($player);
        $pk = new MobEquipmentPacket();
        $pk->actorRuntimeId = $this->getId();
        $pk->item = ItemStackWrapper::legacy(new Item(ItemIds::IRON_SWORD));
        $pk->inventorySlot = 0;
        $pk->hotbarSlot = 0;
        $player->getNetworkSession()->sendDataPacket($pk);
    }

    public function setPose() : void{
        $this->networkProperties->setInt(EntityMetadataProperties::ARMOR_STAND_POSE_INDEX, 8);
    }
}
