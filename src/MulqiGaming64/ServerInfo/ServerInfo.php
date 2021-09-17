<?php

namespace MulqiGaming64\ServerInfo;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as C;
use pocketmine\Player;
use pocketmine\Server;

use pocketmine\command\ConsoleCommandSender;

use pocketmine\utils\Config;
use pocketmine\event\player\PlayerJoinEvent;

use MulqiGaming64\ServerInfo\commands\ServerInfoCommands;
use MulqiGaming64\ServerInfo\libs\jojoe77777\FormAPI\SimpleForm;

class ServerInfo extends PluginBase implements Listener{
	
	/** @var Config $firstJoin */
	private $firstJoin;
   
	public function onEnable(): void{
		$this->saveDefaultConfig();
		$this->firstJoin = new Config($this->getDataFolder() . "firstjoin.yml", Config::YAML);
		$this->getServer()->getCommandMap()->register("ServerInfo",  new ServerInfoCommands("serverinfo", $this));
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
	
	private function isFirst(Player $player): bool{
		$name = strtolower($player->getName());
		if($this->firstJoin->exists($name)){
			return false;
		}
		$this->firstJoin->set($name, "true");
		$this->firstJoin->save();
		return true;
	}
	
	public function onJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		if($this->isFirst($player)){
			if($this->getConfig()->get("on-first-join")){
				$this->sendForm($player, $this->getConfig()->get("join-first-form"));
			}
		} else {
			if($this->getConfig()->get("on-join")){
				$this->sendForm($player, $this->getConfig()->get("join-form"));
			}
		}
		return true;
	}
	
	public function sendForm(Player $player, string $sub): bool{
		$form = new SimpleForm(function (Player $player, int $data = null) use($sub){
            if ($data === null) {
                return false;
            }
           foreach($this->getConfig()->getAll()["sub-form"][$sub]["button"][$data]["action"] as $commands){
           	$this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender(), $this->replaceTag($player, $commands));
           }
        });
        $form->setTitle($this->getConfig()->get("sub-form")[$sub]["title"]);
        $form->setContent($this->getFormContent($player, $sub));
        foreach($this->getConfig()->getAll()["sub-form"][$sub]["button"] as $id => $value){
        	$form->addButton($value["text"]);
        }
        $form->sendToPlayer($player);
        return true;
	}
	
	private function getFormContent(Player $player, string $sub): string{
		$content = $this->getConfig()->get("sub-form")[$sub]["content"];
		$last = count($content); //For remove new line in last line
		$count = 1; // For remove new line in last line
		$result = ""; // For to be accessible
		foreach($content as $text){
			if($count <= $last){
				$result .= $this->replaceTag($player, $text) . C::EOL;
			} else {
				$result .= $this->replaceTag($player, $text);
			}
			$count++;
		}
		return $result;
	}
	
	private function replaceTag(Player $player, string $tag): string{
		$tag = str_replace("{NAME}", $player->getName(), $tag);
		$tag = str_replace("{DISPLAY_NAME}", $player->getDisplayName(), $tag);
		return $tag;
	}
}
