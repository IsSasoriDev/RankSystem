<?php

declare(strict_types=1);

namespace RankSystem\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use RankSystem\Main;
use RankSystem\RankUI;

class RankCommand extends Command {

    public function __construct(private Main $plugin) {
        parent::__construct("rank", "Open the rank UI", "/rank", ["ranks"]);
        $this->setPermission("ranksystem.command.rank");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if (!$this->testPermission($sender)) {
            return;
        }

        if ($sender instanceof Player) {
            $eventListener = $this->plugin->getEventListener();
            $sender->sendForm(new RankUI($this->plugin->getRankManager(), $sender, $eventListener));
        } else {
            $sender->sendMessage("This command can only be used in-game.");
        }
    }
}