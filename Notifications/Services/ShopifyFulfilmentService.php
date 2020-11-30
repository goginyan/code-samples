<?php

namespace Notifications\Services;

use Notifications\DataTransferObjects\Models\NotificationTypeTriggerDTO;
use Notifications\Enums\NotificationChannelEnum;
use Notifications\Enums\NotificationTriggerEnum;
use Notifications\Repositories\NotificationConfigurationRepository;

class ShopifyFulfilmentService
{
    /**
     * Get list of possible Slack Notification Triggers.
     * @return NotificationTriggerEnum[]
     */
    public static function getPossibleTriggers(): array
    {
        return [
            NotificationTriggerEnum::SHIPMENT_NEW_STATUS(),
        ];
    }

    public static function createDefaultTriggers(?int $storeID, ?bool $isActive)
    {
        $repo = new NotificationConfigurationRepository();

        $notificationChannelEnum = NotificationChannelEnum::SHOPIFY_FULFILMENT_EVENT();

        $notificationConfigurationDTO = $repo->getNotificationChannelWithAllTriggers($storeID, $notificationChannelEnum);

        $typeTriggers = $notificationConfigurationDTO->typeTriggers;

        if (is_null($typeTriggers)) {
            // insert new record for Slack Type triggers
            $triggerDTO = new NotificationTypeTriggerDTO(
                null,
                 $storeID,
                 $notificationConfigurationDTO->id,
                 $notificationChannelEnum,
                 NotificationTriggerEnum::SHIPMENT_NEW_STATUS(),
                 $isActive,
                 null,
                 null,
                 null,
                 null,
                 null,
                 null,
                 null
            );

            $triggerDTO = $repo->updateOrInsertTypeTrigger($triggerDTO);
        }

        return $triggerDTO;
    }
}
