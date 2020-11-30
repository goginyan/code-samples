<?php

namespace Notifications\Repositories;

use Notifications\DataTransferObjects\Models\NotificationLogDTO;
use Notifications\Enums\NotificationChannelEnum;
use Notifications\Models\NotificationLog;

class NotificationLogRepository
{
    /**
     * @param NotificationLogDTO $logDTO
     * @return NotificationLogDTO
     */
    public static function create(NotificationLogDTO $logDTO): NotificationLogDTO
    {
        $data = new NotificationLog;
        $data->notification_type_trigger_id = $logDTO->notificationTypeTriggerID;
        $data->notification_channel_id = NotificationChannelEnum::fromSlugToID($logDTO->notificationChannel);
        $data->store_id = $logDTO->storeID;
        $data->shipment_id = $logDTO->shipmentID;
        $data->newest_shipments_history_info_id = $logDTO->newestShipmentsHistoryInfoID;
        $data->job_id = $logDTO->jobId;
        $data->completed_at = $logDTO->completedAt;
        $data->execute_after = $logDTO->executeAfter;
        $data->status = null;

        $data->save();

        return new NotificationLogDTO(
            $data->id,
            null,
            $data->status,
            $logDTO->notificationTypeTrigger,
            $logDTO->notificationChannel,
            $logDTO->storeID,
            $data->shipment_id,
            $data->notification_type_trigger_id,
            $data->newest_shipments_history_info,
            $data->job_id,
            $data->completed_at,
            $data->execute_after,
            $data->created_at,
            $data->updated_at
        );
    }

    /**
     * @param NotificationLogDTO $logDTO
     * @return bool
     */
    public static function updateStatus(NotificationLogDTO $logDTO): bool
    {
        $data = NotificationLog::query()->find($logDTO->id);
        if (! is_null($data)) {
            $data->status = $logDTO->status->value;

            return $data->save();
        }

        return false;
    }
}
