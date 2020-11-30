<?php

namespace Notifications\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Notifications\DataTransferObjects\Models\NotificationConfigurationDTO;
use Notifications\DataTransferObjects\Models\NotificationTypeTriggerDTO;
use Notifications\Enums\NotificationChannelEnum;
use Notifications\Enums\NotificationTriggerEnum;
use Notifications\Models\NotificationTypeTrigger;

class NotificationTypeTriggersRepository
{
    /**
     * @param int $storeId
     * @param int $notificationConfigurationId
     * @param NotificationChannelEnum $notificationChannelEnum
     * @param NotificationTypeTriggerDTO[]|null $triggers
     * @return NotificationConfigurationDTO
     */
    public static function updateByTriggers(
        int $storeId,
        int $notificationConfigurationId,
        NotificationChannelEnum $notificationChannelEnum,
        ?array $triggers
    ): NotificationConfigurationDTO {
        $notificationChannelId = NotificationChannelEnum::fromSlugToID($notificationChannelEnum);
        if (! is_null($triggers)) {
            foreach ($triggers as $trigger) {
                $typeTrigger = NotificationTypeTrigger::query()->where([
                    'store_id' => $storeId,
                    'notification_configuration_id' => $notificationConfigurationId,
                    'notification_channel_id' => $notificationChannelId,
                    'id' => $trigger->id,
                ])->first();

                if (empty($typeTrigger)) {
                    continue;
                }
                $typeTrigger->store_id = $storeId;
                $typeTrigger->notification_configuration_id = $notificationConfigurationId;
                $typeTrigger->notification_channel_id = $notificationChannelId;
                $typeTrigger->is_active = $trigger->isActive;
                $typeTrigger->save();
            }
        }

        $repo = new NotificationConfigurationRepository();

        return $repo->getNotificationChannelWithAllTriggers($storeId, $notificationChannelEnum);
    }

    /**
     * @param int $storeId
     * @param int $notificationConfigurationId
     * @param int $notificationChannelId
     * @param NotificationTypeTriggerDTO[] $triggers
     * @return bool
     */
    public static function checkIfTypeTriggersExists(
        int $storeId,
        int $notificationConfigurationId,
        int $notificationChannelId,
        array $triggers
    ): bool {
        foreach ($triggers as $trigger) {
            $typeTrigger = NotificationTypeTrigger::query()->where([
                'store_id' => $storeId,
                'notification_configuration_id' => $notificationConfigurationId,
                'notification_channel_id' => $notificationChannelId,
                'id' => $trigger->id,
            ])->first();

            if (empty($typeTrigger)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param int $storeID
     * @param int $configId
     * @param NotificationTriggerEnum[] $triggers
     * @param bool $isActive
     */
    public static function createKlaviyoTriggersIfNotExists(int $storeID, int $configId, array $triggers, bool $isActive): void
    {
        foreach ($triggers as $trigger) {
            $typeTrigger = NotificationTypeTrigger::query()->where([
                'store_id' => $storeID,
                'notification_configuration_id' => $configId,
                'notification_channel_id' => NotificationChannelEnum::fromSlugToID(NotificationChannelEnum::KLAVIYO()),
                'trigger_id' => $trigger->value,
            ])->first();

            if (empty($typeTrigger)) {
                NotificationTypeTrigger::query()->create([
                    'store_id' => $storeID,
                    'notification_configuration_id' => $configId,
                    'notification_channel_id' => NotificationChannelEnum::fromSlugToID(NotificationChannelEnum::KLAVIYO()),
                    'trigger_id' => $trigger->value,
                    'is_active' => $isActive,
                ]);
            }
        }
    }

    /**
     * @param array $ids
     * @return Collection|null
     */
    public static function getByIds(array $ids): ?Collection
    {
        return NotificationTypeTrigger::query()->whereIn('id', $ids)->get();
    }

    /**
     * @param array $ids
     * @return NotificationTypeTriggerDTO[]
     */
    public static function createDTOCollectionFromArrayOfIds(array $ids): array
    {
        $result = [];

        $typeTriggers = self::getByIds($ids);

        if ($typeTriggers) {
            foreach ($typeTriggers as $typeTrigger) {
                $result[] = NotificationTypeTriggerDTO::fromModel($typeTrigger);
            }
        }

        return $result;
    }

    /**
     * @param int $storeID
     * @param int $triggerID
     * @param int $channelID
     * @return NotificationTypeTriggerDTO|null
     */
    public static function getTypeTrigger(int $storeID, int $triggerID, int $channelID): ?NotificationTypeTriggerDTO
    {
        $typeTrigger = NotificationTypeTrigger::query()->where([
            'store_id' => $storeID,
            'notification_channel_id' => $channelID,
            'trigger_id' => $triggerID,
        ])->first();

        if (! empty($typeTrigger)) {
            return NotificationTypeTriggerDTO::fromModel($typeTrigger);
        }

        return null;
    }
}
