<?php
declare(strict_types=1);
namespace jasonwynn10\MD;

use jasonwynn10\MD\task\TeleportTask;
use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;

class EventListener implements Listener {
	/** @var MiningDimension $plugin */
	private $plugin;

	public function __construct(MiningDimension $plugin) {
		$plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
		$this->plugin = $plugin;
	}

	/**
	 * @param BlockBreakEvent $ev
	 */
	public function onBreak(BlockBreakEvent $ev) {
		$xzCoords = $this->plugin->testPortalCreated($ev->getBlock());
		if($xzCoords !== null) {
			//TODO: destroy portal blocks in portal frame
			//TODO: send sound of breaking portal block(s)
		}
	}

	/**
	 * @param PlayerMoveEvent $ev
	 */
	public function onMove(PlayerMoveEvent $ev) {
		if(!$ev->isCancelled() and $ev->getPlayer()->getLevel()->getBlockIdAt($ev->getTo()->x, $ev->getTo()->y, $ev->getTo()->z) === Block::PORTAL) {
			$this->plugin->getServer()->getScheduler()->scheduleDelayedTask(new TeleportTask($this->plugin, $ev->getPlayer()), 5 * 20);
		}
	}
}