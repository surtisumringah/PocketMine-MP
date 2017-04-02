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
 * @link   http://www.pocketmine.net/
 *
 *
 */

namespace pocketmine\event\player;

use pocketmine\entity\SkinData;
use pocketmine\event\Cancellable;
use pocketmine\event\Event;

/**
 * Called when a player attempts to connect to the server, before they are authenticated.
 * Plugins can use this event to control things like whitelisting, bans, username fixing.
 */
class PlayerPreLoginEvent extends Event implements Cancellable{
	public static $handlerList = null;

	/** @var string */
	protected $username;
	/** @var string */
	protected $ip;
	/** @var int */
	protected $port;
	/** @var string */
	protected $uuid;
	/** @var SkinData */
	protected $skin;

	/** @var string */
	protected $kickMessage;

	/**
	 * @param string   $username
	 * @param string   $ip
	 * @param int      $port
	 * @param string   $uuid
	 * @param SkinData $skin
	 */
	public function __construct(string $username, string $ip, int $port, string $uuid, SkinData $skin){
		$this->username = $username;
		$this->ip = $ip;
		$this->port = $port;
		$this->uuid = $uuid;
		$this->skin = $skin;
	}

	/**
	 * Returns the username of the player attempting to connect.
	 * @return string
	 */
	public function getUsername() : string{
		return $this->username;
	}

	/**
	 * Sets the username of the player attempting to connect. Plugins may use this method to correct invalid usernames,
	 * for example usernames with spaces in them.
	 *
	 * Usernames set here will be validated after the event is called, so if you set an invalid name here, the player will be
	 * kicked due to supposedly having an invalid name.
	 *
	 * @param string $username
	 */
	public function setUsername(string $username){
		$this->username = $username;
	}

	/**
	 * Returns the string representation of the player's IP address.
	 * @return string
	 */
	public function getAddress() : string{
		return $this->ip;
	}

	/**
	 * Returns the player's port.
	 * @return int
	 */
	public function getPort() : int{
		return $this->port;
	}

	/**
	 * Returns a string representation of the player's unique ID.
	 * @return string
	 */
	public function getUUID() : string{
		return $this->uuid;
	}

	/**
	 * Returns an object containing skin data.
	 * @return SkinData
	 */
	public function getSkin() : SkinData{
		return $this->skin;
	}

	/**
	 * Sets the player's skin.
	 * Note that due to client limitations, the players themselves may not see the changed skin; however other players will.
	 *
	 * @param SkinData $skin
	 */
	public function setSkin(SkinData $skin){
		$this->skin = $skin;
	}

	/**
	 * Cancels the event with the specified reason. This reason will be displayed to the player on the disconnection screen.
	 * @param string $reason
	 */
	public function setCancelledWithReason(string $reason){
		$this->setCancelled(true);
		$this->setKickMessage($reason);
	}

	/**
	 * Returns the message that will be shown on the player's disconnection screen if the event is cancelled.
	 * Defaults to the "You have been disconnected" screen if the event was cancelled but no reason was given.
	 *
	 * @return string
	 */
	public function getKickMessage() : string{
		return $this->kickMessage ?? "disconnectionScreen.noReason";
	}

	/**
	 * Sets the message shown to the player if the event is cancelled.
	 * @param $kickMessage
	 */
	public function setKickMessage(string $kickMessage){
		$this->kickMessage = $kickMessage;
	}

}