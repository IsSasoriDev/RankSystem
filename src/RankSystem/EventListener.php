<?php

namespace RankSystem;

use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\player\Player;

class EventListener implements Listener {

    private RankManager $rankManager;

    public function __construct(RankManager $rankManager) {
        $this->rankManager = $rankManager;
    }

    public function onBlockBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();
        $this->rankManager->addXP($player, 1); // Add 1 XP per block mined
    }
}