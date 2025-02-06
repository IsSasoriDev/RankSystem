<?php

namespace RankSystem;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use RankSystem\RankManager;
use RankSystem\EventListener;
use RankSystem\RankCommand;

class Main extends PluginBase {

    private RankManager $rankManager;

    public function onDisable(): void {
        $this->getRankManager()->savePlayerData(); // Save player data when the server stops
    }

    public function onEnable(): void {
        $this->saveResource("config.yml");
        $this->rankManager = new RankManager($this, new Config($this->getDataFolder() . "config.yml"));
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this->rankManager), $this);

        // Register commands
        $this->getServer()->getCommandMap()->register("ranksystem", new RankCommand($this));
        $this->getServer()->getCommandMap()->register("ranksystem", new RankXPCommand($this)); // Register the /rankxp command
    }

    public function getRankManager(): RankManager {
        return $this->rankManager;
    }
}