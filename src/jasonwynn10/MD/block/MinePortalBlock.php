<?php
declare(strict_types=1);
namespace jasonwynn10\MD\block;

use pocketmine\block\Block;
use pocketmine\block\Thin;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;

class MinePortalBlock extends Thin {
	protected $id = Block::PORTAL;

	public function __construct(){
		parent::__construct($this->id, 0, "Mine Portal");
	}

	public function getName() : string {
		return "Mining Dimension Portal";
	}

	public function getLightLevel() : int {
		return 11;
	}

	public function canBeFlowedInto() : bool {
		return true;
	}

	public function getHardness() : float {
		return 0;
	}

	protected function recalculateBoundingBox() : ?AxisAlignedBB{
		$width = 0.5 - 0.125 / 2;

		return new AxisAlignedBB(
			$this->x + ($this->canConnect($this->getSide(Vector3::SIDE_WEST)) ? 0 : $width),
			$this->y,
			$this->z + ($this->canConnect($this->getSide(Vector3::SIDE_NORTH)) ? 0 : $width),
			$this->x + 1 - ($this->canConnect($this->getSide(Vector3::SIDE_EAST)) ? 0 : $width),
			$this->y + 1,
			$this->z + 1 - ($this->canConnect($this->getSide(Vector3::SIDE_SOUTH)) ? 0 : $width)
		);
	}

	protected function recalculateCollisionBoxes() : array{
		$inset = 0.5 - 0.125 / 2;

		/** @var AxisAlignedBB[] $bbs */
		$bbs = [];

		$connectWest = $this->canConnect($this->getSide(Vector3::SIDE_WEST));
		$connectEast = $this->canConnect($this->getSide(Vector3::SIDE_EAST));

		if($connectWest or $connectEast){
			//X axis (west/east)
			$bbs[] = new AxisAlignedBB(
				$this->x + ($connectWest ? 0 : $inset),
				$this->y,
				$this->z + $inset,
				$this->x + 1 - ($connectEast ? 0 : $inset),
				$this->y + 1,
				$this->z + 1 - $inset
			);
		}

		$connectNorth = $this->canConnect($this->getSide(Vector3::SIDE_NORTH));
		$connectSouth = $this->canConnect($this->getSide(Vector3::SIDE_SOUTH));

		if($connectNorth or $connectSouth){
			//Z axis (north/south)
			$bbs[] = new AxisAlignedBB(
				$this->x + $inset,
				$this->y,
				$this->z + ($connectNorth ? 0 : $inset),
				$this->x + 1 - $inset,
				$this->y + 1,
				$this->z + 1 - ($connectSouth ? 0 : $inset)
			);
		}

		if(empty($bbs)){
			//centre post AABB (only needed if not connected on any axis - other BBs overlapping will do this if any connections are made)
			return [
				new AxisAlignedBB(
					$this->x + $inset,
					$this->y,
					$this->z + $inset,
					$this->x + 1 - $inset,
					$this->y + 1,
					$this->z + 1 - $inset
				)
			];
		}

		return $bbs;
	}
}