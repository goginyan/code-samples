<?php

namespace Notifications\Repositories;

use App\Services\SentryService;
use Data\Enums\FeatureEnum;
use Illuminate\Support\Facades\DB;
use Notifications\DataTransferObjects\Models\NotificationConfigurationDTO;
use Notifications\DataTransferObjects\Models\NotificationTypeTriggerDTO;
use Notifications\Enums\NotificationChannelEnum;
use Notifications\Enums\NotificationTriggerEnum;
use Notifications\Models\NotificationChannel;
use Notifications\Models\NotificationConfiguration;
use Notifications\Models\NotificationTypeTrigger;

class NotificationConfigurationRepository
{
    /**
     * Deactivates all configurations of store except ones provided in $newFeaturesIds.
     *
     * @param int $storeId
     * @param int[] $newFeaturesIds
     */
    public function deactivateUnusedConfigurations(int $storeId, array $newFeaturesIds): void
    {
        $channelIds = NotificationChannel::query()->whereIn('feature_id', $newFeaturesIds)->pluck('id');

        NotificationConfiguration::query()->where('store_id', $storeId)
            ->whereNotIn('notification_channel_id', $channelIds)->update(['is_active' => false]);
    }

    /**
     * Get configuration/configurations or create new ones;.
     *
     * @param int $storeId
     * @param NotificationChannelEnum $channel
     * @param bool $active
     * @return NotificationConfigurationDTO
     */
    public function getOrCreate(int $storeId, NotificationChannelEnum $channel, bool $active): NotificationConfigurationDTO
    {
        $defaultConfig = NotificationConfiguration::getDefaultConfigForChannel($channel);

        // get existing configuration or create new one with default 'json_config'
        $config = NotificationConfiguration::query()->firstOrNew(
            ['store_id' => $storeId, 'notification_channel_id' => NotificationChannelEnum::fromSlugToID($channel)],
            ['json_config' => $defaultConfig]
        );

        $config->is_active = $active;
        $config->save();

        // TODO: Here is great to check if exist or not. If yes return it. If not, create it, and create default type trigger records in database

        return NotificationConfigurationDTO::fromModel($config);
    }

    /**
     * @param int $storeId
     * @param int $channelId
     * @return null|NotificationConfigurationDTO
     */
    public function findByChannel(int $storeId, NotificationChannelEnum $notificationChannelEnum): ?NotificationConfigurationDTO
    {
        $notificationChannelID = NotificationChannelEnum::fromSlugToID($notificationChannelEnum);

        $model = NotificationConfiguration::query()
            ->where('store_id', $storeId)
            ->where('notification_channel_id', $notificationChannelID)
            ->first();

        if (isset($model)) {
            return NotificationConfigurationDTO::fromModel($model);
        }

        return null;
    }

    public function update(NotificationConfigurationDTO $notificationConfigurationDTO)
    {
        /// IMPORTANT WE NEED TO USE ::find()->update in order for mutators to work
        /// NotificationConfiguration::where('id', $notificationConfigurationDTO->id)->update()
        /// does not fire mutators for some reason
        NotificationConfiguration::find($notificationConfigurationDTO->id)->update($notificationConfigurationDTO->toEloquentUpdate());
    }

    public function findByFeature(int $storeId, FeatureEnum $featureEnum): ?NotificationConfigurationDTO
    {
        $notificationChannelEnum = NotificationChannelEnum::fromFeatureEnum($featureEnum);
        if (! $notificationChannelEnum) {
            return null;
        }

        return $this->findByChannel($storeId, $notificationChannelEnum);
    }

    /**
     * @param NotificationConfigurationDTO $data
     * @param bool $flag
     */
    public function updateActiveFlag(NotificationConfigurationDTO $data, bool $flag): void
    {
        NotificationConfiguration::query()->where('id', $data->id)->update(['is_active' => $flag]);
    }

    public function updateOrInsertTypeTrigger(NotificationTypeTriggerDTO $triggerDTO): NotificationTypeTriggerDTO
    {
        if (is_null($triggerDTO->id)) {
            $result = NotificationTypeTrigger::create($triggerDTO->toEloquentUpdate());

            return NotificationTypeTriggerDTO::fromModel($result);
        }
        NotificationTypeTrigger::find($triggerDTO->id)->update($triggerDTO->toEloquentUpdate());

        return $triggerDTO;
    }

    /**
     * Based on trigger select which channels are active for a store.
     * @param int $storeID
     * @param NotificationTriggerEnum $trigger
     * @return NotificationConfigurationDTO[]|null
     */
    public function getAllActiveNotificationChannelsPerTrigger(int $storeID, NotificationTriggerEnum $trigger): ?array
    {
        $selectQuery = 'SELECT
                            notification_configurations.id
                            ,notification_configurations.store_id
                            ,notification_configurations.notification_channel_id
                            ,notification_configurations.json_config
                            ,notification_configurations.is_active
                        FROM
                            notification_type_triggers
                        INNER JOIN notification_configurations
                            ON notification_configurations.id = notification_type_triggers.notification_configuration_id
                        WHERE
                            notification_configurations.is_active = true
                            AND notification_type_triggers.is_active = true
                            AND notification_configurations.store_id = '.$storeID.'
                            AND notification_type_triggers.trigger_id = '.$trigger->value.';';

        return $this->_getNotificationChannelsWithTriggers($selectQuery, [$trigger]);
    }

    /**
     * Get notification channel with all its triggers.
     * @param int $storeID
     * @param NotificationChannelEnum $notificationChannelEnum
     * @return NotificationConfigurationDTO|null
     */
    public function getNotificationChannelWithAllTriggers(int $storeID, NotificationChannelEnum $notificationChannelEnum): ?NotificationConfigurationDTO
    {
        $notificationChannelId = NotificationChannelEnum::fromSlugToID($notificationChannelEnum);
        $selectQuery = 'SELECT
                            id,
                            store_id,
                            notification_channel_id,
                            json_config,
                            is_active
                        FROM
                            notification_configurations
                        WHERE
                            notification_channel_id = '.$notificationChannelId.'
                            AND store_id = '.$storeID.';';

        $data = $this->_getNotificationChannelsWithTriggers($selectQuery, null, true);
        if (is_null($data)) {
            return null;
        } elseif (count($data) == 1) {
            return $data[0];
        } else {
            SentryService::dataMessage('Recheck why we get multiple notification options! ', SentryService::ERROR, null, [
                'storeID' => $storeID,
                'channel_slug' => NotificationChannelEnum::fromSlugToID($notificationChannelEnum),
                'select_query' => $selectQuery,
                'data' => $data,
            ]);

            return $data[0];
        }
    }

    /**
     * @param string $sql Notification select query
     * @param NotificationTriggerEnum[]|null $filterByTriggers Passing null, will result in returning for all
     * @param bool $withInactives
     * @return NotificationConfigurationDTO[]|null
     */
    private function _getNotificationChannelsWithTriggers(string $sql, ?array $filterByTriggers, bool $withInactives = false): ?array
    {
        $triggerIDs = [];
        if (! is_null($filterByTriggers)) {
            foreach ($filterByTriggers as $trigger) {
                $triggerIDs[] = $trigger->value;
            }
        }

        $results = DB::select($sql);

        $notificationConfigList = [];
        foreach ($results as $row) {
            $notificationChannelEnum = NotificationChannelEnum::fromIDToSlug($row->notification_channel_id);
            $notificationConfigDTO = new NotificationConfigurationDTO(
                $row->id,
                $row->store_id,
                $notificationChannelEnum,
                $row->is_active,
                NotificationConfiguration::fromJSONtoDTO(json_decode($row->json_config, true), $notificationChannelEnum),
            );

            $notificationConfigList[] = $notificationConfigDTO;
        }

        $andActive = null;

        foreach ($notificationConfigList as $notificationConfigDTO) {
            $typeTriggers = [];

            $results = NotificationTypeTrigger::query()
                ->where('notification_configuration_id', $notificationConfigDTO->id)
                ->when(! $withInactives, function ($q) {
                    return $q->where('is_active', true);
                })->when($triggerIDs && count($triggerIDs), function ($q) use ($triggerIDs) {
                    return $q->whereIn('trigger_id', $triggerIDs);
                })->orderBy('id', 'ASC')->get();

            foreach ($results as $row) {
                $notificationTypeTrigger = new NotificationTypeTriggerDTO(
                    $row->id,
                    $row->store_id,
                    $row->notification_configuration_id,
                    $notificationConfigDTO->notificationChannel,
                    NotificationTriggerEnum::make($row->trigger_id),
                    $row->is_active,
                    $row->filter_duration_label,
                    $row->filter_duration_minutes,
                    $row->sent_with_delay_label,
                    $row->sent_with_delay_minutes,
                    $row->filter_status,
                    NotificationTypeTrigger::fromJSONArrayToShipmentSubStatuses(json_decode($row->filter_substatus, true)),
                    NotificationTypeTrigger::fromJSONtoDTO(json_decode($row->json_config, true), $notificationConfigDTO->notificationChannel)
                );

                $typeTriggers[] = $notificationTypeTrigger;
            }
            if (! empty($typeTriggers)) {
                $notificationConfigDTO->typeTriggers = $typeTriggers;
            }
        }

        if (empty($notificationConfigList)) {
            return null;
        }

        return $notificationConfigList;
    }
}
