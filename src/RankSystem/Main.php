<?php

declare(strict_types=1);

namespace RankSystem;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use RankSystem\Command\RankCommand;
use RankSystem\Command\RankXPCommand;

class Main extends PluginBase {

    private RankManager $rankManager;
    private EventListener $eventListener;

    protected function onEnable(): void {
        $this->saveResource("config.yml");
        $this->rankManager = new RankManager($this, new Config($this->getDataFolder() . "config.yml"));
        $this->eventListener = new EventListener($this->rankManager);

        $this->getServer()->getPluginManager()->registerEvents($this->eventListener, $this);

        // Register commands
        $this->getServer()->getCommandMap()->register("ranksystem", new RankCommand($this));
        $this->getServer()->getCommandMap()->register("ranksystem", new RankXPCommand($this));
    }

    protected function onDisable(): void {
        $this->rankManager->savePlayerData(); // Save player data when the server stops
    }

    public function getRankManager(): RankManager {
        return $this->rankManager;
    }

    public function getEventListener(): EventListener {
        return $this->eventListener;
    }
}