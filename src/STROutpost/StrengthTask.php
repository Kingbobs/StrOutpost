<?php

namespace benzo\STROutpost;

use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use benzo\Factions\faction\Faction;

class StrengthTask extends Task{

    /** @var Faction|null */
    public $faction = null;
    /** @var ClosureTask */
    public $task;

    public function onRun(int $currentTick): void{
        if(($playerName = StrengthAPI::getCapturer()) !== null){
            $p = Server::getInstance()->getPlayer($playerName);

            if($p !== null){
                $fac = \benzo\Factions\Loader::getInstance()->getFactionFor($p);

                if($this->faction !== null && $fac !== null && $this->faction->getName() === $fac->getName()){
                    StrengthAPI::setCapturer(null);
                    StrengthAPI::setCaptureTime(10);
                    return;
                }
                if(StrengthAPI::getCaptureTime() > 0){
                    StrengthAPI::setCaptureTime(StrengthAPI::getCaptureTime() - 1);
                    if($p = Server::getInstance()->getPlayer(StrengthAPI::getCapturer())){
                        if(!StrengthAPI::isInKoth($p)){
                            $p->sendMessage(TextFormat::RED . "§7§3§l» §bOutpost §r§7| Stopped capturing, you are no longer inside a outpost!");
                            StrengthAPI::setCapturer(null);
                            StrengthAPI::setCaptureTime(10);
                            return;
                        }
                        $p->sendPopup(TextFormat::AQUA . "Capturing Time: " . StrengthAPI::getCaptureTime() . "s");
                    }
                }else{
                    $p = Server::getInstance()->getPlayer(StrengthAPI::getCapturer());
                    StrengthAPI::setCapturer($p->getName());
                    $this->win();
                }
            }
        }else{
            $players = [];
            foreach(Server::getInstance()->getOnlinePlayers() as $player){
                if(StrengthAPI::isInKoth($player)){
                    if($this->faction !== null && ($fac = \benzo\Factions\Loader::getInstance()->getFactionFor($player)) !== null){
                        if($this->faction->getName() === $fac->getName()){
                            continue;
                        }
                    }
                    $players[] = $player->getName();
                }
            }
            if(count($players) >= 1){
                if($p = Server::getInstance()->getPlayer($pName = $players[array_rand($players)])){
                    StrengthAPI::setCapturer($pName);
                    if(StrengthAPI::getLastCapturer() === null){
                        StrengthAPI::setLastCapturer($pName);
                    }
                    $p->sendMessage(TextFormat::GREEN . "You are now capturing!");
                }
            }
        }
    }

    public function win(): void{
        $winner = StrengthAPI::getCapturer();
        $lastCap = StrengthAPI::getLastCapturer();
        StrengthAPI::setCaptureTime(10);
        if($p = Server::getInstance()->getPlayer($winner)){
            if(($pFac = \benzo\Factions\Loader::getInstance()->getFactionFor($p)) === null) {
                $p->sendMessage(TextFormat::RED . "§7§3§l» §bOutpost §r§7| You claimed the Outpost, but you are not in a Faction, so this was cancelled");
                return;
            }
            if($this->faction !== null && $pFac->getName() === $this->faction->getName()){
                return;
            }
            $lastCapPlayer = Server::getInstance()->getPlayer($lastCap ?? "");
            if($lastCapPlayer !== null && $lastCap !== $winner){
                $lastCapPlayer->sendMessage(TextFormat::GREEN . "§7§3§l» §bOutpost §r§7| You won't gain anymore STR because someone took your Cap");
            }
            if($this->task !== null) Loader::get()->getScheduler()->cancelTask($this->task->getTaskId());
            $this->task = null;
            $this->faction = $pFac;
            $faction = $this->faction;
            Loader::get()->getScheduler()->scheduleRepeatingTask($this->task = new ClosureTask(function ()use($faction): void{
                $checkNull = \benzo\Factions\Loader::getInstance()->getFaction($faction->getName()) !== null;
                if($faction !== null && $faction instanceof Faction && $checkNull){
                    $faction->addSTR(25);
                    $faction->broadcastMessage("+25 STR");
                }
            }), 20 * 300);
        }

        Server::getInstance()->broadcastMessage("§7§3§l» §bOutpost §r§7|" . TextFormat::GRAY . $winner . " has captured the STR Outpost!");
        StrengthAPI::setLastCapturer($winner);
    }

}