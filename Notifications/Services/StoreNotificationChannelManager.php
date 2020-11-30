<?php

namespace Notifications\Services;

use Notifications\Enums\NotificationChannelEnum;

class StoreNotificationChannelManager
{
    public static function syncByFeatures(int $storeID, array $featureIDs): void
    {
        // TODO: implement turn on and off different channels, as well as create records where neeed.
        // create rec
    }

    private static function _createChannelConfig(int $storeID, NotificationChannelEnum $channel): void
    {
        // TODO: creates default db records when notification channel is available.
        // create rec
    }

    public static function getAllAvailable(int $storeID): void
    {
        // TODO: return data informatiom about all available channels for a specific store.s
    }

    public static function getSpecificChannelConfig(int $storeID, NotificationChannelEnum $channel): void
    {
        // TODO: return data informatiom about all available channels for a specific store.
    }

    public static function resetConfigToDefault(int $storeID, NotificationChannelEnum $channel): void
    {
        // TODO: reset store notification channels to default.
    }
}
