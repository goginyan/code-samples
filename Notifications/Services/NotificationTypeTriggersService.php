<?php

declare(strict_types=1);

namespace Notifications\Services;

use Notifications\DataTransferObjects\Models\NotificationTypeTriggerDTO;

class NotificationTypeTriggersService
{
    /**
     * @param array $triggers
     * @return NotificationTypeTriggerDTO[]
     */
    public static function createDTOCollectionFromArray(array $triggers): array
    {
        $result = [];

        foreach ($triggers as $trigger) {
            $result[] = new NotificationTypeTriggerDTO(
                $trigger['id'] ?? null,
                $trigger['store_id'] ?? null,
                $trigger['notification_configuration_id'] ?? null,
                $trigger['notification_channel'] ?? null,
                $trigger['trigger'] ?? null,
                $trigger['is_active'] ?? null,
                $trigger['filter_duration_label'] ?? null,
                $trigger['filter_duration_minutes'] ?? null,
                $trigger['sentWith_delay_label'] ?? null,
                $trigger['sentWith_delay_minutes'] ?? null,
                $trigger['filter_status'] ?? null,
                $trigger['filter_substatus'] ?? null,
                $trigger['config'] ?? null
            );
        }

        return $result;
    }
}
