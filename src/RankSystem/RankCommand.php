<?php

namespace RankSystem;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class RankCommand extends Command {

    private Main $plugin;

    public function __construct(Main $plugin) {
        parent::__construct("rank", "Open the rank UI", "/rank", ["ranks"]);
        $this->setPermission("ranksystem.command.rank"); // Set the permission here
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if (!$this->testPermission($sender)) {
            return; // Stop execution if the sender doesn't have permission
        }

        if ($sender instanceof Player) {
            // Pass both RankManager and Player to the RankUI constructor
            $sender->sendForm(new RankUI($this->plugin->getRankManager(), $sender));
        } else {
            $sender->sendMessage("This command can only be used in-game.");
        }
    }
}