<?php

declare(strict_types=1);

namespace FiraAja\Sponges;

use pocketmine\block\BlockFactory;
use pocketmine\block\VanillaBlocks;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockSpreadEvent;
use pocketmine\event\player\PlayerBucketEmptyEvent;
use pocketmine\math\Vector3;
use pocketmine\world\Position;
use FiraAja\Sponges\Tasks\setBlockTask;
use FiraAja\Sponges\Tasks\absorbWaterTask;
use pocketmine\block\Block;

class Main extends PluginBase implements Listener{
    
    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
    }
    
    public function onBlockPlaceEvent(BlockPlaceEvent $event){
        $block = $event->getBlock();
        if($block->getMeta() == 0 and $block->getId() == 19){
            if(self::absorbWater(new Position($block->getPosition()->getX(), $block->getPosition()->getY(), $block->getPosition()->getZ(), $block->getPosition()->getWorld()))){
                $this->getScheduler()->scheduleDelayedTask(new setBlockTask($this, $block->getPosition()->getWorld(), new Vector3($block->getPosition()->getX(), $block->getPosition()->getY(), $block->getPosition()->getZ()), BlockFactory::getInstance()->get(VanillaBlocks::SPONGE()->getId(), 1), true, true), 1);
            }
        }
    }

    public function onWaterFlow(BlockSpreadEvent $event){
        $source = $event->getSource();
        $block = $event->getBlock();
       
        if($source->getId() == 8){
            $sponge = self::hasSpongeNear($block->getPosition()->getWorld(), $block->getPosition()->getX(), $block->getPosition()->getY(), $block->getPosition()->getZ());
            if($sponge instanceof Block){
                $this->getScheduler()->scheduleDelayedTask(new absorbWaterTask($this, new Position($sponge->getPosition()->getX(), $sponge->getPosition()->getY(), $sponge->getPosition()->getZ(), $sponge->getPosition()->getWorld())), 1);
                $this->getScheduler()->scheduleDelayedTask(new setBlockTask($this, $sponge->getPosition()->getWorld(), new Vector3($sponge->getPosition()->getX(), $sponge->getPosition()->getY(), $sponge->getPosition()->getZ()), BlockFactory::getInstance()->get(VanillaBlocks::SPONGE()->getId(), 1), true, true), 1);
            }
        }
    }
    
    public function onBucketUse(PlayerBucketEmptyEvent $event){
        $block = $event->getBlockClicked();
        $sponge = self::hasSpongeNear($block->getPosition()->getWorld(), $block->getPosition()->getX(), $block->getPosition()->getY(), $block->getPosition()->getZ());
        if($sponge instanceof Block){
            $this->getScheduler()->scheduleDelayedTask(new absorbWaterTask($this, new Position($sponge->getPosition()->getX(), $sponge->getPosition()->getY(), $sponge->getPosition()->getZ(), $sponge->getPosition()->getWorld())), 1);
            $this->getScheduler()->scheduleDelayedTask(new setBlockTask($this, $sponge->getPosition()->getWorld(), new Vector3($sponge->getPosition()->getX(), $sponge->getPosition()->getY(), $sponge->getPosition()->getZ()), BlockFactory::getInstance()->get(VanillaBlocks::SPONGE()->getId(), 1), true, true), 1);
        }
    }

    public function hasSpongeNear($world, $xBlock, $yBlock, $zBlock){
        for ($x = -1; $x <= 1; ++$x) {
            for ($y = -1; $y <= 1; ++$y) {
                for ($z = -1; $z <= 1; ++$z) {
                    $block = $world->getBlockAt($xBlock + $x, $yBlock + $y, $zBlock + $z);
                    if ($block->getId() == 19 and $block->getMeta() == 0) {
                        return $block;
                    }

                }
            }
        }
        return false;
    }
    
    public function absorbWater(Position $center){
        $world = $center->getWorld();
        $waterRemoved = 0;
        $yBlock = $center->getY();
        $zBlock = $center->getZ();
        $xBlock = $center->getX();
        $radius = 5;
        $l = false;
        $touchingWater = false;
        for($x = -1; $x <= 1; ++$x){
            for($y = -1; $y <= 1; ++$y){
                for($z = -1; $z <= 1; ++$z){
                    $block = $world->getBlockAt($xBlock + $x, $yBlock + $y, $zBlock + $z);
                    if($block->getId() == 9 || $block->getId() == 8){
                        $touchingWater = true;
                    }
                }
            }
        }
        if($touchingWater){
            for ($x = $center->getX()-$radius; $x <= $center->getX()+$radius; $x++) {
                $xsqr = ($center->getX()-$x) * ($center->getX()-$x);
                for ($y = $center->getY()-$radius; $y <= $center->getY()+$radius; $y++) {
                    $ysqr = ($center->getY()-$y) * ($center->getY()-$y);
                    for ($z = $center->getZ()-$radius; $z <= $center->getZ()+$radius; $z++) {
                        $zsqr = ($center->getZ()-$z) * ($center->getZ()-$z);
                        if(($xsqr + $ysqr + $zsqr) <= ($radius*$radius)) {
                            if($y > 0) {
                                $level = $center->getWorld();
                                if($level->getBlockAt($x,$y,$z)->getId() == 9 || $level->getBlockAt($x,$y,$z)->getId() == 8){
                                    $l = true;
                                    $level->setBlock(new Vector3($x, $y, $z), BlockFactory::getInstance()->get(VanillaBlocks::AIR()->getId(), 0));
                                }

                            }    
                        }
                    }
                }
            }
        }
        return $l;
    }
}
