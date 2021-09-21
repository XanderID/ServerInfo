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
        foreach($this->plugin->getConfig()->get("subcategory-form") as $subcategoryform => $value){
        	$available[] = $subcategoryform;
        }
        if(!isset($args[0])){
        	$usage = "Usage: /serverinfo subcategory" . C::EOL . C::GREEN . "subcategory: " . C::EOL . C::WHITE;
        	foreach($available as $subcategory){
        		$usage .= "- " . $subcategory . C::EOL;
        	}
        	$sender->sendMessage($usage);
        } else {
        	$subcategorycmd = strtolower($args[0]);
        	if(!in_array($subcategorycmd, $available)){
        		$usage = "Usage: /serverinfo subcategory" . C::EOL . C::GREEN . "subcategory: " . C::EOL . C::WHITE;
        		foreach($available as $subcategory){
        			$usage .= "- " . $subcategory . C::EOL;
        		}
        		$sender->sendMessage($usage);
        		return false;
        	} else {
        		$this->plugin->sendForm($sender, $subcategorycmd);
        		return true;
        	}
		}
	}
}
