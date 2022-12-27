<?php

declare(strict_types=1);

namespace FiraAja\Sponges\Tasks;

use pocketmine\block\BlockFactory;
use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\scheduler\Task;
use pocketmine\world\particle\BlockBreakParticle;

use pocketmine\world\Position;
use pocketmine\world\World;

use pocketmine\math\Vector3;


class setBlockTask extends Task{
    
    private Main $plugin;

    public function __construct(Main $plugin, World $world, Vector3 $vector, Block $block, Position $position){
        $this->plugin = $plugin;
        $this->world = $world;
        $this->vector = $vector;
        $this->block = $block;
        $this->position = $position
    }

    public function getPlugin(): Plugin{
        return $this->plugin;
    }

    public function onRun(): void {
        $this->getWorld->addParticle($this->position->add(0.5, 0.5, 0.5), new BlockBreakParticle(VanillaBlocks::STONE()));
        $this->getWorld->setBlock($this->vector, $this->block, $this->position);
    }
}
