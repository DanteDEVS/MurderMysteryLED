<?php
declare(strict_types=1);

namespace mm\utils;

#include <rules/DataPacket.h>

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\ClientboundPacket;
use pocketmine\network\mcpe\protocol\PacketHandlerInterface;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\utils\Limits;

class SpawnPositionPacket extends DataPacket implements ClientboundPacket{
	public const NETWORK_ID = ProtocolInfo::SET_SPAWN_POSITION_PACKET;

	public const TYPE_PLAYER_SPAWN = 0;
	public const TYPE_WORLD_SPAWN = 1;

	public int $spawnType;
	public int $x;
	public int $y;
	public int $z;
	public int $dimension;
	public int $x2;
	public int $y2;
	public int $z2;
	
	public BlockPosition $spawnPosition;
	public BlockPosition $causingBlockPosition;
	
	public function encodePayload(PacketSerializer $out): void{
		$out->putVarInt($this->spawnType);
		$out->putBlockPosition($this->spawnPosition);
		$out->putVarInt($this->dimension);
		$out->putBlockPosition($this->causingBlockPosition);
	}
	
	public function decodePayload(PacketSerializer $in): void{
		$this->spawnType = $in->getVarInt();
		$this->spawnPosition = $in->getBlockPosition();
		$this->dimension = $in->getVarInt();
		$this->causingBlockPosition = $in->getBlockPosition();
	}

	public function handle(PacketHandlerInterface $handler) : bool{
		return $handler->handleSetSpawnPosition($this);
	}
}
