<?php

declare(strict_types=1);

namespace FiraAja\Sponges\Tasks;

use pocketmine\block\BlockFactory;
use pocketmine\block\VanillaBlocks;
use pocketmine\scheduler\Task;
use pocketmine\world\particle\BlockBreakParticle;

class setBlockTask extends Task{

    public function __construct($that, $level, $vector, $block, $firstP, $secondP){
        $this->plugin = $that;
        $this->level = $level;
        $this->vector = $vector;
        $this->block = $block;
        $this->fP = $firstP;
        $this->sP = $secondP;
    }

    public function getPlugin(){
        return $this->plugin;
    }

    public function onRun(): void {
        $this->level->addParticle($this->vector, new BlockBreakParticle(BlockFactory::getInstance()->get(VanillaBlocks::STONE()->getId(),0)));
        $this->level->setBlock($this->vector, $this->block, $this->fP, $this->sP);
    }
}
