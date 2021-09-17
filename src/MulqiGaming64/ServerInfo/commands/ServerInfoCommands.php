<?php

namespace MulqiGaming64\ServerInfo\commands;

use pocketmine\Player;
use pocketmine\math\Vector3;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\utils\TextFormat as C;
use MulqiGaming64\ServerInfo\ServerInfo;

class ServerInfoCommands extends PluginCommand {
	
	/** @var ServerInfo $plugin */
	private $plugin;
	
	public function __construct(string $name, ServerInfo $plugin){
        parent::__construct($name, $plugin);
		$this->setDescription("ServerInfo");
		$this->setAliases(["info", "servinfo"]);
        $this->plugin = $plugin;
    }
    
    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender instanceof Player){
			$sender->sendMessage("Use Commands In Game Please");
            return true;
        }
        $available = [];
        foreach($this->plugin->getConfig()->get("sub-form") as $subform => $value){
        	$available[] = $subform;
        }
        if(!isset($args[0])){
        	$usage = "Usage: /serverinfo Sub" . C::EOL . C::GREEN . "Sub: " . C::EOL . C::WHITE;
        	foreach($available as $sub){
        		$usage .= "- " . $sub . C::EOL;
        	}
        	$sender->sendMessage($usage);
        } else {
        	$subcmd = strtolower($args[0]);
        	if(!in_array($subcmd, $available)){
        		$usage = "Usage: /serverinfo Sub" . C::EOL . C::GREEN . "Sub: " . C::EOL . C::WHITE;
        		foreach($available as $sub){
        			$usage .= "- " . $sub . C::EOL;
        		}
        		$sender->sendMessage($usage);
        		return false;
        	} else {
        		$this->plugin->sendForm($sender, $subcmd);
        		return true;
        	}
		}
	}
}
