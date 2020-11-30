<?php

declare(strict_types=1);

namespace Notifications\DataTransferObjects\Models;

use DateTime;
use Illuminate\Contracts\Support\Arrayable;
use Notifications\Enums\NotificationChannelEnum;
use Notifications\Enums\NotificationLogStatusEnum;
use Notifications\Enums\NotificationTriggerEnum;

class NotificationLogDTO implements Arrayable
{
    public ?int $id;
    public ?NotificationLogStatusEnum $status;
    public ?string $notificationGUID;
    public NotificationTriggerEnum $notificationTypeTrigger;
    public NotificationChannelEnum $notificationChannel;
    public int $storeID;
    public int $shipmentID;
    public int $notificationTypeTriggerID;
    public ?int $newestShipmentsHistoryInfoID;
    public ?string $jobId;
    public ?DateTime $completedAt;
    public ?DateTime $executeAfter;
    public ?DateTime $createdAt;
    public ?DateTime $updatedAt;

    public function __construct(
        ?int $id,
        ?string $notificationGUID,
        ?NotificationLogStatusEnum $status,
        NotificationTriggerEnum $notificationTypeTrigger,
        NotificationChannelEnum $notificationChannel,
        int $storeID,
        int $shipmentID,
        int $notificationTypeTriggerID,
        ?int $newestShipmentsHistoryInfoID,
        ?string $jobId,
        ?DateTime $completedAt,
        ?DateTime $executeAfter,
        ?DateTime $createdAt,
        ?DateTime $updatedAt
    ) {
        $this->id = $id;
        $this->notificationGUID = $notificationGUID;
        $this->status = $status;
        $this->notificationTypeTrigger = $notificationTypeTrigger;
        $this->notificationChannel = $notificationChannel;
        $this->storeID = $storeID;
        $this->shipmentID = $shipmentID;
        $this->notificationTypeTriggerID = $notificationTypeTriggerID;
        $this->newestShipmentsHistoryInfoID = $newestShipmentsHistoryInfoID;
        $this->jobId = $jobId;
        $this->completedAt = $completedAt;
        $this->executeAfter = $executeAfter;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function toArray(): array
    {
        // TODO: seems this is up to seconds and not miliseconds. Maybe is good to add milsec calc.
        $executionTime = null;
        if (! is_null($this->completedAt) && ! is_null($this->createdAt)) {
            $executionTime = (string) round(($this->completedAt->getTimestamp() - $this->createdAt->getTimestamp()), 2);
        }

        return array_filter([
            'id' => $this->id,
            'status' => $this->status ? $this->status->value : null,
            'notificationGUID' => $this->notificationGUID,
            'notification_type_trigger' => $this->notificationTypeTrigger ? NotificationTriggerEnum::toSlug($this->notificationTypeTrigger) : null,
            'notification_channel' => $this->notificationChannel ? $this->notificationChannel->value : null,
            'store_id' => $this->storeID,
            'shipment_id' => $this->shipmentID,
            'notification_type_trigger_id' => $this->notificationTypeTriggerID,
            'newest_shipments_history_info_id' => $this->newestShipmentsHistoryInfoID,
            'job_id' => $this->jobId,
            'exec_time_sec' => $executionTime,
            'completed_at' => $this->completedAt ? $this->completedAt->format('Y-m-d H:i:s') : null,
            'execute_after' => $this->executeAfter ? $this->executeAfter->format('Y-m-d H:i:s') : null,
            'created_at' => $this->createdAt ? $this->createdAt->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updatedAt ? $this->updatedAt->format('Y-m-d H:i:s') : null,
        ]);
    }
}
