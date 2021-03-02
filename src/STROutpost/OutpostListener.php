<?php

namespace benzo\STROutpost;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use benzo\Factions\faction\Faction;

class OutpostListener implements Listener {

    private $cancelOutpost = [];

    public function onMove(PlayerMoveEvent $event): void{
        $player = $event->getPlayer();
        $to = $event->getTo();
        $from = $event->getFrom();

        if(Loader::get()->isDataSet())
        if(($fac = \benzo\Factions\Loader::getInstance()->getFactionFor($player)) !== null){
            $claimerFaction = Loader::get()->task->faction;
            if($fac === ($claimerFaction === null ? null : $claimerFaction->getName())){
                if(!isset($this->cancelOutpost[$player->getName()])) {
                    $this->cancelOutpost[$player->getName()] = $player->getName();
                    return;
                }
            }else{
                if(isset($this->cancelOutpost[$player->getName()])){
                    unset($this->cancelOutpost[$player->getName()]);
                }
            }
            return;
        }
        if(isset($this->cancelOutpost[$player->getName()])){
            return;
        }
        if(Loader::get()->isDataSet() && !SetupManagement::isInSetup($player)){
            if(StrengthAPI::isInKoth($to) && !StrengthAPI::isInKoth($from) && StrengthAPI::getCapturer() === null){
                StrengthAPI::setCapturer($player->getName());
                $player->sendMessage(TextFormat::GREEN . "You are capturing the STR outpost!");
            }

            if(StrengthAPI::isInKoth($from) && !StrengthAPI::isInKoth($to) and StrengthAPI::getCapturer() === $player->getName()){
                StrengthAPI::setCapturer(null);
                StrengthAPI::setLastCapturer($player->getName());
                StrengthAPI::setCaptureTime(10);
                $player->sendMessage(TextFormat::GREEN . "You have been knocked out.");
            }
        }

    }

    public function onBreak(BlockBreakEvent $event): void{
        $block = $event->getBlock();
        $player = $event->getPlayer();

        if(SetupManagement::isInSetup($player)){
            $setup = SetupManagement::$setupPlayers[$player->getName()];
            $config = Loader::get()->data;
            switch($setup){
                case SetupManagement::POS_SPAWN:
                    SetupManagement::incrementSetup($player);
                    $config->set("spawn", Loader::get()->translateToData($block->add(0, 2)));
                    $player->sendMessage(TextFormat::GREEN . "Break a block to set STR Outpost pos A");
                    $event->setCancelled();
                    break;
                case SetupManagement::POS_A:
                    SetupManagement::incrementSetup($player);
                    $config->set("A", Loader::get()->translateToData($block));
                    $player->sendMessage(TextFormat::GREEN . "Break a block to set STR Outpost pos B");
                    $event->setCancelled();
                    break;
                case SetupManagement::POS_B:
                    $config->setNested("B", Loader::get()->translateToData($block));
                    $config->set("world", $player->getLevel()->getName());
                    $player->sendMessage(TextFormat::GREEN . "You have successfully setup the STR Outpost!");
                    SetupManagement::removeFromSetup($player);
                    $event->setCancelled();
                    break;
            }
            $config->save();
        }
    }

    public function onQuit(PlayerQuitEvent $event): void{
        $player = $event->getPlayer();
        if(isset($this->cancelOutpost[$player->getName()])){
            unset($this->cancelOutpost[$player->getName()]);
        }
    }
}