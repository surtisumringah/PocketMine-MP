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


namespace pocketmine\item;


use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ShortTag;

class ItemStack{

	/**
	 * @var Item
	 * The type of this item.
	 */
	protected $type;

	/**
	 * @var int
	 * The number of items currently in this stack.
	 */
	protected $count;

	/**
	 * @var string
	 * Serialized NBT tags attached to the item stack.
	 */
	protected $nbt;

	/**
	 * @var int
	 * The maximum size this item stack can reach. Defaults to the max stack size specified by the item stack.
	 */
	protected $maxSize;

	/**
	 * @var int
	 * Stores the stack's damage, if applicable. This applies to things like tools and armour.
	 */
	protected $damage;

	/**
	 * @param Item $type The target item type.
	 * @param int $count
	 * @param string $nbt
	 */
	public function __construct(Item $type, int $count, string $nbt = ""){
		$this->type = $type;
		$this->maxSize = $this->type->getMaxStackSize();
		$this->setCount($count);
		$this->nbt = $nbt;
	}

	/**
	 * Returns the type of the item in this stack.
	 * @return Item
	 */
	public function getItemType() : Item{
		return $this->type;
	}

	/**
	 * Returns the count of items in this stack.
	 * @return int
	 */
	public function getCount() : int{
		return $this->count;
	}

	/**
	 * Sets the number of items in this stack.
	 * @param int $count
	 *
	 * @throws \InvalidArgumentException if the count exceeds the maximum allowed size.
	 */
	public function setCount(int $count){
		if($count > $this->maxSize){
			throw new \InvalidArgumentException("Specified count ($count) exceeds stack's maximum size ($this->maxSize)");
		}

		$this->count = $count;
	}

	/**
	 * Returns the maximum count this item stack can reach.
	 * @return int
	 */
	public function getMaxCount() : int{
		return $this->maxSize;
	}

	/**
	 * Sets the max count of the stack.
	 * @param int $max
	 */
	public function setMaxCount(int $max){
		if($count > 255 or $count < 0){
			throw new \InvalidArgumentException("Invalid max stack size $max, must be in the range 0-255");
		}

		$this->maxSize = $max;
	}

	public function isFull() : bool{
		return $this->count === $this->maxSize;
	}

	/**
	 * Returns the serialized NBT of this item stack.
	 * @return string
	 */
	public function getNBT() : string{
		return $this->nbt;
	}

	/**
	 * Sets the NBT of this item stack
	 * @param string $nbt
	 */
	public function setNBT(string $nbt){
		$this->nbt = $nbt;
	}

	/**
	 * Returns whether this item stack has NBT data attached to it.
	 * @return bool
	 */
	public function hasNBT() : bool{
		return $this->nbt !== "";
	}

	/**
	 * Removes the stack's NBT data if it has any.
	 */
	public function removeNBT(){
		$this->nbt = "";
	}

	/**
	 * Decrements the stack count, converting the stack type to air if the count becomes less than 1.
	 *
	 * @return bool if there are any items left in the stack.
	 */
	public function reduce(){
		if($this->count > 1){
			$this->count--;
			return true;
		}else{
			$this->count = 0;
			$this->type = Item::get(Item::AIR);
			$this->nbt = "";
			return false;
		}
	}

	/**
	 * Applies damage to the item stack, if applicable.
	 * @param int $amount
	 */
	public function damage(int $amount = 1){
		if($this->isDamageable()){
			$this->damage++;
			if($this->damage >= $this->type->getMaxDurability()){
				$this->reduce();
			}
		}else{
			throw new \BadMethodCallException("Item of this type cannot have damage applied to it");
		}
	}

	public function isDamageable() : bool{
		//TODO: add Unbreakable NBT tag check
		return $this->type instanceof DamageableItemType; //and $this->getCompoundTag()
	}

}