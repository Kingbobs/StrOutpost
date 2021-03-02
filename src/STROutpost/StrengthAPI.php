<?php

namespace STROutpost;

use pocketmine\level\Position;

class StrengthAPI{

    /** @var string|null */
    private static $lastCapturer;
    /** @var string|null */
    private static $capturer;
    /** @var int */
    private static $captureTime = 10;

    public static function setLastCapturer(?string $lastCapturer): void{
        self::$lastCapturer = $lastCapturer;
    }

    public static function getLastCapturer(): ?string{
        return self::$lastCapturer;
    }

    public static function setCapturer(?string $capturer): void{
        self::$capturer = $capturer;
    }

    public static function getCapturer(): ?string{
        return self::$capturer;
    }

    public static function setCaptureTime(int $captureTime): void{
        self::$captureTime = $captureTime;
    }

    public static function getCaptureTime(): ?int{
        return self::$captureTime ?? 10;
    }

    public static function isInKoth(Position $pos): bool{
        $posA = null;
        $posB = null;
        $config = Loader::get()->data;
        if(Loader::get()->isDataSet()) {
            $posA = Loader::get()->translateToVector($config->get("A"));
            $posB = Loader::get()->translateToVector($config->get("B"));
        }else return false;
        $minX = min($posA->x, $posB->x);
        $maxX = max($posA->x, $posB->x);
        $minZ = min($posA->z, $posB->z);
        $maxZ = max($posA->z, $posB->z);

        if ($pos->getX() >= $minX && $pos->getX() <= $maxX && $pos->getZ() >= $minZ && $pos->getZ() <= $maxZ) {
            if ($pos->getLevel()->getName() === $config->get("world")){
                return true;
            }
        }
        return false;
    }

}
