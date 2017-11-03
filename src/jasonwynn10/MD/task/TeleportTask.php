<?php

namespace jasonwynn10\MD\task;

use jasonwynn10\MD\MiningDimension;
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
		foreach($this->getOwner()->getPortals() as $xzCoords => $bb) {
			if($bb->intersectsWith($player->getBoundingBox())) {
				$this->getOwner()->teleportPlayer($player, $xzCoords);
			}
		}
	}

	/**
	 * @return MiningDimension
	 */
	public function getOwner() : Plugin {
		return $this->owner;
	}
}