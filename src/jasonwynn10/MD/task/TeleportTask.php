<?php

namespace jasonwynn10\MD\task;

use jasonwynn10\MD\MiningDimension;
use pocketmine\block\Block;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\PluginTask;

class TeleportTask extends PluginTask {
	/** @var string $player */
	private $player;

	/**
	 * TeleportTask constructor.
	 *
	 * @param Plugin $owner
	 * @param Player $player
	 */
	public function __construct(Plugin $owner, Player $player) {
		parent::__construct($owner);
		$this->player = $player->getName();
	}

	/**
	 * @param int $currentTick
	 */
	public function onRun(int $currentTick) {
		$player = $this->getOwner()->getServer()->getPlayer($this->player);
		if($player !== null and $player->getLevel()->getBlockIdAt($player->x + 1, $player->y, $player->z) === Block::PORTAL) {
			$this->getOwner()->teleportPlayer($player);
		}
	}

	/**
	 * @return MiningDimension
	 */
	public function getOwner() : Plugin {
		return $this->owner;
	}
}