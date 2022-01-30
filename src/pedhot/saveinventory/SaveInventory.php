<?php

namespace pedhot\saveinventory;

use pocketmine\item\Item;
use pocketmine\player\Player;
use function array_map;

class SaveInventory
{

    /** @var array */
    private static array $inventories;

    public static function saveInventory(Player $player, bool $withClear = false): void {
        self::$inventories[$player->getUniqueId()->toString()] = [
            "armorInventory" => array_map(function (Item $item): array {
                return $item->jsonSerialize();
            }, $player->getArmorInventory()->getContents()),
            "inventory" => array_map(function (Item $item): array {
                return $item->jsonSerialize();
            }, $player->getInventory()->getContents())
        ];
        if ($withClear) {
            $player->getArmorInventory()->clearAll();
            $player->getInventory()->clearAll();
        }
    }

    public static function sendSavedInventory(Player $player): void {
        if (isset(self::$inventories[$player->getUniqueId()->toString()])) return;

        $inventory = self::$inventories[$player->getUniqueId()->toString()];

        $player->getArmorInventory()->setContents(array_map(function (array $data): Item {
            return Item::jsonDeserialize($data);
        }, $inventory["armorInventory"]));

        $player->getInventory()->setContents(array_map(function (array $data): Item {
            return Item::jsonDeserialize($data);
        }, $inventory["inventory"]));

        unset($inventory);
    }

}