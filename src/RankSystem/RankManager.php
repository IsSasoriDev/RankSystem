<?php

namespace RankSystem;

use pocketmine\player\Player;
use pocketmine\utils\Config;

class RankManager {

    private Config $config;
    private Config $playerData;
    private Main $plugin;

    public function __construct(Main $plugin, Config $config) {
        $this->plugin = $plugin;
        $this->config = $config;
        $this->playerData = new Config($this->plugin->getDataFolder() . "playerData.yml", Config::YAML);
    }

    public function getRank(Player $player): string {
        $xp = $this->getXP($player);
        $ranks = $this->config->get("ranks", []);
        foreach ($ranks as $rank => $data) {
            if ($xp >= $data["min_xp"] && $xp <= $data["max_xp"]) {
                return $rank;
            }
        }
        return "E Ranker"; // Default rank
    }

    public function getXP(Player $player): int {
        return $this->playerData->get(strtolower($player->getName()), 0);
    }

    public function addXP(Player $player, int $amount): void {
        $name = strtolower($player->getName());
        $currentXP = $this->getXP($player);
        $newXP = $currentXP + $amount;
        $this->playerData->set($name, $newXP);
        $this->playerData->save(); // Save the data to the file
    }

    public function checkRankUp(Player $player): ?string {
        $xp = $this->getXP($player);
        $currentRank = $this->getRank($player);
        $ranks = $this->config->get("ranks", []);

        foreach ($ranks as $rank => $data) {
            if ($xp >= $data["min_xp"] && $xp <= $data["max_xp"] && $rank !== $currentRank) {
                return $rank; // Player has reached a new rank
            }
        }

        return null; // Player has not reached a new rank
    }

    public function getRankColor(string $rank): string {
        return $this->config->get("ranks")[$rank]["color"] ?? "§7";
    }

    public function savePlayerData(): void {
        $this->playerData->save(); // Save player data to the file
    }
}