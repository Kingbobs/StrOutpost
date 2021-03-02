<?php

namespace STROutpost;

use pocketmine\Player;

class SetupManagement {

    const POS_SPAWN = 0;
    const POS_A = 1;
    const POS_B = 2;

    /** @var int[] */
    public static $setupPlayers = [];

    public static function isInSetup(Player $player): bool{
        return isset(self::$setupPlayers[$player->getName()]);
    }

    public static function addToSetup(Player $player): void{
        self::$setupPlayers[$player->getName()] = self::POS_SPAWN;
    }

    public static function removeFromSetup(Player $player): void{
        if(self::isInSetup($player)) unset(self::$setupPlayers[$player->getName()]);
    }

    public static function incrementSetup(Player $player): void{
        self::$setupPlayers[$player->getName()] += 1;
    }

}
