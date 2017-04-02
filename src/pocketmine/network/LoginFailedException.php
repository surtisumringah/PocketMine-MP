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


namespace pocketmine\network;

/**
 * Exception used to interrupt the login sequence and disconnect players when an issue occurs during login, such as incompatible protocol, bad name, etc.
 */
class LoginFailedException extends NetworkException{

	protected $notify = true;

	public function __construct($message = "", $notify = true){
		$this->notify = $notify;
		parent::__construct($message);
	}

	/**
	 * Returns whether to send a disconnection notification to the client.
	 *
	 * @return bool
	 */
	public function shouldNotify() : bool{
		return $this->notify;
	}
}