<?php

namespace mm\utils;

use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStack;
use pocketmine\player\Player;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;
use pocketmine\item\Item;

class SwordEntity extends Entity{

    public static function getNetworkTypeId(): string
    {
        return self::NETWORK_ID;
    }

    protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo($this->height, $this->width);
    }

    public const NETWORK_ID = "minecraft:armor_stand";

    public $width = 2.0;
    public $height = 2.0;
    
    protected function sendSpawnPacket(Player $player) : void{
        parent::sendSpawnPacket($player);
        $pk = new MobEquipmentPacket();
        $pk->actorRuntimeId = $this->getId();
        $pk->item = ItemStackWrapper::legacy(new ItemStack(ItemIds::IRON_SWORD, 0, 1, 0,null, null, null));
        $pk->inventorySlot = 0;
        $pk->hotbarSlot = 0;
        $player->sendData($this->getViewers(), [$pk]);
    }

    public function setPose() : void{
        $this->getNetworkProperties()->setInt(EntityMetadataProperties::ARMOR_STAND_POSE_INDEX, 8);
    }
}
