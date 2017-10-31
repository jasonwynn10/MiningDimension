<?php
declare(strict_types=1);
namespace jasonwynn10\MD;

use jasonwynn10\MD\generator\MiningWorldGenerator;
use pocketmine\event\Listener;
use pocketmine\level\generator\Generator;
use pocketmine\plugin\PluginBase;

class MiningDimension extends PluginBase implements Listener {
	/** @var int $levelId */
	private $levelId;

	public function onLoad() {
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
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
}