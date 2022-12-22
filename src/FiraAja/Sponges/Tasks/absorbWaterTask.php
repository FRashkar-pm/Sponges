<?php

declare(strict_types=1);

namespace FiraAja\Sponges\Tasks;

use pocketmine\scheduler\Task;

class absorbWaterTask extends Task{
    public function __construct($that, $position){
        $this->plugin = $that;
        $this->position = $position;
    }

    public function getPlugin(){
        return $this->plugin;
    }

    public function onRun(): void {
        $this->getPlugin()->absorbWater($this->position);
    }
}
