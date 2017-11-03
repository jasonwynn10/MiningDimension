<?php
declare(strict_types=1);
namespace jasonwynn10\MD;

use jasonwynn10\MD\block\MinePortalBlock;
use jasonwynn10\MD\block\PortalFrame;
use jasonwynn10\MD\generator\MiningWorldGenerator;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\level\generator\Generator;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class MiningDimension extends PluginBase {
	/** @var int $levelId */
	private $levelId;
	/** @var MiningDimension $instance */
	private static $instance;

	public function onLoad() {
		self::$instance = $this;
		$this->saveDefaultConfig();
		BlockFactory::registerBlock(new MinePortalBlock(), true);
		BlockFactory::registerBlock(new PortalFrame(), true);
		Generator::addGenerator(MiningWorldGenerator::class, "mining");
	}
	public function onEnable() {
		foreach($this->getServer()->getLevels() as $level) {
			if($level->getProvider()->getGenerator() === "mining") {
				$this->levelId = $level->getId();
				$level->checkTime();
				$level->stopTime();
				$level->checkTime();
				break;
			}
		}
		new EventListener($this);
	}

	/**
	 * @param Player $player
	 * @param int    $xzCoords
	 *
	 * @return bool
	 */
	public function teleportPlayer(Player $player, int $xzCoords) : bool {
		$coords = array_map("floatval", explode(",", $xzCoords));
		$level = $this->getServer()->getLevel($this->levelId);
		if(!$level instanceof Level)
			return false;
		$pos = new Position($coords[0], self::getHeight(), $coords[1], $level);
		return $player->teleport($pos);
	}

	/**
	 * @param Position $position
	 *
	 * @return string|null
	 */
	public function testPortalCreated(Position $position) : ?string {
		if($position->getLevel()->getBlockIdAt($position->x + 1, $position->y, $position->z) === Block::PORTAL) {
			$pos = new Vector3($position->x + 1, $position->y, $position->z);
			$bb = $this->calculatePortalSpace($pos->x, $pos->z);
			return "{$bb->minX},{$bb->minZ}";
		}
		if($position->getLevel()->getBlockIdAt($position->x - 1, $position->y, $position->z) === Block::PORTAL) {
			$pos = new Vector3($position->x - 1, $position->y, $position->z);
			$bb = $this->calculatePortalSpace($pos->x, $pos->z);
			return "{$bb->minX},{$bb->minZ}";
		}
		if($position->getLevel()->getBlockIdAt($position->x, $position->y + 1, $position->z) === Block::PORTAL) {
			$pos = new Vector3($position->x, $position->y + 1, $position->z);
			$bb = $this->calculatePortalSpace($pos->x, $pos->z);
			return "{$bb->minX},{$bb->minZ}";
		}
		if($position->getLevel()->getBlockIdAt($position->x, $position->y - 1, $position->z) === Block::PORTAL) {
			$pos = new Vector3($position->x, $position->y - 1, $position->z);
			$bb = $this->calculatePortalSpace($pos->x, $pos->z);
			return "{$bb->minX},{$bb->minZ}";
		}
		if($position->getLevel()->getBlockIdAt($position->x, $position->y, $position->z + 1) === Block::PORTAL) {
			$pos = new Vector3($position->x, $position->y, $position->z + 1);
			$bb = $this->calculatePortalSpace($pos->x, $pos->z);
			return "{$bb->minX},{$bb->minZ}";
		}
		if($position->getLevel()->getBlockIdAt($position->x, $position->y, $position->z - 1) === Block::PORTAL) {
			$pos = new Vector3($position->x, $position->y, $position->z - 1);
			$bb = $this->calculatePortalSpace($pos->x, $pos->z);
			return "{$bb->minX},{$bb->minZ}";
		}
		return null;
	}

	/**
	 * @param float $x
	 * @param float $z
	 *
	 * @return AxisAlignedBB|null
	 */
	private function calculatePortalSpace(float $x, float $z) : ?AxisAlignedBB {
		// TODO: return space of all portal blocks which make a valid portal
		return null;
	}

	/**
	 * @return int
	 */
	public static function getHeight() : int {
		return self::$instance->getConfig()->get("surface-height", 80);
	}

	/**
	 * @return int
	 */
	public static function getSurfaceBlock() : int {
		$string = self::$instance->getConfig()->get("surface-block-type", Block::GRASS);
		if(is_numeric($string)) {
			return (int) $string;
		}elseif(defined(Block::class."::".strtoupper(trim($string)))) {
			return (int) constant(Block::class."::".strtoupper(trim($string)));
		}
		return Block::GRASS;
	}
}