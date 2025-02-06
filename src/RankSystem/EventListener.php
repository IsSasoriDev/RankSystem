<?php

declare(strict_types=1);

namespace RankSystem;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\player\Player;

class EventListener implements Listener {

    private array $playerStats = [];
    private array $playtime = [];

    public function __construct(private RankManager $rankManager) {}

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $name = strtolower($player->getName());
        $this->playtime[$name] = time(); // Store the join time
    }

    public function onPlayerQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();
        $name = strtolower($player->getName());

        // Calculate playtime in seconds
        if (isset($this->playtime[$name])) {
            $playtimeSeconds = time() - $this->playtime[$name];
            $this->playerStats[$name]["playtime"] = ($this->playerStats[$name]["playtime"] ?? 0) + $playtimeSeconds;
            unset($this->playtime[$name]);
        }

        // Save player stats when they leave
        if (isset($this->playerStats[$name])) {
            $this->rankManager->savePlayerStats($name, $this->playerStats[$name]);
        }
    }

    public function onBlockBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();
        $this->rankManager->addXP($player, 1); // Add 1 XP per block mined

        // Check if the player has reached a new rank
        $newRank = $this->rankManager->checkRankUp($player);
        if ($newRank !== null) {
            $color = $this->rankManager->getRankColor($newRank);
            $player->sendMessage("§aCongratulations! You have reached the rank: " . $color . $newRank);
            $this->rankManager->getPlugin()->getServer()->broadcastMessage("§a" . $player->getName() . " has reached the rank: " . $color . $newRank);
        }
    }

    public function onPlayerDeath(PlayerDeathEvent $event): void {
        $player = $event->getPlayer();
        $name = strtolower($player->getName());

        // Increment death count
        if (!isset($this->playerStats[$name])) {
            $this->playerStats[$name] = ["deaths" => 0, "kills" => 0, "playtime" => 0];
        }
        $this->playerStats[$name]["deaths"]++;
    }

    public function onEntityDamageByEntity(EntityDamageByEntityEvent $event): void {
        $damager = $event->getDamager();
        $victim = $event->getEntity();

        if ($damager instanceof Player && $victim instanceof Player) {
            $name = strtolower($damager->getName());

            // Increment kill count if the victim dies
            if ($victim->getHealth() - $event->getFinalDamage() <= 0) {
                if (!isset($this->playerStats[$name])) {
                    $this->playerStats[$name] = ["deaths" => 0, "kills" => 0, "playtime" => 0];
                }
                $this->playerStats[$name]["kills"]++;
            }
        }
    }

    public function getPlayerStats(Player $player): array {
        $name = strtolower($player->getName());
        return $this->playerStats[$name] ?? ["deaths" => 0, "kills" => 0, "playtime" => 0];
    }
}