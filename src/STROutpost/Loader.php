<?php

namespace STROutpost;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class Loader extends PluginBase {

    /** @var Loader */
    private static $instance;
    /** @var OutpostListener */
    public $event;
    /** @var Config */
    public $data;
    /** @var StrengthTask */
    public $task;

    public function onEnable(): void{
        $this->getServer()->getPluginManager()->registerEvents($this->event = new OutpostListener(), $this);
        $this->getScheduler()->scheduleRepeatingTask($this->task = new StrengthTask(), 20);
        $this->data = new Config($this->getDataFolder() . "data.yml", Config::YAML);
        $this->data->save();
    }

    public function onDisable(): void{
        $this->saveResource("data.yml");
    }

    public static function get(): self{
        return self::$instance;
    }

    public function onLoad(): void{
        self::$instance = $this;
    }

    public function translateToVector(string $data): Position{
        $exp = explode(":", $data);
        $worldName = $this->data->get("world");
        $world = $this->getServer()->getLevelByName($worldName);
        if (!$world instanceof \pocketmine\level\Level) {
            throw new \InvalidArgumentException("Invalid world: " . $worldName);
        }
        return new Position(intval($exp[0]) ?? 0, intval($exp[1]) ?? 0, intval($exp[2]) ?? 0, $world);
    }

    public function translateToData(Vector3 $pos): string{
        return $pos->getX() . ":" . $pos->getY() . ":" . $pos->getZ();
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if($command->getName() == "stroutpost"){
            if($sender instanceof Player){
                if($sender->isOp()){
                    if(!isset($args[0])){
                        $sender->sendMessage("Usage: /stroutpost (spawn|setup)");
                        return false;
                    }
                    switch($args[0]){
                        case "setup":
                            if(SetupManagement::isInSetup($sender)){
                                $sender->sendMessage(TextFormat::RED . "You are already in setup");
                                return false;
                            }
                            SetupManagement::addToSetup($sender);
                            $sender->sendMessage(TextFormat::GREEN . "Break a block to set koth spawn point.");
                            break;
                        case "spawn":
                            if(SetupManagement::isInSetup($sender)){
                                $sender->sendMessage(TextFormat::RED . "You are already in setup");
                                return false;
                            }
                            $spawn = $this->translateToVector($this->data->get("spawn"));
                            if($spawn instanceof Position){
                                $sender->teleport($spawn);
                                $sender->sendMessage(TextFormat::GREEN . "Teleported!");
                            } else {
                                $sender->sendMessage(TextFormat::RED . "Spawn point not set.");
                            }
                            break;
                    }
                }
            }
        }
        return true;
    }

    public function isDataSet(): bool{
        return $this->data->get("world") !== null && is_string($this->data->get("world"));
    }

}
