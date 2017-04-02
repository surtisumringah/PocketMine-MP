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


namespace pocketmine\entity;

/**
 * Stores data about an entity's skin, usually a player's.
 */
class SkinData{

	protected $skin;
	protected $skinModel;

	public function __construct(string $skinData, string $skinModel){
		$this->skin = $skinData;
		$this->skinModel = $skinModel;
	}

	public function getData() : string{
		return $this->skin;
	}

	public function getModel() : string{
		return $this->skinModel;
	}

	public function isValid() : bool{
		//TODO: add model validation
		return strlen($this->skin) === 64 * 64 * 4 or strlen($this->skin) === 64 * 32 * 4;
	}
}