<?php

namespace RankSystem;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class RankXPCommand extends Command {

    private Main $plugin;

    public function __construct(Main $plugin) {
        parent::__construct("rankxp", "Give XP to a player for ranking", "/rankxp <player> <amount>", ["rxp"]);
        $this->setPermission("ranksystem.command.rankxp");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if (!$this->testPermission($sender)) {
            return; // Stop execution if the sender doesn't have permission
        }

        if (count($args) < 2) {
            $sender->sendMessage(TextFormat::RED . "Usage: /rankxp <player> <amount>");
            return;
        }

        $playerName = $args[0];
        $amount = (int) $args[1];

        if ($amount < 0) {
            $sender->sendMessage(TextFormat::RED . "The amount must be a positive number.");
            return;
        }

        $player = $this->plugin->getServer()->getPlayerExact($playerName);
        if ($player === null) {
            $sender->sendMessage(TextFormat::RED . "Player not found.");
            return;
        }

        $rankManager = $this->plugin->getRankManager();
        $rankManager->addXP($player, $amount);

        $sender->sendMessage(TextFormat::GREEN . "Gave " . $player->getName() . " " . $amount . " XP for ranking.");
        $player->sendMessage(TextFormat::GREEN . "You received " . $amount . " XP for ranking.");
    }
}