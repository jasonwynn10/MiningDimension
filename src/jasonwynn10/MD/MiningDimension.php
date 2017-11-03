<?php
declare(strict_types=1);
namespace jasonwynn10\MD;

use jasonwynn10\MD\block\MinePortalBlock;
use jasonwynn10\MD\block\PortalFrame;
use jasonwynn10\MD\generator\MiningWorldGenerator;
use jasonwynn10\MD\utils\PortalCalculationUtils;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\level\generator\Generator;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class MiningDimension extends PluginBase {
	/** @var MiningDimension $instance */
	private static $instance;

	/**
	 * @return MiningDimension
	 */
	public static function getInstance() : MiningDimension {
		return self::$instance;
	}

	public function onLoad() : void {
		self::$instance = $this;
		$this->saveDefaultConfig();
		BlockFactory::registerBlock(new MinePortalBlock(), true);
		BlockFactory::registerBlock(new PortalFrame(), true);
		Generator::addGenerator(MiningWorldGenerator::class, "mining");
	}

	public function onEnable() : void {
		foreach($this->getServer()->getLevels() as $level) {
			if($level->getProvider()->getGenerator() === "mining") {
				PortalCalculationUtils::$levelId = $level->getId();
				$level->checkTime();
				$level->stopTime();
				$level->checkTime();
				break;
			}
		}
		new EventListener($this);
	}

	/**
	 * @api
	 * @param Player $player
	 *
	 * @return bool
	 */
	public function teleportPlayer(Player $player) : bool {
		$position = PortalCalculationUtils::getDimensionChangePosition($player);
		return $position->isValid() ? $player->teleport($position) : false;
	}

	/**
	 * @api
	 * @param Position $position
	 *
	 * @return string|null
	 */
	public function testPortalCreated(Position $position) : ?string {
		if($position->getLevel()->getBlockIdAt($position->x + 1, $position->y, $position->z) === Block::PORTAL) {
			$bb = PortalCalculationUtils::calculatePortalSpace($position->getLevel()->getBlockAt($position->x + 1, $position->y, $position->z));
			return "{$bb->minX},{$bb->minZ}";
		}
		if($position->getLevel()->getBlockIdAt($position->x - 1, $position->y, $position->z) === Block::PORTAL) {
			$bb = PortalCalculationUtils::calculatePortalSpace($position->getLevel()->getBlockAt($position->x - 1, $position->y, $position->z));
			return "{$bb->minX},{$bb->minZ}";
		}
		if($position->getLevel()->getBlockIdAt($position->x, $position->y + 1, $position->z) === Block::PORTAL) {
			$bb = PortalCalculationUtils::calculatePortalSpace($position->getLevel()->getBlockAt($position->x, $position->y + 1, $position->z));
			return "{$bb->minX},{$bb->minZ}";
		}
		if($position->getLevel()->getBlockIdAt($position->x, $position->y - 1, $position->z) === Block::PORTAL) {
			$bb = PortalCalculationUtils::calculatePortalSpace($position->getLevel()->getBlockAt($position->x, $position->y - 1, $position->z));
			return "{$bb->minX},{$bb->minZ}";
		}
		if($position->getLevel()->getBlockIdAt($position->x, $position->y, $position->z + 1) === Block::PORTAL) {
			$bb = PortalCalculationUtils::calculatePortalSpace($position->getLevel()->getBlockAt($position->x, $position->y, $position->z + 1));
			return "{$bb->minX},{$bb->minZ}";
		}
		if($position->getLevel()->getBlockIdAt($position->x, $position->y, $position->z - 1) === Block::PORTAL) {
			$bb = PortalCalculationUtils::calculatePortalSpace($position->getLevel()->getBlockAt($position->x, $position->y, $position->z - 1));
			return "{$bb->minX},{$bb->minZ}";
		}
		return null;
	}
}