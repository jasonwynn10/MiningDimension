<?php
declare(strict_types=1);
namespace jasonwynn10\MD\populator;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\populator\Populator;
use pocketmine\utils\Random;

class GroundCoverPopulator extends Populator {
	public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) {
		$chunk = $level->getChunk($chunkX, $chunkZ);
		for($x = 0; $x < 16; ++$x) {
			for($z = 0; $z < 16; ++$z) {
				$cover = [
					Block::get(Block::GRASS),
					Block::get(Block::DIRT),
					Block::get(Block::DIRT),
					Block::get(Block::DIRT)
				];
				if(count($cover) > 0) {
					$diffY = 0;
					if(!$cover[0]->isSolid()) {
						$diffY = 1;
					}

					$column = $chunk->getBlockIdColumn($x, $z);
					for($y = 127; $y > 0; --$y) {
						if($column{$y} !== "\x00" and !BlockFactory::get(ord($column{$y}))->isTransparent()) {
							break;
						}
					}
					$startY = min(127, $y + $diffY);
					$endY = $startY - count($cover);
					for($y = $startY; $y > $endY and $y >= 0; --$y) {
						$b = $cover[$startY - $y];
						if($column{$y} === "\x00" and $b->isSolid()) {
							break;
						}
						if($b->getDamage() === 0) {
							$chunk->setBlockId($x, $y, $z, $b->getId());
						}else{
							$chunk->setBlock($x, $y, $z, $b->getId(), $b->getDamage());
						}
					}
				}
			}
		}
	}
}