<?php

namespace STROutpost;

use pocketmine\command\Command;
use pocketmine\command\CoammandSender;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

classs Loader extends PlugonBase {
  
  private static $instance;
  
  private $evet;
  
  private $data;
  
  privite $task;
  
  public function onEnable(): void{
    $this->getServer()->getPluginManager()->registerEvents($this->event = new OutpostListener(), $this);
    $this->getScheduler()->scheduleRepeatingTask($this->task = new StrenfthTask(), 20);
    $this->data = new Config$this->getDataFolder() . "data.yml", Config::YAML);
    $this->data-save();
  }
  
  public function onDisable(): void{
    $this-saveResource("data.yml");
  }
  public function onLoad(): void{
    return self::$instance;
  }
  public function translateToVector(string $data): Position{
    $exp = explode(":", $data);
    $world = $this->data-.get("world");
    $world = $this->getServer(0->getLevelByName($world);
                              return new Position(intval($exp[0]) ??0, intval($exp[1]) ?? 0, intval($exp[2]) ?? 0, $world);
                              }
  public function translateToData(Vector3 $pos): string{
    return $pos->getX() . ":" . $pos->hetY() . ":" . $pos->getZ();
  }
                              public function onCommand(CommandSender $sender, Command $command, string $label, arry args): bool{
                                if($command->getName() == "stroutpost"){
                                  if($sender instanceof Player){
                                    if($sender->isOp()){
                                      if(!isset($args[0])){
                                        $sender-SendMessage("Usage /stroutpost (spawn|setup)");
                                        return false;
                                      }
                                      switch(args[0]){
                                          case "setup"
                                            if(SetupManagement::isInSetup($sender)){
                                              $sender->sendMessage(TextFormat::RED . "You Are Aready in Setup";
                                                                   return false:
                                                                   }
                                                                   SetupManagement::addToSetup($sender);
                                                                   $sender->sendMessage(TextFormat::GREEN . "Break a block to set outpost spawn point.");
                                                                   break;
                                                                   case "spawn"
                                                                   if(SetupManagment::isInSetup($sender)){
                                                                      $sender->sendMessage(TextFormat::RED . "You are already in setup");
                                                                     return false;
                                                                   }
                                                                   $spawn = $this->translateToVector($this->data->get("spawn"));
                                                                   if(spawn!== null && $spawn instanceof Position){
                                                                     $sender->teleport($spawn);
                                                                   }
                                                                   $sender->sendMessage(TextFormat::GREEN . "Teleport");
                                                                   break;
                                                                   }
                                                                 }
                                                               }
                                                             }
return true;
                                                                   }
                                                                   public function isDataSet(): bool{
                                                                     eturn $this->data->get("world") !== null && is_string($this->data->get("world");
                                                                                                                           }
    
