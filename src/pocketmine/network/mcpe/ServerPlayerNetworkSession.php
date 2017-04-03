<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

declare(strict_types=1);

namespace pocketmine\network\mcpe;

use pocketmine\entity\SkinData;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\Timings;
use pocketmine\math\Vector3;
use pocketmine\network\AdvancedSourceInterface;
use pocketmine\network\LoginFailedException;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\network\mcpe\protocol\AddHangingEntityPacket;
use pocketmine\network\mcpe\protocol\AddItemEntityPacket;
use pocketmine\network\mcpe\protocol\AddItemPacket;
use pocketmine\network\mcpe\protocol\AddPaintingPacket;
use pocketmine\network\mcpe\protocol\AddPlayerPacket;
use pocketmine\network\mcpe\protocol\AdventureSettingsPacket;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\BatchPacket;
use pocketmine\network\mcpe\protocol\BlockEntityDataPacket;
use pocketmine\network\mcpe\protocol\BlockEventPacket;
use pocketmine\network\mcpe\protocol\BlockPickRequestPacket;
use pocketmine\network\mcpe\protocol\ChangeDimensionPacket;
use pocketmine\network\mcpe\protocol\ChunkRadiusUpdatedPacket;
use pocketmine\network\mcpe\protocol\ClientboundMapItemDataPacket;
use pocketmine\network\mcpe\protocol\ClientToServerHandshakePacket;
use pocketmine\network\mcpe\protocol\CommandBlockUpdatePacket;
use pocketmine\network\mcpe\protocol\CommandStepPacket;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;
use pocketmine\network\mcpe\protocol\ContainerSetContentPacket;
use pocketmine\network\mcpe\protocol\ContainerSetDataPacket;
use pocketmine\network\mcpe\protocol\ContainerSetSlotPacket;
use pocketmine\network\mcpe\protocol\CraftingDataPacket;
use pocketmine\network\mcpe\protocol\CraftingEventPacket;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\DisconnectPacket;
use pocketmine\network\mcpe\protocol\DropItemPacket;
use pocketmine\network\mcpe\protocol\EntityEventPacket;
use pocketmine\network\mcpe\protocol\ExplodePacket;
use pocketmine\network\mcpe\protocol\FullChunkDataPacket;
use pocketmine\network\mcpe\protocol\HurtArmorPacket;
use pocketmine\network\mcpe\protocol\InteractPacket;
use pocketmine\network\mcpe\protocol\InventoryActionPacket;
use pocketmine\network\mcpe\protocol\ItemFrameDropItemPacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\MapInfoRequestPacket;
use pocketmine\network\mcpe\protocol\MobArmorEquipmentPacket;
use pocketmine\network\mcpe\protocol\MobEffectPacket;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\network\mcpe\protocol\MoveEntityPacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\network\mcpe\protocol\PlayerFallPacket;
use pocketmine\network\mcpe\protocol\PlayerInputPacket;
use pocketmine\network\mcpe\protocol\PlayerListPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\PlayStatusPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\RemoveBlockPacket;
use pocketmine\network\mcpe\protocol\RemoveEntityPacket;
use pocketmine\network\mcpe\protocol\ReplaceItemInSlotPacket;
use pocketmine\network\mcpe\protocol\RequestChunkRadiusPacket;
use pocketmine\network\mcpe\protocol\ResourcePackChunkDataPacket;
use pocketmine\network\mcpe\protocol\ResourcePackChunkRequestPacket;
use pocketmine\network\mcpe\protocol\ResourcePackClientResponsePacket;
use pocketmine\network\mcpe\protocol\ResourcePackDataInfoPacket;
use pocketmine\network\mcpe\protocol\ResourcePacksInfoPacket;
use pocketmine\network\mcpe\protocol\ResourcePackStackPacket;
use pocketmine\network\mcpe\protocol\RespawnPacket;
use pocketmine\network\mcpe\protocol\RiderJumpPacket;
use pocketmine\network\mcpe\protocol\ServerToClientHandshakePacket;
use pocketmine\network\mcpe\protocol\SetCommandsEnabledPacket;
use pocketmine\network\mcpe\protocol\SetDifficultyPacket;
use pocketmine\network\mcpe\protocol\SetEntityDataPacket;
use pocketmine\network\mcpe\protocol\SetEntityLinkPacket;
use pocketmine\network\mcpe\protocol\SetEntityMotionPacket;
use pocketmine\network\mcpe\protocol\SetHealthPacket;
use pocketmine\network\mcpe\protocol\SetPlayerGameTypePacket;
use pocketmine\network\mcpe\protocol\SetSpawnPositionPacket;
use pocketmine\network\mcpe\protocol\SetTimePacket;
use pocketmine\network\mcpe\protocol\SetTitlePacket;
use pocketmine\network\mcpe\protocol\ShowCreditsPacket;
use pocketmine\network\mcpe\protocol\SpawnExperienceOrbPacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\StopSoundPacket;
use pocketmine\network\mcpe\protocol\TakeItemEntityPacket;
use pocketmine\network\mcpe\protocol\TextPacket;
use pocketmine\network\mcpe\protocol\TransferPacket;
use pocketmine\network\mcpe\protocol\UnknownPacket;
use pocketmine\network\mcpe\protocol\UpdateAttributesPacket;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\network\mcpe\protocol\UpdateTradePacket;
use pocketmine\network\mcpe\protocol\UseItemPacket;
use pocketmine\network\PacketException;
use pocketmine\Player;
use pocketmine\resourcepacks\ResourcePack;
use pocketmine\Server;
use pocketmine\utils\UUID;

class ServerPlayerNetworkSession implements NetworkSession{

	/** @var Server */
	protected $server;
	/** @var AdvancedSourceInterface */
	protected $interface;

	protected $name = "unknown";
	/** @var SkinData */
	protected $skin = null;

	protected $ip;
	protected $port;
	protected $uuid;

	/** @var Player|null */
	protected $player = null;

	/** @var int */
	protected $protocol = ProtocolInfo::CURRENT_PROTOCOL;
	protected $gameVersion = ProtocolInfo::MINECRAFT_VERSION_NETWORK;

	protected $status = NetworkSession::STATUS_UNCONNECTED;

	/** @var BatchPacket */
	protected $currentBatch; //TODO

	public function __construct(Server $server, AdvancedSourceInterface $interface, string $ip, int $port){
		$this->interface = $interface;
		$this->ip = $ip;
		$this->port = $port;
		$this->server = $server;
	}

	public function getName(){
		return $this->name;
	}

	public function getAddress() : string{
		return $this->ip;
	}

	public function getPort() : int{
		return $this->port;
	}

	public function getDescription() : string{
		return $this->getName() . ", IP: " . $this->getAddress() . ", port: " . $this->getPort();
	}

	public function getUniqueId(){
		return $this->uuid;
	}

	/**
	 * @return Server
	 */
	public function getServer(){
		return $this->server;
	}

	public function isEncryptionEnabled() : bool{
		return false; //TODO
	}

	public function isAuthenticated() : bool{
		return false; //TODO
	}

	/**
	 * @param DataPacket $pk
	 * @param bool       $immediate Whether to skip batching and send this packet directly.
	 */
	public function sendPacket(DataPacket $pk, bool $immediate = false){
		if(($this->status & $pk->getAcceptableStatus()) === 0){
			throw new PacketException("Attempted to send " . get_class($pk) . " to " . $this->getDescription() . " at a bad time");
		}
		$timings = Timings::getSendDataPacketTimings($pk);
		$timings->startTiming();

		$immediate = true; //TODO: remove this and implement batch (and not the mess we have currently)

		if($immediate or !$pk->canBeBatched()){
			$this->interface->putPacket($this, $pk, false, $immediate);
		}else{
			//TODO: batch stuff
		}

		$timings->stopTiming();
	}

	public function receivePacket(DataPacket $packet){
		$timings = Timings::getReceiveDataPacketTimings($packet);
		$timings->startTiming();

		try{
			$packet->decode();
			if(!$packet->feof()){
				$remaining = $packet->get(true);
				$this->server->getLogger()->debug(strlen($remaining) . " bytes still unread in " . $packet->getName() . " from " . $this->getDescription() . " (remaining buffer hex: 0x" . bin2hex($remaining) . ")");
			}
			if(($this->status & $packet->getAcceptableStatus()) === 0){
				throw new PacketException("Received unexpected MCPE packet " . get_class($packet) . " from " . $this->getDescription());
			}

			$this->server->getPluginManager()->callEvent($ev = new DataPacketReceiveEvent($this, $packet));

			if(!$ev->isCancelled() and !$packet->handle($this)){
				//TODO: remove haxxx
				if($this->player !== null){
					if(!$packet->handle($this->player)){
						$this->server->getLogger()->debug("Unhandled MCPE packet " . $packet->getName() . " received from " . $this->getDescription() . ": 0x" . bin2hex($packet->buffer));
					}
				}else{
					$this->server->getLogger()->debug("Cannot handle " . $packet->getName() . " due to unhandled and null player");
				}
			}
		}catch(\Throwable $e){
			if($this->status === NetworkSession::STATUS_UNCONNECTED){
				//Server crashed, panic and get rid of the client (not logged in yet, don't leave them hanging)
				$this->disconnect("Internal server error", true);
			}
			$this->server->getLogger()->logException($e);
		}

		$timings->stopTiming();
	}

	public function tick(int $tickDiff = 1){

	}

	/**
	 * Ends the network session and disconnects the client.
	 *
	 * @param string $message Message to show to client and/or in console.
	 * @param bool   $notifyClient Whether to send a DisconnectPacket to the client. If the client disconnected of their own accord, this should be false.
	 * @param bool   $showDisconnectScreen If notifying the client, whether to show them the disconnect screen or not. If false, they'll be sent straight to the menu.
	 */
	public function disconnect(string $message = "unknown", bool $notifyClient = true, bool $showDisconnectScreen = true){
		if($notifyClient){
			$pk = new DisconnectPacket();
			if($showDisconnectScreen){
				$pk->hideDisconnectionScreen = false;
				$pk->message = $message;
			}else{
				$pk->hideDisconnectionScreen = true; //No need to set a message if we're not going to show the disconnect screen.
			}
			$this->sendPacket($pk, true);
		}

		//TODO: handle feedback loops properly
		if($this->player !== null){
			$player = $this->player;
			$this->player = null;
			$player->close($player->getLeaveMessage(), $message, $notifyClient);
		}
		$this->interface->close($this, $message);
		$this->status = NetworkSession::STATUS_UNCONNECTED;
	}

	/**
	 * @param LoginPacket $packet
	 *
	 * @return bool
	 */
	public function handleLogin(LoginPacket $packet) : bool{
		try{
			if($packet->protocol !== ProtocolInfo::CURRENT_PROTOCOL){
				if($packet->protocol < ProtocolInfo::CURRENT_PROTOCOL){
					$message = "disconnectionScreen.outdatedClient";
					$status = PlayStatusPacket::LOGIN_FAILED_CLIENT;
				}else{
					$message = "disconnectionScreen.outdatedServer";
					$status = PlayStatusPacket::LOGIN_FAILED_SERVER;
				}
				$this->sendPlayStatus($status, true);
				throw new LoginFailedException($message, false);
			}

			$this->uuid = UUID::fromString($packet->clientUUID);
			//TODO: add more cool stuff from LoginPacket for plugins to mess with

			/** @var PlayerPreLoginEvent $ev */
			$ev = $this->server->onPlayerPreLogin($packet->username, $this->ip, $this->port, $packet->clientUUID, new SkinData($packet->skin, $packet->skinId));
			if($ev->isCancelled()){
				throw new LoginFailedException($ev->getKickMessage(), true);
			}else{
				$this->name = $ev->getUsername();
				$this->skin = $ev->getSkin();
			}

			$this->authenticate();

		}catch(LoginFailedException $e){
			//Disconnect client normally
			$this->disconnect($e->getMessage(), $e->shouldNotify());
		}

		return true;
	}

	public function authenticate(){
		//TODO: add authentication
		$this->onAuthenticated();
	}

	public function onAuthenticated(){
		$this->enableEncryption();
	}

	public function sendPlayStatus(int $status, bool $immediate = false){
		$pk = new PlayStatusPacket();
		$pk->status = $status;
		$this->sendPacket($pk, $immediate);
	}

	public function handlePlayStatus(PlayStatusPacket $packet) : bool{
		return false;
	}

	public function enableEncryption(){
		//TODO: implement protocol encryption
		$this->onEncryptionEnabled();
	}

	public function onEncryptionEnabled(){
		$this->sendPlayStatus(PlayStatusPacket::LOGIN_SUCCESS);

		$this->sendResourcePacksInfo();
	}

	public function handleServerToClientHandshake(ServerToClientHandshakePacket $packet) : bool{
		return false;
	}

	public function handleClientToServerHandshake(ClientToServerHandshakePacket $packet) : bool{
		//TODO: implement encryption
		//$this->onEncryptionEnabled();
		return false;
	}

	public function handleDisconnect(DisconnectPacket $packet) : bool{
		return false;
	}

	public function handleResourcePacksInfo(ResourcePacksInfoPacket $packet) : bool{
		return false;
	}

	public function sendResourcePacksInfo(){
		$pk = new ResourcePacksInfoPacket();
		$manager = $this->server->getResourceManager();
		$pk->resourcePackEntries = $manager->getResourceStack();
		$pk->mustAccept = $manager->resourcePacksRequired();
		$this->sendPacket($pk);
	}

	public function handleResourcePackStack(ResourcePackStackPacket $packet) : bool{
		return false;
	}

	public function handleResourcePackClientResponse(ResourcePackClientResponsePacket $packet) : bool{
		switch($packet->status){
			case ResourcePackClientResponsePacket::STATUS_REFUSED:
				//TODO: add lang strings for this
				$this->disconnect("You must accept resource packs to join this server.", true);
				break;
			case ResourcePackClientResponsePacket::STATUS_SEND_PACKS:
				$manager = $this->server->getResourceManager();
				foreach($packet->packIds as $uuid){
					$pack = $manager->getPackById($uuid);
					if(!($pack instanceof ResourcePack)){
						//Client requested a resource pack but we don't have it available on the server
						$this->disconnect("disconnectionScreen.resourcePack", true);
						$this->server->getLogger()->debug("Got a resource pack request for unknown pack with UUID " . $uuid . ", available packs: " . implode(", ", $manager->getPackIdList()));
						return false;
					}

					$pk = new ResourcePackDataInfoPacket();
					$pk->packId = $pack->getPackId();
					$pk->maxChunkSize = 1048576; //1MB
					$pk->chunkCount = $pack->getPackSize() / $pk->maxChunkSize;
					$pk->compressedPackSize = $pack->getPackSize();
					$pk->sha256 = $pack->getSha256();
					$this->sendPacket($pk);
				}

				break;
			case ResourcePackClientResponsePacket::STATUS_HAVE_ALL_PACKS:
				$pk = new ResourcePackStackPacket();
				$manager = $this->server->getResourceManager();
				$pk->resourcePackStack = $manager->getResourceStack();
				$pk->mustAccept = $manager->resourcePacksRequired();
				$this->sendPacket($pk);
				break;
			case ResourcePackClientResponsePacket::STATUS_COMPLETED:
				$this->createPlayer();
				break;
			default:
				return false;
		}

		return true;
	}

	public function createPlayer(){
		//TODO: create the player
		try{
			$this->player = new Player($this->server, $this, $this->skin);
		}catch(LoginFailedException $e){
			$this->disconnect($e->getMessage(), true);
		}
	}

	public function handleText(TextPacket $packet) : bool{
		return false;
	}

	public function handleSetTime(SetTimePacket $packet) : bool{
		return false;
	}

	public function handleStartGame(StartGamePacket $packet) : bool{
		return false;
	}

	public function handleAddPlayer(AddPlayerPacket $packet) : bool{
		return false;
	}

	public function handleAddEntity(AddEntityPacket $packet) : bool{
		return false;
	}

	public function handleRemoveEntity(RemoveEntityPacket $packet) : bool{
		return false;
	}

	public function handleAddItemEntity(AddItemEntityPacket $packet) : bool{
		return false;
	}

	public function handleAddHangingEntity(AddHangingEntityPacket $packet) : bool{
		return false;
	}

	public function handleTakeItemEntity(TakeItemEntityPacket $packet) : bool{
		return false;
	}

	public function handleMoveEntity(MoveEntityPacket $packet) : bool{
		return false;
	}

	public function handleMovePlayer(MovePlayerPacket $packet) : bool{
		return false;
	}

	public function handleRiderJump(RiderJumpPacket $packet) : bool{
		return false;
	}

	public function handleRemoveBlock(RemoveBlockPacket $packet) : bool{
		return false;
	}

	public function handleUpdateBlock(UpdateBlockPacket $packet) : bool{
		return false;
	}

	public function handleAddPainting(AddPaintingPacket $packet) : bool{
		return false;
	}

	public function handleExplode(ExplodePacket $packet) : bool{
		return false;
	}

	public function handleLevelSoundEvent(LevelSoundEventPacket $packet) : bool{
		return false;
	}

	public function handleLevelEvent(LevelEventPacket $packet) : bool{
		return false;
	}

	public function handleBlockEvent(BlockEventPacket $packet) : bool{
		return false;
	}

	public function handleEntityEvent(EntityEventPacket $packet) : bool{
		return false;
	}

	public function handleMobEffect(MobEffectPacket $packet) : bool{
		return false;
	}

	public function handleUpdateAttributes(UpdateAttributesPacket $packet) : bool{
		return false;
	}

	public function handleMobEquipment(MobEquipmentPacket $packet) : bool{
		return false;
	}

	public function handleMobArmorEquipment(MobArmorEquipmentPacket $packet) : bool{
		return false;
	}

	public function handleInteract(InteractPacket $packet) : bool{
		$entity = $this->player->getLevel()->getEntity($packet->target);

		if($entity !== null){
			switch($packet->action){
				case InteractPacket::ACTION_LEFT_CLICK:
					$this->player->attackEntity($entity);
					break;
				case InteractPacket::ACTION_RIGHT_CLICK:
				case InteractPacket::ACTION_LEAVE_VEHICLE:
				case InteractPacket::ACTION_MOUSEOVER:
					break; //TODO: handle these
				default:
					$this->server->getLogger()->debug("Unhandled/unknown interaction type " . $packet->action . "received from ". $this->getName());
					return false;
			}
		}

		return true;
	}

	public function handleBlockPickRequest(BlockPickRequestPacket $packet) : bool{
		return false;
	}

	public function handleUseItem(UseItemPacket $packet) : bool{
		if($packet->face === -1){
			return $this->player->rightClickAir($packet->item, $packet->slot);
		}elseif($packet->face >= 0 and $packet->face <= 5){
			return $this->player->rightClickBlock($packet->item, $packet->slot, new Vector3($packet->x, $packet->y, $packet->z), $packet->face, new Vector3($packet->fx, $packet->fy, $packet->fz));
		}

		return false;
	}

	public function handlePlayerAction(PlayerActionPacket $packet) : bool{
		return false;
	}

	public function handlePlayerFall(PlayerFallPacket $packet) : bool{
		return false;
	}

	public function handleHurtArmor(HurtArmorPacket $packet) : bool{
		return false;
	}

	public function handleSetEntityData(SetEntityDataPacket $packet) : bool{
		return false;
	}

	public function handleSetEntityMotion(SetEntityMotionPacket $packet) : bool{
		return false;
	}

	public function handleSetEntityLink(SetEntityLinkPacket $packet) : bool{
		return false;
	}

	public function handleSetHealth(SetHealthPacket $packet) : bool{
		return false;
	}

	public function handleSetSpawnPosition(SetSpawnPositionPacket $packet) : bool{
		return false;
	}

	public function handleAnimate(AnimatePacket $packet) : bool{
		return false;
	}

	public function handleRespawn(RespawnPacket $packet) : bool{
		return false;
	}

	public function handleDropItem(DropItemPacket $packet) : bool{
		return false;
	}

	public function handleInventoryAction(InventoryActionPacket $packet) : bool{
		return false;
	}

	public function handleContainerOpen(ContainerOpenPacket $packet) : bool{
		return false;
	}

	public function handleContainerClose(ContainerClosePacket $packet) : bool{
		$this->player->closeInventoryWindow($packet->windowid, true);
		return true;
	}

	public function handleContainerSetSlot(ContainerSetSlotPacket $packet) : bool{
		return false;
	}

	public function handleContainerSetData(ContainerSetDataPacket $packet) : bool{
		return false;
	}

	public function handleContainerSetContent(ContainerSetContentPacket $packet) : bool{
		return false;
	}

	public function handleCraftingData(CraftingDataPacket $packet) : bool{
		return false;
	}

	public function handleCraftingEvent(CraftingEventPacket $packet) : bool{
		return false;
	}

	public function handleAdventureSettings(AdventureSettingsPacket $packet) : bool{
		return false;
	}

	public function handleBlockEntityData(BlockEntityDataPacket $packet) : bool{
		return false;
	}

	public function handlePlayerInput(PlayerInputPacket $packet) : bool{
		return false;
	}

	public function handleFullChunkData(FullChunkDataPacket $packet) : bool{
		return false;
	}

	public function handleSetCommandsEnabled(SetCommandsEnabledPacket $packet) : bool{
		return false;
	}

	public function handleSetDifficulty(SetDifficultyPacket $packet) : bool{
		return false;
	}

	public function handleChangeDimension(ChangeDimensionPacket $packet) : bool{
		return false;
	}

	public function handleSetPlayerGameType(SetPlayerGameTypePacket $packet) : bool{
		if($packet->gamemode !== ($this->player->getGamemode() & 0x01)){
			//GUI gamemode change, set it back to original for now (only possible through client bug or hack with current allowed client permissions)
			$pk = new SetPlayerGameTypePacket();
			$pk->gamemode = $this->player->getGamemode() & 0x01;
			$this->sendPacket($pk);
			$this->player->sendSettings(); //TODO: move this
		}
		return true;
	}

	public function handlePlayerList(PlayerListPacket $packet) : bool{
		return false;
	}

	/*public function handleTelemetryEvent(EventPacket $packet) : bool{
		return false;
	}*/ //TODO

	public function handleSpawnExperienceOrb(SpawnExperienceOrbPacket $packet) : bool{
		return false;
	}

	public function handleClientboundMapItemData(ClientboundMapItemDataPacket $packet) : bool{
		return false;
	} //TODO

	public function handleMapInfoRequest(MapInfoRequestPacket $packet) : bool{
		return false;
	} //TODO

	public function handleRequestChunkRadius(RequestChunkRadiusPacket $packet) : bool{
		$this->player->setViewDistance($packet->radius); //TODO: make sure the player actually exists
		return true;
	}

	public function handleChunkRadiusUpdated(ChunkRadiusUpdatedPacket $packet) : bool{
		return false;
	}

	public function handleItemFrameDropItem(ItemFrameDropItemPacket $packet) : bool{
		return false;
	}

	public function handleReplaceItemInSlot(ReplaceItemInSlotPacket $packet) : bool{
		return false;
	}

	/*public function handleGameRulesChanged(GameRulesChangedPacket $packet) : bool{
		return false;
	}*/ //TODO

	/*public function handleCamera(CameraPacket $packet) : bool{
		return false;
	}*/ //edu only :(

	public function handleAddItem(AddItemPacket $packet) : bool{
		return false;
	}

	/*public function handleBossEvent(BossEventPacket $packet) : bool{
		return false;
	}*/

	public function handleShowCredits(ShowCreditsPacket $packet) : bool{
		return false;
	}

	public function handleAvailableCommands(AvailableCommandsPacket $packet) : bool{
		return false;
	}

	public function handleCommandStep(CommandStepPacket $packet) : bool{
		return false;
	}

	public function handleCommandBlockUpdate(CommandBlockUpdatePacket $packet) : bool{
		return false;
	}

	public function handleUpdateTrade(UpdateTradePacket $packet) : bool{
		return false;
	}

	public function handleResourcePackDataInfo(ResourcePackDataInfoPacket $packet) : bool{
		return false;
	}

	public function handleResourcePackChunkData(ResourcePackChunkDataPacket $packet) : bool{
		return false;
	}

	public function handleResourcePackChunkRequest(ResourcePackChunkRequestPacket $packet) : bool{
		$manager = $this->server->getResourceManager();
		$pack = $manager->getPackById($packet->packId);
		if(!($pack instanceof ResourcePack)){
			$this->disconnect("disconnectionScreen.resourcePack", true);
			$this->server->getLogger()->debug("Got a resource pack chunk request for unknown pack with UUID " . $packet->packId . ", available packs: " . implode(", ", $manager->getPackIdList()));

			return true;
		}

		$pk = new ResourcePackChunkDataPacket();
		$pk->packId = $pack->getPackId();
		$pk->chunkIndex = $packet->chunkIndex;
		$pk->data = $pack->getPackChunk(1048576 * $packet->chunkIndex, 1048576);
		$pk->progress = (1048576 * $packet->chunkIndex);
		$this->sendPacket($pk);
		return true;
	}

	public function handleTransfer(TransferPacket $packet) : bool{
		return false;
	}

	public function handlePlaySound(PlaySoundPacket $packet) : bool{
		return false;
	}

	public function handleStopSound(StopSoundPacket $packet) : bool{
		return false;
	}

	public function handleSetTitle(SetTitlePacket $packet) : bool{
		return false;
	}

	public function handleUnknown(UnknownPacket $packet) : bool{
		$this->server->getLogger()->debug("Unknown packet received from " . $this->getDescription() . ": 0x" . bin2hex($packet->buffer));
		return true;
	}
}