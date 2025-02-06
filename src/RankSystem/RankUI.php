<?php

namespace RankSystem;

use pocketmine\form\Form;
use pocketmine\player\Player;

class RankUI implements Form {

    private RankManager $rankManager;
    private Player $player;

    public function __construct(RankManager $rankManager, Player $player) {
        $this->rankManager = $rankManager;
        $this->player = $player; // Store the player object
    }

    public function handleResponse(Player $player, $data): void {
        // Handle form response (if needed)
    }

    public function jsonSerialize(): array {
        $rank = $this->rankManager->getRank($this->player); // Use the stored player object
        $color = $this->rankManager->getRankColor($rank);
        return [
            "type" => "form",
            "title" => "§l§cRank System",
            "content" => "§l§6Your Rank: " . $color . $rank . "\n§l§6Your XP: " . $this->rankManager->getXP($this->player) ,
            "buttons" => [
                ["text" => "Close"]
            ]
        ];
    }
}