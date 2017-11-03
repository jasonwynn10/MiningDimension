<?php
declare(strict_types=1);
namespace jasonwynn10\MD\block;

use pocketmine\block\StoneBricks;

class PortalFrame extends StoneBricks {
	public function getHardness() : float {
		return 1.7;
	}

	public function getName() : string {
		return "Portal Frame";
	}
}