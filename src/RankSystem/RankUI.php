<?php

declare(strict_types=1);

namespace RankSystem;

use pocketmine\form\Form;
use pocketmine\player\Player;

class RankUI implements Form {

    public function __construct(
        private RankManager $rankManager,
        private Player $player,
        private EventListener $eventListener
    ) {}

    public function handleResponse(Player $player, $data): void {
        // Handle form response (if needed)
    }

    public function jsonSerialize(): array {
        $rank = $this->rankManager->getRank($this->player);
        $color = $this->rankManager->getRankColor($rank);
        $xp = $this->rankManager->getXP($this->player);

        // Get player statistics
        $stats = $this->eventListener->getPlayerStats($this->player);
        $deaths = $stats["deaths"];
        $kills = $stats["kills"];
        $playtimeSeconds = $stats["playtime"] ?? 0;

        // Calculate playtime in hours, minutes, and seconds
        $hours = intval($playtimeSeconds / 3600);
        $minutes = intval(($playtimeSeconds % 3600) / 60);
        $seconds = $playtimeSeconds % 60;

        $content = "§l§4Your Rank: " . $color . $rank . "\n";
        $content .= "Your XP: " . $xp . "\n";
        $content .= "Playtime: " . $hours . "h " . $minutes . "m " . $seconds . "s\n";
        $content .= "Kills: " . $kills . "\n";
        $content .= "Deaths: " . $deaths;

        return [
            "type" => "form",
            "title" => "Rank System",
            "content" => $content,
            "buttons" => [
                ["text" => "Close"]
            ]
        ];
    }
}