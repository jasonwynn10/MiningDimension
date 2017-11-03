<?php
declare(strict_types=1);
namespace jasonwynn10\MD\utils;

use pocketmine\block\Block;
use pocketmine\level\Position;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\Server;

class PortalCalculationUtils {
	/** @var int $levelId */
	public static $levelId = 2;
	/**
	 * @param Block $position
	 *
	 * @return null|AxisAlignedBB
	 */
	public static function calculatePortalSpace(Block $position) : ?AxisAlignedBB {
		for($side = Vector3::SIDE_DOWN; $side <= Vector3::SIDE_EAST; $side++) {
			$sideBlock = $position->getSide($side);
			if($sideBlock->getId() === Block::PORTAL or $sideBlock->getId() === Block::OBSIDIAN) {
				self::recursiveBlockMap($sideBlock);
			}
		}
		// TODO: return space of all portal blocks which make a valid portal
		return null;
	}

	/**
	 * @param Block $position
	 *
	 * @return array
	 */
	private static function recursiveBlockMap(Block $position) : array {
		// TODO: return array of strings indicating portal shape

		// A = Air, O = Obsidian, P = Portal

		// AOOA
		// OPPO
		// OPPO
		// OPPO
		// AOOA

		for($side = Vector3::SIDE_DOWN; $side <= Vector3::SIDE_EAST; $side++) {
			$sideBlock = $position->getSide($side);
			if($sideBlock->getId() === Block::PORTAL or $sideBlock->getId() === Block::OBSIDIAN) {
				self::recursiveBlockMap($sideBlock);
			}
		}
		return [];
	}

	/**
	 * @param Position $position
	 *
	 * @return Position
	 */
	public static function getDimensionChangePosition(Position $position) : Position {
		if($position->getLevel()->getId() === self::$levelId) {
			$x = 0;
			$z = 0;
			$level = Server::getInstance()->getLevel(1);
			return new Position($x,BuildingUtils::getHeight() + 1.5, $z, $level);
		} else {
			// TODO: check nearest portal in mining dimension
			$x = 0;
			$z = 0;
			$level = Server::getInstance()->getLevel(self::$levelId);
			return new Position($x,BuildingUtils::getHeight() + 1.5, $z, $level);
		}
	}

	/**
	 * @param Vector3 $position
	 *
	 * @return Vector3
	 */
	public function getNearestPortal(Vector3 $position) : Vector3 {
		return $position;
	}
}