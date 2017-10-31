<?php
declare(strict_types=1);
namespace jasonwynn10\MD\generator;

use jasonwynn10\MD\populator\CavePopulator;
use jasonwynn10\MD\populator\GroundCoverPopulator;
use jasonwynn10\MD\populator\MineshaftPopulator;
use pocketmine\block\Block;
use pocketmine\block\CoalOre;
use pocketmine\block\DiamondOre;
use pocketmine\block\Dirt;
use pocketmine\block\GoldOre;
use pocketmine\block\Gravel;
use pocketmine\block\IronOre;
use pocketmine\block\LapisOre;
use pocketmine\block\RedstoneOre;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\biome\Biome;
use pocketmine\level\generator\Generator;
use pocketmine\level\generator\noise\Simplex;
use pocketmine\level\generator\object\OreType;
use pocketmine\level\generator\populator\Ore;
use pocketmine\level\generator\populator\Populator;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

class MiningWorldGenerator extends Generator {
	/** @var ChunkManager $level */
	protected $level;
	/** @var Random $random */
	protected $random;
	/** @var array $setings */
	protected $settings;
	/** @var Simplex $noiseBase */
	protected $noiseBase;
	/** @var Populator[] $populators */
	protected $populators = [];
	/** @var Populator[] $generationPopulators */
	protected $generationPopulators = [];

	const NOT_OVERWRITABLE = [
		Block::STONE,
		Block::GRAVEL,
		Block::BEDROCK,
		Block::DIAMOND_ORE,
		Block::GOLD_ORE,
		Block::LAPIS_ORE,
		Block::REDSTONE_ORE,
		Block::IRON_ORE,
		Block::COAL_ORE,
		Block::WATER,
		Block::STILL_WATER
	];

	public function __construct(array $settings = []) {
		parent::__construct($settings);
		$this->settings = $settings;
	}
	public function init(ChunkManager $level, Random $random) {
		$this->level = $level;
		$this->random = $random;

		$this->random->setSeed($this->level->getSeed());
		$this->noiseBase = new Simplex($this->random, 4, 1 / 4, 1 / 32);
		$this->random->setSeed($this->level->getSeed());

		$this->generationPopulators[] = new GroundCoverPopulator();

		$cave = new CavePopulator();
		$cave->setBaseAmount(0);
		$cave->setRandomAmount(2);
		$this->generationPopulators[] = $cave;

		$mineshaft = new MineshaftPopulator();
		$mineshaft->setBaseAmount(0);
		$mineshaft->setRandomAmount(102);
		$this->populators[] = $mineshaft;

		$ores = new Ore(); // TODO: increase ore spawn ratios
		$ores->setOreTypes([
			new OreType(new CoalOre(), 20, 16, 0, 128),
			new OreType(new IronOre(), 20, 8, 0, 64),
			new OreType(new RedstoneOre(), 8, 7, 0, 16),
			new OreType(new LapisOre(), 1, 6, 0, 32),
			new OreType(new GoldOre(), 2, 8, 0, 32),
			new OreType(new DiamondOre(), 1, 7, 0, 16),
			new OreType(new Dirt(), 20, 32, 0, 128),
			new OreType(new Gravel(), 10, 16, 0, 128)
		]);
		$this->populators[] = $ores;
	}
	public function getName() : string {
		return "mining";
	}
	public function getSettings() : array {
		return $this->settings;
	}
	public function getSpawn() : Vector3 {
		return new Vector3(127.5, 128, 127.5);
	}

	/**
	 * @param int $chunkX
	 * @param int $chunkZ
	 */
	public function generateChunk(int $chunkX, int $chunkZ) {
		$this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->level->getSeed());

		$noise = Generator::getFastNoise3D($this->noiseBase, 16, 128, 16, 4, 8, 4, $chunkX * 16, 0, $chunkZ * 16);

		$chunk = $this->level->getChunk($chunkX, $chunkZ);
		for($x = 0; $x < 16; $x++) {
			for($z = 0; $z < 16; $z++) {
				$chunk->setBiomeId($x, $z, Biome::DESERT);
			}
		}

		foreach($this->generationPopulators as $populator) {
			$populator->populate($this->level, $chunkX, $chunkZ, $this->random);
		}
	}

	/**
	 * @param int $chunkX
	 * @param int $chunkZ
	 */
	public function populateChunk(int $chunkX, int $chunkZ) {
		$this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->level->getSeed ());
		foreach($this->populators as $populator) {
			$populator->populate($this->level, $chunkX, $chunkZ, $this->random);
		}

		// Filling lava (lakes & rivers underground)...
		for($x = $chunkX; $x < $chunkX + 16; $x ++)
			for($z = $chunkZ; $z < $chunkZ + 16; $z ++)
				for($y = 1; $y < 11; $y ++)
					if (!in_array($this->level->getBlockIdAt($x, $y, $z), self::NOT_OVERWRITABLE))
						$this->level->setBlockIdAt($x, $y, $z, Block::LAVA);
	}
}